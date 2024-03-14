<?php declare(strict_types=1);

namespace NwgncyZeobvSwitcherFix\Service;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

use Shopware\Core\Content\Seo\Hreflang\HreflangStruct;
use Shopware\Core\Content\Seo\HreflangLoaderParameter;
use Shopware\Core\Content\Seo\Hreflang\HreflangCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Content\Seo\SeoUrl\SeoUrlEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Zeobv\StoreSwitcher\Content\StoreSwitcherDomain\StoreSwitcherDomainEntity;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class HreflangService
 *
 * @package NwgncyZeobvSwitcherFix\Subscriber\HreflangService
 */
class HreflangService
{
    private RouterInterface $router;
    private EntityRepository $seoUrlRepository;
    private EntityRepository $salesChannelDomainRepository;
    private EntityRepository $storeSwitcherDomainRepository;
    private Connection $connection;

    public function __construct(
        RouterInterface $router,
        EntityRepository $seoUrlRepository,
        EntityRepository $salesChannelDomainRepository,
        EntityRepository $storeSwitcherDomainRepository,
        Connection $connection
    ) {
        $this->router = $router;
        $this->seoUrlRepository = $seoUrlRepository;
        $this->salesChannelDomainRepository = $salesChannelDomainRepository;
        $this->storeSwitcherDomainRepository = $storeSwitcherDomainRepository;
        $this->connection = $connection;
    }

    /**
     * @param HreflangLoaderParameter $parameter
     *
     * @return HreflangCollection
     */
    public function load(HreflangLoaderParameter $parameter): HreflangCollection
    {

       $salesChannelContext = $parameter->getSalesChannelContext();

        if (!$salesChannelContext->getSalesChannel()->isHreflangActive()) {
            return new HreflangCollection();
        }
        //$fsObject->appendToFile($filePath, @\Kint::dump(date('h:i:s')));
        if ($parameter->getRoute() === 'frontend.home.page') {
            return $this->generateHreflangHome($parameter->getSalesChannelContext());
        }

        // load time 21 seconds, Symfony\Bundle\FrameworkBundle\Routing\Router
        $pathInfo = $this->router->generate($parameter->getRoute(), $parameter->getRouteParameters(), RouterInterface::ABSOLUTE_PATH);
        $seoUrls = $this->getSEOUrls(
            $salesChannelContext->getContext(),
            $pathInfo,
            $parameter->getRouteParameters()['productId'] ?? null
        );
        $storeSwitcherDomains = $this->getSalesChannelSwitcherDomains($salesChannelContext);
        // We need at least two links
        if ($seoUrls->getTotal() < 1) {
            return new HreflangCollection();
        }
        $hreflangCollection = new HreflangCollection();
        /** @var SeoUrlEntity $seoUrl */
        foreach ($seoUrls as $seoUrl) {
            /** @var StoreSwitcherDomainEntity $storeSwitcherDomain */
            foreach ($storeSwitcherDomains as $storeSwitcherDomain) {
                if (
                    !$storeSwitcherDomain->getSalesChannelDomain()
                    || $seoUrl->getSalesChannelId() !== $storeSwitcherDomain->getSalesChannelDomain()->getSalesChannelId()
                    || $seoUrl->getLanguageId() !== $storeSwitcherDomain->getSalesChannelDomain()->getLanguageId()
                ) {
                    continue;
                }

                $storeSwitcherDomainUrl = $storeSwitcherDomain->getUrl();

                $seoUrl->setUrl($storeSwitcherDomainUrl . '/' . $seoUrl->getSeoPathInfo());

                if ($storeSwitcherDomain->getSalesChannelDomainId() === $salesChannelContext->getSalesChannel()->getHreflangDefaultDomainId()) {
                    $hrefLang = new HreflangStruct();
                    $hrefLang->setUrl($storeSwitcherDomainUrl . '/' . $seoUrl->getSeoPathInfo());
                    $hrefLang->setLocale('x-default');
                    $hreflangCollection->add($hrefLang);
                }

                $hrefLang = new HreflangStruct();
                $hrefLang->setUrl($storeSwitcherDomainUrl . '/' . $seoUrl->getSeoPathInfo());
                $locale = $seoUrl->getLanguage()->getLocale()->getCode();

                if ($storeSwitcherDomain->getSalesChannelDomain()->isHreflangUseOnlyLocale()) {
                    $locale = substr($locale, 0, 2);
                }

                $hrefLang->setLocale($locale);

                $hreflangCollection->add($hrefLang);
            }
        }
        return $hreflangCollection;
    }

    public function getUriForProductId(string $productId): string
    {
        return $this->router->generate(
            'frontend.detail.page',
            ['productId' => $productId]
        );
    }

