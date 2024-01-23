<?php

namespace BelVGGeoIp\Service;

use BelVGGeoIp\Service\GeoIp\LocalData;
use BelVGGeoIp\Service\GeoIp\RemoteHost;
use Exception as Exception;
use GeoIp2\Model\Country;
use GeoIp2\ProviderInterface;
use Psr\Log\LoggerInterface;

/**
 *
 */
class GeoData
{
    /**
     * @var string
     */
    public static string $PROVIDER_TYPE = 'GeoIpLocal';
    /**
     * @var ProviderInterface|null
     */
    protected ?ProviderInterface $provider = null;

//    private readonly LoggerInterface $logger;
    public function __construct(private readonly LoggerInterface $logger)
    {
        $this->init();
    }

    /**
     * @return void
     */
    protected function init()
    {
        try {
            $class = $this->getServiceClass();
            if (class_exists($class)) {
                $this->provider = (new $class())->getProvider();
            }
        } catch (Exception $e) {
            $this->logger->warning('BelVGGeoIp_GeoData:Error message', ['exception' => $e]);
        }
    }

    /**
     * @return string
     */
    protected function getServiceClass(): string
    {
        switch (self::$PROVIDER_TYPE) {
            case 'GeoIpLocal':
                $class = LocalData::class;
                break;
            default:
                $class = RemoteHost::class;
        }
        return $class;
    }

    /**
     * @param string $ip
     * @return Country|null
     */
    public function getCountry(string $ip): ?Country
    {
        if ($this->provider) {
            try {
                return $this->provider->country($ip);
            } catch (Exception $e) {
                $this->logger->warning('BelVG_CountryPopup:Error message', ['exception' => $e]);
            }
        }
        return null;
    }

    /**
     * @param string $ip
     * @return string|null
     */
    public function getCountryIsoCode(string $ip): ?string
    {
        $country = $this->getCountry($ip);
        return $country?->country->isoCode;
    }


}
