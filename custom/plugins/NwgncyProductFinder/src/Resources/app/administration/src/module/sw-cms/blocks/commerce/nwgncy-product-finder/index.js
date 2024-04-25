import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'nwgncy-product-finder',
    category: 'commerce',
    label: 'Newwwagency Product Finder',
    component: 'sw-cms-block-nwgncy-product-finder',
    previewComponent: 'sw-cms-preview-nwgncy-product-finder',
    defaultConfig: {
        marginBottom: '0',
        marginTop: '0',
        marginLeft: '0',
        marginRight: '0',
        sizingMode: 'full_width'
    },
    slots: {
        productFinder: "nwgncy-product-finder"
    }
});