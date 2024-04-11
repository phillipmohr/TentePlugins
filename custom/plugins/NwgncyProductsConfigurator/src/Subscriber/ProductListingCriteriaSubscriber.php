<?php declare(strict_types=1);

namespace Nwgncy\ProductsConfigurator\Subscriber;

use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductListingCriteriaSubscriber implements EventSubscriberInterface
{
    private EntityRepository $downloadRepository;

    public function __construct(
        EntityRepository $downloadRepository
    )
    {
        $this->downloadRepository = $downloadRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductListingCriteriaEvent::class => 'onProductListingCriteria'
        ];
    }

    public function onProductListingCriteria(ProductListingCriteriaEvent $event)
    {
        $request = $event->getRequest();

        if ($request->query->get('hasCadFile', false)) {

            $productDownloadCriteria = new Criteria();
            $productDownloadCriteria->addAssociation('downloadTab');
            $productDownloadCriteria->addFilter(new EqualsFilter('downloadTab.id', '018D0D4F63B677A299881E7D9DE7781F'));
            $downloadProductsCollection = $this->downloadRepository->search($productDownloadCriteria, $event->getContext());

            if ($downloadProductsCollection->getTotal() > 0) {
            
                $downloadProducts = $downloadProductsCollection->getElements();
    
                $productsWithFiles = [];
                foreach ($downloadProducts as $downloadProduct) {
                    $productsWithFiles[] = $downloadProduct->getProductId();
                }
                $productsWithFiles = array_unique($productsWithFiles);

                $criteria = $event->getCriteria();

                $criteria->addFilter(
                    new EqualsAnyFilter('product.id', $productsWithFiles)
                );
            }
        }
    }
}