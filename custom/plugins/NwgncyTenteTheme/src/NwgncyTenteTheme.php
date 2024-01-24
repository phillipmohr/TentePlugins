<?php declare(strict_types=1);

namespace Nwgncy\TenteTheme;

use Nwgncy\TenteTheme\Utils\CustomFieldInstaller;
use Shopware\Core\Framework\Plugin;
use Shopware\Storefront\Framework\ThemeInterface;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use \Shopware\Core\Defaults;

class NwgncyTenteTheme extends Plugin implements ThemeInterface
{
}
