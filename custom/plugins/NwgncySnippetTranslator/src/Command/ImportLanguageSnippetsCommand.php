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

        if (!empty($rootFilesArr)) {

            $stopReading = 0;
            $stopReadingAfter = 10;
            foreach ($rootFilesArr as $file) {
                
                $data = $file->jsonSerialize();

                $type = $data['type'];
                // $io->text( $type);
                if ($type == 'dir') {


                    $needle   = 'are';
                    $path = $file->jsonSerialize()['path'];
                    if (strpos($path, 'nwgncy') !== false) {
                        $io->text( $file->jsonSerialize()['path']);
                    }

                    
                }

                if ($stopReading == $stopReadingAfter) {
                    // break;
                }
                $stopReading++;
                // if ($file->isDir()) {

                //     $io->text($file->path());

                //     $stopReading++;
                // }
                // if ($stopReading == $stopReadingAfter) {
                //     break;
                // }

            }
        }

        exit;
        




        $filesArr = $files->toArray();

        $currentSnippetList = $translationHelper->getSnippetsForImport($context);

        $countUpdated = 0;
        $countNew = 0;
        $foundFiles = (string)count($filesArr);
        $io->text('Found files: ' . $foundFiles);

        if (!empty($filesArr)) {
            $stopReading = 0;
            $stopReadingAfter = 30;
            foreach ($filesArr as $file) {
                $data = $file->jsonSerialize();

                $io->text($data['type']);
                $io->text($data['path']);
                if (!$file->isDir()) {
                    $path = $data['path'];
                    try {
                        $fileStream = $fileSystemPublic->readStream($path);
                    } catch (\Exception $e) {
                        $io->text($e->getMessage());
                    }

                }
                if ($stopReading == $stopReadingAfter) {
                    break;
                }
                $stopReading++;
                // if (!$file->isDir()) {
                    
                //     $fileStream = $fileSystemPublic->readStream($file->path());
                //     $line = 0;
                //     while ($data = fgetcsv($fileStream, null, '|')) {

                //         if ($line == 0) {

                //             $languagePackName = $data[0];

                //             if (isset($languagesNameId[$languagePackName])) {
                //                 $io->text('Found Packagename: ' . $languagePackName);
                //             } else {
                //                 $io->error('No language pack found for: ' . $languagePackName);
                //                 exit;
                //             }
                //             // $io->text($languagePackName);
                //             // break;
                //             $languagePackId = $languagesNameId[$languagePackName];
                //             $snippets = $currentSnippetList[$languagePackId];
                //             $line++;
                //             continue;
     
                //         }
                //         $translationKey = $data[0];
                //         $translation = rtrim($data[1]);

                //         $translation = str_replace("'", '"', $translation);

                //         $currentData = $snippets[$translationKey];

                //         $author = $currentData['author'];
                //         $snippetId = $currentData['id'];
                //         $currentTranslation = $currentData['value'];

                //         if ($currentTranslation == $translation) {
                //             // $io->text('Same value new: ' . $translation);
                //             // $io->text('Same value current: ' . $currentTranslation);
                //             continue;
                //         }

                //         if (empty($author)) {
                //             $author = 'System';
                //         }
    
                //         $io->text($languagePackName . ' Importing translation for key: ' . $translationKey . ' Translation: ' . $translation);
                //         // $translation = str_replace('target="blank"', 'target="_blank"', $translation);

                //         if (empty($snippetId)) {
                //             $countNew++;
                //             $translationHelper->createSnippet($languagePackId, $translationKey, $translation, $author, $context);
                //         } else {
                //             // $io->text('Current: ' . $currentTranslation);
                //             // $io->text('Updated: ' . $languagePackName . ' Translationkey ' . $translationKey . ' Translation: ' . $translation . ' ID' . $snippetId);
                //             // $io->text('Updated: ' . $translation);
                            
                //             $countUpdated++;
                //             $translationHelper->updateSnippet($snippetId, $languagePackId, $translationKey, $translation, $author, $context);
                //         }


                //     }
 
                // }
 
            }
        }
        $io->success('Imported into DB: ' . $countNew);
        $io->success('Updated: ' . $countUpdated);
        return 0;
    }
}
