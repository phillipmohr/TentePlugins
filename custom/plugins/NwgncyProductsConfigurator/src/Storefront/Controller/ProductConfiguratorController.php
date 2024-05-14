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
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\FetchModeHelper;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\QueryBuilder;
use NwgncyPropsExportImport\Service\Property;
use Doctrine\DBAL\ArrayParameterType;
use Shopware\Storefront\Page\Search\SearchPageLoader;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
*/
class ProductConfiguratorController extends StorefrontController
{
    private EntityRepository $propertyGroupOptionRepository;

    private EntityRepository $productRepository;

    private EntityRepository $productVisibilityRepository;

    private Connection $connection;

    private SearchPageLoader $searchPageLoader;

    public function __construct(
        SearchPageLoader $searchPageLoader,
        Connection $connection,
        EntityRepository $propertyGroupOptionRepository,
        EntityRepository $productRepository,
        EntityRepository $productVisibilityRepository,
        Property $property
        
    ) {
        $this->searchPageLoader = $searchPageLoader;
        $this->connection = $connection;
        $this->propertyGroupOptionRepository = $propertyGroupOptionRepository;
        $this->productRepository = $productRepository;
        $this->productVisibilityRepository = $productVisibilityRepository;
        $this->property = $property;

    }

    /**
    * @Route("/products-configurator/available-options", name="storefront.respective-available-options.get", methods={"GET"}, defaults={"XmlHttpRequest"=true, "_httpCache" = true})
    */
    public function getAvailableOptions(Request $request, SalesChannelContext $salesChannelContext): JsonResponse
    {
        // $filePath = '/var/www/html/tentecom/public/getAvailableOptions.html';
        // $fsObject = new Filesystem();
        // $fsObject->touch($filePath);
        // $fsObject->chmod($filePath, 0777);
        // $fsObject->dumpFile($filePath, @\Kint::dump('new'));

        $context = $salesChannelContext->getContext();

        $optionsIdsQueryParam = $request->get('properties');

        $maxKeyValuesPairsQuery = $request->get('max-property', []);
        $minKeyValuesPairsQuery = $request->get('min-property', []);
        $cadFileFilter = $request->get('hasCadFile', false);
        $fastDeliveryFilter = $request->get('fastDelivery', false);
        $search = $request->get('search', false);

        $configuratorPropertyGroupIdsQuery = $request->get('confiGroupIds');
        $configuratorPropertyGroupIds = explode(',', $configuratorPropertyGroupIdsQuery);

        $defaultCategoryId = '';

        $optionIdsArray = [];
        if ($optionsIdsQueryParam) {
            $optionsIdsQueryParamExploded = explode('|', $optionsIdsQueryParam);
            if ($optionsIdsQueryParamExploded) {
                $optionIdsArray = $optionsIdsQueryParamExploded;
            }
        } 

        $defaultCategoryOptions = $this->getDefaultCategoryOptions($context, $optionIdsArray);

        $optionIdsArray = $defaultCategoryOptions['optionsIds'];
        $selectDefaultCategory = $defaultCategoryOptions['selectDefaultCategory'];
        $currentCategoryId = $defaultCategoryOptions['currentCategoryId'];

        if ($search !== false) {
            $productIds = $this->search($salesChannelContext, $request);
        } else {
            $minMaxProps = $this->getRangedProperties($salesChannelContext, $minKeyValuesPairsQuery, $maxKeyValuesPairsQuery);
            $productIds = $this->getProductIdsBySelectedProperties($salesChannelContext, $optionIdsArray, $minMaxProps, $cadFileFilter, $fastDeliveryFilter);
        }
        
        // $fsObject->appendToFile($filePath, @\Kint::dump($productIds));

        if (empty($productIds)) {
            $emptyResult = true;
            $productPropertyIds = [$currentCategoryId];
        } else {
            $emptyResult = false;
            $productPropertyIds = $this->getPropertiesByProductsIds($productIds, $configuratorPropertyGroupIds);
        }
        // $fsObject->appendToFile($filePath, @\Kint::dump($productPropertyIds));

        return new JsonResponse([
            "availableOptionIds" => $productPropertyIds,
            "selectDefaultCategory" => $selectDefaultCategory,
            "defaultCategoryId" => $defaultCategoryId,
            "emptyResult" => $emptyResult,
        ]);
        
    }

