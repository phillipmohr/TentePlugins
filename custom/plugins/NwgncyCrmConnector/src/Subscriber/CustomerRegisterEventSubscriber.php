<?php declare(strict_types=1);

namespace Nwgncy\CrmConnector\Subscriber;

use Nwgncy\CrmConnector\Service\CrmService;
use Shopware\Core\Checkout\Customer\Event\CustomerRegisterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomerRegisterEventSubscriber implements EventSubscriberInterface
{
     private CrmService $crmService;
     public function __construct(CrmService $crmService)
     {
          $this->crmService = $crmService;
     }
     
     public static function getSubscribedEvents(): array
     {
          return [
               CustomerRegisterEvent::class => 'onCustomerRegistered'
          ];
     }

     public function onCustomerRegistered(CustomerRegisterEvent $event)
     {
          $this->crmService->processEvent($event);
     }
}