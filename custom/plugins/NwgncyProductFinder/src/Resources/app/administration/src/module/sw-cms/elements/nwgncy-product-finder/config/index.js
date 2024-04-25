import template from './sw-cms-el-config-nwgncy-product-finder.html.twig';
import './sw-cms-el-config-nwgncy-product-finder.scss';

const { Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

/**
 * @private
 * @package content
 */
export default {
    template,

    inject: ['repositoryFactory', 'feature'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            categoryPropertyGroupOptionCollection: null,
            propertyGroupCollection: null,
            measuredPropertyGroupCollection: null,
        };
    },

    computed: {
        propertyGroupRepository() {
            return this.repositoryFactory.create('property_group');
        },

        propertyGroupOptionRepository() {
            return this.repositoryFactory.create('property_group_option');
        },

        propertyGroups() {
            if (this.element?.data?.propertyGroups && this.element.data.propertyGroups.length > 0) {
                return this.element.data.propertyGroups;
            }
            return null;
        },

        categoryPropertyGroupOptions() {
            if (this.element?.data?.categoryPropertyGroupOptions && this.element.data.categoryPropertyGroupOptions.length > 0) {
                return this.element.data.categoryPropertyGroupOptions;
            }
            return null;
        },

        categoryPropertyGroupSelectContext() {
            const context = { ...Shopware.Context.api };
            context.inheritance = true;

            return context;
        },

        propertyGroupMultiSelectContext() {
            const context = { ...Shopware.Context.api };
            context.inheritance = true;

            return context;
        },
        
        measuredPropertyGroupMultiSelectContext() {
            const context = { ...Shopware.Context.api };
            context.inheritance = true;

            return context;
        },

        propertyGroupOptionMultiSelectContext() {
            const context = { ...Shopware.Context.api };
            context.inheritance = true;

            return context;
        },

        propertyGroupCriteria() {
            const criteria = new Criteria(1, 1000);
            return criteria;
        },
        categoryPropertyGroupOptionCriteria() {
            const criteria = new Criteria(1, 1000);
            return criteria;
        },

    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('nwgncy-product-finder');

            this.categoryPropertyGroupOptionCollection = new EntityCollection('/property_group_option', 'property_group_option', Shopware.Context.api);
            this.propertyGroupCollection = new EntityCollection('/property_group', 'property_group', Shopware.Context.api);
            this.measuredPropertyGroupCollection = new EntityCollection('/property_group', 'property_group', Shopware.Context.api);



            if (this.element.config.propertyGroups.value.length > 0) {
                const configPropertyGroupsCriteria = new Criteria(1, 1000);
                configPropertyGroupsCriteria.setIds(this.element.config.propertyGroups.value);
    
                this.propertyGroupRepository
                .search(configPropertyGroupsCriteria, { ...Shopware.Context.api, inheritance: true })
                .then((result) => {
                    this.propertyGroupCollection = result;
                });
            }

            if (this.element.config.measuredPropertyGroups.value.length > 0) {
                const configMeasuredPropertyGroupsCriteria = new Criteria(1, 1000);
                configMeasuredPropertyGroupsCriteria.setIds(this.element.config.measuredPropertyGroups.value);
                this.propertyGroupRepository
                .search(configMeasuredPropertyGroupsCriteria, { ...Shopware.Context.api, inheritance: true })
                .then((result) => {
                    this.measuredPropertyGroupCollection = result;
                });
            }

            if (this.element.config.categoryPropertyGroupOptions.value.length > 0) {
                const configCategoryPropertyGroupOptionCriteria = new Criteria(1, 1000);
                configCategoryPropertyGroupOptionCriteria.setIds(this.element.config.categoryPropertyGroupOptions.value);
    
                this.propertyGroupOptionRepository
                .search(configCategoryPropertyGroupOptionCriteria, { ...Shopware.Context.api, inheritance: true })
                .then((result) => {
                    this.categoryPropertyGroupOptionCollection = result;
                });
            }
        },

        onPropertyGroupsChange() {
            this.element.config.propertyGroups.value = this.propertyGroupCollection.getIds();

            if (!this.element?.data) {
                return;
            }

            this.$set(this.element.data, 'propertyGroups', this.propertyGroupCollection);
        },
        

        onMeasuredPropertyGroupsChange() {
            this.element.config.measuredPropertyGroups.value = this.measuredPropertyGroupCollection.getIds();

            if (!this.element?.data) {
                return;
            }

            this.$set(this.element.data, 'measuredPropertyGroups', this.measuredPropertyGroupCollection);
        },

        onCategoryPropertyGroupOptionsChange() {
            this.element.config.categoryPropertyGroupOptions.value = this.categoryPropertyGroupOptionCollection.getIds();

            if (!this.element?.data) {
                return;
            }

            this.$set(this.element.data, 'categoryPropertyGroupOptions', this.categoryPropertyGroupOptionCollection);
        },

        isSelectedPropertyGroup(itemId) {
            return this.propertyGroupCollection.has(itemId);
        },
    },
};
