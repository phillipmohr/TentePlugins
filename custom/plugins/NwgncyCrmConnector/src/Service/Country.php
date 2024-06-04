<?php declare(strict_types=1);


namespace Nwgncy\CrmConnector\Service;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\Filesystem\Filesystem;
use Shopware\Core\Defaults;

class Country
{

    protected $countryRepository;

    public function __construct(
        EntityRepository $countryRepository
    )
    {
        $this->countryRepository = $countryRepository;
    }

    public function getCountryIsoCodeByName($context, $languageId, $countryName)
    {
        $criteria = (new Criteria())
            ->addAssociation('translations');
           
        $criteria->addFilter(new EqualsFilter('translations.language.id', $languageId));
        $criteria->addFilter(new EqualsFilter('translations.name', $countryName));

        $result =  $this->countryRepository->search($criteria, $context);

        if ($result->getTotal() > 0) {
            return $result->first()->getIso();
        }
        return $this->searchByDefaultLanguageId($context, $countryName);
    }
 
    public function searchByDefaultLanguageId($context, $countryName)
    {
        $criteria = (new Criteria())
            ->addAssociation('translations');
           
        $criteria->addFilter(new EqualsFilter('translations.language.id', Defaults::LANGUAGE_SYSTEM));
        $criteria->addFilter(new EqualsFilter('translations.name', $countryName));

        $result =  $this->countryRepository->search($criteria, $context);

        if ($result->getTotal() > 0) {
            return $result->first()->getIso();
        }

        return '';
    }
} 
