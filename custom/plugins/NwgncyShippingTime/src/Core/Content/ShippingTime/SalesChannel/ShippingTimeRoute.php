<?php declare(strict_types=1);

namespace NwgncyShippingTime\Core\Content\ShippingTime\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;


#[Route(defaults: ['_routeScope' => ['storefront']])]
class ShippingTimeRoute extends AbstractShippingTimeRoute
{
    public function __construct(private readonly EntityRepository $shippingTimeRepository)
    {
    }

    public function getDecorated(): AbstractShippingTimeRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route( 
        path: '/store-api/nwgncy-shipping-time',
        name: 'store-api.nwgncy-shipping-time.search',
        methods: ['GET', 'POST'],
        defaults: ["XmlHttpRequest", true]
    )]
    public function load(Criteria $criteria, SalesChannelContext $context, string $productId): ShippingTimeRouteResponse
    {

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));
        $criteria->addFilter(new EqualsFilter('salesChannelId', $context->getSalesChannelId()));

        return new ShippingTimeRouteResponse($this->shippingTimeRepository->search($criteria, $context->getContext()));
    }
}
