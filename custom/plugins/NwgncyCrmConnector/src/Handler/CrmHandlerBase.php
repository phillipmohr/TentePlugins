<?php declare(strict_types=1);

namespace Nwgncy\CrmConnector\Handler;

use Nwgncy\CrmConnector\Struct\CrmRecord;
use Nwgncy\CrmConnector\Struct\CrmResponse;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Throwable;

abstract class CrmHandlerBase
{
     protected LoggerInterface $logger;
     protected SystemConfigService $systemConfigService;

     const METHOD_GET = 'GET';
     const METHOD_POST = 'POST';
     const METHOD_PUT = 'PUT';
     const METHOD_DELETE = 'DELETE';
  
     public function __construct(LoggerInterface $logger, SystemConfigService $systemConfigService)
     {
         $this->logger = $logger;
         $this->systemConfigService = $systemConfigService;
     }

     abstract public function sendData(CrmRecord $record);

     protected function logCrmSuccess(string $CrmName, CrmResponse $crmResponse, CrmRecord $crmRecord) {
          $logMessageArr = [
               $message = 'CRM: ' . $CrmName
          ];
          $logMessageArr [] = 'Response Message: ' . $crmResponse->getMessage();
          $logMessageArr [] = 'Time: ' . $crmRecord->getDcTimestamp();
          $logMessageArr [] = 'Company: ' . $crmRecord->getCompanyName();

          $message = implode(". ", $logMessageArr);

          $this->logger->info($message);
     }

     protected function logCrmError(string $CrmName, Throwable $exception, CrmRecord $crmRecord, string $type = 'error') {
          $logMessageArr = [
               $message = 'CRM: ' . $CrmName
          ];
          $logMessageArr [] = 'Response Message: ' . $exception->getMessage();
          $logMessageArr [] = 'Time: ' . $crmRecord->getDcTimestamp();
          $logMessageArr [] = 'Company: ' . $crmRecord->getCompanyName();

          $message = implode(". ", $logMessageArr);

          if ($type == "critical") {
               $this->logger->critical($message);
          } else {
               $this->logger->error($message);
          }
     }
     
     protected function createCrmResponse($response): CrmResponse
     {
         $crmResponse = new CrmResponse();
         $crmResponse->setSuccess(($response->getStatusCode() === 200) ? true : false);
 
         $headerValuesAsString = "";
         foreach ($response->getHeaders() as $name => $values) {
             $headerValuesAsString .= $name . ': ' . implode(', ', $values) . "\t";
         }
         $crmResponse->setMessage($headerValuesAsString);
 
         return $crmResponse;
     }

     
}
