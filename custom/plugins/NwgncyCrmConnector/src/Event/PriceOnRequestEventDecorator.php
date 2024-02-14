<?php

declare(strict_types=1);

namespace Nwgncy\CrmConnector\Event;

use Nimbits\NimbitsPriceOnRequestNext\Events\MailBeforeSendEvent;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PriceOnRequestEventDecorator
{
     public const EVENT_NAME = 'nimbits.product_inquiry.before_send';

     protected SalesChannelContext $context;
     protected array $mailvars;

     public function __construct(SalesChannelContext $context, array $mailvars)
     {
          $this->context = $context;
          $this->mailvars = $mailvars;
     }

     public function getMailvars(): array
     {
          return $this->mailvars;
     }

     public function setMailvars($mailvars): void
     {
          $this->mailvars = $mailvars;
     }

     public function getName(): string
     {
        return self::EVENT_NAME;
     }
  
     public function getSalesChannelContext(): SalesChannelContext
     {
        return $this->context;
     }
       
     public function getContext(): Context
     { 
        return $this->context->getContext();
     }
}
