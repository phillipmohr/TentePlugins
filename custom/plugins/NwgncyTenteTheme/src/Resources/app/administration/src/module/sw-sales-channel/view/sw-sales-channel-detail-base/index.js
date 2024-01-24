/**
 * @package sales-channel
 */

import template from './sw-sales-channel-detail-base.html.twig';

const { Component, Mixin, Context, Defaults } = Shopware;
const { Criteria } = Shopware.Data;

export default {
    template,

    data() {
        return {
            serviceCategoriesLeftCollection: null,
        };
    },

    computed: {
        serviceCategoryLeftCriteria() {
            const criteria = new Criteria(1, 25);
            criteria.addFilter(Criteria.equals('id', this.salesChannel.customFields?.serviceCategoryLeftId || null));

            return criteria;
        },

        serviceCategoriesLeft() {
            return this.serviceCategoriesLeftCollection ? this.serviceCategoriesLeftCollection : [];
        },
    },

    methods: {
        createCategoryCollections() {
            if (!this.salesChannel) {
                return;
            }
            this.createCategoriesCollection(this.mainCategoryCriteria, 'mainCategoriesCollection');
            this.createCategoriesCollection(this.footerCategoryCriteria, 'footerCategoriesCollection');
            this.createCategoriesCollection(this.serviceCategoryCriteria, 'serviceCategoriesCollection');
            this.createCategoriesCollection(this.serviceCategoryLeftCriteria, 'serviceCategoriesLeftCollection');
        },

        async createCategoriesCollection(criteria, collectionName) {
            this[collectionName] = await this.categoryRepository.search(criteria, Shopware.Context.api);
        },

        onServiceLeftSelectionAdd(item) {
            this.$set(this.salesChannel, 'customFields', {
                serviceCategoryLeftId: item.id,
            });
        },

        onServiceLeftSelectionRemove() {
            if (this.salesChannel.customFields?.serviceCategoryLeftId) {
                this.salesChannel.customFields.serviceCategoryLeftId = null;
            }                
        },
    },
};
