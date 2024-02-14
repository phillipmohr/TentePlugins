<?php

namespace Nwgncy\CadConnector\Service;

use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpClient\HttpClient;
use GuzzleHttp\Client;
use Throwable;
use Exception;

class CadDownloadService
{

    private LoggerInterface $logger;
    private EntityRepository $productRepository;
    private SystemConfigService $systemConfigService;

    /**
     * @var ClientInterface
     */
    protected $client;

    const FORMAT_PDF = 'PDF';
    const FORMAT_STEP = 'STEP-2.14';
    const EXTENSION_PDF = "pdf";
    const EXTENSION_STEP = "stp";



    public function __construct(EntityRepository $productRepository, SystemConfigService $systemConfigService, LoggerInterface $logger)
    {
        $this->productRepository = $productRepository;
        $this->systemConfigService = $systemConfigService;
        $this->logger = $logger;

        $this->client = new Client();
    }

    public function handlePdfDownload(string $productId, SalesChannelContext $salesChannelContext)
    {
        $criteria = new Criteria([$productId]);
        $product = $this->productRepository->search($criteria, $salesChannelContext->getContext())->first();

        if ($product instanceof ProductEntity) {

            return $this->getUrlForProduct($product, self::FORMAT_PDF, $salesChannelContext);
        }
    }


    public function handleStpDownload(string $productId, SalesChannelContext $salesChannelContext)
    {

        $criteria = new Criteria([$productId]);
        $product = $this->productRepository->search($criteria, $salesChannelContext->getContext())->first();

        if ($product instanceof ProductEntity) {

            return $this->getUrlForProduct($product, self::FORMAT_STEP, $salesChannelContext);
        }
    }

    public function getUrlForProduct(ProductEntity $product, string $format, SalesChannelContext $salesChannelContext): ?string
    {
        /*$tenteCadFile = $product->getTenteCadFile();

        if ($tenteCadFile) {
            $tenteCadFile = pathinfo($tenteCadFile, PATHINFO_FILENAME);

            if ($format === self::FORMAT_PDF) {
                $tenteCadFile .= '-3D.' . self::EXTENSION_PDF;
            } else {
                // stp as fallback
                $tenteCadFile .= '.' . self::EXTENSION_STEP;
            }

            $tenteCadFileUrl = "$this->tenteCadFileUrlBase/$tenteCadFile";

            if ($this->isUrlReachable($tenteCadFileUrl, $salesChannelContext)) {
                return $tenteCadFileUrl;
            }
        }
        */
        /*dd($product);
        $cadFileLink = $product->getCadFileLink();
        */

        //if ($cadFileLink) {
            return $this->getUrlFromCadena($product, $format, $salesChannelContext);
        //}

        return null;
    }

    private function getUrlFromCadena(ProductEntity $product, string $format, SalesChannelContext $salesChannelContext): ?string
    {   
        $jobUrl = $this->createPartserverCadCreationJob($product, $format, $salesChannelContext);

        $cadResponse = $this->getUrlContentsExtended($jobUrl, (float)$this->getTimeout());

        if ($cadResponse === null) {
            throw new Exception('Timeout generating CAD file');
        }

        /** @var SimpleXMLElement|false $xml */
        $xml = simplexml_load_string(
            trim($cadResponse->getContent()),
            'SimpleXMLElement',
            LIBXML_ERR_ERROR
        );

        $errors = libxml_get_errors();

        if (!empty($errors) || !$xml instanceof \SimpleXMLElement) {
            throw new Exception('Invalid response XML');
        }

        $orderNo = (string)$xml->ORDERNO;

        if ($orderNo === '') {
            return null;
        }

        $files = $this->getCreatedCadFilesFromXml($xml);
        $cadRequestOrderUrl = $this->systemConfigService->get('NwgncyCadConnector.config.cadRequestOrderUrl', $salesChannelContext->getSalesChannelId());


        foreach ($files as $fileName) {
            $url = implode('/', [
                $cadRequestOrderUrl,
                urlencode($orderNo),
                urlencode($fileName)
            ]);

            if ($this->isUrlReachable($url, $salesChannelContext)) {
                return $url;
            }
        }

        return null;
    }


