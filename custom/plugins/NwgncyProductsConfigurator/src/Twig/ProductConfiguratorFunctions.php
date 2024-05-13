<?php declare(strict_types=1);
 
namespace Nwgncy\ProductsConfigurator\Twig;

use Nwgncy\ProductsConfigurator\Utils\CommonFunctions;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Content\Property\PropertyGroupEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProductConfiguratorFunctions extends AbstractExtension
{
     private EntityRepository $categoryRepository;
     private EntityRepository $propertyGroupRepository;
     private EntityRepository $propertyGroupOptionRepository;

     public function __construct(EntityRepository $categoryRepository, EntityRepository $propertyGroupRepository, EntityRepository $propertyGroupOptionRepository) {
          $this->categoryRepository = $categoryRepository;
          $this->propertyGroupRepository = $propertyGroupRepository;
          $this->propertyGroupOptionRepository = $propertyGroupOptionRepository;
     }

     public function getFunctions()
     {
          return [
               new TwigFunction('getCategoryName', [$this, 'getCategoryName']),
               new TwigFunction('getPropertyGroupName', [$this, 'getPropertyGroupName']),
               new TwigFunction('getPropertyGroupOptionsByIds', [$this, 'getPropertyGroupOptionsByIds']),
               new TwigFunction('getPropertyOptionsByGroupId', [$this, 'getPropertyOptionsByGroupId']),

          ];
     }
     

     public function getCategoryName(string $categoryId, SalesChannelContext $salesChannelContext): string
     {
          $context = $salesChannelContext->getContext();
          $categorySearchCriteria = new Criteria([$categoryId]);
          $category = $this->categoryRepository->search($categorySearchCriteria, $context)->first();
          if ($category instanceof CategoryEntity) {
               return $category->getTranslation('name') ?? '';
          }
          return '';
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

     public function getPropertyOptionsByGroupId(string $propertyGroupId, bool $isMeasure, SalesChannelContext $salesChannelContext) {
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
}
