<?php
declare(strict_types=1);

namespace NwgncyCustomFields\Utils;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class CustomFieldInstaller
{
    /**
     *
     */
    public const CUSTOM_FIELDS_FOR_SALES_CHANNEL = 'custom_fields_for_sales_channel';
    /**
     *
     */
    public const CUSTOM_FIELDS_FOR_SALES_CHANNEL_ADDRESS = 'custom_fields_for_sales_channel_address';
    /**
     *
     */
    public const CUSTOM_FIELDS_FOR_SALES_CHANNEL_PHONE = 'custom_fields_for_sales_channel_phone';
    /**
     *
     */
    public const CUSTOM_FIELDS_FOR_SALES_CHANNEL_PHONE_LINK = 'custom_fields_for_sales_channel_phone_link';
    /**
     *
     */
    public const CUSTOM_FIELDS_FOR_SALES_CHANNEL_EMAIL = 'custom_fields_for_sales_channel_email';
    /**
     *
     */
    public const CUSTOM_FIELDS_FOR_SALES_CHANNEL_COMPANY_NAME = 'custom_fields_for_sales_channel_company_name';
    /**
     *
     */
    public const CUSTOM_FIELDS_FOR_SALES_CHANNEL_WORK_TIME = 'custom_fields_for_sales_channel_work_time';
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
            'id' => md5(self::CUSTOM_FIELDS_FOR_SALES_CHANNEL),
            'entityName' => 'sales_channel'
        ]];
        $config = [
            'label' => [
                'en-GB' => 'custom fields for the sales channel',
                'de-DE' => 'benutzerdefinierte Felder für den Vertriebskanal',
            ],
            'translated' => true
        ];
        return
            [
                [
                    'id' => md5(self::CUSTOM_FIELDS_FOR_SALES_CHANNEL),
                    'name' => self::CUSTOM_FIELDS_FOR_SALES_CHANNEL,
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
            'componentName' => 'sw-text-editor',
            'customFieldType' => 'textEditor',
            'customFieldPosition' => 1,
            'validation' => 'required',
            'label' => [
                'de-DE' => 'Anschrift',
                'en-GB' => 'Address'
            ],
        ];
        $return[] = [
            'id' => md5(self::CUSTOM_FIELDS_FOR_SALES_CHANNEL_ADDRESS),
            'name' => self::CUSTOM_FIELDS_FOR_SALES_CHANNEL_ADDRESS,
            'type' => CustomFieldTypes::HTML,
            'customFieldSetId' => md5(self::CUSTOM_FIELDS_FOR_SALES_CHANNEL),
            'config' => $configAddress
        ];

        $textField = [
            self::CUSTOM_FIELDS_FOR_SALES_CHANNEL_PHONE => ['de-DE' => 'Telefon', 'en-GB' => 'Phone'],
            self::CUSTOM_FIELDS_FOR_SALES_CHANNEL_PHONE_LINK => ['de-DE' => 'Telefonverbindung', 'en-GB' => 'Phone Link'],
            self::CUSTOM_FIELDS_FOR_SALES_CHANNEL_EMAIL => ['de-DE' => 'Email', 'en-GB' => 'Email'],
            self::CUSTOM_FIELDS_FOR_SALES_CHANNEL_COMPANY_NAME => ['de-DE' => 'Firmenname', 'en-GB' => 'Company name'],
            self::CUSTOM_FIELDS_FOR_SALES_CHANNEL_WORK_TIME => ['de-DE' => 'Arbeitszeit', 'en-GB' => 'Work time'],
        ];
        foreach ($textField as $name => $label) {
            $config = [
                'type' => 'text',
                'label' => $label,
                'placeholder' => $label,
                'componentName' => 'sw-field',
                'customFieldType' => 'text',
                'customFieldPosition' => 1,
            ];
            $return[] = [
                'id' => md5($name),
                'name' => $name,
                'type' => CustomFieldTypes::TEXT,
                'customFieldSetId' => md5(self::CUSTOM_FIELDS_FOR_SALES_CHANNEL),
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