    public function getTranslationSubQuery($salesChannelContext)
    {

        $context = $salesChannelContext->getContext();
        $languageId = $context->getLanguageId();

        $fallbackLanguageId = '2FBB5FE2E29A4D70AA5854CE7CE3E20B';

        $query = new QueryBuilder($this->connection);

        $query->select([
            'LOWER(HEX(property_group_option_translation_outer.property_group_option_id)) as propertyGroupOptionIdOuter',
            'LOWER(HEX(property_group_option_translation_fallback_1.property_group_option_id)) as fallBackPropertyGroupOptionId',
            'LOWER(HEX(property_group_option_translation_fallback_1.language_id)) as fallBackPropertyLanguageId',
            'property_group_option_translation_fallback_1.name as fallbackName',
            'property_group_option_translation_outer.property_group_option_id',
            'property_group_option_translation_outer.name'
        ]);

        $query->from('property_group_option_translation', 'property_group_option_translation_outer');
        
        $query->leftJoin(
            'property_group_option_translation_outer',
            'property_group_option_translation',
            'property_group_option_translation_fallback_1',
            'property_group_option_translation_outer.property_group_option_id = property_group_option_translation_fallback_1.property_group_option_id  AND property_group_option_translation_fallback_1.language_id = :fallbackLanguageId'
        );

        $query->where('property_group_option_translation_outer.language_id = :outerLanguageId');

        $query->setParameter('outerLanguageId', Uuid::fromHexToBytes($languageId));
        $query->setParameter('fallbackLanguageId', $fallbackLanguageId);

        return $query;
    }

    public function getVisibleProductsIdsBySalesChannel($salesChannelId): array
    {
        $query = new QueryBuilder($this->connection);

        $query->select(['DISTINCT UPPER(HEX(product_visibility.product_id)) as id'])
              ->from('product_visibility')
              ->where('HEX(product_visibility.sales_channel_id) = :salesChannelId');

        $query->setParameter('salesChannelId', $salesChannelId);

        $result = $query->executeQuery()->fetchAllAssociative();

        if (!empty($result)) {
            $ids = [];
            foreach ($result as $productId) {
                $ids[] = $productId['id'];
            }
            return $ids;
        }
        return [];
    }
    
    public function search($context, $request): array
    {
        $page = $this->searchPageLoader->load($request, $context);
        $listing = $page->getListing();

        if ($listing->getTotal() > 0) {
            return $listing->getEntities()->getIds();
        }
        return [];
    }
    
    public function getSelectedProps($minMaxProps, $props): array
    {

        $defaultSelection = [];
        if (!empty($minMaxProperties)) {
            foreach ($minMaxProperties['groupId'] as $groupId => $propertyIds) {
                foreach ($propertyIds as $propertyId => $data) {
                    $defaultSelection[] = $propertyId;
                }
            }
        }
        return array_merge($defaultSelection, $props);
    }
    
    public function getProductFilterSelect(): string
    {
        $subSelect = new QueryBuilder($this->connection);

        $subSelect->select('LOWER(HEX(id)) AS id','property_ids')
                    ->from('product', 'filtered_products')
                    ->where('filtered_products.id IN (:productIds)');

        return $subSelect->getSQL();
    }
    
