<?php declare(strict_types=1);

namespace Nwgncy\ProductsConfigurator\Storefront\Controller;

use Nwgncy\ProductsConfigurator\Utils\CommonFunctions;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
*/
class ProductConfiguratorController extends StorefrontController
{
    private EntityRepository $propertyGroupOptionRepository;
    private EntityRepository $productRepository;
    private EntityRepository $productVisibilityRepository;

    public function __construct(EntityRepository $propertyGroupOptionRepository, EntityRepository $productRepository, EntityRepository $productVisibilityRepository) {
        $this->propertyGroupOptionRepository = $propertyGroupOptionRepository;
        $this->productRepository = $productRepository;
        $this->productVisibilityRepository = $productVisibilityRepository;
    }

    /**
    * @Route("/products-configurator/available-options", name="storefront.respective-available-options.get", methods={"GET"}, defaults={"XmlHttpRequest"=true})
    */
    public function getAvailableOptions(Request $request, SalesChannelContext $salesChannelContext): JsonResponse
    {
        $context = $salesChannelContext->getContext();
        $salesChannelId = $salesChannelContext->getSalesChannelId();
        $optionsIdsQueryParam = $request->get('options');
        $minKeyValuesPairsQuery = $request->get('minValues');
        $maxKeyValuesPairsQuery = $request->get('maxValues');

        $minAvailableOptionIds = [];
        $maxAvailableOptionIds = [];

        $initialMinValues = [];
        $initialMaxValues = [];
        $selectDefaultCategory = false;
        $defaultCategoryId = '';

        if ($minKeyValuesPairsQuery) {
            $minKeyValuesPairs = json_decode(urldecode($minKeyValuesPairsQuery), true);
            if (is_array($minKeyValuesPairs)) {
                $minValuesPropertyGroupsIds = array_keys($minKeyValuesPairs);
                $initialMinValues = array_values($minKeyValuesPairs);
                foreach ($minValuesPropertyGroupsIds as $propertyGroupId) {
                    $criteria = new Criteria();
                    $criteria->addFilter(new EqualsFilter('groupId', $propertyGroupId));
                    $propertyGroupOptionsResult = $this->propertyGroupOptionRepository->search($criteria, $context);
                    $propertyGroupOptionsResultEntities = $propertyGroupOptionsResult->getEntities();
                    if ($propertyGroupOptionsResultEntities instanceof PropertyGroupOptionCollection) {
                        foreach ($propertyGroupOptionsResultEntities as $propertyGroupOptionsResultEntity) {
                            if ($propertyGroupOptionsResultEntity instanceof PropertyGroupOptionEntity) {
                                $id = $propertyGroupOptionsResultEntity->getId();
                                $name = $propertyGroupOptionsResultEntity->getTranslation('name') ?? '';
                                $value = CommonFunctions::parsefloatFromString($name);
                                if ($value !== null && $value >= $minKeyValuesPairs[$propertyGroupId]) {
                                    $minAvailableOptionIds[$propertyGroupId][] = $id;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($maxKeyValuesPairsQuery) {
            $maxKeyValuesPairs = json_decode(urldecode($maxKeyValuesPairsQuery), true);
            if (is_array($maxKeyValuesPairs)) {
                $maxValuesPropertyGroupsIds = array_keys($maxKeyValuesPairs);
                $initialMaxValues = array_values($maxKeyValuesPairs);
                foreach ($maxValuesPropertyGroupsIds as $propertyGroupId) {
                    $criteria = new Criteria();
                    $criteria->addFilter(new EqualsFilter('groupId', $propertyGroupId));
                    $propertyGroupOptionsResult = $this->propertyGroupOptionRepository->search($criteria, $context);
                    $propertyGroupOptionsResultEntities = $propertyGroupOptionsResult->getEntities();
                    if ($propertyGroupOptionsResultEntities instanceof PropertyGroupOptionCollection) {
                        foreach ($propertyGroupOptionsResultEntities as $propertyGroupOptionsResultEntity) {
                            if ($propertyGroupOptionsResultEntity instanceof PropertyGroupOptionEntity) {
                                $id = $propertyGroupOptionsResultEntity->getId();
                                $name = $propertyGroupOptionsResultEntity->getTranslation('name') ?? '';
                                $value = CommonFunctions::parsefloatFromString($name);
                                if ($value !== null && $value <= $maxKeyValuesPairs[$propertyGroupId]) {
                                    $maxAvailableOptionIds[$propertyGroupId][] = $id;
                                }
                            }
                        }
                    }
                }
            }
        }

        $commonMinAndMaxAvailableOptionIds = [];

        foreach ($minAvailableOptionIds as $propertyGroupId => $minOptions) {
            if (array_key_exists($propertyGroupId, $maxAvailableOptionIds)) {
                $maxOptions = $maxAvailableOptionIds[$propertyGroupId];
                $commonOptions = array_intersect($minOptions, $maxOptions);
                $commonMinAndMaxAvailableOptionIds[$propertyGroupId] = $commonOptions;
            } else {
                $commonMinAndMaxAvailableOptionIds[$propertyGroupId] = $minOptions;
            }
        }
        foreach ($maxAvailableOptionIds as $propertyGroupId => $maxOptions) {
            if (!array_key_exists($propertyGroupId, $commonMinAndMaxAvailableOptionIds)) {
                $commonMinAndMaxAvailableOptionIds[$propertyGroupId] = $maxOptions;
            }
        }

        $optionIdsArray = [];
        if ($optionsIdsQueryParam) {
            $optionsIdsQueryParamExploded = explode('|', $optionsIdsQueryParam);
            if ($optionsIdsQueryParamExploded) {
                $optionIdsArray = $optionsIdsQueryParamExploded;
            }
        } 
    
        $categoryPropertyCriteria = new Criteria();
        $categoryPropertyCriteria->addFilter(new EqualsFilter('groupId', $this->getCategoryPropertyGroupId()));
        $categoryPropertyOptions = $this->propertyGroupOptionRepository->search($categoryPropertyCriteria, $context)->getEntities();
    
        $categoryPropertyOptionIds = $categoryPropertyOptions->getIds();
    
        if (!empty($optionIdsArray)) {
            
            $foundCategoryProperty = false;
            foreach ($categoryPropertyOptionIds as $categoryPropertyOptionId) {
                
                if (in_array($categoryPropertyOptionId, $optionIdsArray)) {
                    $foundCategoryProperty = true;
                    break;
                }
            }

            if (!$foundCategoryProperty) {
                $defaultCategoryId = $this->getPreselectCategoryPropertyId();
                array_push($optionIdsArray, $defaultCategoryId);
                $selectDefaultCategory = true;
            }

        } else {
            $defaultCategoryId = $this->getPreselectCategoryPropertyId();
            array_push($optionIdsArray, $defaultCategoryId);
        }

        $visibleProductsIds = [];
        $visibilityCriteria = new Criteria();
        $visibilityCriteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
        $salesChannelVisibleProducts = $this->productVisibilityRepository->search($visibilityCriteria, $context)->getEntities();
        foreach ($salesChannelVisibleProducts as $productVisibility) {
            if ($productVisibility instanceof ProductVisibilityEntity) {
                $visibleProductsIds[] = $productVisibility->getProductId();
            }
        }

        $criteria = new Criteria();
        foreach ($optionIdsArray as $optionId) {
            $criteria->addFilter(new ContainsFilter('propertyIds', $optionId));
        }
        if (!empty($commonMinAndMaxAvailableOptionIds)) {
            $orFilters = [];
            foreach ($commonMinAndMaxAvailableOptionIds as $propertyGroupId => $propertyIds) {
                foreach ($propertyIds as $optionId) {
                    $orFilters[] = new ContainsFilter('propertyIds', $optionId);
                }
            }
            if (count($orFilters) > 0) {
                $criteria->addFilter(new OrFilter($orFilters));
            }
        }

        $products = $this->productRepository->search($criteria, $context)->getEntities();

        $availableOptionIds = [];
        foreach ($products as $product) {
            if ($product instanceof ProductEntity) {
                $productId = $product->getId();
                if (in_array($productId, $visibleProductsIds)) {
                    $productOptions = $product->getPropertyIds();
                    if (is_array($productOptions)) {
                        $availableOptionIds = array_merge($availableOptionIds, $productOptions);
                    }
                }
            }
        }
        $availableOptionIds = array_unique($availableOptionIds);
    
        if (count($availableOptionIds) < 1) {
            $availableOptionIds = $optionIdsArray;
            $availableOptionIds = array_merge($availableOptionIds, $initialMinValues);
            $availableOptionIds = array_merge($availableOptionIds, $initialMaxValues);
        }

        $availableOptionIdsValues = array_values($availableOptionIds);

        return new JsonResponse([
            "availableOptionIds" => $availableOptionIdsValues,
            "selectDefaultCategory" => $selectDefaultCategory,
            "defaultCategoryId" => $defaultCategoryId
        
        ]);
        
    }

    public function getPreselectCategoryPropertyId(): String
    {
        return '018a6a875d9b77a88cc7edab549b33ce';
    }
    public function getCategoryPropertyGroupId(): String
    {
        return '018a6a86ccd0746d879ac44523974aa3';
    }

}