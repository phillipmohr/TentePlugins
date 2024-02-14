<?php declare(strict_types=1);

namespace Nwgncy\ProductFinder\Twig;

use Nwgncy\ProductFinder\Utils\CommonFunctions;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Content\Property\PropertyGroupEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProductFinderFunctions extends AbstractExtension
{
     private EntityRepository $propertyGroupRepository;
     private EntityRepository $propertyGroupOptionRepository;
     private EntityRepository $categoryRepository;

     public function __construct(EntityRepository $propertyGroupRepository, EntityRepository $propertyGroupOptionRepository, EntityRepository $categoryRepository) {
          $this->propertyGroupRepository = $propertyGroupRepository;
          $this->propertyGroupOptionRepository = $propertyGroupOptionRepository;
          $this->categoryRepository = $categoryRepository;
     }

     public function getFunctions()
     {
          return [
               new TwigFunction('getPropertyGroupName', [$this, 'getPropertyGroupName']),
               new TwigFunction('getPropertyGroupOptionsByIds', [$this, 'getPropertyGroupOptionsByIds']),
               new TwigFunction('getPropertyOptionsByGroupId', [$this, 'getPropertyOptionsByGroupId']),
               new TwigFunction('getMainProductsCategoryUrl', [$this, 'getMainProductsCategoryUrl']),
               new TwigFunction('getUnitByGroupId', [$this, 'getUnitByGroupId']),
          ];
     }

     public function getPropertyGroupName(string $propertyGroupId, SalesChannelContext $salesChannelContext): string
     {
          $context = $salesChannelContext->getContext();
          $propertyGroupSearchCriteria = new Criteria([$propertyGroupId]);
          $propertyGroup = $this->propertyGroupRepository->search($propertyGroupSearchCriteria, $context)->first();
          if ($propertyGroup instanceof PropertyGroupEntity) {
               return $propertyGroup->getTranslation('name') ?? '';
          }
          return '';
     }

     public function getPropertyGroupOptionsByIds(array $propertyGroupOptionIds, SalesChannelContext $salesChannelContext): array
     {
          $context = $salesChannelContext->getContext();
          $propertyGroupOptionSearchCriteria = new Criteria($propertyGroupOptionIds);
          $propertyGroupOptionSearchCriteria->addSorting(new FieldSorting('name', FieldSorting::DESCENDING));
          $propertyGroupOptions = $this->propertyGroupOptionRepository->search($propertyGroupOptionSearchCriteria, $context);
          
          $propertyGroupOptionNames = [];
          foreach ($propertyGroupOptions as $propertyGroupOption) {
               if ($propertyGroupOption instanceof PropertyGroupOptionEntity) {
                    $propertyGroupOptionNames[] = [
                         'id' => $propertyGroupOption->getId(),
                         'name' => $propertyGroupOption->getTranslation('name') ?? '',
                    ];
               }
          }
          return $propertyGroupOptionNames;
     }

     public function getPropertyOptionsByGroupId(String $propertyGroupId, bool $isMeasure, SalesChannelContext $salesChannelContext) {
          $context = $salesChannelContext->getContext();
          $propertyGroupOptionCriteria = new Criteria();
          $propertyGroupOptionCriteria->addFilter(new EqualsFilter('groupId', $propertyGroupId));
          $propertyGroupPropertiesResult = $this->propertyGroupOptionRepository->search($propertyGroupOptionCriteria, $context);
          $propertyGroupPropertiesEntities = $propertyGroupPropertiesResult->getEntities();
          $propertyGroupOptions = [];
          if ($propertyGroupPropertiesEntities instanceof PropertyGroupOptionCollection) {
               $elements = $propertyGroupPropertiesEntities->getElements();
               foreach ($elements as $element) {
                    $name = $element->getTranslation('name') ?? '';
                    $elementArray = [
                         'id' => $element->getId(),
                         'name' => $name,
                    ];
                    if ($isMeasure) {
                         $parsedValue = CommonFunctions::parsefloatFromString($name);
                         $elementArray['parsedValue'] = $parsedValue;
                    }
                    $propertyGroupOptions[] = $elementArray;
               }
               usort($propertyGroupOptions, function ($a, $b) {
                    $nameA = $a['name'] ?? '';
                    $nameB = $b['name'] ?? '';
                    return strnatcmp($nameA, $nameB);
               });
          }
          return $propertyGroupOptions;
     }

     public function getMainProductsCategoryUrl(SalesChannelContext $salesChannelContext) {
          $context  = $salesChannelContext->getContext();
          $criteria = new Criteria();
          $trueCustomFieldSelector = [true, 'true', 1, '1'];
          $criteria->addFilter(new EqualsAnyFilter('customFields.nwgncy_is_the_main_products_category', $trueCustomFieldSelector));
          $categoryResult = $this->categoryRepository->search($criteria, $context)->first();
          if ($categoryResult instanceof CategoryEntity) {
               return $categoryResult->getTranslation('name') ?? '';
          }
          return '';
     }

     public function getUnitByGroupId(string $propertyGroupId, SalesChannelContext $salesChannelContext) {
          $unit = '';
          $context = $salesChannelContext->getContext();
          $criteria = new Criteria();
          $criteria->addFilter(new EqualsFilter('groupId', $propertyGroupId));
          $propertyGroupOption = $this->propertyGroupOptionRepository->search($criteria, $context)->first();
          if ($propertyGroupOption instanceof PropertyGroupOptionEntity) {
               $propertyGroupOptionName = $propertyGroupOption->getTranslation('name');
               if ($propertyGroupOptionName) {
                    $unit = CommonFunctions::extractUnit($propertyGroupOptionName);
               }
          }
          return $unit;
     }
}
