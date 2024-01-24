<?php declare(strict_types=1);

namespace BelVGGeoIp\Service\GeoIp;

use GeoIp2\ProviderInterface;

/**
 *
 */
abstract class GeoIpProvider
{
    /**
     * @var ProviderInterface|null
     */
    protected ?ProviderInterface $provider = null;

    /**
     * @return ProviderInterface|null
     */
    abstract public function init(): ?ProviderInterface;

    /**
     *
     */
    public function __construct()
    {
        $this->provider = $this->init();
    }

    /**
     * @return ProviderInterface|null
     */
    public function getProvider(): ?ProviderInterface
    {
        return $this->provider;
    }
}