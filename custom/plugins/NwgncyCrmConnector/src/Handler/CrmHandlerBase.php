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

     protected LoggerInterface $customLogger;
     protected SystemConfigService $systemConfigService;

     const METHOD_GET = 'GET';
     const METHOD_POST = 'POST';
     const METHOD_PUT = 'PUT';
     const METHOD_DELETE = 'DELETE';
  
    public function __construct(
          LoggerInterface $logger,
          SystemConfigService $systemConfigService,
          LoggerInterface $customLogger
          )
     {
         $this->logger = $logger;
         $this->systemConfigService = $systemConfigService;
         $this->customLogger = $customLogger;
     }

     abstract public function sendData(CrmRecord $record);

     protected function logCrmSuccess(string $CrmName, CrmResponse $crmResponse, CrmRecord $crmRecord) {
          
          $logMessageArr = [
               $message = 'CRM: ' . $CrmName
          ];
          $logMessageArr [] = 'Response Message: ' . $crmResponse->getMessage();
          $logMessageArr [] = 'Time: ' . $crmRecord->getDcTimestamp();

          $message = implode(". ", $logMessageArr);

          $message .= ' ';
          $message .= $crmRecord->getDataForLogging();

          $this->customLogger->info($message);
     }

     protected function logCrmError(string $CrmName, Throwable $exception, CrmRecord $crmRecord, string $type = 'error') {
          $logMessageArr = [
               $message = 'CRM: ' . $CrmName
          ];

          $logMessageArr [] = 'Response Message: ' . $exception->getMessage();
          $logMessageArr [] = 'Time: ' . $crmRecord->getDcTimestamp();

          $message = implode(". ", $logMessageArr);

          $message .= ' ';
          $message .= $crmRecord->getDataForLogging();

          if ($type == "critical") {
               $this->customLogger->critical($message);
          } else {
               $this->customLogger->error($message);
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
