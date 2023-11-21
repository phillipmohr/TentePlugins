<?php
declare(strict_types=1);

namespace BelVGCustomFields\Utils;

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
    public const CUSTOM_FIELDS_FOR_SALES_CHANNEL_CONTACT = 'custom_fields_for_sales_channel_contact';
    /**
     *
     */
    public const CUSTOM_FIELDS_FOR_SALES_CHANNEL_SHORT_CONTACT = 'custom_fields_for_sales_channel_short_contact';
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
        $configContact = [
            'componentName' => 'sw-text-editor',
            'customFieldType' => 'textEditor',
            'customFieldPosition' => 1,
            'validation' => 'required',
            'label' => [
                'de-DE' => 'Kontakt',
                'en-GB' => 'contact'
            ],
        ];

        $configShortContact = [
            'componentName' => 'sw-text-editor',
            'customFieldType' => 'textEditor',
            'customFieldPosition' => 1,
            'validation' => 'required',
            'label' => [
                'de-DE' => 'kurzer Kontakt',
                'en-GB' => 'short contact'
            ],
        ];

        $return[] = [
            'id' => md5(self::CUSTOM_FIELDS_FOR_SALES_CHANNEL_CONTACT),
            'name' => self::CUSTOM_FIELDS_FOR_SALES_CHANNEL_CONTACT,
            'type' => CustomFieldTypes::HTML,
            'customFieldSetId' => md5(self::CUSTOM_FIELDS_FOR_SALES_CHANNEL),
            'config' => $configContact
        ];

        $return[] = [
            'id' => md5(self::CUSTOM_FIELDS_FOR_SALES_CHANNEL_SHORT_CONTACT),
            'name' => self::CUSTOM_FIELDS_FOR_SALES_CHANNEL_SHORT_CONTACT,
            'type' => CustomFieldTypes::HTML,
            'customFieldSetId' => md5(self::CUSTOM_FIELDS_FOR_SALES_CHANNEL),
            'config' => $configShortContact
        ];
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