<?php

declare(strict_types=1);

namespace NwgncyTenteOptimizer\Service;

use Shopware\Core\Defaults;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Zeobv\StoreSwitcher\Service\LocationInfoService\LocationInfoServiceFactory;
use Zeobv\StoreSwitcher\Service\LocationInfoService\LocationInfoServiceInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Zeobv\StoreSwitcher\Service\ConfigService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainEntity;

/**
 * Class LocationService
 *
 * @package Zeobv\StoreSwitcher\Service
 */
class LocationService
{
    /**
     * @var LocationInfoServiceFactory
     */
    protected LocationInfoServiceFactory $locationInfoServiceFactory;
    protected EntityRepository $domainRepository;
    protected EntityRepository $languageRepository;
    protected EntityRepository $localeRepository;
    /**
     * @var ConfigService
     */
    protected ConfigService $configService;

    public function __construct(
        LocationInfoServiceFactory $locationInfoServiceFactory,
        ConfigService $configService,
        EntityRepository $domainRepository,
        EntityRepository $languageRepository,
        EntityRepository $localeRepository
    ) {
        $this->locationInfoServiceFactory = $locationInfoServiceFactory;
        $this->configService = $configService;
        $this->domainRepository = $domainRepository;
        $this->languageRepository = $languageRepository;
        $this->localeRepository = $localeRepository;
    }

    /**
     * @param Request             $request
     * @param SalesChannelContext $salesChannelContext
     *
     * @return string|null
     */
    public function determineCountryCodeFromRequest(Request $request, SalesChannelContext $salesChannelContext): ?string
    {
        $session = $request->getSession();



        if (
            empty($this->configService->getDebugIp($salesChannelContext->getSalesChannel())) &&
            $locationData = $session->get(LocationInfoServiceInterface::CACHE_KEY) and $locationData->getCountryCode()
        ) {
            return $locationData->getCountryCode();
        }

        $ipService = $this->configService->getIpInfoService($salesChannelContext->getSalesChannel());

        if (is_null($ipService)) {
            return null;
        }

        /** @var LocationInfoServiceInterface|null $locationInfoService */
        $locationInfoService = $this->locationInfoServiceFactory->create($ipService);

        if (is_null($locationInfoService)) {
            return null;
        }

        $locationData = $locationInfoService->getLocationInfoFromIp(
            $this->getIpFromRequest($request, $salesChannelContext),
            $this->configService->getAccessKey($salesChannelContext->getSalesChannel())
        );

        if (!$locationData->getException()) {
            $session->set(LocationInfoServiceInterface::CACHE_KEY, $locationData);
        } else {
            $session->set(LocationInfoServiceInterface::CACHE_KEY, $locationData->clearException());
        }

        return $locationData->getCountryCode();
    }

    /**
     * @param Request             $request
     * @param SalesChannelContext $context
     *
     * @return string
     */
    private function getIpFromRequest(Request $request, SalesChannelContext $context): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $clientIp = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $clientIp = $_SERVER['REMOTE_ADDR'];
        }
        $ipStart = substr($clientIp, 0, 3);

        if ($this->configService->getDebugMode($context->getSalesChannel())) {
            $debugIp = $this->configService->getDebugIp($context->getSalesChannel());

            return $debugIp ?? $clientIp;
        }

        return $clientIp;
    }


    public function getSalesChannelsDomains(string $salesChannelId, Context $context): EntitySearchResult
    {
        $criteria = new Criteria();
        $criteria->addAssociations(
            [
                'language.locale',
                'language.locale.translations',
                'language.translationCode',
                'salesChannel',
            ],
        );
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));

        return $this->domainRepository->search($criteria, $context);
    }


    public function getDefaultTargetDomain(string $baseUrl, string $countryCode = 'DE', Context $context): ?SalesChannelDomainEntity
    {
        // dd($countryCode);
        $languageCode = $this->getPreferredLanguage();
        $url = $baseUrl . $languageCode . '-' . strtolower($countryCode);
        
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('url', $url));
        $criteria->addFilter(new EqualsFilter('salesChannel.active', true));
        $criteria->addFilter(new EqualsFilter('salesChannel.type.id', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));
        
        /** @var ?SalesChannelDomainEntity $result */
        $result = $this->domainRepository->search($criteria, $context)->first();
        if($result) {
            return $result;

        } else {

            $urlAlternative = $baseUrl . $languageCode;
            
            $criteria = new Criteria();
            $criteria->addFilter(new ContainsFilter('url', $urlAlternative));
            $criteria->addFilter(new EqualsFilter('salesChannel.active', true));
            $criteria->addFilter(new EqualsFilter('salesChannel.type.id', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));
            
            /** @var ?SalesChannelDomainEntity $result */
            $result = $this->domainRepository->search($criteria, $context)->first();

            if($result) {

                return $result;

            } else {

                $defaultUrl = $baseUrl . 'de-DE';
                
                $criteria = new Criteria();
                $criteria->addFilter(new EqualsFilter('url', $defaultUrl));
                $criteria->addFilter(new EqualsFilter('salesChannel.active', true));
                $criteria->addFilter(new EqualsFilter('salesChannel.type.id', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));
                
                /** @var ?SalesChannelDomainEntity $result */
                $result = $this->domainRepository->search($criteria, $context)->first();

                return $result;

            }
            
        }

    }

    public function getLanguageById(string $languageId, Context $context): ?LanguageEntity
    {
        $criteria = new Criteria([$languageId]);
        $criteria->addAssociation('locale');

        /** @var ?LanguageEntity $result */
        $result = $this->languageRepository->search($criteria, $context)->first();

        return $result;
    }

    public function getPreferredLanguage()
    {

        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

        $languages = [];

        // Split the string by commas
        $langs = explode(',', $acceptLanguage);

        foreach ($langs as $lang) {
            // Split by semicolon to get the language and optional quality value
            $lang = explode(';', $lang);
            $languages[] = $lang[0];
        }

        if (!empty($languages[1])) {
            return $languages[1];
        } elseif (!empty($languages[0])) {
            return $languages[0];
        } else {
            return 'en';
        }
        

    }
    public function getCountryFromAcceptLanguage()
    {
        // Check if the HTTP_ACCEPT_LANGUAGE is set
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }

        // Get the HTTP_ACCEPT_LANGUAGE header
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

        // Split the string by comma to get the language preferences
        $languagePreferences = explode(',', $acceptLanguage);

        // Loop through the language preferences
        foreach ($languagePreferences as $language) {
            // Split each preference by semicolon (for cases where quality factors are present)
            $languageParts = explode(';', $language);

            // The first part is the language code with optional region (e.g., en-US)
            $locale = $languageParts[0];

            // Split the locale by hyphen or underscore to separate the language and region
            if (strpos($locale, '-') !== false) {
                $localeParts = explode('-', $locale);
            } elseif (strpos($locale, '_') !== false) {
                $localeParts = explode('_', $locale);
            } else {
                $localeParts = [$locale];
            }

            // If a region code is present, it will be the second part
            if (isset($localeParts[1])) {
                $country = strtoupper($localeParts[1]);
                return $country;
            }
        }

        // If no country code was found, return null
        return null;
    }
}
