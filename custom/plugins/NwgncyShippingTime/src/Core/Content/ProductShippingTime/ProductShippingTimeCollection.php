<?php declare(strict_types=1);

namespace NwgncyShippingTime\Core\Content\ProductShippingTime;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(ProductShippingTimeEntity $entity)
 * @method void set(string $key, ProductShippingTimeEntity $entity)
 * @method ProductShippingTimeEntity[] getIterator()
 * @method ProductShippingTimeEntity[] getElements()
 * @method ProductShippingTimeEntity|null get(string $key)
 * @method ProductShippingTimeEntity|null first()
 * @method ProductShippingTimeEntity|null last()
 */
class ProductShippingTimeCollection extends EntityCollection
{
    /**
     * @return list<string>
     */
    public function getProductIds(): array
    {
        return $this->fmap(fn (ProductShippingTimeEntity $shippingTime) => $shippingTime->getProductId());
    }

    public function filterByProductId(string $id): self
    {
        return $this->filter(fn (ProductShippingTimeEntity $shippingTime) => $shippingTime->getProductId() === $id);
    }

    public function filterBySalesChannelId(string $id): self
    {
        return $this->filter(fn (ProductShippingTimeEntity $shippingTime) => $shippingTime->getSalesChannelId() === $id);
    }

    protected function getExpectedClass(): string
    {
        return ProductShippingTimeEntity::class;
    }

    public function getApiAlias(): string
    {
        return 'nwgncy_product_shipping_time_collection';
    }

}
 