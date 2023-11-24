<?php declare(strict_types=1);

namespace NwgncyCustomFields;

use NwgncyCustomFields\Utils\CustomFieldInstaller;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;

/**
 *
 */
class NwgncyCustomFields extends Plugin
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