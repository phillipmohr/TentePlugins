<?php declare(strict_types=1);

namespace NwgncyPropsExportImport\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use NwgncyPropsExportImport\Service\Property;
use NwgncyPropsExportImport\Service\Language;
use Shopware\Core\Framework\Context;

class ExportPropertyGroupTranslations extends Command
{



    // Command name
    protected static $defaultName = 'property-group-translations:export';
    // Provides a description, printed out in bin/console
    protected function configure(): void
    {
        $this->setDescription('Exports Property Group Translations.');
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

        $io = new ShopwareStyle($input, $output);
        $io->info('Starting Export');

        $context = Context::createDefaultContext();

        $propertyHelper = $this->property;
        $languageHelper = $this->language;

        $systemLanguagesIdName = $languageHelper->getLanguagesIdName($context);
        $systemLanguagesNameId = array_flip($systemLanguagesIdName);

        $propsGroup = $propertyHelper->getAllPropertyGroups($context);
         $groupElements = $propsGroup->getElements();
       
        $totalGroupElements = count($groupElements);

        $io->info('Found ' . (string)$totalGroupElements . ' groups');

        $dataTemplate = [];
        foreach ($systemLanguagesNameId as $name => $id) {
            if ($name == 'English') {
                continue;
            }
            $dataTemplate[$name] = '';
        }

        $dataTemplate = ['English' => ''] + $dataTemplate;

        $propGroupExportData = [];

        foreach ($groupElements as $groupElement) {
            
            $groupName = $groupElement->getName();
            $groupId = $groupElement->getId();
            $dataTemplate = array('Group Id' => $groupId) + $dataTemplate;

            $groupTranslation = $dataTemplate;

            $translations = $groupElement->getTranslations()->getElements();
            foreach ($translations as $translation) {

                $name = $translation->getName();

                if (is_null($name)) {
                    $name = $groupName;
                }
                
                $languageName = $systemLanguagesIdName[$translation->getLanguageId()];
                $groupTranslation[$languageName] = $name;
            }
            $propGroupExportData[] = $groupTranslation;

        }
 
        $srcDir = dirname(__DIR__, 1);
        $publicDir = $srcDir . '/Resources/public';
        $filePath = $publicDir . '/Property_Groups_Translations.csv';

        $file = fopen($filePath, 'wa+');

        flock($file, LOCK_EX);

        fputcsv($file, array_keys($propGroupExportData[0]), '|');

        foreach ($propGroupExportData as $row) {
            fputcsv($file, $row, '|');
        }

        flock($file, LOCK_UN);

        fclose($file);

        $io->success('Export done');

        return 0;
    }
}
