<?php declare(strict_types=1);

namespace Nwgncy\CadConnector\Service;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Nwgncy\CrmConnector\Event\CadFileDownloadEvent;

class CadDownloadEventService
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function fireEvent(SalesChannelContext $context)
    {
        $this->eventDispatcher->dispatch(new CadFileDownloadEvent($context));
    }
}