<?php declare(strict_types=1);

namespace Nwgncy\TenteTheme;

use Nwgncy\TenteTheme\Utils\CustomFieldInstaller;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Storefront\Framework\ThemeInterface;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use \Shopware\Core\Defaults;

class NwgncyTenteTheme extends Plugin implements ThemeInterface
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        if ($this->checkIfCustomFieldExists($installContext->getContext(), 'product_download')) {
            $this->createDownloadProductCustomFiled($installContext->getContext());
        }
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);

        if ($this->checkIfCustomFieldExists($updateContext->getContext(), 'product_download')) {
            $this->createDownloadProductCustomFiled($updateContext->getContext());
        }
    }

    public function createDownloadProductCustomFiled(Context $context): void
    {
        /** @var EntityRepository $categoryRepository */
        $categoryRepository = $this->container->get('custom_field_set.repository');

        $categoryRepository->upsert(
            [
                [
                    'name' => 'product_download',
                    'active' => true,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Product download',
                            'de-DE' => 'Product download',
                        ],
                    ],
                    'customFields' => [
                        [
                            'name' => 'product_download_is',
                            'label' => 'Product download',
                            'type' => CustomFieldTypes::SWITCH,
                            'config' => [
                                'label' => [
                                    'en-GB' => 'Is product download?',
                                    'de-DE' => 'Is product download?',
                                ]
                            ]
                        ],
                    ],
                    'relations' => [
                        [
                            'entityName' => 'product',
                        ],
                    ],

                ],
            ],
            $context
        );
    }

    public function checkIfCustomFieldExists(Context $context, $name): bool
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', $name));

        /** @var EntityRepository $categoryRepository */
        $categoryRepository = $this->container->get('custom_field_set.repository');

        $categoryId = $categoryRepository->searchIds($criteria, $context)->getIds();

        if (empty($categoryId)) {
            return true;
        }

        return false;
    }
}
