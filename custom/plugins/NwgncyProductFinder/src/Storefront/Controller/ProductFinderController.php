<?php declare(strict_types=1);

namespace Nwgncy\ProductFinder\Storefront\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Symfony\Component\Filesystem\Filesystem;
use Nwgncy\ProductFinder\Service\Property;
use Nwgncy\ProductFinder\Utils\CommonFunctions;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\FetchModeHelper;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\QueryBuilder;
use Doctrine\DBAL\ArrayParameterType;
/**
 * @Route(defaults={"_routeScope"={"storefront"}})
*/
class ProductFinderController extends StorefrontController
{
    private EntityRepository $productRepository;

    private EntityRepository $productVisibilityRepository;
    
    private Property $property;

    private Connection $connection;
    
    public function __construct(
        Connection $connection,
        EntityRepository $productRepository,
        EntityRepository $productVisibilityRepository,
        Property $property,
    ) {
        $this->connection = $connection;
        $this->productRepository = $productRepository;
        $this->productVisibilityRepository = $productVisibilityRepository;
        $this->property = $property;
    }

    /**
    * @Route("/product-finder/available-options", name="storefront.product-finder.available-options.get", methods={"GET"}, defaults={"XmlHttpRequest"=true})
    */
    public function getAvailableOptionsForFinder(Request $request, SalesChannelContext $salesChannelContext): JsonResponse
    {

        // $filePath = '/var/www/html/tentecom/public/getAvailableOptionsForFinder.html';
        // $fsObject = new Filesystem();
        // $fsObject->touch($filePath);
        // $fsObject->chmod($filePath, 0777);
        // $fsObject->dumpFile($filePath, @\Kint::dump('new'));


        // the selected category
        $categoryIdParam = $request->get('category');
        $propertyGroupsParamSelect = $request->get('selectPropertyGroups', []);
        $propertyGroupsParamSlider = $request->get('sliderPropertyGroups', []);
        $selectedPropertiesParam = $request->get('selectedProperties', []);
        // the properties of the select
        $defaultPropertyIdsParam = $request->get('defaultPropertyIds', []);
   
       
        $propertyGroupIds = [];
        foreach ([$propertyGroupsParamSelect => ',', $propertyGroupsParamSlider => ','] as $query => $delimter) {
            $propertyGroupIds = array_unique(array_merge($propertyGroupIds, $this->queryToArray($query, $delimter)));
        }

        // translations for select/slider boxes
        $propertyGroupsData = $this->getPropertyGroupTranslations($salesChannelContext, $propertyGroupIds);
 
        // 
        $optionalProps = $this->getPropertiesByGroupIds($salesChannelContext, $propertyGroupIds);

        $selectedPropertiesArr = $this->queryToArray($selectedPropertiesParam, '|');
        $categoryId = $this->queryToArray($categoryIdParam, '|');

        // the props from selected category + selected options from select box
        $mandatoryProps = array_merge($selectedPropertiesArr, $categoryId);

        // get productIds by property Ids
        $filteredProductIds = $this->filterProductsByPropertyIds($salesChannelContext, $mandatoryProps, $optionalProps);


        $propertyIds = $this->getAvailableOptionsByProductId($filteredProductIds, $propertyGroupIds);



        $propertiesByGroupId = $this->getPropertiesTranslations($salesChannelContext, $propertyIds, $propertyGroupIds);

        $selectedProperties = false;
        if ($selectedPropertiesParam) {
            $selectedProperties = explode(',',$selectedPropertiesParam);
        }

        $template = [];
        if ($propertyGroupsParamSelect) {
            $selectGroupIds = explode(',',$propertyGroupsParamSelect);
            $defaultPropertyIds = explode(',',$defaultPropertyIdsParam);

            foreach ($selectGroupIds as $groupId) {

                    $options = $this->getSelectOptionsByGroupId($salesChannelContext, $groupId);
                    foreach ($options as $key => $data) {
                        $propertyId = $data['propertyId'];
                        if (!in_array($propertyId, $defaultPropertyIds)) {
                            unset($options[$key]);
                        }
                    }
                    if (!empty($options)) {

                        $groupName = $propertyGroupsData[$groupId];
                        $template[] = $this->prepareSelect($groupId, $groupName, $options, $selectedProperties);
                    }
            }
        }

        if ($propertyGroupsParamSlider) {
            $sliderIds = explode(',',$propertyGroupsParamSlider);

            foreach ($sliderIds as $groupId) {
                
                if (isset($propertiesByGroupId[$groupId])) {
                    $options = $propertiesByGroupId[$groupId];
                    $groupName = $propertyGroupsData[$groupId];
                    $template[] = $this->prepareSlider($groupId, $groupName, $options, $selectedProperties);
                }
            }
        } 

        $availableOptions = [];
        foreach ($propertyIds as $id) {
            $availableOptions[] = strtolower($id);
        }

        return new JsonResponse([
            "availableOptionIds" => $availableOptions,
            "sliderTemplate" => $template
        ]);
    }

