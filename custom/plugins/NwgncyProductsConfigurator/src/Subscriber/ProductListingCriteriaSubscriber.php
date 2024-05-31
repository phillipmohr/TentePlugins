<?php declare(strict_types=1);

namespace Nwgncy\ProductsConfigurator\Subscriber;

use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Symfony\Component\HttpFoundation\Request;

class ProductListingCriteriaSubscriber implements EventSubscriberInterface
{
    private EntityRepository $downloadRepository;

    private EntityRepository $shippingTimeRepository;

    public function __construct(
        EntityRepository $downloadRepository,
        EntityRepository $shippingTimeRepository
    )
    {
        $this->downloadRepository = $downloadRepository;
        $this->shippingTimeRepository = $shippingTimeRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductListingCriteriaEvent::class => 'onProductListingCriteria'
            // ProductListingResultEvent::class => 'filterResult'
        ];
    }
    public function filterResult(ProductListingResultEvent $event)
    {

        $result = $event->getResult();
        $request = $event->getRequest();
        $salesChannelContext = $event->getSalesChannelContext();
        $salesChannelId = $salesChannelContext->getSalesChannel()->getId();


        if ($result->getTotal() == 0) {
            return false;
        }

        $resultProductIds = $result->getIds();

        if ($request->query->get('fastDelivery', false)) {

            $shippingTimeCriteria = new Criteria();
            $shippingTimeCriteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
            $shippingTimeCriteria->addFilter(new EqualsFilter('shippingTime', 1));
            $shippingTimeCollection = $this->shippingTimeRepository->search($shippingTimeCriteria, $event->getContext());

            if ($shippingTimeCollection->getTotal() > 0) {

                $shippingTimeProductIds = $shippingTimeCollection->getEntities()->getProductIds();
    
                $shippingTimeProductIds = [];
                $resultProductIds = [];

                $idsToRemove = array_diff($resultProductIds, $shippingTimeProductIds);
                
                if (!empty($idsToRemove)) {
                    foreach ($idsToRemove as $idToRemove) {
                        $result->remove($idToRemove);
                    }
                }
    
            } else {

                foreach ($resultProductIds as $idToRemove) {
                    $result->remove($idToRemove);
                } 

            }
    
        }

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

                $idsToRemove = array_diff($resultProductIds, $productsWithFiles);

                if (!empty($idsToRemove)) {
                    foreach ($idsToRemove as $idToRemove) {
                        $result->remove($idToRemove);
                    }
                }

            } else {
                foreach ($resultProductIds as $idToRemove) {
                    $result->remove($idToRemove);
                } 
            }
        }
    }

    public function onProductListingCriteria(ProductListingCriteriaEvent $event)
    {
        $request = $event->getRequest();
        $criteria = $event->getCriteria();

        $salesChannelContext = $event->getSalesChannelContext();
        $salesChannelId = $salesChannelContext->getSalesChannel()->getId();

        $properties = $this->getPropertyIds($request);

        $stainlessPropId = '018e80180fec71ffa6bc1e08fd791ebb';
        $electricConductingPropId = '018e801211907288993bddfb03923a20';

        if (in_array($stainlessPropId, $properties) && in_array($electricConductingPropId, $properties)) {
            
            $postFilters = $criteria->getPostFilters();
            if (!empty($postFilters)) {

                foreach ($postFilters as $postFilter) {
    
                    if ($postFilter->getOperator() === 'AND') {
    
                        $newFilterStainless = new OrFilter([
                            new EqualsAnyFilter('product.optionIds', [$stainlessPropId]),
                            new EqualsAnyFilter('product.propertyIds', [$stainlessPropId]),
                        ]);
            
                        $newFilterElectric = new OrFilter([
                            new EqualsAnyFilter('product.optionIds', [$electricConductingPropId]),
                            new EqualsAnyFilter('product.propertyIds', [$electricConductingPropId]),
                        ]);
            
                        $postFilter->addQuery($newFilterElectric);
                        $postFilter->addQuery($newFilterStainless);
                        break;
                    }
                }
            }
        }

        if ($request->query->get('fastDelivery', false)) {

            $shippingTimeCriteria = new Criteria();
            $shippingTimeCriteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
            $shippingTimeCriteria->addFilter(new EqualsFilter('shippingTime', 1));
            $shippingTimeCollection = $this->shippingTimeRepository->search($shippingTimeCriteria, $event->getContext());

            if ($shippingTimeCollection->getTotal() > 0) {
                
                $shippingTimeProductIds = $shippingTimeCollection->getEntities()->getProductIds();

                $criteria->addFilter(
                    new EqualsAnyFilter('product.id', $shippingTimeProductIds)
                );
            }
        }

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

    private function getPropertyIds(Request $request): array
    {
        $ids = $request->query->get('properties', '');
        if ($request->isMethod(Request::METHOD_POST)) {
            $ids = $request->request->get('properties', '');
        }

        if (\is_string($ids)) {
            $ids = explode('|', $ids);
        }

        /** @var list<string> $ids */
        $ids = array_filter((array) $ids);

        return $ids;
    }
}