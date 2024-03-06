<?php declare(strict_types=1);

namespace SnippetsImport\Service;

use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\Snippet\SnippetService;
use Shopware\Core\Framework\Adapter\Translation\Translator;
use Symfony\Component\Filesystem\Filesystem as Logger;

class Translation
{
    /**
     * @var Filesystem
     */
    private $snippetService;

    private $translator;

    private $snippetSetRepository;


    public function __construct(
        SnippetService $snippetService,
        Translator $translator,
        EntityRepository $snippetSetRepository,
        EntityRepository $snippetRepository
    )
    {
        $this->snippetService = $snippetService;
        $this->translator = $translator;
        $this->snippetSetRepository = $snippetSetRepository;
        $this->snippetRepository = $snippetRepository;
    }



    public function createSnippet($setId, $translationKey, $value, $author, $context)
    {

        $id = Uuid::randomHex();
        $data = [
            'id' => $id,
            'setId' => $setId,
            'translationKey' => $translationKey,
            'value' => $value,
            'author' => $author,
        ];
        $this->snippetRepository->create([$data], $context);
    }


    public function updateSnippet($id, $setId, $translationKey, $value, $author, $context)
    {
        $data = [
            'id' => $id,
            'setId' => $setId,
            'translationKey' => $translationKey,
            'value' => $value,
            'author' => $author,
        ];

        $this->snippetRepository->update([$data], $context);
    }


    public function getSnippetsBySetId($setId, $context)
    {

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('setId', $setId));

        return $this->snippetRepository->search($criteria, $context);
    }

    public function getSnippetsForImport(Context $context)
    {
        $sort = [
            'sortBy' => 'id',
            'sortDirection' => 'ASC'
        ];

        $list = $this->getList(1, 20000, $context, [], $sort);

        $fullSnippetsList = [];

        foreach ($list['data'] as $value) {
            foreach ($value as $data) {
                $fullSnippetsList[$data['setId']][$data['translationKey']] = $data;
            }
        }

        return $fullSnippetsList;
    }

    public function getAllLanguages($context)
    {
        return $this->snippetSetRepository->search($criteria = new Criteria(), $context);
    }



    public function getSnippets(Context $context)
    {

        $languages = $this->getStoreLanguagesNameId($context); 

        // \Kint::dump($languages);
        // exit;
        // $localId = $this->getSnippetSetId('de-DE');
        // $catalogues = $this->getCatalogues();
        // $catalogue = $this->getCatalogue('en-US');
        
        $sort = [
            'sortBy' => 'id',
            'sortDirection' => 'ASC'
        ];

        $list = $this->getList(1, 20000, $context, [], $sort);

        // $languages = $this->getLanguages();
        // \Kint::dump($languages);
        // $enUsId = $languages['en-US'];


        $fullSnippetsList = [];

        foreach ($list['data'] as $value) {

            foreach ($value as $data) {
                // \Kint::dump($data);

                // if (empty($languages[$data['setId']])) {
                    // \Kint::dump($data);
                    // exit;
                // }

                $languageName = $languages[$data['setId']];
                // \Kint::dump($languageName);
                // exit;
                $fullSnippetsList[$languageName][] = [$data['translationKey'], $data['value']];

                // if ($data['setId'] == $enUsId) {
                //     $fullSnippetsList[$data['translationKey']] = $data;
                // }



            }
        }
        // $fsObject = new Logger();
        // $filePath = '/var/www/html/tentecom/public/test4.html';
        // $fsObject->touch($filePath);
        // $fsObject->chmod($filePath, 0777);
        //  $fsObject->appendToFile($filePath, @\Kint::dump($fullSnippetsList));
        // \Kint::dump($localId);
        // \Kint::dump($catalogues);
        // \Kint::dump($catalogue);
        return $fullSnippetsList;
        // \Kint::dump($fullSnippetsList['LanguagePack en-US']);
        // exit;
        // getStorefrontSnippets(MessageCatalogueInterface $catalog, string $snippetSetId, ?string $fallbackLocale = null, ?string $salesChannelId = null): array

        // $this->snippetService->getStorefrontSnippets();
        // return false;
    }

    // public function saveLanguageSnippetsToCSV($data)
    // {
    //     return $this->snippetService->getRegionFilterItems($context);
    // }


    public function getStoreLanguagesNameId($context)
    {

        $storeLanguages = $this->getAllLanguages($context);

        $languages = [];
        $langElems = $storeLanguages->getElements();

        foreach ($storeLanguages->getElements() as $language) {
            $languages[$language->getId()] = $language->getName();
        }

        return $languages;
    }

    public function getRegionFilterItems($context)
    {
        return $this->snippetService->getRegionFilterItems($context);
    }

    public function getSnippetSetId($locale)
    {
        return $this->translator->getSnippetSetId($locale);
    }

    public function getLanguages()
    {
 
        $isoSetIdArr = [];
        $isos = $this->getTenteIsos();

        foreach ($isos as $iso) {
            $isoId = $this->getSnippetSetId($iso);
            $isoSetIdArr[$isoId] = $iso;
            // $isoSetIdArr[$iso] = $isoId;
        }
        // \Kint::dump($isoSetIdArr);
        // exit;
        return $isoSetIdArr;
    }

    public function getTenteIsos()
    {
        return ['de-DE',
                'en-GB',
                'bs-BA',
                'bg-BG',
                'cs-CZ',
                'da-DK',
                'el-GR',
                'en-US',
                'es-ES',
                'fi-FI',
                'fr-FR',
                'hi-IN',
                'hr-HR',
                'hu-HU',
                'id-ID',
                'it-IT',
                'ko-KR',
                'lv-LV',
                'nl-NL',
                'nn-NO',
                'pl-PL',
                'pt-PT',
                'ro-RO',
                'ru-RU',
                'sk-SK',
                'sl-SI',
                'sr-RS',
                'sv-SE',
                'tr-TR',
                'uk-UA',
                'vi-VN',
                'zh-CN',
                'en-GB',
                'es-ES',
                'en-GB',
                'fr-FR',
                'nl-NL'
            ];
    }

    public function getCatalogues()
    {
        return $this->translator->getCatalogues();
    }

    public function getCatalogue(?string $locale = null)
    {
        return $this->translator->getCatalogue($locale);
    }

    public function getList(int $page, int $limit, Context $context, array $requestFilters, array $sort): array
    {
        return $this->snippetService->getList($page,$limit,$context,$requestFilters,$sort);
    }

}