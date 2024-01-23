<?php

namespace BelVGGeoIp\Service\GeoIp;
//require_once 'vendor/autoload.php';
require_once '/var/www/html/tente/html/custom/static-plugins/BelVGGeoIp/vendor2/autoload.php';


use GeoIp2\Database\Reader;
use GeoIp2\ProviderInterface;
use MaxMind\Db\Reader\InvalidDatabaseException;

/**
 *
 */
class LocalData extends GeoIpProvider
{
    /**
     *
     */
    public const LIB_PATH = '/GeoIp/lib/GeoLite2-Country.mmdb';

    /**
     * @return ProviderInterface|null
     * @throws InvalidDatabaseException
     */
    public function init(): ?ProviderInterface
    {
        return $this->initReader();
    }

    /**
     * @return ProviderInterface|null
     * @throws InvalidDatabaseException
     */
    public function initReader(): ?ProviderInterface
    {
        $libPath = $this->getPathLibrary();
        if ($libPath) {
            return new Reader($libPath);
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getPathLibrary(): ?string
    {
        $pathFile = dirname(__DIR__, 1) . self::LIB_PATH;
        if (file_exists($pathFile)) {
            return $pathFile;
        }
        return null;
    }
}
