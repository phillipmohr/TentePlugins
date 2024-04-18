<?php
declare(strict_types=1);

namespace NwgncyShippingTime\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use NwgncyShippingTime\Core\Content\ShippingTime\SalesChannel\ShippingTimeRoute;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Component\Filesystem\Filesystem;

class ProductPageLoadedSubscriber implements EventSubscriberInterface
{

    private ShippingTimeRoute $shippingTimeRoute;

    public function __construct(ShippingTimeRoute $shippingTimeRoute)
    {
        $this->shippingTimeRoute = $shippingTimeRoute;
    }

   public static function getSubscribedEvents(): array
   {
       return [
           ProductPageLoadedEvent::class => 'onProductPageLoaded',
       ];
   }

   public function onProductPageLoaded(ProductPageLoadedEvent $event)
   {

        $page = $event->getPage();
        $productId = $page->getProduct()->getId();

        $shipptingTimeRoutResponse = $this->shippingTimeRoute->load(new Criteria(), $event->getSalesChannelContext(), $productId);
        $shippingTimeCollection = $shipptingTimeRoutResponse->getObject();

        $fastDeliveryAvailable = false;
        if ($shippingTimeCollection->getTotal() > 0) {
            $fastDeliveryAvailable = (bool)$shippingTimeCollection->first()->getShippingTime();
        }

        $event->getPage()->addExtension('fastDeliveryAvailable', new ArrayStruct(['fastDeliveryAvailable' => $fastDeliveryAvailable]));
   }
}