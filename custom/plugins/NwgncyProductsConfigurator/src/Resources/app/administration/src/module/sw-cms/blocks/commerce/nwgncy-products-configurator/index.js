import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'nwgncy-products-configurator',
    category: 'commerce',
    label: 'Products Configurator',
    component: 'sw-cms-block-nwgncy-products-configurator',
    previewComponent: 'sw-cms-preview-nwgncy-products-configurator',
    defaultConfig: {
        marginBottom: '0',
        marginTop: '0',
        marginLeft: '0',
        marginRight: '0',
        sizingMode: 'full_width'
    },
    slots: {
        productsConfigurator: "nwgncy-products-configurator"
    }
});