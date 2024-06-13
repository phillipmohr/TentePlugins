<?php declare(strict_types=1);

namespace Nwgncy\CrmConnector\Handler\Sap;

use Exception;
use Nwgncy\CrmConnector\Handler\CrmHandlerBase;
use Nwgncy\CrmConnector\Struct\CrmRecord;
use Nwgncy\CrmConnector\Struct\CrmResponse;
use Symfony\Component\HttpClient\HttpClient;
use Throwable;
use Symfony\Component\Filesystem\Filesystem;

class SapCrmHandler extends CrmHandlerBase
{
     const CRM_NAME = "SAP";

     public function sendDataFromCustomForms(CrmRecord $record) { 

          
          try {
               // $fsObject = new Filesystem();

               // $filePath = '/var/www/html/tentecom/public/sendDataFromCustomForms.html';
               // $fsObject->touch($filePath);
               // $fsObject->chmod($filePath, 0777);

               $url = 'https://link.tente.com/u/register.php';

               $httpClientInterface = HttpClient::create();
               
               $method = SELF::METHOD_GET;

               $recordDataArr = $record->getCustomFormData() + $record->getDataToCustomFormHiddenInputs();

               // $fsObject->appendToFile($filePath, @\Kint::dump($recordDataArr));
               $queryParameters = http_build_query($recordDataArr);
               // $fsObject->appendToFile($filePath, @\Kint::dump($queryParameters));
               $url .= '?' . $queryParameters;
               // exit;
               $response = $httpClientInterface->request($method, $url);
              
               
            //    dd([
            //        "CRM" => 'Sap',
            //        "status" => $responseStatusCode,
            //        "content" => $responseContent,
            //        "headers" => $responseHeaders,
            //        "info" => $responseInfo
            //    ]);
               // $fsObject->appendToFile($filePath, @\Kint::dump($response));
               $crmResponse = $this->createCrmResponse($response);
               // $fsObject->appendToFile($filePath, @\Kint::dump($crmResponse));
               if ($crmResponse->isSuccess()) {
                   $this->logCrmSuccess(SELF::CRM_NAME, $crmResponse, $record);
               } else {
                   $this->logCrmError(SELF::CRM_NAME, new Exception($crmResponse->getMessage(), $response->getStatusCode()), $record);
               }

              return $crmResponse;
               
          } catch (Throwable $th) {
               $this->logCrmError(SELF::CRM_NAME, $th, $record, 'critical');
               $crmResponse = new CrmResponse();
               $crmResponse->setMessage('Internal error');
               $crmResponse->setSuccess(false);

               return $crmResponse;
          }
     }

     public function sendData(CrmRecord $record) { 

          try {
               $requestLeadEndpoint = $this->systemConfigService->get('NwgncyCrmConnector.config.sapRequestLeadEndpoint');
               $cadEndpoint = $this->systemConfigService->get('NwgncyCrmConnector.config.sapCadEndpoint');
               $url = $requestLeadEndpoint;

               if ($record->getCadRequest() == 'true') {
                    $url = $cadEndpoint;
               }

               $record = $this->fillEmptyRequiredFields($record);
     
               $httpClientInterface = HttpClient::create();
               
               $method = SELF::METHOD_GET;
               $headers = [
                    'Authorization' => 'Basic ' . $this->buildCredentials(),
               ];
               $options =  [
                    'headers' => $headers,
               ];

               $recordDataArr = $record->getData();

               if (!empty($record->getProductName())) {
                    $recordDataArr['productname'] = $record->getProductName();
               }

               $queryParameters = http_build_query($recordDataArr);
               $url .= '?' . $queryParameters;

               $response = $httpClientInterface->request($method, $url, $options);
 

            //    $responseStatusCode = $response->getStatusCode();
            //    $responseContent = $response->getContent();
            //    $responseHeaders = $response->getHeaders();
            //    $responseInfo = $response->getInfo();

   
            //    dd([
            //        "CRM" => 'Sap',
            //        "status" => $responseStatusCode,
            //        "content" => $responseContent,
            //        "headers" => $responseHeaders,
            //        "info" => $responseInfo
            //    ]);
               
               $crmResponse = $this->createCrmResponse($response);

               if ($crmResponse->isSuccess()) {
                   $this->logCrmSuccess(SELF::CRM_NAME, $crmResponse, $record);
               } else {
                   $this->logCrmError(SELF::CRM_NAME, new Exception($crmResponse->getMessage(), $response->getStatusCode()), $record);
               }

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

     private function buildCredentials () {
          $user = $this->systemConfigService->get('NwgncyCrmConnector.config.sapAuthUser') ?? '';
          $password = $this->systemConfigService->get('NwgncyCrmConnector.config.sapAuthPassword') ?? '';
          $credentials = base64_encode("$user:$password");
          return $credentials;
     }
     
     private function fillEmptyRequiredFields(CrmRecord $crmRecord) : CrmRecord {
          if ($crmRecord->getCompanyName() == '') {
               $crmRecord->setCompanyName('undefined');
          }
          if ($crmRecord->getFirstname() == '') {
               $crmRecord->setFirstname('undefined');
          }
          if ($crmRecord->getLastname() == '') {
               $crmRecord->setLastname('undefined');
          }
          return $crmRecord;
     }
}
