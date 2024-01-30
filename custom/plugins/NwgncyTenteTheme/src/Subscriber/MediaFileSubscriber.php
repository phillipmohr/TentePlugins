<?php declare(strict_types=1);

namespace Nwgncy\TenteTheme\Subscriber;

use Shopware\Core\Content\Media\Event\MediaFileExtensionWhitelistEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MediaFileSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            MediaFileExtensionWhitelistEvent::class => 'addEntryToFileExtensionWhitelist'
        ];
    }

    public function addEntryToFileExtensionWhitelist(MediaFileExtensionWhitelistEvent $event): void
    {
        $whiteList = $event->getWhitelist();
        $whiteList[] = 'stp';

        $event->setWhitelist($whiteList);
    }
}