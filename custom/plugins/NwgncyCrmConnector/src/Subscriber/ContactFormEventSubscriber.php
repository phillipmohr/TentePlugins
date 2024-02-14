<?php declare(strict_types=1);

namespace Nwgncy\CrmConnector\Subscriber;

use Nwgncy\CrmConnector\Event\ContactFormEventDecorated;
use Nwgncy\CrmConnector\Service\CrmService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactFormEventSubscriber implements EventSubscriberInterface
{
     private CrmService $crmService;
     public function __construct(CrmService $crmService)
     {
          $this->crmService = $crmService;
     }
     
     public static function getSubscribedEvents(): array
     {
          return [
               ContactFormEventDecorated::class => 'onContactFormSent'
          ];
     }

     public function onContactFormSent(ContactFormEventDecorated $event)
     {
          $this->crmService->processEvent($event);
     } 
}



