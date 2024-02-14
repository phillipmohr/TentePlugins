<?php declare(strict_types=1);

namespace Nwgncy\CrmConnector\Subscriber;

use Nwgncy\CrmConnector\Event\CadFileDownloadEvent;
use Nwgncy\CrmConnector\Service\CrmService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CadFileDownloadEventSubscriber implements EventSubscriberInterface
{
     private CrmService $crmService;
     public function __construct(CrmService $crmService)
     {
          $this->crmService = $crmService;
     }
     
     public static function getSubscribedEvents(): array
     {
          return [
               CadFileDownloadEvent::class => 'onCadFileDownloaded'
          ];
     }

     public function onCadFileDownloaded(CadFileDownloadEvent $event)
     {
          $this->crmService->processEvent($event);
     }
}
