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
                ['addStoreSwitcherInfo', -9999],
            ],
        ];
    }

    /**
     * @param StorefrontRenderEvent $event
     * @return void
     */
    public function addStoreSwitcherInfo(StorefrontRenderEvent $event): void
    {
        $session = $this->request->getSession();
        if ($session->has('countryData')) {
            $countryData = $session->get('countryData');
            $this->sortCountry($event, $countryData['countryIsoCode']);
        } else {
            $ip = IpAddress::getIpAddress();
            $country = $this->geoData->getCountry($ip);
            $isoCode = $country?->country->isoCode;
            $continentCode = $country?->continent->code;

            $this->sortCountry($event, $isoCode);
            $countryData = [
                'countryIsoCode' => $isoCode,
                'continentCode' => $continentCode
            ];
            $session->set('countryData', $countryData);
        }

        $event->setParameter('geoIP', new ArrayEntity($countryData));
    }

    /**
     * @param StorefrontRenderEvent $event
     * @param string|null $isoCode
     * @return void
     */
    protected function sortCountry(StorefrontRenderEvent $event, ?string $isoCode): void
    {
        if ($isoCode) {
            $options = $event->getParameters()['zeobvStoreSwitcher']?->get('options');
            $options->sort(function ($a, $b) use ($isoCode) {
                $isoA = $a->getCountry()->getIso();
                $isoB = $b->getCountry()->getIso();
                if ($isoA == $isoCode) {
                    return -1;
                }
                if ($isoB == $isoCode) {
                    return 1;
                }
                return 0;
            });
        }
    }
}