<?php declare(strict_types=1);

namespace NwgncyPropsExportImport\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use NwgncyPropsExportImport\Service\Property;
use NwgncyPropsExportImport\Service\Language;
use Shopware\Core\Framework\Context;

class ImportPropertyGroupTranslations extends Command
{
    protected $io;
    protected $property;
    protected $language;
    // Command name
    protected static $defaultName = 'property-group-translations:import';
    // Provides a description, printed out in bin/console
    protected function configure(): void
    {
        $this->setDescription('Imports Property Group Translations.');
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
        $this->io->info('Starting Import');

        $context = Context::createDefaultContext();

        $file = $this->getCsvImportFile();

        if  ($file === false) {
            return 0;
        } 

        $languagesIdName = $this->language->getLanguagesIdName($context); 
        $languagesNameId = array_flip($languagesIdName);

        $line = 0;
        $languageHeaderArr = [];
        while ($data = fgetcsv($file, null, ';')) {

            if ($line == 0) {
                $languageHeaderArr = $data;
                $line++;
                continue;
            }

            $groupId = $data[0];

            $translations = [];
            for ($i = 1; $i <= count($languageHeaderArr) - 1; $i++) {

                $languageName = $languageHeaderArr[$i];

                if (!isset($languagesNameId[$languageName])) {
                    $this->io->error('Language ' . $languageName . ' not found in system languages');
                    return 0;
                }

                $languageId = $languagesNameId[$languageName];

                $propGroupTranslation = trim($data[$i]);

                if (!empty($propGroupTranslation)) {
                    $translations[$languageId] = $propGroupTranslation;
                }

            }
 
            $translationsArr = [];

            foreach ($translations as $languageId => $translation) {
                $translationsArr[$languageId] = [
                    'name' => $translation
                ];
            }

            $result = $this->property->updatePropertyGroupTranslationByGroupId($context, $groupId, $translationsArr);

            if (count($result->getErrors()) > 0) { 
                $this->io->error('Error found');
                exit;
            }

            $line++;

        }
        $this->io->success('Import finished');
        return 0;
    }

    public function getCsvImportFile() {

        $srcDir = dirname(__DIR__, 1);
        $importFilesFolder = $srcDir . '/Resources/public/import';

        if (is_dir($importFilesFolder)) {

            $importFile = $importFilesFolder . '/Property_Groups_Translations_full.csv';
            if (is_file($importFile)) {

                return fopen($importFile, 'r');
            
            } else {
                $this->io->error('No import file called Property_Groups_Translations_full.csv found in' . $importFilesFolder);
                return false;
            }

        } else {
            $this->io->error('Folder not found in: ' . $importFilesFolder);
            return false;
        }
    }
}
