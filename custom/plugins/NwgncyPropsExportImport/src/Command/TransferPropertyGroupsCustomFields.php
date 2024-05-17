<?php declare(strict_types=1);

namespace NwgncyPropsExportImport\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use NwgncyPropsExportImport\Service\Property;
use NwgncyPropsExportImport\Service\Language;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Input\InputArgument;

class TransferPropertyGroupsCustomFields extends Command
{
    protected $io;

    protected $property;

    protected $language;

    protected $languageIds;

    // Command name
    protected static $defaultName = 'property-group-custom-fields:transfer';
    // Provides a description, printed out in bin/console
    protected function configure(): void
    {
        $this->setDescription('Transfers property group custom field data.');
        // $this->addArgument('customFieldName', InputArgument::REQUIRED, 'The field name to transfer from');
    }
    
    public function __construct(
        Property $property,
        Language $language
    ) {
        $this->property = $property;
        $this->language = $language;

        parent::__construct(self::$defaultName);
    }

    // Actual code executed in the command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->io = new ShopwareStyle($input, $output);
        $this->io->info('Starting Custom Field Value Transfer');

        $context = Context::createDefaultContext();

        $languagesIdName = $this->language->getLanguagesIdName($context); 
        $languagesNameId = array_flip($languagesIdName);

        $this->setLanguageIds($context);
              

        $defaultLanguageId = $languagesNameId['English'];

        $propertyGroups = $this->property->getAllPropertyGroups($context);

        foreach ($propertyGroups as $propertyGroup) {

            $propertyGroupId = $propertyGroup->getId();
            $translations = $propertyGroup->getTranslations();

            foreach ($translations as $translation) {
                $languageId = $translation->getLanguageId();
                
                if ($languageId == $defaultLanguageId) {

                    $customFields = $translation->getCustomFields();
                   
                    if (!empty($customFields['custom_technical_data_type'])) {
                        $this->transferCustomFieldData($context, $translations, $propertyGroupId, 'custom_technical_data_type', $customFields['custom_technical_data_type'], $defaultLanguageId);
                    }
                }

            }
        }

        $this->io->success('Transfer successfull');
        return 0;
    }

    public function transferCustomFieldData($context, $translations, $propertyGroupId, $customFieldName, $customFieldValue, $languageIdToSkip) {

        $translationsArr = [];
        foreach ($translations as $translation) {
            
            $languageId = $translation->getLanguageId();

            if ($languageId == $languageIdToSkip) {
                continue;
            }

            $customFields = $translation->getCustomFields();

            $customFields[$customFieldName] = $customFieldValue;

            $translationsArr[$languageId] = [
                'customFields' => $customFields
            ];
            // $testPropertyId = '018a69c4214d776da107a46799894604';
            // $testLanguageId = '018a69406dfc703c89d2e685ae3df462';
            // if ($testPropertyId == $propertyGroupId && $testLanguageId == $languageId) {
            //     $result = $this->property->updatePropertyGroupTranslationByGroupId($context, $propertyGroupId, $translationsArr);
            // }

        }

        $result = $this->property->updatePropertyGroupTranslationByGroupId($context, $propertyGroupId, $translationsArr);

    }

    public function setLanguageIds($context) {
        $this->languageIds = $this->language->getLanguageIds($context);
    }

    public function getLanguageIds() {
        return $this->languageIds;
    }
}
