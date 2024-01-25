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

        if ($this->checkIfCustomFieldExists($installContext->getContext(), 'social_media_links')) {
            $this->createSocialMediaCustomFiled($installContext->getContext());
        }
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);

        if ($this->checkIfCustomFieldExists($updateContext->getContext(), 'product_download')) {
            $this->createDownloadProductCustomFiled($updateContext->getContext());
        }

        if ($this->checkIfCustomFieldExists($updateContext->getContext(), 'social_media_links')) {
            $this->createSocialMediaCustomFiled($updateContext->getContext());
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

    public function createSocialMediaCustomFiled(Context $context): void
    {
        /** @var EntityRepository $categoryRepository */
        $categoryRepository = $this->container->get('custom_field_set.repository');

        $categoryRepository->upsert(
            [
                [
                    'name' => 'social_media_links',
                    'active' => true,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Social media links',
                            'de-DE' => 'Social media links',
                        ],
                    ],
                    'customFields' => [
                        [
                            'name' => 'social_media_links_instagram',
                            'label' => 'Instagram link',
                            'type' => CustomFieldTypes::TEXT,
                            'config' => [
                                'label' => [
                                    'en-GB' => 'Instagram link',
                                    'de-DE' => 'Instagram link'
                                ],
                                'customFieldType' => CustomFieldTypes::TEXT
                            ]
                        ],
                        [
                            'name' => 'social_media_links_facebook',
                            'label' => 'Facebook link',
                            'type' => CustomFieldTypes::TEXT,
                            'config' => [
                                'label' => [
                                    'en-GB' => 'Facebook link',
                                    'de-DE' => 'Facebook link'
                                ],
                                'customFieldType' => CustomFieldTypes::TEXT
                            ]
                        ],
                        [
                            'name' => 'social_media_links_twitter',
                            'label' => 'Twitter link',
                            'type' => CustomFieldTypes::TEXT,
                            'config' => [
                                'label' => [
                                    'en-GB' => 'Twitter link',
                                    'de-DE' => 'Twitter link'
                                ],
                                'customFieldType' => CustomFieldTypes::TEXT
                            ]
                        ],
                        [
                            'name' => 'social_media_links_youtube',
                            'label' => 'YouTube link',
                            'type' => CustomFieldTypes::TEXT,
                            'config' => [
                                'label' => [
                                    'en-GB' => 'YouTube link',
                                    'de-DE' => 'YouTube link'
                                ],
                                'customFieldType' => CustomFieldTypes::TEXT
                            ]
                        ],
                        [
                            'name' => 'social_media_links_linkedin',
                            'label' => 'Linkedin link',
                            'type' => CustomFieldTypes::TEXT,
                            'config' => [
                                'label' => [
                                    'en-GB' => 'Linkedin link',
                                    'de-DE' => 'Linkedin link'
                                ],
                                'customFieldType' => CustomFieldTypes::TEXT
                            ]
                        ],
                        [
                            'name' => 'social_media_links_xing',
                            'label' => 'Xing link',
                            'type' => CustomFieldTypes::TEXT,
                            'config' => [
                                'label' => [
                                    'en-GB' => 'Xing link',
                                    'de-DE' => 'Xing link'
                                ],
                                'customFieldType' => CustomFieldTypes::TEXT
                            ]
                        ],
                    ],
                    'relations' => [
                        [
                            'entityName' => 'sales_channel',
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