    public function getAvailableOptionsByProductId($productIds, $configuratorPropertyGroupIds)
    {

        $query = new QueryBuilder($this->connection);

        $query->select([
            'DISTINCT HEX(product_property.property_group_option_id) as propertyId'
        ]);

        $query->from('product_property', 'product_property');
        $query->join('product_property', 'property_group_option', 'property_group_option', 'product_property.property_group_option_id = property_group_option.id');
        $query->where('product_id IN (:productIds)');
        $query->andWhere('property_group_option.property_group_id IN (:propertyGroupIds)');
        $query->setParameter('productIds', Uuid::fromHexToBytesList($productIds), ArrayParameterType::BINARY);
        $query->setParameter('propertyGroupIds', Uuid::fromHexToBytesList($configuratorPropertyGroupIds), ArrayParameterType::BINARY);

        $result = $query->executeQuery()->fetchAllAssociative();

        if (!empty($result)) {
            $ids = [];
            foreach ($result as $productId) {
                $ids[] = $productId['propertyId'];
            }
            return $ids;
        }
        return [];
    }
    public function getPropertiesByGroupIds($salesChannelContext, $groupIds) {


        $query = new QueryBuilder($this->connection);
        $query->select([
            'LOWER(HEX(property_group_option.id)) as propertyId'
        ]);

        $query->from('property_group_option');
        $query->where('property_group_id IN (:propertyGroupIds)');
        $query->setParameter('propertyGroupIds', Uuid::fromHexToBytesList($groupIds), ArrayParameterType::BINARY);
        $result = $query->executeQuery()->fetchAllAssociative();

        if (!empty($result)) {
            $ids = [];
            foreach ($result as $productId) {
                $ids[] = $productId['propertyId'];
            }
            return $ids;
        }
        return [];
    }

    
    public function getPropertyGroupTranslations($salesChannelContext, $propertyGroupIds): array
    {

        $context = $salesChannelContext->getContext();
        $languageId = $context->getLanguageId();
        $fallbackLanguageId = '2FBB5FE2E29A4D70AA5854CE7CE3E20B';

        $query = new QueryBuilder($this->connection);

        $query->select([
            'LOWER(HEX(id)) as groupId',
            'COALESCE(property_group_translation_normal.name, property_group_translation_fallback.name) AS name'
        ]);

        $query->from('property_group', 'pg');

        $query->leftJoin(
            'pg',
            'property_group_translation',
            'property_group_translation_normal',
            'pg.id = property_group_translation_normal.property_group_id  AND property_group_translation_normal.language_id = :languageId'
        );

        $query->leftJoin(
            'pg',
            'property_group_translation',
            'property_group_translation_fallback',
            'pg.id = property_group_translation_fallback.property_group_id  AND property_group_translation_fallback.language_id = :fallbackLanguageId'
        );

        $query->where('id IN (:propertyGroupIds)');

        $query->setParameter('fallbackLanguageId', hex2bin($fallbackLanguageId));
        $query->setParameter('languageId', Uuid::fromHexToBytes($languageId));
        $query->setParameter('propertyGroupIds', Uuid::fromHexToBytesList($propertyGroupIds), ArrayParameterType::BINARY);
        $result = $query->executeQuery()->fetchAllAssociative();

        $groupData = [];
        foreach ($result as $data) {
            $groupData[$data['groupId']] = $data['name'];
        }
        return $groupData;
    }

