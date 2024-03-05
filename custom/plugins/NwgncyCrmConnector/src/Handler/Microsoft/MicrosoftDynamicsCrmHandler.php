<?php declare(strict_types=1);

namespace Nwgncy\CrmConnector\Handler\Microsoft;

use Exception;
use Nwgncy\CrmConnector\Handler\CrmHandlerBase;
use Nwgncy\CrmConnector\Struct\CrmRecord;
use Nwgncy\CrmConnector\Struct\CrmResponse;
use Symfony\Component\HttpClient\HttpClient;
use Throwable;

class MicrosoftDynamicsCrmHandler extends CrmHandlerBase
{
    const CRM_NAME = "Microsoft Dynamics";
 

    public function sendData(CrmRecord $record) {
        try {
            $requestLeadEndpoint = $this->systemConfigService->get('NwgncyCrmConnector.config.microsoftDynamicsRequestLeadEndpoint');
            $cadEndpoint = $this->systemConfigService->get('NwgncyCrmConnector.config.microsoftDynamicsCadEndpoint');
            $url = $requestLeadEndpoint;


            if ($record->getCadRequest() == 'true') {
                $url = $cadEndpoint;
            }

            $httpClientInterface = HttpClient::create();
            $method = SELF::METHOD_GET;

            $recordDataArr = $record->getData();
            $queryParameters = http_build_query($recordDataArr);
            $url .= '?' . $queryParameters;
    
            $response = $httpClientInterface->request($method, $url);
            
            $crmResponse = $this->createCrmResponse($response);

            if ($crmResponse->isSuccess()) {
                $this->logCrmSuccess(SELF::CRM_NAME, $crmResponse, $record);
            } else {
                $this->logCrmError(SELF::CRM_NAME, new Exception($crmResponse->getMessage(), $response->getStatusCode()), $record);
            }

            // $responseStatusCode = $response->getStatusCode();
            // $responseContent = $response->getContent();
            // $responseHeaders = $response->getHeaders();
            // $responseInfo = $response->getInfo();


            // dd([
            //     "CRM" => 'Dynamics',
            //     "status" => $responseStatusCode,
            //     "content" => $responseContent,
            //     "headers" => $responseHeaders,
            //     "info" => $responseInfo
            // ]);



            return $crmResponse;

            
        } catch (Throwable $th) {
            $this->logCrmError(SELF::CRM_NAME, $th, $record, 'critical');
            $crmResponse = new CrmResponse();
            $crmResponse->setMessage('Internal error');
            $crmResponse->setSuccess(false);
            //dd($th);
            return $crmResponse;
        }
    }

}