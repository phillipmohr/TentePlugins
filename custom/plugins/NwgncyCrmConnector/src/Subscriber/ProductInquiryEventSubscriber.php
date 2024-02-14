<?php declare(strict_types=1);

namespace Nwgncy\CrmConnector\Subscriber;

use Nimbits\NimbitsPriceOnRequestNext\Events\MailBeforeSendEvent;
use Nwgncy\CrmConnector\Service\CrmService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductInquiryEventSubscriber implements EventSubscriberInterface
{
     private CrmService $crmService;
     public function __construct(CrmService $crmService)
     {
          $this->crmService = $crmService;
     }
     
     public static function getSubscribedEvents(): array
     {
          return [
               MailBeforeSendEvent::class => 'onProductInquired'
          ];
     }

     public function onProductInquired(MailBeforeSendEvent $event)
     {
          $this->crmService->processEvent($event);
     }
}