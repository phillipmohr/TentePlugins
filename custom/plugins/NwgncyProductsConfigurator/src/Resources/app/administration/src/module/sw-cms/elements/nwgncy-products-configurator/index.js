/**
 * @private
 * @package content
 */
Shopware.Component.register('sw-cms-el-preview-nwgncy-products-configurator', () => import('./preview'));
/**
 * @private
 * @package content
 */
Shopware.Component.register('sw-cms-el-config-nwgncy-products-configurator', () => import('./config'));
/**
 * @private
 * @package content
 */
Shopware.Component.register('sw-cms-el-nwgncy-products-configurator', () => import('./component'));

const Criteria = Shopware.Data.Criteria;

const propertyGroupCriteria = new Criteria(1, 2000);
const propertyGroupOptionCriteria = new Criteria(1, 2000);
/**
 * @private
 * @package content
 */
Shopware.Service('cmsService').registerCmsElement({
    name: 'nwgncy-products-configurator',
    label: 'sw-cms.elements.productSlider.label',
    component: 'sw-cms-el-nwgncy-products-configurator',
    configComponent: 'sw-cms-el-config-nwgncy-products-configurator',
    previewComponent: 'sw-cms-el-preview-nwgncy-products-configurator',
    defaultConfig: {
        categoryPropertyGroup: {
            source: 'static',
            value: null,
            required: true,
            entity: {
                name: 'property_group',
                criteria: propertyGroupCriteria,
            },
        },
        propertyGroups: {
            source: 'static',
            value: [],
            required: true,
            entity: {
                name: 'property_group',
                criteria: propertyGroupCriteria,
            },
        },
        measuredPropertyGroups: {
            source: 'static',
            value: [],
            required: false,
            entity: {
                name: 'property_group',
                criteria: propertyGroupCriteria,
            },
        },
        propertyGroupOptions: {
            source: 'static',
            value: [],
            required: true,
            entity: {
                name: 'property_group_option',
                criteria: propertyGroupOptionCriteria,
            },
        },
    },
    collect: Shopware.Service('cmsService').getCollectFunction(),
});
