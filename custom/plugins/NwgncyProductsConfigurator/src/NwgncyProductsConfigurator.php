<?php

declare(strict_types=1);

namespace Nwgncy\ProductsConfigurator;

use Shopware\Core\Framework\Plugin;
use Nwgncy\ProductsConfigurator\Utils\CustomFieldInstaller;

use Shopware\Core\Framework\Plugin\Context\ActivateContext;

class NwgncyProductsConfigurator extends Plugin
{
     /**
      * @param ActivateContext $activateContext
      * @return void
      */
     public function activate(ActivateContext $activateContext): void
     {
          (new CustomFieldInstaller($this->container))->activate($activateContext);
          parent::activate($activateContext);
     }
}
