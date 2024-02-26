<?php
namespace Nwgncy\CadConnector\Storefront\Controller;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nwgncy\CadConnector\Service\CadDownloadService;
use Nwgncy\CadConnector\Service\CadDownloadEventService;
use Symfony\Component\HttpFoundation\Response;
use Nwgncy\CrmConnector\Event\CadFileDownloadEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class CadDownloadController extends StorefrontController
{

    public function __construct(private readonly CadDownloadService $cadDownloadService, private readonly CadDownloadEventService $cadDownloadEventService, private readonly EventDispatcherInterface $eventDispatcher, private readonly EntityRepository $customerRepository) {
       
    }


    #[Route(path: '/cad-download/pdf', name: 'storefront.cad-download.pdf.post', methods: ['POST'], defaults: ['XmlHttpRequest' => true])]
    public function downloadPdfFile(Request $request, SalesChannelContext $salesChannelContext)
    {
        $cadUrl = $request->get('cadFileUrl');
        $customerId = $request->get('customerId');

        $criteria = new Criteria([$customerId]);
        $criteria->addAssociation('addresses');
        $criteria->addAssociation('salutation');
        $criteria->addAssociation('defaultBillingAddress.country');
        $criteria->addAssociation('defaultBillingAddress.countryState');
        $criteria->addAssociation('defaultBillingAddress.salutation');
        $criteria->addAssociation('defaultShippingAddress.country');
        $criteria->addAssociation('defaultShippingAddress.countryState');
        $criteria->addAssociation('defaultShippingAddress.salutation');

        $customerEntity = $this->customerRepository->search($criteria, $salesChannelContext->getContext())->first();


        $event = new CadFileDownloadEvent(
            $salesChannelContext,
            $customerEntity
       );

       
       $this->eventDispatcher->dispatch(
            $event
       );
    
        $filename = basename($cadUrl);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
    
        // Set the file content by fetching it from the $cadUrl
        $pdfContent = file_get_contents($cadUrl);
        $response->setContent($pdfContent);
    
        return $response;
    }


    #[Route(path: '/cad-download/stp', name: 'storefront.cad-download.stp.post', methods: ['POST'], defaults: ['XmlHttpRequest' => true])]
    public function downloadStpFile(Request $request, SalesChannelContext $salesChannelContext)
    {

        $cadUrl = $request->get('cadFileUrl');
        $customerId = $request->get('customerId');

        $criteria = new Criteria([$customerId]);
        $criteria->addAssociation('addresses');
        $criteria->addAssociation('salutation');
        $criteria->addAssociation('defaultBillingAddress.country');
        $criteria->addAssociation('defaultBillingAddress.countryState');
        $criteria->addAssociation('defaultBillingAddress.salutation');
        $criteria->addAssociation('defaultShippingAddress.country');
        $criteria->addAssociation('defaultShippingAddress.countryState');
        $criteria->addAssociation('defaultShippingAddress.salutation');

        $customerEntity = $this->customerRepository->search($criteria, $salesChannelContext->getContext())->first();
        
        
        $event = new CadFileDownloadEvent(
            $salesChannelContext,
            $customerEntity
       );

       $this->eventDispatcher->dispatch(
            $event
       );

        
        $filename = basename($cadUrl);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
    
        // Set the file content by fetching it from the $cadUrl
        $pdfContent = file_get_contents($cadUrl);
        $response->setContent($pdfContent);
    
        return $response;
    }
}