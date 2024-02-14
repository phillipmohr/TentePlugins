<?php declare(strict_types = 1);

namespace Nwgncy\CrmConnector\Struct;

use Shopware\Core\Framework\Struct\Struct;

class CrmResponse extends Struct
{
     private bool $success;

     private string $message;

     public function isSuccess(): bool
     {
          return $this->success;
     }

     public function setSuccess($success): void
     {
          $this->success = $success;
     }


     public function getMessage(): string
     {
          return $this->message;
     }

     public function setMessage($message): void
     {
          $this->message = $message;

     }
}
