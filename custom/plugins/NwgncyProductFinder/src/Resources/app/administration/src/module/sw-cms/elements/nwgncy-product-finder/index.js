/**
 * @private
 * @package content
 */
Shopware.Component.register('sw-cms-el-preview-nwgncy-product-finder', () => import('./preview'));
/**
 * @private
 * @package content
 */
Shopware.Component.register('sw-cms-el-config-nwgncy-product-finder', () => import('./config'));
/**
 * @private
 * @package content
 */
Shopware.Component.register('sw-cms-el-nwgncy-product-finder', () => import('./component'));

const Criteria = Shopware.Data.Criteria;

const propertyGroupCriteria = new Criteria(1, 1000);
const propertyGroupOptionCriteria = new Criteria(1, 1000);
/**
 * @private
 * @package content
 */
Shopware.Service('cmsService').registerCmsElement({
    name: 'nwgncy-product-finder',
    label: 'sw-cms.elements.productSlider.label',
    component: 'sw-cms-el-nwgncy-product-finder',
    configComponent: 'sw-cms-el-config-nwgncy-product-finder',
    previewComponent: 'sw-cms-el-preview-nwgncy-product-finder',
    defaultConfig: {
        categoryPropertyGroupOptions: {
            source: 'static',
            value: [],
            required: true,
            entity: {
                name: 'property_group_option',
                criteria: propertyGroupOptionCriteria,
            },
        },
        propertyGroups: {
            source: 'static',
            value: [],
            required: false,
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
    },
    collect: Shopware.Service('cmsService').getCollectFunction(),
});
