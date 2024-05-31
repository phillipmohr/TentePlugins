<?php
declare(strict_types=1);

namespace Nwgncy\TenteTheme\Subscriber;

use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductListingSubscriber implements EventSubscriberInterface
{
   public static function getSubscribedEvents(): array
   {
       return [
           ProductEvents::PRODUCT_LOADED_EVENT => 'onProductsLoaded',
           ProductListingCriteriaEvent::class => 'handleRequest',
           ProductSearchCriteriaEvent::class => 'handleRequest',
           ProductEvents::PRODUCT_SUGGEST_RESULT => "onProductsLoaded",
       ];
   }

   public function handleRequest(ProductListingCriteriaEvent $event)
   {
       $event->getCriteria()->addAssociation('properties');
       $event->getCriteria()->addAssociation('categories');
       $event->getCriteria()->addAssociation('properties.group');
   }

   public function onProductsLoaded(EntityLoadedEvent $event)
   {
       if (!($event->getContext()->getSource() instanceof SalesChannelApiSource)) {
           return;
       }

       foreach ($event->getEntities() as $productEntity) {
           /** @var SalesChannelProductEntity $productEntity */
           $properties = $productEntity->getProperties();
           if ($properties) {
               $grouped = $properties->groupByPropertyGroups();
               $grouped->sortByPositions();
               $grouped->sortByConfig();
               $productEntity->setSortedProperties($grouped);
           }
       }
   }
}