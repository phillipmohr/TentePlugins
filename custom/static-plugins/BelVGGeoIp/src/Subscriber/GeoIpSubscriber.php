<?php declare(strict_types=1);

namespace BelVGGeoIp\Subscriber;

use BelVGGeoIp\Service\GeoData;
use BelVGGeoIp\Service\IpAddress;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 *
 */
class GeoIpSubscriber implements EventSubscriberInterface
{

    /**
     * @param RequestStack $request
     * @param GeoData $geoData
     */
    public function __construct(protected RequestStack $request,
                                protected GeoData      $geoData)
    {
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            StorefrontRenderEvent::class => [
                ['addStoreSwitcherInfo'],
            ],
        ];
    }

    /**
     * @param StorefrontRenderEvent $event
     */
    public function addStoreSwitcherInfo(StorefrontRenderEvent $event): void
    {
        $ip = IpAddress::getIpAddress();
        $country = $this->geoData->getCountry($ip);
        $event->setParameter('geoIP', new ArrayEntity([
            'country' => $country,
            'countryIsoCode' => $country?->country->isoCode
        ]));
    }
}