<?php declare(strict_types=1);

namespace NwgncyTenteOptimizer\Subscriber;


use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Storefront\Page\Page;
use Doctrine\DBAL\Connection;
use Shopware\Storefront\Pagelet\Header\HeaderPagelet;
use Shopware\Core\Content\Seo\Hreflang\HreflangCollection;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\Framework\Struct\StructCollection;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\Locale\LocaleEntity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Zeobv\StoreSwitcher\Service\ConfigService;
use NwgncyTenteOptimizer\Service\HreflangService;
use Zeobv\StoreSwitcher\Content\StoreSwitcherDomain\StoreSwitcherDomainEntity;
use Zeobv\StoreSwitcher\Content\StoreSwitcherDomain\StoreSwitcherDomainCollection;
use NwgncyTenteOptimizer\Service\LocationService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;

/**
 * Class StorefrontSubscriber
 *
 * @package NwgncyTenteOptimizer\Subscriber
 */
class StorefrontSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;
    protected configService $configService;
    protected HreflangService $hreflangService;
    protected LocationService $locationService;
    protected EntityRepository $storeSwitcherDomainRepository;
    protected EntityRepository $localeRepository;
    protected EntityRepository $productRepository;
    protected Connection $connection;
    protected SeoUrlPlaceholderHandlerInterface $handler;
    
 
    public function __construct(
        RouterInterface $router,
        ConfigService $configService,
        HreflangService $hreflangService,
        LocationService $locationService,
        EntityRepository $storeSwitcherDomainRepository,
        EntityRepository $localeRepository,
        EntityRepository $productRepository,
        Connection $connection,
        SeoUrlPlaceholderHandlerInterface $handler
    )
    {
        $this->router = $router;
        $this->configService = $configService;
        $this->hreflangService = $hreflangService;
        $this->locationService = $locationService;
        $this->storeSwitcherDomainRepository = $storeSwitcherDomainRepository;
        $this->localeRepository = $localeRepository;
        $this->productRepository = $productRepository;
        $this->connection = $connection;
        $this->handler = $handler;
        
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            StorefrontRenderEvent::class => [
                ['addStoreSwitcherInfo'],
            ],
        ];
    }

    /**
     * @param StorefrontRenderEvent $event
     */
    public function addStoreSwitcherInfo(StorefrontRenderEvent $event)
    {
        // $fsObject = new Filesystem();

        // $filePath = '/var/www/html/tentecom/public/storeswitcher.html';
        // $fsObject->touch($filePath);
        // $fsObject->chmod($filePath, 0777);
        // $fsObject->appendToFile($filePath, @\Kint::dump(date('h:i:s')));
        // return;
        if (!$this->pluginIsActive($event->getSalesChannelContext())) {
            return;
        }

        // $fsObject->appendToFile($filePath, @\Kint::dump(date('h:i:s')));

        $salesChannel = $event->getSalesChannelContext()->getSalesChannel();
        $request = $event->getRequest();

        $configuredStoreSwitcherDomains = $this->getStorePickerOptions($event->getSalesChannelContext());

        // $fsObject->appendToFile($filePath, @\Kint::dump(date('h:i:s')));
        $visitorScUrl = null;

        if ($request->cookies->has('tente-sc-url')) {
            $visitorScUrl = $request->cookies->get('tente-sc-url');
        } else {

            $countryCode = null;

            if($this->locationService->determineCountryCodeFromRequest($request, $event->getSalesChannelContext())) {
                $countryCode = $this->locationService->determineCountryCodeFromRequest($request, $event->getSalesChannelContext());
    
            } else {
                $countryCode = $this->locationService->getCountryFromAcceptLanguage();
            }

            $sc = $this->locationService->getDefaultTargetDomain($request->getUri(), $countryCode, $event->getSalesChannelContext()->getContext());

            $request->cookies->set('tente-sc-url', $sc->getUrl());

            $visitorScUrl = $sc->getUrl();
        }
        
        
        if($_SERVER['REQUEST_URI'] == '/') {
            $this->redirect($visitorScUrl);
        }

        if ($countryCode) {
            /** @var StoreSwitcherDomainEntity $storeSwitcherDomain */
            foreach ($configuredStoreSwitcherDomains as $storeSwitcherDomain) {
                if (strtolower($storeSwitcherDomain->getIso()) === strtolower($countryCode)) {
                    $storeSwitcherDomain->addExtension('suggested', new ArrayStruct(['suggested' => true]));
                }
            }
        }

        // $fsObject->appendToFile($filePath, @\Kint::dump(date('h:i:s')));

        $hrefLangCollection = $this->hreflangService->getHrefLangInformationFromRequest($request);



        // $fsObject->appendToFile($filePath, @\Kint::dump(date('h:i:s')));

        if (
            $this->configService->shouldOverrideHrefLang($salesChannel) &&
            $hrefLangCollection instanceof StructCollection &&
            $hrefLangCollection->count()
        ) {
            $event->setParameter('storeSwitcherHrefLang', $hrefLangCollection);
        }
        // $fsObject->appendToFile($filePath, @\Kint::dump(date('h:i:s')));

        $config = $this->configService->getAll($salesChannel);

        // $fsObject->appendToFile($filePath, @\Kint::dump(date('h:i:s')));

        $event->setParameter('zeobvStoreSwitcher', new ArrayEntity([
            'showStorePickerModal' => boolval($config['showLanguageSwitcherInPopupOnFirstVisit'] ?? false),
            'config' => $config,
            'activeDomain' => $this->getActiveStoreSwitcherDomainFromEvent($event, $configuredStoreSwitcherDomains),
            'options' => $this->mapCurrentRequestToStoreSwitcherDomainUrls(
                $configuredStoreSwitcherDomains,
                $request,
                $salesChannel,
                $hrefLangCollection
            )
        ]));

        // $fsObject->appendToFile($filePath, @\Kint::dump(date('h:i:s')));
    }


    private function Redirect($url, $permanent = false)
    {
        header('Location: ' . $url, true, $permanent ? 301 : 302);

        exit();
    }


    /**
     * @param StorefrontRenderEvent $event
     * @param EntityCollection $storeSwitcherDomains
     *
     * @return StoreSwitcherDomainEntity|null
     */
    private function getActiveStoreSwitcherDomainFromEvent(StorefrontRenderEvent $event, EntityCollection $storeSwitcherDomains): ?StoreSwitcherDomainEntity
    {
        $parameters = $event->getParameters();

        if (empty($parameters) || empty($parameters['page'])) {
            return null;
        }

        $page = $parameters['page'];

        if (!$page instanceof Page || !$page->getHeader() instanceof HeaderPagelet) {
            return null;
        }

        /** @var LocaleEntity $locale */
        $locale = $this->getCurrentLocaleFromLanguage($page->getHeader()->getActiveLanguage(), $event->getContext());

        if ($locale === null) {
            return null;
        }

        $currentLanguageCode = $locale->getCode();

        $countryIso = $this->getCountryIsoFromLocale($currentLanguageCode);

        if (empty($countryIso)) {
            return null;
        }

        return $storeSwitcherDomains->filter(function (StoreSwitcherDomainEntity $domain) use ($countryIso) {
            return strtolower($domain->getIso()) === $countryIso;
        })->first();
    }

    /**
     * Map the given request against the configured store switcher domain URL properties in effort to keep the user on the same page if possible.
     */
    private function mapCurrentRequestToStoreSwitcherDomainUrls(
        EntitySearchResult $switcherDomainCollection,
        Request $request,
        SalesChannelEntity $currentSalesChannel,
        ?HreflangCollection $hrefLangCollection
    ): StoreSwitcherDomainCollection {
        $requestUri = $request->attributes->get('resolved-uri', '/');
        $productId = $request->attributes->get('productId', null);
        $baseUrls = [];
        // $fsObject = new Filesystem();

        // $filePath = '/var/www/html/tentecom/public/productId.html';
        // $fsObject->touch($filePath);
        // $fsObject->chmod($filePath, 0777);
        // $fsObject->appendToFile($filePath, @\Kint::dump($hrefLangCollection));
        # Replace the store switcher domain urls with fully qualified urls based on the current request
        $mappedSwitcherDomains = $switcherDomainCollection->map(
            function (StoreSwitcherDomainEntity $storeSwitcherDomain) use ($requestUri, $productId, $currentSalesChannel, &$baseUrls) {
                $salesChannelDomain = $storeSwitcherDomain->getSalesChannelDomain();
                $baseUrls[$storeSwitcherDomain->getId()] = $storeSwitcherDomain->getUrl();

                if ($salesChannelDomain === null) {
                    return $storeSwitcherDomain;
                }

                if ($productId !== null && $salesChannelDomain->getSalesChannelId() !== $currentSalesChannel->getId()) {
                    $visibleInOtherSalesChannel = $this->productVisibleInSalesChannel(
                        $productId,
                        $storeSwitcherDomain->getSalesChannelDomain()->getSalesChannelId()
                    );

                    if ($visibleInOtherSalesChannel === false) {
                        return $storeSwitcherDomain;
                    }

                    $requestUri = $this->hreflangService->getUriForProductId(
                        $productId
                    );
                }


                $storeSwitcherDomain->setUrl(
                    rtrim($storeSwitcherDomain->getUrl(), '/') . $requestUri
                );

                return $storeSwitcherDomain;
            }
        );

        # If we have hreflang information, we can try to map the URLs to the correct domain
        if ($hrefLangCollection !== null && $hrefLangCollection->count() > 0) {
            $mappedSwitcherDomains = $switcherDomainCollection->map(
                function (StoreSwitcherDomainEntity $storeSwitcherDomain) use ($hrefLangCollection, $baseUrls) {
                    $domainIso = $storeSwitcherDomain->getIso();

                    foreach ($hrefLangCollection as $hrefLangInformation) {
                        if ($hrefLangInformation->getLocale() === 'x-default') {
                            continue;
                        }

                        $countryIso = $this->getCountryIsoFromLocale($hrefLangInformation->getLocale());

                        if (
                            $countryIso === $domainIso
                            && strpos(
                                $hrefLangInformation->getUrl(),
                                $baseUrls[$storeSwitcherDomain->getId()]
                            ) !== false
                        ) {
                            $storeSwitcherDomain->setUrl($hrefLangInformation->getUrl());
                            break;
                        }
                    }

                    return $storeSwitcherDomain;
                }
            );
        }

        usort($mappedSwitcherDomains, function (StoreSwitcherDomainEntity $a, StoreSwitcherDomainEntity $b) {
            return $a->getPosition() <=> $b->getPosition();
        });

        return new StoreSwitcherDomainCollection($mappedSwitcherDomains);
    }

    /**
     * @param SalesChannelContext $salesChannelContext
     *
     * @return EntitySearchResult
     */
    private function getStorePickerOptions(SalesChannelContext $salesChannelContext): EntitySearchResult
    {
        $criteria = new Criteria;
        $criteria->setTitle('Get Store Picker Options');
        $criteria->addFilter(new EqualsFilter(
            'salesChannelId',
            $salesChannelContext->getSalesChannel()->getId()
        ));

        # Associations required to be able to call StoreSwitcherDomainEntity::getIso()
        $criteria->addAssociations(['country', 'salesChannelDomain.language.locale']);

        return $this->storeSwitcherDomainRepository->search(
            $criteria,
            $salesChannelContext->getContext()
        );
    }

    /**
     * @param SalesChannelContext $salesChannelContext
     *
     * @return bool
     */
    private function pluginIsActive(SalesChannelContext $salesChannelContext): bool
    {
        return $this->configService->getIsActive($salesChannelContext->getSalesChannel());
    }
    
    private function getCountryIsoFromLocale(string $locale): string
    {
        return strtolower(substr($locale, strpos($locale, '-') + 1));
    }

    private function getCurrentLocaleFromLanguage(LanguageEntity $activeLanguage, Context $context): LocaleEntity
    {
        $criteria = new Criteria([$activeLanguage->getLocaleId()]);

        /** @var LocaleEntity $localeEntity */
        $localeEntity = $this->localeRepository->search($criteria, $context)->first();

        return $localeEntity;
    }

    private function productVisibleInSalesChannel(string $productId, string $salesChannelId): bool
    {
        $criteria = new Criteria([$productId]);

        $criteria->addFilter(new EqualsFilter('visibilities.salesChannelId', $salesChannelId));

        $criteria->addAssociation('visibilities');

        $context = Context::createDefaultContext();
        $context->setConsiderInheritance(true);

        $product = $this->productRepository->search($criteria, $context)->first();

        return $product !== null;
    }
}