    public function getSelectOptionsByGroupId($salesChannelContext, $propertyGroupId): array
    {

        $context = $salesChannelContext->getContext();
        $languageId = $context->getLanguageId();
        $fallbackLanguageId = '2FBB5FE2E29A4D70AA5854CE7CE3E20B';

        $query = new QueryBuilder($this->connection);

        $query->select([
            'LOWER(HEX(property_group_option.id)) AS propertyId',
            'COALESCE(property_group_option_translation_normal.name, property_group_option_translation_fallback.name) AS name',
            'COALESCE(property_group_option_translation_normal.position, property_group_option_translation_fallback.position) AS position',
        ]);

        $query->from('property_group_option');

        $query->leftJoin(
            'property_group_option',
            'property_group_option_translation',
            'property_group_option_translation_normal',
            'property_group_option.id = property_group_option_translation_normal.property_group_option_id  AND property_group_option_translation_normal.language_id = :languageId'
        );

        $query->leftJoin(
            'property_group_option',
            'property_group_option_translation',
            'property_group_option_translation_fallback',
            'property_group_option.id = property_group_option_translation_fallback.property_group_option_id  AND property_group_option_translation_fallback.language_id = :fallbackLanguageId'
        );

        $query->where('property_group_option.property_group_id = (:propertyGroupId)');

        $query->setParameter('fallbackLanguageId', hex2bin($fallbackLanguageId));
        $query->setParameter('languageId', Uuid::fromHexToBytes($languageId));
        $query->setParameter('propertyGroupId', Uuid::fromHexToBytes($propertyGroupId));
        $result = $query->executeQuery()->fetchAllAssociative();

        return $result;

    }
    public function getPropertiesTranslations($salesChannelContext, $propertyIds, $propertyGroupIds): array
    {
  
        $context = $salesChannelContext->getContext();
        $languageId = $context->getLanguageId();
        $fallbackLanguageId = '2FBB5FE2E29A4D70AA5854CE7CE3E20B';

        $query = new QueryBuilder($this->connection);

        $query->select([
            'LOWER(HEX(property_group_option.property_group_id)) as groupId',
            'LOWER(HEX(property_group_option.id)) AS propertyId',
            'COALESCE(property_group_option_translation_normal.name, property_group_option_translation_fallback.name) AS name',
            'COALESCE(property_group_option_translation_normal.position, property_group_option_translation_fallback.position) AS position',

        ]);

        $query->from('property_group_option');

        $query->leftJoin(
            'property_group_option',
            'property_group_option_translation',
            'property_group_option_translation_normal',
            'property_group_option.id = property_group_option_translation_normal.property_group_option_id  AND property_group_option_translation_normal.language_id = :languageId'
        );

        $query->leftJoin(
            'property_group_option',
            'property_group_option_translation',
            'property_group_option_translation_fallback',
            'property_group_option.id = property_group_option_translation_fallback.property_group_option_id  AND property_group_option_translation_fallback.language_id = :fallbackLanguageId'
        );

        $query->where('property_group_option.id IN (:props)');
        $query->andWhere('property_group_option.property_group_id IN (:propertyGroupIds)');

        $query->setParameter('fallbackLanguageId', hex2bin($fallbackLanguageId));
        $query->setParameter('languageId', Uuid::fromHexToBytes($languageId));
        $query->setParameter('props', Uuid::fromHexToBytesList($propertyIds), ArrayParameterType::BINARY);
        $query->setParameter('propertyGroupIds', Uuid::fromHexToBytesList($propertyGroupIds), ArrayParameterType::BINARY);
        $results = $query->executeQuery()->fetchAllAssociative();

        $groupedResults = [];
        foreach ($results as $result) {
            $groupId = $result['groupId'];
            unset($result['groupId']);
            $groupedResults[$groupId][] = $result;
        }
        return $groupedResults;
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

    public function filterProductsByPropertyIds($salesChannelContext, $mandatoryProps, $optionalProps)
    {

        $salesChannelId = $salesChannelContext->getSalesChannelId();
        $productIdsToFilter = $this->getVisibleProductsIdsBySalesChannel($salesChannelId);

        $query = new QueryBuilder($this->connection);

        $query->select([
            'LOWER(upper_product.id) AS productId'
        ]);    

        $query->setParameter('productIds', Uuid::fromHexToBytesList($productIdsToFilter), ArrayParameterType::BINARY);
        $query->from('(' . $this->getProductFilterSelect() . ')', 'upper_product');

        $propertyIdsSelect = 'upper_product.property_ids';

        // if (!empty($mandatoryProps)) {

        $concatWheres = '';
        $count = 0;
        foreach ($mandatoryProps as $key => $value) {
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
        // }
       
        // $countOptional = 0;
        // $concatOptional = '';
        // foreach ($optionalProps as $key => $propertyId) {
            
        //     $bindingKey = (string)$key . '_property_optional';

        //     if ($countOptional == 0) {
        //         $concatOptional .= "  JSON_CONTAINS(". $propertyIdsSelect .", JSON_ARRAY(:". $bindingKey .")) ";
        //     } else {
        //         $concatOptional .= " OR ( JSON_CONTAINS(". $propertyIdsSelect .", JSON_ARRAY(:". $bindingKey .")) )";
        //     }
                    
        //     $query->setParameter($bindingKey, $propertyId);

        //     $countOptional++;
        // }

        // $query->andWhere( $concatOptional );

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

    public function getProductFilterSelect(): string
    {
        $subSelect = new QueryBuilder($this->connection);

        $subSelect->select('LOWER(HEX(id)) AS id','property_ids')
                    ->from('product', 'filtered_products')
                    ->where('filtered_products.id IN (:productIds)');

        return $subSelect->getSQL();
    }
    
    public function prepareSlider($groupId, $groupName, $options, $selectedProperties)
    {

        $html = '';

        if (empty($options) || count($options) == 1) {
            return [$groupId => $html];
        }

        $propertyGroupUnit = CommonFunctions::extractUnit($options[0]['name']);

        usort($options, function ($a, $b) {
            $nameA = $a['name']; 
            $nameB = $b['name'];
            $nameANumbersOnly = preg_replace('/[^0-9,]/', '', $nameA);
            $nameAClean = str_replace(',', '.', $nameANumbersOnly);
            $nameBNumbersOnly = preg_replace('/[^0-9,]/', '', $nameB);
            $nameBClean = str_replace(',', '.', $nameBNumbersOnly);
            return $nameAClean > $nameBClean ? 1 : -1;
        });

        $divGroupId = 'product-finder-property-group-' . $groupId;
                
        $propertyGroupOptionsTotal = (count($options) - 1);
        $optionElements = $options;

        $html .= '<div class="field js-product-finder-property-option-group-field js-product-finder-property-option-group-slider-field"  data-property-group="' . $groupId . '" data-element-type="slider">';
            $html .= '<label class="property-group-label" for="' . $divGroupId . '">';
            $html .= '<span>' . $groupName;
            $html .= '<span class="icon icon-arrow-medium-down icon-xs"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewBox="0 0 16 16"><use xlink:href="#icons-solid-arrow-medium-down" fill="#758CA3" fill-rule="evenodd"></use></svg></span>';
            $html .= ' </span>'; 
            $html .= '</label>';
            $html .= '<div class="dropdown-content slider">';
            $html .= '<div class="min-max" data-property-group="' . $groupId . '">';
                    $html .= '<div class="nwgncy-finder-slider-values">';
                    $html .= '<div class="nwgncy-finder-slider-range1"></div>';
                    $html .= '<div class="nwgncy-finder-slider-dash"> &dash; </div>';
                    $html .= '<div class="nwgncy-finder-slider-range2"></div>';
                    $html .= '<div class="nwgncy-finder-slider-unit"> ' . $propertyGroupUnit . ' </div>';
                    $html .= '</div>';
                    $html .= '<div class="nwgncy-finder-slider-container">';
                    $html .= '<div class="nwgncy-finder-slider-track"></div>';
                    $html .= '<input type="range" min="0" max="' . $propertyGroupOptionsTotal . '" value="0" class="nwgncy-finder-slider-1" step="1">';
                    $html .= '<input type="range" min="0" max="' . $propertyGroupOptionsTotal . '" value="' . $propertyGroupOptionsTotal . '" class="nwgncy-finder-slider-2" step="1">';
                        $html .= '<div class="nwgncy-finder-slider-buttons">';
                        $optionIndex = 0;
                            foreach ($optionElements as $optionElement) {
                                $html .= '<span data-value="' . $optionIndex . '"></span>';
                                $optionIndex++;
                            }
                        $html .= '</div>';

                        $html .= '<datalist class="nwgncy-finder-slider-number">';
                            foreach ($optionElements as $optionElement) {
                                $value = CommonFunctions::parsefloatFromString($optionElement['name']);
                                $html .= '<option id="' . $optionElement['propertyId'] .'" value="' . $value . '"></option>';
                            }
                        $html .= '</datalist>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';

        return [$groupId => $html];
    }

    public function prepareSelect($groupId, $groupName, $options, $selectedProperties)
    {

        $html = '';

        $divGroupId = 'product-finder-property-group-' . $groupId;

        $display = '';
        if (empty($options) ) {
            return [$groupId => $html];
        }

        if ($selectedProperties) {
            
            foreach ($options as $option) {
                
                $optionId = $option['propertyId'];
                if (in_array($optionId, $selectedProperties)) {
                    $groupName = $option['name'];
                    break;
                }
            }
        }

        $html .= '<div class="field js-product-finder-property-option-group-field"  data-property-group="' . $groupId . '" ' . $display . ' data-element-type="select">';
            $html .= '<label class="property-group-label" for="' . $divGroupId . '">';
            $html .= '<span><span class="js-property-group-label-name" data-property-group="' . $groupId . '">' . $groupName;
            $html .= '<span class="icon icon-arrow-medium-down icon-xs"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewBox="0 0 16 16"><use xlink:href="#icons-solid-arrow-medium-down" fill="#758CA3" fill-rule="evenodd"></use></svg></span>';
            $html .= '</span></span>';
            $html .= '</label>';
                $html .= '<div class="dropdown-content">';
                    $html .= '<div class="options-list">';

                    foreach ($options as $optionElement) {
                        $optionDomId = 'finder-group-option-' . $optionElement['propertyId'];
    
                        $html .= '<div class="js-option-field">';
                        $html .= '<label class="property-group-option-label" for="' . $optionDomId .'">' . $optionElement['name'] . '</label>';
                        $html .= '<input class="js-finder-property-option-input" id="' . $optionDomId .'" name="' . $groupId .'" value="' . $optionElement['propertyId'] .'" data-option-name="' . $optionElement['name'] . '" type="radio"/>';
                        $html .= '</div>';

                    }

                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';

        return [$groupId => $html];
    }

    public function prepareOptions($elements)
    {
        foreach ($elements as $element) {
            $name = $element->getTranslation('name') ?? '';
            $elementArray = [
                 'id' => $element->getId(),
                 'name' => $name,
                 'position' => $element->getPosition(),
            ];
            $parsedValue = CommonFunctions::parsefloatFromString($name);
            $elementArray['parsedValue'] = $parsedValue;
 
            $propertyGroupOptions[] = $elementArray;
       }
       usort($propertyGroupOptions, function ($a, $b) {
            $nameA = $a['name'] ?? '';
            $nameB = $b['name'] ?? '';
            return strnatcmp($nameA, $nameB);
       });
       return $propertyGroupOptions;
    }

    public function logTime($message, $first = false)
    {
        $currentTimestamp = microtime(true);
        $timestampString = sprintf('%.6f', $currentTimestamp);
        $dateTime = \DateTime::createFromFormat('U.u', $timestampString);
        $timeWithMilliseconds = $dateTime->format('Y-m-d H:i:s.u');
        $filePath = '/var/www/html/tentecom/public/logTimeFinder.html';
        $fsObject = new Filesystem();
        $fsObject->touch($filePath);
        $fsObject->chmod($filePath, 0777);

        if ($first) {
            $fsObject->dumpFile($filePath, @\Kint::dump('new'));
        }
        $fsObject->appendToFile($filePath, @\Kint::dump($message));
        $fsObject->appendToFile($filePath, @\Kint::dump($timeWithMilliseconds));
        

    }
    public function queryToArray($query, $delimter):array {
        if(empty($query)) {
            return [];
        }
        return explode($delimter, $query);
    }

}