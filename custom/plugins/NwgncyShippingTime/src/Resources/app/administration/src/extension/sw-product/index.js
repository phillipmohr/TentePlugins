const {Module} = Shopware;

import './page/sw-product-detail';
import './view/sw-product-detail-nst';

Module.register('sw-product-detail-nst-tab', {
    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.product.detail') {
            currentRoute.children.push({
                name: 'sw.product.detail.nst',
                path: '/sw/product/detail/:id/nst',
                component: 'sw-product-detail-nst',
                meta: {
                    parentPath: "sw.product.index"
                }
            });
        }
        next(currentRoute);
    }
});
    