    public function getUrlContents(string $url, SalesChannelContext $salesChannelContext): string
    {
        $options = [];

        $endpointUsername = $this->systemConfigService->get('NwgncyCadConnector.config.endpointUsername', $salesChannelContext->getSalesChannelId());
        $endpointPassword = $this->systemConfigService->get('NwgncyCadConnector.config.endpointPassword', $salesChannelContext->getSalesChannelId());

        $options['auth_basic'] = "$endpointUsername:$endpointPassword";
        $options['verify_peer'] = false;
        $response = $this->client->request('GET', $url, $options);
        dd($response);


        return $response->getBody()->getContents();
        // return $this->getClient()->request(Request::METHOD_GET, $url, $options)->getContent();


    }

    /**
     * @throws Exception
     */
    private function createPartserverCadCreationJob(ProductEntity $product, string $format, SalesChannelContext $salesChannelContext): string
    {
        $productCustomFields = $product->getCustomFields();
        $productCadFileName = '';
        if (is_array($productCustomFields) && array_key_exists('tente_product_cad_filename', $productCustomFields)) {
            $productCadFileName = $productCustomFields['tente_product_cad_filename'];
        }
        $params = [
            'language' => 'german',
            'firm' => 'tente',
            'cgiaction' => 'download',
            'emailformat' => 'text',
            'user_company' => 'TENTE',
            'user' => 'DOWNLOADSCRIPT',
            'email' => 'marketing@tente.de',
            'ok_url' => '<%download_xml%>',
            'ok_url_type' => 'text',
            'format' => urlencode($format),
            'part' => '{prt=tente,' . $productCadFileName . '}',
        ];
        

        $cadOrderUrl = $this->systemConfigService->get('NwgncyCadConnector.config.cadOrderUrl', $salesChannelContext->getSalesChannelId());

        $url = $cadOrderUrl . '?' . http_build_query($params);

        $xmlUrl = trim($this->getUrlContents($url, $salesChannelContext));

        if (
            strpos($xmlUrl, 'http://') === 0 ||
            strpos($xmlUrl, 'https://') === 0
        ) {
            return $xmlUrl;
        } else {
            $message = 'Unexpected response from partcommunity (';
            $message .= "URL={$url},";
            $message .= "Format={$format},";
            $message .= "Artno={$product->getName()},";
            $message .= "ArtbaseId={$product->getEan()}";
            $message .= ')';
            throw new Exception($message);
        }
    }


    public function getUrlContentsExtended(string $uri, float $timeout = 60)
    {
        if ($timeout === 0) {
            $timeout = PHP_INT_MAX;
        }

        $start = microtime(true);

        do {
            try {

                $httpClientInterface = HttpClient::create();
                $response = $httpClientInterface->request('GET', $uri);

                if ($response->getStatusCode() === 200) {
                    return $response;
                }
            } catch (Exception $e) {
                // ignore
            }

            $timeRemaining = $timeout - (microtime(true) - $start);

            if ($timeRemaining > 0.5) {
                usleep(500 * 1000);
            }
        } while ($timeRemaining > 0);

        return null;
    }

    /**
     * @throws Exception
     */
    private function getCreatedCadFilesFromXml(\SimpleXMLElement $xml): array
    {
        $result = [];

        try {
            foreach ($xml->SECTION->CONTENTS->children() as $part) {
                foreach ($part->children() as $children) {
                    if ($children->getName() === 'FILENAME') {
                        $result[] = $children;
                    }
                }
            }
        } catch (Exception $ex) {
            throw new Exception(__METHOD__ . ': Invalid partcommunity CAD XML: ' . $xml->asXML());
        }

        return $result;
    }


    private function isUrlReachable(string $url, SalesChannelContext $salesChannelContext): bool
    {

        $endpointUsername = $this->systemConfigService->get('NwgncyCadConnector.config.endpointUsername', $salesChannelContext->getSalesChannelId());
        $endpointPassword = $this->systemConfigService->get('NwgncyCadConnector.config.endpointPassword', $salesChannelContext->getSalesChannelId());

        $options['auth_basic'] = "$endpointUsername:$endpointPassword";
        $httpClientInterface = HttpClient::create();
        $response = $httpClientInterface->request('HEAD', $url, $options);

        $status = $response->getStatusCode();

        return $status >= 200 && $status < 300;
    }

    /**
     * Get TimeOut in seconds.
     *
     * @return int
     */
    public function getTimeout(): int
    {
        return 60;
    }
}
