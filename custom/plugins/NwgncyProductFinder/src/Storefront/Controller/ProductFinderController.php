<?php declare(strict_types=1);

namespace Nwgncy\ProductFinder\Storefront\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
*/
class ProductFinderController extends StorefrontController
{
    private EntityRepository $productRepository;
    private EntityRepository $productVisibilityRepository;

    public function __construct(EntityRepository $productRepository, EntityRepository $productVisibilityRepository) {
        $this->productRepository = $productRepository;
        $this->productVisibilityRepository = $productVisibilityRepository;
    }

    /**
    * @Route("/product-finder/available-options", name="storefront.product-finder.available-options.get", methods={"GET"}, defaults={"XmlHttpRequest"=true})
    */
    public function getAvailableOptionsForFinder(Request $request, SalesChannelContext $salesChannelContext): JsonResponse
    {
        $context = $salesChannelContext->getContext();
        $salesChannelId = $salesChannelContext->getSalesChannelId();
        $optionsIdsQueryParam = $request->get('options');

        $optionIdsArray = [];
        if ($optionsIdsQueryParam) {
            $optionsIdsQueryParamExploded = explode('|', $optionsIdsQueryParam);
            if ($optionsIdsQueryParamExploded) {
                $optionIdsArray = $optionsIdsQueryParamExploded;
            }
        }

        $visibleProductsIds = [];
        $visibilityCriteria = new Criteria();
        $visibilityCriteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
        $salesChannelVisibleProducts = $this->productVisibilityRepository->search($visibilityCriteria, $context)->getEntities();
        foreach ($salesChannelVisibleProducts as $productVisibility) {
            if ($productVisibility instanceof ProductVisibilityEntity) {
                $visibleProductsIds[] = $productVisibility->getProductId();
            }
        }

        $criteria = new Criteria();
        foreach ($optionIdsArray as $optionId) {
            $criteria->addFilter(new ContainsFilter('optionIds', $optionId));
        }

        $products = $this->productRepository->search($criteria, $context)->getEntities();

        $availableOptionIds = [];
        foreach ($products as $product) {
            if ($product instanceof ProductEntity) {
                $productParentId = $product->getParentId();
                if ($productParentId !== null) {
                    if (in_array($productParentId, $visibleProductsIds)) {
                        $productOptions = $product->getOptionIds();
                        if (is_array($productOptions)) {
                            $availableOptionIds = array_merge($availableOptionIds, $productOptions);
                        }
                    }
                }
            }
        }
        $availableOptionIds = array_unique($availableOptionIds);
    
        if (count($availableOptionIds) < 1) {
            $availableOptionIds = $optionIdsArray;
        }

        $availableOptionIdsValues = array_values($availableOptionIds);

        return new JsonResponse(["availableOptionIds" => $availableOptionIdsValues]);
    }      
}