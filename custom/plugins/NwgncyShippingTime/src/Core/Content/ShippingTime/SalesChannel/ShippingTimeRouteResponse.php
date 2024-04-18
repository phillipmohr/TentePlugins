<?php declare(strict_types=1);

namespace NwgncyShippingTime\Core\Content\ShippingTime\SalesChannel;

use NwgncyShippingTime\Core\Content\ProductShippingTime\ProductShippingTimeCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

/**
 * @property EntitySearchResult $object
 */
class ShippingTimeRouteResponse extends StoreApiResponse
{
    public function getShippingTimes(): ProductShippingTimeCollection
    {
        /** @var ProductShippingTimeCollection $collection */
        $collection = $this->object->getEntities();

        return $collection;
    }
}
