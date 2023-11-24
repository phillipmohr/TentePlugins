<?php declare(strict_types=1);

namespace Nwgncy\TenteTheme\Service;

use Cbax\ModulLexicon\Components\LexiconSeo;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Framework\Routing\RequestTransformer;


class LexiconSeoDecorator extends LexiconSeo
{
    public function __construct(
        private readonly ?SystemConfigService $systemConfigService,
        private readonly ?EntityRepository    $seoUrlRepository,
        private readonly ?EntityRepository    $salesChannelRepository,
        private readonly ?EntityRepository    $entityRepository,
        private readonly ?Slugify             $slugify,
        private readonly ?LoggerInterface     $logger,
        private readonly ?EntityRepository    $logEntryRepository,
        private readonly ?TranslatorInterface $translator,
        private readonly ?EntityRepository    $languageRepository,
        private readonly ?Connection          $connection,
        private readonly ?LexiconSeo          $decoratedService
    )
    {
    }

    public function getDecorated(): LexiconSeo
    {
        return $this->decoratedService;
    }

    public function getSeoUrls(string $route_name, Context $context, SalesChannelContext $salesChannelContext): array
    {

        $salesChannelId = $salesChannelContext->getSalesChannelId();
        $languageId     = $salesChannelContext->getLanguageId();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('languageId', $languageId));
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
        $criteria->addFilter(new EqualsFilter('routeName', $route_name));
        $criteria->addFilter(new EqualsFilter('isDeleted', 0));
        $criteria->addFilter(new EqualsFilter('isCanonical', 1));
        $seoUrls    = $this->seoUrlRepository->search($criteria, $context)->getElements();
        $allSeoUrls = [];

        foreach ($seoUrls as $seoUrl) {
            $allSeoUrls[$seoUrl->get('pathInfo')] = $seoUrl->get('seoPathInfo');
        }

        return $allSeoUrls;

    }

    public function getSeoUrl(string $route_name, string $path_info, Context $context, Request $request): string
    {
        $salesChannelContext = $request->attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT);
        $shop_url            = $request->attributes->get(RequestTransformer::STOREFRONT_URL);
        $salesChannelId      = $salesChannelContext->getSalesChannelId();
        $languageId          = $salesChannelContext->getLanguageId();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('languageId', $languageId));
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
        $criteria->addFilter(new EqualsFilter('routeName', $route_name));
        $criteria->addFilter(new EqualsFilter('pathInfo', $path_info));
        $criteria->addFilter(new EqualsFilter('isDeleted', 0));
        $criteria->addFilter(new EqualsFilter('isCanonical', 1));

        $seoUrl = $this->seoUrlRepository->search($criteria, $context)->first();
        if (!empty($seoUrl)) {
            return $shop_url . '/' . $seoUrl->get('seoPathInfo');
        } else {
            return $shop_url . $path_info;
        }
    }

    public function deleteSeoUrls(): array
    {
        return $this->getDecorated()->deleteSeoUrls();
    }

    public function generateSeoUrls(Context $context, string $adminLocalLanguage = 'en-GB'): array
    {
        return $this->getDecorated()->generateSeoUrls($context, $adminLocalLanguage);
    }

    public function tryGetSnippet(string $snippetName, ?string $locale = null, ?string $fallbackSnippet = null, string $defaultLocale = "en-GB"): string
    {
        return $this->getDecorated()->tryGetSnippet($snippetName, $locale, $fallbackSnippet, $defaultLocale);
    }

    public function createSeoUrls(Context $context, ?string $locale): array
    {
        return $this->getDecorated()->createSeoUrls($context, $locale);
    }

    public function getMessageText(string $snippetPath, ?string $locale): string
    {
        return $this->getDecorated()->getMessageText($snippetPath, $locale);
    }

    public function sCleanupPath(string $path): string
    {
        return $this->getDecorated()->sCleanupPath($path);
    }

}

