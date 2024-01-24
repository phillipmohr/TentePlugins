<?php declare(strict_types=1);

namespace Nwgncy\TenteTheme\Twig;

use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Twig\Extension\AbstractExtension;
use Shopware\Core\Framework\Context;
use Twig\TwigFunction;

class TenteThemeTwigFunctions extends AbstractExtension
{
     private EntityRepository $categoryRepository;
     private EntityRepository $landingPageRepository;

     public function __construct(EntityRepository $categoryRepository, EntityRepository $landingPageRepository) {
          $this->categoryRepository = $categoryRepository;
          $this->landingPageRepository = $landingPageRepository;
     }

     public function getFunctions()
     {
          return [
               new TwigFunction('getServiceMenuLeft', [$this, 'getServiceMenuLeft']),
               new TwigFunction('getContactPageID', [$this, 'getContactPageID'])
          ];
     }

     public function getContactPageID(SalesChannelContext $salesChannelContext)
     {
          $context = $salesChannelContext->getContext();
          $contactPageId = null;
            $criteria = new Criteria();
            $landings = $this->landingPageRepository->search($criteria, $context)->getElements();
            foreach($landings as $landing) {
                if(is_array($landing->getCustomFields())) {
                    if($landing->getCustomFields()['is_contact_landing_page'] == true) {
                        $contactPageId = $landing->getId();
                    }
                }
            }

            return $contactPageId;
     }

     public function getServiceMenuLeft(SalesChannelContext $salesChannelContext): ?CategoryCollection
     {
          $context = $salesChannelContext->getContext();
          $salesChannel = $salesChannelContext->getSalesChannel();
          if ($salesChannel instanceof SalesChannelEntity) {
               $salesChannelCustomFields = $salesChannel->getCustomFields();
               if (is_array($salesChannelCustomFields) && array_key_exists('serviceCategoryLeftId', $salesChannelCustomFields) && $serviceCategoryLeftId = $salesChannelCustomFields['serviceCategoryLeftId']) {
                    $criteria = new Criteria([$serviceCategoryLeftId]);
                    $criteria->addAssociation('children');
                    $serviceMenuLeftCategory = $this->categoryRepository->search($criteria, $context)->first();
                    if ($serviceMenuLeftCategory instanceof CategoryEntity) {
                         $children = $serviceMenuLeftCategory->getChildren();
                         return $children;
                    }
               }
          }
          return null;
     }
}
