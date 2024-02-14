<?php declare(strict_types=1);

namespace Nwgncy\CrmConnector\Event;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

class CadFileDownloadEvent extends Event implements ShopwareSalesChannelEvent
{
   protected SalesChannelContext $salesChannelContext;
   
   public const EVENT_NAME = 'cad_file.download';

   public function __construct(SalesChannelContext $context)
   {
      $this->salesChannelContext = $context;
   }

   public function getName(): string
   {
      return self::EVENT_NAME;
   }

   public function getContext(): Context
   { 
      return $this->salesChannelContext->getContext();
   }

   public function getSalesChannelContext(): SalesChannelContext
   {
      return $this->salesChannelContext;
   }
}