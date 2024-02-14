<?php
namespace Nwgncy\CadConnector\Storefront\Controller;

use Doctrine\DBAL\Connection;
use PDO;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nwgncy\CadConnector\Service\CadDownloadService;
use Nwgncy\CadConnector\Service\CadDownloadEventService;
/**
 * @Route(defaults={"_routeScope"={"storefront"}})
*/
class CadDownloadController extends StorefrontController
{

    private CadDownloadService $cadDownloadService;
    private CadDownloadEventService $cadDownloadEventService;

    public function __construct(CadDownloadService $cadDownloadService, CadDownloadEventService $cadDownloadEventService) {
        $this->cadDownloadService = $cadDownloadService;
        $this->cadDownloadEventService = $cadDownloadEventService;
    }

    /**
    * @Route("/cad-download/pdf", name="storefront.cad-download.pdf.post", methods={"POST"}, defaults={"XmlHttpRequest"=true})
    */
    public function downloadPdfFile(Request $request, SalesChannelContext $salesChannelContext)
    {
        $productId = $request->get('productId');
        $this->cadDownloadEventService->fireEvent($salesChannelContext);
        return $this->cadDownloadService->handlePdfDownload($productId, $salesChannelContext);
    }

    /**
    * @Route("/cad-download/stp", name="storefront.cad-download.stp.post", methods={"POST"}, defaults={"XmlHttpRequest"=true})
    */
    public function downloadStpFile(Request $request, SalesChannelContext $salesChannelContext)
    {
        $productId = $request->get('productId');
        $this->cadDownloadEventService->fireEvent($salesChannelContext);
        return $this->cadDownloadService->handleStpDownload($productId, $salesChannelContext);
    }
}