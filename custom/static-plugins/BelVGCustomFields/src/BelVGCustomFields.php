<?php declare(strict_types=1);

namespace BelVGCustomFields;

use BelVGCustomFields\Utils\CustomFieldInstaller;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;

/**
 *
 */
class BelVGCustomFields extends Plugin
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