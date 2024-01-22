<?php declare(strict_types=1);

namespace BelVGGeoIp\Subscriber;

use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class GeoIpSubscriber implements EventSubscriberInterface
{
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


        $event->setParameter('geoIP', new ArrayEntity([

            'currentCountry' => [1, 2, 3, 4, 5]

        ]));
    }
}