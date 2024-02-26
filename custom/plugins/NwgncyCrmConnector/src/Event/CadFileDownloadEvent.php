<?php declare(strict_types=1);

namespace Nwgncy\CrmConnector\Event;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\Event\CustomerAware;
use Shopware\Core\Framework\Event\FlowEventAware;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Event\SalesChannelAware;
use Shopware\Core\Framework\Event\EventData\EventDataCollection;
use Shopware\Core\Framework\Event\EventData\EntityType;
use Shopware\Core\Checkout\Customer\CustomerDefinition;

class CadFileDownloadEvent extends Event implements ShopwareSalesChannelEvent, CustomerAware
{
   
   public const EVENT_NAME = 'tente.cad_file.download';

   public function __construct(private readonly SalesChannelContext $salesChannelContext, private readonly CustomerEntity $customer)
   {
   }

   public function getName(): string
   {
      return self::EVENT_NAME;
   }

   public function getContext(): Context
   { 
      return $this->salesChannelContext->getContext();
   }

   public function getCustomer(): CustomerEntity
   {
       return $this->customer;
   }

   public function getSalesChannelContext(): SalesChannelContext
   {
      return $this->salesChannelContext;
   }

   public function getCustomerId(): string
   {
       return $this->getCustomer()->getId();
   }

   public static function getAvailableData(): EventDataCollection
   {
       return (new EventDataCollection())
           ->add('customer', new EntityType(CustomerDefinition::class));
   }
}