    public function getDefaultCategoryOptions($context, $optionIds):array
    {

        $selectDefaultCategory = false;
        $categoryPropertyCriteria = new Criteria();
        $categoryPropertyCriteria->addFilter(new EqualsFilter('groupId', $this->getCategoryPropertyGroupId()));
        $categoryPropertyOptions = $this->propertyGroupOptionRepository->search($categoryPropertyCriteria, $context)->getEntities();
        $categoryPropertyOptionIds = $categoryPropertyOptions->getIds();
        $currentCategoryId = '';


        if (!empty($optionIds)) {
            
            $foundCategoryProperty = false;
            foreach ($categoryPropertyOptionIds as $categoryPropertyOptionId) {
                
                if (in_array($categoryPropertyOptionId, $optionIds)) {
                    $foundCategoryProperty = true;
                    $currentCategoryId = $categoryPropertyOptionId;
                    break;
                }
            }

            if (!$foundCategoryProperty) {
                $defaultCategoryId = $this->getPreselectCategoryPropertyId();
                $currentCategoryId = $defaultCategoryId;
                array_push($optionIds, $defaultCategoryId);
                $selectDefaultCategory = true;
            }

        } else {
            $defaultCategoryId = $this->getPreselectCategoryPropertyId();
            $currentCategoryId = $defaultCategoryId;
            array_push($optionIds, $defaultCategoryId);
        }

        return [
            'selectDefaultCategory' => $selectDefaultCategory,
            'optionsIds' => $optionIds,
            'currentCategoryId' => $currentCategoryId,
        ];

    }
    public function getProductsWithFastDelivery($salesChannelId): array
    {


        $query = new QueryBuilder($this->connection);

        $query->select([
            'HEX(npst.product_id) AS productId'
        ]);

        $query->from('nwgncy_product_shipping_time', 'npst');
    
        $query->where('npst.sales_channel_id = :salesChannelId')
              ->andWhere('shipping_time = 1');
        
        $query->innerJoin('npst', 'product_visibility', 'pv', 'npst.product_id = pv.product_id AND pv.sales_channel_id = :salesChannelId');

        $query->setParameter('salesChannelId', Uuid::fromHexToBytes($salesChannelId));

        $result = $query->executeQuery()->fetchAllAssociative();

        $products = [];

        if (!empty($result)) {
            foreach ($result as $productId) {
                $products[] = $productId['productId'];
            }
    
            return $products;
        }
        return [];
    }

    public function getProductsWithCadFile($salesChannelId): array
    {
        // $filePath = '/var/www/html/tentecom/public/getProductsWithCadFile.html';
        // $fsObject = new Filesystem();
        // $fsObject->touch($filePath);
        // $fsObject->chmod($filePath, 0777);
        // $fsObject->dumpFile($filePath, @\Kint::dump('new'));

        $query = new QueryBuilder($this->connection);

        $query->select([
            'HEX(apd.product_id) AS productId'
        ]);    
        $query->from('acris_product_download', 'apd');
        $query->where('apd.download_tab_id = :downloadTabId');
        $query->innerJoin('apd', 'product_visibility', 'pv', 'apd.product_id = pv.product_id AND pv.sales_channel_id = :salesChannelId');
        $query->setParameter('downloadTabId', Uuid::fromHexToBytes('018D0D4F63B677A299881E7D9DE7781F'));
        $query->setParameter('salesChannelId', Uuid::fromHexToBytes($salesChannelId));

        $result = $query->executeQuery()->fetchAllAssociative();

        $products = [];
        if (!empty($result)) {
            foreach ($result as $productId) {
                $products[] = $productId['productId'];
            }
    
            return $products;
        }
        return [];
    }

