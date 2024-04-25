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

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
*/
class ProductFinderController extends StorefrontController
{
    private EntityRepository $productRepository;

    private EntityRepository $productVisibilityRepository;
    
    private Property $property;

    public function __construct(
        EntityRepository $productRepository,
        EntityRepository $productVisibilityRepository,
        Property $property,
    ) {
        $this->productRepository = $productRepository;
        $this->productVisibilityRepository = $productVisibilityRepository;
        $this->property = $property;
    }

    /**
    * @Route("/product-finder/available-options", name="storefront.product-finder.available-options.get", methods={"GET"}, defaults={"XmlHttpRequest"=true})
    */
    public function getAvailableOptionsForFinder(Request $request, SalesChannelContext $salesChannelContext): JsonResponse
    {
        $context = $salesChannelContext->getContext();
        $salesChannelId = $salesChannelContext->getSalesChannelId();
        $optionsIdsQueryParam = $request->get('options');
        $propertyGroupsParamSelect = $request->get('selectPropertyGroups', false);
        $propertyGroupsParamSlider = $request->get('sliderPropertyGroups', false);
        
        $optionIdsArray = [];
        if ($optionsIdsQueryParam) {
            $optionsIdsQueryParamExploded = explode('|', $optionsIdsQueryParam);
            if ($optionsIdsQueryParamExploded) {
                $optionIdsArray = $optionsIdsQueryParamExploded;
            }
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
        
        
        $template = [];
        if ($propertyGroupsParamSelect) {
            $selectIds = explode(',',$propertyGroupsParamSelect);

            $propertySelectGroups = $this->property->getPropertyGroupsById($context, $selectIds);

            if ($propertySelectGroups->getTotal() > 0) {
                $groups = $propertySelectGroups->getElements();
    
                
                foreach ($groups as $propertyGroup) {
                    $template[] = $this->prepareSelect($propertyGroup, $availableOptionIds);

                    
                }

            }
        }
        if ($propertyGroupsParamSlider) {
            $sliderIds = explode(',',$propertyGroupsParamSlider);
            $propertySliderGroups = $this->property->getPropertyGroupsById($context, $sliderIds);

            if ($propertySliderGroups->getTotal() > 0) {
                $groups = $propertySliderGroups->getElements();
                foreach ($groups as $propertyGroup) {
                    $template[] = $this->prepareSlider($propertyGroup, $availableOptionIds);
                }
            }
        } 


        if (count($availableOptionIds) < 1) {
            $availableOptionIds = $optionIdsArray;
        }

        $availableOptionIdsValues = array_values($availableOptionIds);

        return new JsonResponse(
            [
                "availableOptionIds" => $availableOptionIdsValues,
                "sliderTemplate" => $template
            ]);
    }

    public function prepareSlider($propertyGroup, $availableOptions)
    {

       
        $options = $propertyGroup->getOptions();
        $optionsIds = $options->getIds();
        $filteredIds = array_intersect($optionsIds, $availableOptions);
        $filteredOptions = $options->getList($filteredIds);
        $html = '';

        $groupName = $propertyGroup->getTranslation('name');
        $groupId = $propertyGroup->getId();

        $customFields = $propertyGroup->getCustomFields();
           
        if (empty($filteredIds)) {
            return [$groupId => $html];
        }

        if (isset($customFields['acris_filter_unit'])) {
            $propertyGroupUnit = $customFields['acris_filter_unit'];
        } else {

            $singleOption = $filteredOptions->first();
            $singleOptionName = $singleOption->getTranslation('name');

            $propertyGroupUnit = CommonFunctions::extractUnit($singleOptionName);
        }

        $divGroupId = 'product-finder-property-group-' . $groupId;
        $minId = $divGroupId . '-min';
        $maxId = $divGroupId . '-max';
                
        $propertyGroupOptions = $filteredOptions->getElements();
        $propertyGroupOptionsTotal = (count($propertyGroupOptions) - 1);
        $optionElements = $this->prepareOptions($propertyGroupOptions);


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
                                $html .= '<option id="' . $optionElement['id'] .'" value="' . $optionElement['parsedValue'] . '"></option>';
                            }
                        $html .= '</datalist>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';

        return [$groupId => $html];
    }

    public function prepareSelect($propertyGroup, $availableOptions)
    {
       
        $options = $propertyGroup->getOptions();
        $optionsIds = $options->getIds();
        $filteredIds = array_intersect($optionsIds, $availableOptions);
        
        $html = '';

        $groupName = $propertyGroup->getName();
        $groupId = $propertyGroup->getId();



        $divGroupId = 'product-finder-property-group-' . $groupId;

        $display = '';
        $optionElements = [];
        if (empty($filteredIds)) {
            $display = 'style="display: none;"';
        }

        $html .= '<div class="field js-product-finder-property-option-group-field"  data-property-group="' . $groupId . '" ' . $display . ' data-element-type="select">';
            $html .= '<label class="property-group-label" for="' . $divGroupId . '">';
            $html .= '<span><span class="js-property-group-label-name" data-property-group="' . $groupId . '">' . $groupName;
            $html .= '<span class="icon icon-arrow-medium-down icon-xs"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewBox="0 0 16 16"><use xlink:href="#icons-solid-arrow-medium-down" fill="#758CA3" fill-rule="evenodd"></use></svg></span>';
            $html .= '</span></span>';
            $html .= '</label>';
                $html .= '<div class="dropdown-content">';
                    $html .= '<div class="options-list">';
                    if (!empty($filteredIds)) {

                        $filteredOptions = $options->getList($filteredIds);
                        $propertyGroupOptions = $filteredOptions->getElements();
                        $optionElements = $this->prepareOptions($propertyGroupOptions);
                        foreach ($optionElements as $optionElement) {
                            $optionDomId = 'finder-group-option-' . $optionElement['id'];
     
                            $html .= '<div class="js-option-field">';
                            $html .= '<label class="property-group-option-label" for="' . $optionDomId .'">' . $optionElement['name'] . '</label>';
                            $html .= '<input class="js-finder-property-option-input" id="' . $optionDomId .'" name="' . $groupId .'" value="' . $optionElement['id'] .'" data-option-name="' . $optionElement['name'] . '" type="radio"/>';
                            $html .= '</div>';

                        }
                    }
                    // $html .= '</div>';
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


}