<?php declare(strict_types=1);

namespace NwgncyShippingTime\Core\Content\ShippingTime\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

abstract class AbstractShippingTimeRoute
{
    abstract public function getDecorated(): AbstractShippingTimeRoute;

    abstract public function load(Criteria $criteria, SalesChannelContext $context, string $productId): ShippingTimeRouteResponse;
}