    public function getProductIdsBySelectedProperties($salesChannelContext, $propsArr, $minMaxProperties, $cadFileFilter, $fastDeliveryFilter)
    {
        // $filePath = '/var/www/html/tentecom/public/getProductIdsBySelectedProperties.html';
        // $fsObject = new Filesystem();
        // $fsObject->touch($filePath);
        // $fsObject->chmod($filePath, 0777);
        // $fsObject->dumpFile($filePath, @\Kint::dump('new'));


        $salesChannelId = $salesChannelContext->getSalesChannelId();

        $fastDeliveryProductIds = [];
        $cadFileProductIds = [];
        $productIdsToFilter = [];
        if ($cadFileFilter && !$fastDeliveryFilter) {
            $productIdsToFilter = $this->getProductsWithCadFile($salesChannelId);
        }

        if ($fastDeliveryFilter && !$cadFileFilter) {
            $productIdsToFilter = $this->getProductsWithFastDelivery($salesChannelId);
        }

        if ($fastDeliveryFilter && $cadFileFilter) {
            $fastDeliveryProductIds = $this->getProductsWithFastDelivery($salesChannelId);
            $cadFileProductIds = $this->getProductsWithCadFile($salesChannelId);
            $productIdsToFilter = array_unique(array_intersect($cadFileProductIds, $fastDeliveryProductIds));
        }

        if (!$fastDeliveryFilter && !$cadFileFilter) {
            $productIdsToFilter = $this->getVisibleProductsIdsBySalesChannel($salesChannelId);
        }
 
        $query = new QueryBuilder($this->connection);

        $query->select([
            'LOWER(upper_product.id) AS productId',
            'upper_product.property_ids'
        ]);    

        $query->setParameter('productIds', Uuid::fromHexToBytesList($productIdsToFilter), ArrayParameterType::BINARY);

        $query->from('(' . $this->getProductFilterSelect() . ')', 'upper_product');
        
        $propertyIdsSelect = 'upper_product.property_ids';

        $concatWheres = '';
        $count = 0;
        foreach ($propsArr as $key => $value) {
            $bindingKey = (string)$key . '_property';

            if ($count == 0) {
                $concatWheres .= " ( JSON_CONTAINS(". $propertyIdsSelect .", JSON_ARRAY(:". $bindingKey .")) )";
            } else {
                $concatWheres .= " AND ( JSON_CONTAINS(". $propertyIdsSelect .", JSON_ARRAY(:". $bindingKey .")) )";
            }

            $query->setParameter($bindingKey, $value);
            $count++;
        }
        $query->where( $concatWheres );
          
        if (!empty($minMaxProperties)) {

            $countBindingKey = 0;
            $countGroupId = 0;
            foreach ($minMaxProperties['groupId'] as $groupId => $propertyIds) {
                
                $count = 0;

                $concatSecond = '';
                foreach ($propertyIds as $propertyId => $data) {
                    
                    $bindingKey = (string)$countBindingKey . '_property_min_max';

                    if ($count == 0) {
                        $concatSecond .= "  JSON_CONTAINS(". $propertyIdsSelect .", JSON_ARRAY(:". $bindingKey .")) ";
                    } else {
                        $concatSecond .= " OR ( JSON_CONTAINS(". $propertyIdsSelect .", JSON_ARRAY(:". $bindingKey .")) )";
                    }
                            
                    $query->setParameter($bindingKey, $propertyId);

                    $count++;
                    $countBindingKey++;
                }

                $query->andWhere( $concatSecond );
                $countGroupId++;
            }
        }

        $result = $query->executeQuery()->fetchAllAssociative();

        $products = [];
        if (!empty($result)) {
            foreach ($result as $productId) {
                $products[] = $productId['productId'];
            }
            return $products;
        }
        return [];
    }

