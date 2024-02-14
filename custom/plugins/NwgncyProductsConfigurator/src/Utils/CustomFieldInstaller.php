<?php
declare(strict_types=1);

namespace Nwgncy\ProductsConfigurator\Utils;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Symfony\Component\DependencyInjection\ContainerInterface;


class CustomFieldInstaller
{
    public const CUSTOM_FIELDS_FOR_CATEGORY = 'nwgncy_main_product_category';

    public const CUSTOM_FIELDS_FOR_CATEGORY_IS_MAIN_PRODUCTS_CATEGORY = 'nwgncy_is_the_main_products_category';

    /**
     * @var EntityRepository|object|null
     */
    protected EntityRepository $customFieldRepository;
    /**
     * @var EntityRepository|object|null
     */
    protected EntityRepository $customFieldSetRepository;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->customFieldSetRepository = $container->get('custom_field_set.repository');
        $this->customFieldRepository = $container->get('custom_field.repository');
    }

    /**
     * @return array[]
     */
    protected function getFieldSet(): array
    {
        $relations = [[
            'id' => md5(self::CUSTOM_FIELDS_FOR_CATEGORY),
            'entityName' => 'category'
        ]];
        $config = [
            'label' => [
                'en-GB' => 'Is the main products category',
                'de-DE' => 'Ist die Hauptproduktkategorie',
            ],
            'translated' => false
        ];
        return
            [
                [
                    'id' => md5(self::CUSTOM_FIELDS_FOR_CATEGORY),
                    'name' => self::CUSTOM_FIELDS_FOR_CATEGORY,
                    'config' => $config,
                    'relations' => $relations
                ]
            ];
    }

    /**
     * @param Context $context
     * @return array
     */
    protected function getCustomFields(Context $context): array
    {
        $configAddress = [
            'componentName' => 'sw-switch-field',
            'customFieldType' => CustomFieldTypes::SWITCH,
            'customFieldPosition' => 1,
            'label' => [
                'de-DE' => 'Ist die Hauptproduktkategorie',
                'en-GB' => 'Is the main products category'
            ],
        ];
        $return[] = [
            'id' => md5(self::CUSTOM_FIELDS_FOR_CATEGORY_IS_MAIN_PRODUCTS_CATEGORY),
            'name' => self::CUSTOM_FIELDS_FOR_CATEGORY_IS_MAIN_PRODUCTS_CATEGORY,
            'type' => CustomFieldTypes::SWITCH,
            'customFieldSetId' => md5(self::CUSTOM_FIELDS_FOR_CATEGORY),
            'config' => $configAddress
        ];

        $textField = [
            self::CUSTOM_FIELDS_FOR_CATEGORY_IS_MAIN_PRODUCTS_CATEGORY => ['de-DE' => 'Ist die Hauptproduktkategorie', 'en-GB' => 'Is the main products category'],
        ];

        foreach ($textField as $name => $label) {
            $config = [
                'type' => 'text',
                'label' => $label,
                'placeholder' => $label,
                'componentName' => 'sw-switch-field',
                'customFieldType' => CustomFieldTypes::SWITCH,
                'customFieldPosition' => 1,
            ];
            $return[] = [
                'id' => md5($name),
                'name' => $name,
                'type' => CustomFieldTypes::SWITCH,
                'customFieldSetId' => md5(self::CUSTOM_FIELDS_FOR_CATEGORY),
                'config' => $config
            ];

        }
        return $return;
    }

    /**
     * @param ActivateContext $activateContext
     * @return void
     */
    public function activate(ActivateContext $activateContext): void
    {
        foreach ($this->getFieldSet() as $customFieldSet) {
            $this->upsertCustomFieldSet($customFieldSet, $activateContext->getContext());
        }
        foreach ($this->getCustomFields($activateContext->getContext()) as $customField) {
            $this->upsertCustomField($customField, $activateContext->getContext());
        }
    }

    /**
     * @param array $customFieldSet
     * @param Context $context
     * @param $activate
     * @return void
     */
    protected function upsertCustomFieldSet(array $customFieldSet, Context $context, $activate = true): void
    {
        $customFieldSet['active'] = $activate;
        $this->customFieldSetRepository->upsert([$customFieldSet], $context);
    }

    /**
     * @param array $customField
     * @param Context $context
     * @param $activate
     * @return void
     */
    protected function upsertCustomField(array $customField, Context $context, $activate = true): void
    {
        $customField['active'] = $activate;
        $this->customFieldRepository->upsert([$customField], $context);
    }
}