<?php declare(strict_types=1);

namespace NwgncyAcrisProductDownloadsCSFix\Subscriber;

use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class ProductPageSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageCriteriaEvent::class => 'onProductPageCriteriaLoaded'
        ];
    }

    public function onProductPageCriteriaLoaded(ProductPageCriteriaEvent $event): void
    {
        $productId = $event->getProductId();
        $criteria = $event->getCriteria();
        $criteria->getAssociation('acrisDownloads.downloadTab.acrisDownloads')
            ->addFilter(new EqualsFilter('productId', $productId));

    }
}