    /**
     * @param Request $request
     *
     * @return HreflangCollection|null
     */
    public function getHrefLangInformationFromRequest(Request $request): ?HreflangCollection
    {

        $route = $request->attributes->get('_route');

        if ($route === null) {
            return null;
        } 
        $routeParams = $request->attributes->get('_route_params', []);
        $salesChannelContext = $request->attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT);
        $parameter = new HreflangLoaderParameter($route, $routeParams, $salesChannelContext);

        # collection of href lang configurations, but might contain duplicate elements
        $hrefLangCollection = $this->load($parameter); # load time about 23 seconds: 

        $uniqueHrefLangElements = [];

        /** @var HrefLangStruct $hrefLang */
        foreach ($hrefLangCollection as $hrefLang) {
            # deduplicate by forming an id of the unique combination of locale and url
            $id = md5($hrefLang->getLocale() . $hrefLang->getUrl());

            $uniqueHrefLangElements[$id] = $hrefLang;
        }

        # Create a new hreflang collection from unique hreflang entities
        return new HreflangCollection($uniqueHrefLangElements);
    }

    /**
     * @param SalesChannelContext $salesChannelContext
     *
     * @return HreflangCollection
     */
    protected function generateHreflangHome(SalesChannelContext $salesChannelContext): HreflangCollection
    {
        $collection = new HreflangCollection();

        $storeSwitcherDomains = $this->getSalesChannelSwitcherDomains($salesChannelContext);

        $storeSwitcherDomainUrls = [];

        /** @var StoreSwitcherDomainEntity $storeSwitcherDomain */
        foreach ($storeSwitcherDomains as $storeSwitcherDomain) {
            $storeSwitcherDomainUrls[] = $storeSwitcherDomain->getUrl();
        }

        $criteria = new Criteria();
        $criteria->addAssociations(['language.locale', 'salesChannel']);
        $criteria->addFilter(new OrFilter([
            new EqualsFilter('id', $salesChannelContext->getSalesChannel()->getHreflangDefaultDomainId()),
            new EqualsAnyFilter('url', $storeSwitcherDomainUrls),
        ]));

        /** @var SalesChannelDomainEntity[] $domains */
        $domains = $this->salesChannelDomainRepository->search($criteria, $salesChannelContext->getContext());

        if (count($domains) <= 1) {
            return new HreflangCollection();
        }

        foreach ($domains as $domain) {
            $hrefLang = new HreflangStruct();
            $hrefLang->setUrl($domain->getUrl());
            $locale = $domain->getLanguage()->getLocale()->getCode();

            if ($domain->isHreflangUseOnlyLocale()) {
                $locale = substr($locale, 0, 2);
            }

            if ($domain->getId() === $domain->getSalesChannel()->getHreflangDefaultDomainId()) {
                $mainLang = clone $hrefLang;
                $mainLang->setLocale('x-default');
                $collection->add($mainLang);
            }

            $hrefLang->setLocale($locale);
            $collection->add($hrefLang);
        }

        return $collection;
    }

    /**
     * @param SalesChannelContext $salesChannelContext
     *
     * @return EntitySearchResult
     */
    protected function getSalesChannelSwitcherDomains(SalesChannelContext $salesChannelContext): EntitySearchResult
    {
        static $result = [];

        if (isset($result[$salesChannelContext->getSalesChannel()->getId()])) {
            return $result[$salesChannelContext->getSalesChannel()->getId()];
        }

        $criteria = new Criteria;
        $criteria->setTitle("HREFLang service store switcher domains");
        $criteria->addAssociation('salesChannelDomain');
        $criteria->addFilter(new EqualsFilter(
            'salesChannelId',
            $salesChannelContext->getSalesChannel()->getId()
        ));

        $result[$salesChannelContext->getSalesChannel()->getId()] = $this->storeSwitcherDomainRepository->search(
            $criteria,
            $salesChannelContext->getContext()
        );

        return $result[$salesChannelContext->getSalesChannel()->getId()];
    }

    private function getSEOUrls(Context $context, string $pathInfo, ?string $productId = null): EntitySearchResult
    {
        $criteria = new Criteria();
        // $criteria->addAssociations([
        //     'language.locale', 'salesChannel.productVisibilities'
        // ]);
        $criteria->addAssociations([
            'language.locale'
        ]);
        $criteria->addFilter(new EqualsFilter('isCanonical', true));
        $criteria->addFilter(new EqualsFilter('pathInfo', $pathInfo));
        $criteria->setTitle("HREFLang get SEO Urls");

        if ($productId !== null) {
            $parentId = $this->connection->fetchOne(
                'SELECT HEX(`product`.`parent_id`) as `parentId` FROM `product` WHERE `product`.`id` = :productId',
                [
                    'productId' => Uuid::fromHexToBytes($productId)
                ]
            );

            if ($parentId !== null) {
                $productId = $parentId;
            }

            $criteria->addFilter(new EqualsFilter(
                'salesChannel.productVisibilities.productId',
                $productId
            ));
        }
        $collection = $this->seoUrlRepository->search($criteria, $context);
        return $collection;
    }
}
