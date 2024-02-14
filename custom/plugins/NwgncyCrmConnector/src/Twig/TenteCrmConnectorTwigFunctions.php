<?php declare(strict_types=1);

namespace Nwgncy\CrmConnector\Twig;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Country\CountryCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TenteCrmConnectorTwigFunctions extends AbstractExtension
{
     private EntityRepository $countryRepository;

     public function __construct(EntityRepository $countryRepository) {
          $this->countryRepository = $countryRepository;
     }

     public function getFunctions()
     {
          return [
               new TwigFunction('getCountries', [$this, 'getCountries']),
          ];
     }

     public function getCountries(SalesChannelContext $salesChannelContext): CountryCollection
     {
          $context = $salesChannelContext->getContext();
          $criteria = new Criteria();
          $countriesResult = $this->countryRepository->search($criteria, $context)->getEntities();
          return $countriesResult;
     }
}
