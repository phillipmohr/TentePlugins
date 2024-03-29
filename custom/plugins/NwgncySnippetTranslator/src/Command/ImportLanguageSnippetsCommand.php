<?php declare(strict_types=1);

namespace SnippetsImport\Command;
use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteException;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SnippetsImport\Service\Translation;
use SnippetsImport\Service\FilesAndFolders;
use Symfony\Component\Filesystem\Filesystem;

class ImportLanguageSnippetsCommand extends Command
{

    public function __construct(
        Translation $translation,
        FilesAndFolders $filesAndFolders
    ) {
        $this->translation = $translation;
        $this->filesAndFolders = $filesAndFolders;

        parent::__construct(self::$defaultName);
    }

    // Command name
    protected static $defaultName = 'translation-snippets:import';

    // Provides a description, printed out in bin/console
    protected function configure(): void
    {
        $this->setDescription('Import Translation Snippets from CSV Files');
    }

    // Actual code executed in the command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ShopwareStyle($input, $output);

        $context = Context::createDefaultContext();

        $files = $this->filesAndFolders->getFilesFromPublicFolder('snippets');

        $translationHelper = $this->translation;
        $writerHelper = $this->filesAndFolders;

        $languages = $translationHelper->getStoreLanguagesNameId($context); 
        $languagesNameId = array_flip($languages);

        $fileSystemPublic = $writerHelper->getShopwareFileSystemPublic();

        $files = $fileSystemPublic->listContents('bundles/nwgncysnippettranslator', true);

        $result = (string)$fileSystemPublic->has('bundles/nwgncysnippettranslator');
        $io->text($result);

        $rootFiles = $fileSystemPublic->listContents('/bundles', true);

        $rootFilesArr = $rootFiles->toArray();

        $foundRootFiles = (string)count($rootFilesArr);
        $io->text('Found files in root: ' . $foundRootFiles);
        

        $currentDir = dirname(__FILE__);
        $translationFilesFolder = dirname(__FILE__) . '/snippets';

        $io->text($translationFilesFolder);
        $currentSnippetList = $translationHelper->getSnippetsForImport($context);
        $countUpdated = 0;
        $countNew = 0;

        if (is_dir($translationFilesFolder)) {
            $items = glob($translationFilesFolder . '/*');
            foreach ($items as $item) {
                if (is_file($item)) {

                    $file = fopen($item,"r");

                    $line = 0;
                    while ($data = fgetcsv($file, null, '|')) {

                        if ($line == 0) {

                            $languagePackName = $data[0];

                            if (isset($languagesNameId[$languagePackName])) {
                                $io->text('Found Packagename: ' . $languagePackName);
                            } else {
                                $io->error('No language pack found for: ' . $languagePackName);
                                exit;
                            }
                            // $io->text($languagePackName);
                            // break;
                            $languagePackId = $languagesNameId[$languagePackName];
                            $snippets = $currentSnippetList[$languagePackId];
                            $line++;
                            continue;
    
                        }
                        $translationKey = $data[0];
                        $translation = rtrim($data[1]);

                        $translation = str_replace("'", '"', $translation);

                        $currentData = $snippets[$translationKey];

                        $author = $currentData['author'];
                        $snippetId = $currentData['id'];
                        $currentTranslation = $currentData['value'];

                        if ($currentTranslation == $translation) {
                            // $io->text('Same value new: ' . $translation);
                            // $io->text('Same value current: ' . $currentTranslation);
                            continue;
                        }

                        if (empty($author)) {
                            $author = 'System';
                        }
    
                        $io->text($languagePackName . ' Importing translation for key: ' . $translationKey . ' Translation: ' . $translation);
                        // $translation = str_replace('target="blank"', 'target="_blank"', $translation);

                        if (empty($snippetId)) {
                            $countNew++;
                            $translationHelper->createSnippet($languagePackId, $translationKey, $translation, $author, $context);
                        } else {
                            // $io->text('Current: ' . $currentTranslation);
                            // $io->text('Updated: ' . $languagePackName . ' Translationkey ' . $translationKey . ' Translation: ' . $translation . ' ID' . $snippetId);
                            // $io->text('Updated: ' . $translation);
                            
                            $countUpdated++;
                            $translationHelper->updateSnippet($snippetId, $languagePackId, $translationKey, $translation, $author, $context);
                        }


                    }
                }
            }
        }

        $io->success('Imported into DB: ' . $countNew);
        $io->success('Updated: ' . $countUpdated);
        return 0;
    }
}
