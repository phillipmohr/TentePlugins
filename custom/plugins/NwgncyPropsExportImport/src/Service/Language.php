<?php declare(strict_types=1);

namespace NwgncyPropsExportImport\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;

class Language
{

    protected $languageRepository;

    public function __construct(
        EntityRepository $languageRepository
    )
    {
        $this->languageRepository = $languageRepository;
    }

    public function getAllLanguages($context)
    {
        $criteria = new Criteria();
        return $this->languageRepository->search($criteria, $context);
    }
 
    public function getPropsTranslations($context)
    {
         $criteria = (new Criteria())
            ->addAssociation('propertyGroupTranslations')
            ->addAssociation('propertyGroupOptionTranslations');

        return $this->languageRepository->search($criteria, $context);
    }
 
    public function getLanguagesIdName($context)
    {
        $criteria = new Criteria();
        $languages = $this->languageRepository->search($criteria, $context);

        $idNameArr = [];

        foreach ($languages->getElements() as $language) {
            $idNameArr[$language->getId()] = $language->getName();
        }
        return $idNameArr;
    }
} 
