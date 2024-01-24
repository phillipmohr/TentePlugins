<?php declare(strict_types=1);

namespace BelVGGeoIp\Service\GeoIp;

use GeoIp2\ProviderInterface;
use GeoIp2\WebService\Client;

/**
 *
 */
class RemoteHost extends GeoIpProvider
{
    /**
     *
     */
    protected const ACCOUNT_ID = 568511;
    /**
     *
     */
    protected const LICENSE_KEY = 'kBqrf8F3xbkLYmj0';
    /**
     *
     */
    protected const LOCALES = ['en'];
    /**
     *
     */
    protected const OPTIONS = ['host' => 'geolite.info'];

    /**
     * @return int
     */
    protected function getAccountId(): int
    {
        return self::ACCOUNT_ID;
    }

    /**
     * @return string
     */
    protected function getLicenseKey(): string
    {
        return self::LICENSE_KEY;
    }

    /**
     * @return string[]
     */
    protected function getLocales(): array
    {
        return self::LOCALES;
    }

    /**
     * @return string[]
     */
    protected function getOptions(): array
    {
        return self::OPTIONS;
    }

    /**
     * @return ProviderInterface|null
     */
    public function init(): ?ProviderInterface
    {
        return $this->initСlient();
    }

    /**
     * @return ProviderInterface|null
     */
    public function initСlient(): ?ProviderInterface
    {
        return new Client($this->getAccountId(), $this->getLicenseKey(), $this->getLocales(), $this->getOptions());
    }
}