    public function getRangedProperties($salesChannelContext, $minProps, $maxProps) {

        $context = $salesChannelContext->getContext();
        $languageId = $context->getLanguageId();
        $fallbackLanguageId = '2FBB5FE2E29A4D70AA5854CE7CE3E20B';

        if (empty($minProps) && empty($maxProps)) {
            return [];
        }

        $minProperties = array_keys($minProps);
        $maxProperties = array_keys($maxProps);

        $propertyIds = array_unique(array_merge($minProperties, $maxProperties));

        $searchForProps = [];
        foreach ($propertyIds as $key => $value) {
            $searchForProps[] = $value;
        }

        $rangeQuery = new QueryBuilder($this->connection);

        $rangeQuery->select([
            'LOWER(HEX(property_group_option.property_group_id)) as groupId',
            'LOWER(HEX(property_group_option.id)) AS propertyId',
            'COALESCE(property_group_option_translation_normal.name, property_group_option_translation_fallback.name) AS name'
        ]);

        $rangeQuery->from('property_group_option');

        $rangeQuery->leftJoin(
            'property_group_option',
            'property_group_option_translation',
            'property_group_option_translation_normal',
            'property_group_option.id = property_group_option_translation_normal.property_group_option_id  AND property_group_option_translation_normal.language_id = :languageId'
        );

        $rangeQuery->leftJoin(
            'property_group_option',
            'property_group_option_translation',
            'property_group_option_translation_fallback',
            'property_group_option.id = property_group_option_translation_fallback.property_group_option_id  AND property_group_option_translation_fallback.language_id = :fallbackLanguageId'
        );

        $rangeQuery->where('property_group_id IN (:props)');

        $rangeQuery->setParameter('fallbackLanguageId', hex2bin($fallbackLanguageId));
        $rangeQuery->setParameter('languageId', Uuid::fromHexToBytes($languageId));
        $rangeQuery->setParameter('props', Uuid::fromHexToBytesList($searchForProps), ArrayParameterType::BINARY);
        $rangeQueryResult = $rangeQuery->executeQuery()->fetchAllAssociative();
        $propsToFilter = [];

        foreach ($rangeQueryResult as $result) {
            
            $value = CommonFunctions::parsefloatFromString($result['name']);

            if ($value >= $minProps[$result['groupId']] && $value <= $maxProps[$result['groupId']]) {
                $propsToFilter['groupId'][$result['groupId']][$result['propertyId']] = [
                    'propertyId' => $result['propertyId'],
                    'sqlPropValue' => $value,
                    'min' => $minProps[$result['groupId']],
                    'max' => $maxProps[$result['groupId']],
                ];
            }

        }
        return $propsToFilter;
    }

    public function getCategoryPropertyGroupId(): String
    {
        return '018a6a86ccd0746d879ac44523974aa3';
    }
    public function getPreselectCategoryPropertyId(): String
    {
        return '018a6a875d9b77a88cc7edab549b33ce';
    }
    public function logTime($message, $first = false)
    {
        $currentTimestamp = microtime(true);
        $timestampString = sprintf('%.6f', $currentTimestamp);
        $dateTime = \DateTime::createFromFormat('U.u', $timestampString);
        $timeWithMilliseconds = $dateTime->format('Y-m-d H:i:s.u');
        $filePath = '/var/www/html/tentecom/public/logTime.html';
        $fsObject = new Filesystem();
        $fsObject->touch($filePath);
        $fsObject->chmod($filePath, 0777);

        if ($first) {
            $fsObject->dumpFile($filePath, @\Kint::dump('new'));
        }
        $fsObject->appendToFile($filePath, @\Kint::dump($message));
        $fsObject->appendToFile($filePath, @\Kint::dump($timeWithMilliseconds));

    }

    public function getPropertiesByProductsIds($productIds, $configuratorPropertyGroupIds)
    {

        $query = new QueryBuilder($this->connection);

        $query->select([
            'DISTINCT LOWER(HEX(product_property.property_group_option_id)) as optionId'
        ]);

        $query->from('product_property', 'product_property');
        $query->join('product_property', 'property_group_option', 'property_group_option', 'product_property.property_group_option_id = property_group_option.id');
        $query->where('product_id IN (:productIds)');
        $query->andWhere('property_group_option.property_group_id IN (:propertyGroupIds)');
        $query->setParameter('productIds', Uuid::fromHexToBytesList($productIds), ArrayParameterType::BINARY);
        $query->setParameter('propertyGroupIds', Uuid::fromHexToBytesList($configuratorPropertyGroupIds), ArrayParameterType::BINARY);

        $result = $query->executeQuery()->fetchAllAssociative();

        if (!empty($result)) {
            $props = [];
            foreach ($result as $prop) {
                $props[] = $prop['optionId'];
            }
            return $props;
        }
        return [];
    }
}