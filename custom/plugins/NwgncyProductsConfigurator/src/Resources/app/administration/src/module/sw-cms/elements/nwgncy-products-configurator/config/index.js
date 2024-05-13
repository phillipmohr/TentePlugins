import template from './sw-cms-el-config-nwgncy-products-configurator.html.twig';
import './sw-cms-el-config-nwgncy-products-configurator.scss';

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
            categoryPropertyGroupModel: null,
            propertyGroupCollection: null,
            measuredPropertyGroupCollection: null,
            propertyGroupOptionCollection: null,
        };
    },

    computed: {
        propertyGroupRepository() {
            return this.repositoryFactory.create('property_group');
        },

        propertyGroupOptionRepository() {
            return this.repositoryFactory.create('property_group_option');
        },

        categoryPropertyGroup() {
            if (this.element?.data?.categoryPropertyGroup) {
                return this.element.data.categoryPropertyGroup;
            }
            return null;
        },

        propertyGroups() {
            if (this.element?.data?.propertyGroups && this.element.data.propertyGroups.length > 0) {
                return this.element.data.propertyGroups;
            }
            return null;
        },

        propertyGroupOptions() {
            if (this.element?.data?.propertyGroupOptions && this.element.data.propertyGroupOptions.length > 0) {
                return this.element.data.propertyGroupOptions;
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
            const criteria = new Criteria(1, 2000);
            return criteria;
        },
        propertyGroupOptionCriteria() {
            const criteria = new Criteria(1, 2000);
            return criteria;
        },

    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('nwgncy-products-configurator');

            this.categoryPropertyGroupModel = null;
            this.propertyGroupCollection = new EntityCollection('/property_group', 'property_group', Shopware.Context.api);
            this.measuredPropertyGroupCollection = new EntityCollection('/property_group', 'property_group', Shopware.Context.api);
            this.propertyGroupOptionCollection = new EntityCollection('/property_group_option', 'property_group_option', Shopware.Context.api);

            if (this.element.config.categoryPropertyGroup.value) {
                this.categoryPropertyGroupModel = this.element.config.categoryPropertyGroup.value;
            }

            if (this.element.config.propertyGroups.value.length > 0) {
                const configPropertyGroupsCriteria = new Criteria(1, 2000);
                configPropertyGroupsCriteria.setIds(this.element.config.propertyGroups.value);
    
                this.propertyGroupRepository
                .search(configPropertyGroupsCriteria, { ...Shopware.Context.api, inheritance: true })
                .then((result) => {
                    this.propertyGroupCollection = result;
                });
            }

            if (this.element.config.measuredPropertyGroups.value.length > 0) {
                const configMeasuredPropertyGroupsCriteria = new Criteria(1, 2000);
                configMeasuredPropertyGroupsCriteria.setIds(this.element.config.measuredPropertyGroups.value);
                this.propertyGroupRepository
                .search(configMeasuredPropertyGroupsCriteria, { ...Shopware.Context.api, inheritance: true })
                .then((result) => {
                    this.measuredPropertyGroupCollection = result;
                });
            }

            if (this.element.config.propertyGroupOptions.value.length > 0) {
                const configPropertyGroupOptionCriteria = new Criteria(1, 2000);
                configPropertyGroupOptionCriteria.setIds(this.element.config.propertyGroupOptions.value);
    
                this.propertyGroupOptionRepository
                .search(configPropertyGroupOptionCriteria, { ...Shopware.Context.api, inheritance: true })
                .then((result) => {
                    this.propertyGroupOptionCollection = result;
                });
            }
        },

        onCategoryPropertyGroupChange() {
            this.element.config.categoryPropertyGroup.value = this.categoryPropertyGroupModel;

            if (!this.element?.data) {
                return;
            }

            this.$set(this.element.data, 'categoryPropertyGroup', this.categoryPropertyGroupModel);
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

        onPropertyGroupOptionsChange() {
            this.element.config.propertyGroupOptions.value = this.propertyGroupOptionCollection.getIds();

            if (!this.element?.data) {
                return;
            }

            this.$set(this.element.data, 'propertyGroupOptions', this.propertyGroupOptionCollection);
        },

        isSelectedPropertyGroup(itemId) {
            return this.propertyGroupCollection.has(itemId);
        },
    },
};
