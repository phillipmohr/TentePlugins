import template from './index.html.twig';

const { Component, Mixin } = Shopware;
const Criteria = Shopware.Data.Criteria;
 
Component.register('shipping-time-entity-grid', {
    inject: ['repositoryFactory', 'acl'],

    template,

    props: {
        entity: {
            type: String,
            required: true
        },
        defaultItem: {
            type: Object,
            required: true,
            default() {
                return {};
            }
        }
    },

    data() {
        return {
            shippingItemsLoaded: false,
            salesChannels: null,
            salesChannelsValues: [],
            shippingTimes: null,
            shippingTimesBySalesChannel: [],
            item: null,
            entity: null,
            newEntity: null,
            totalCount: 0,
            isLoading: false,

        };
    },

    computed: {

        shippingTimeOptions() {
            return [
                {
                    value: 0,
                    label: this.$tc('nwgncy-shipping-time.options.shippingTimeOne')
                },
                {
                    value: 1,
                    label: this.$tc('nwgncy-shipping-time.options.shippingTimeTwo')
                },
            ];
        },

        salesChannelsValuesForSelects() {
            return this.salesChannelsValues;
        },

        repository() {
            return this.repositoryFactory.create(this.entity);
        },

        salesChannelRepository() {
            return this.repositoryFactory.create('sales_channel');
        },
    },
    created() {
        this.createdComponent();
    },
    methods: {
        createdComponent() {
            this.getShippingTimeItems();
            this.getSalesChannels();
        },

        getShippingTimeItems() {
            this.isLoading = true;
            const criteria = new Criteria();
  
            for (const [field, value] of Object.entries(this.defaultItem)) {
                criteria.addFilter(Criteria.equals(field, value));
            }

            this.repository.search(criteria, Shopware.Context.api).then((shippingTimes) => {
                this.salesChannelsValues = [];
                if (shippingTimes.total > 0) {
                   
                    shippingTimes.forEach((item) => {
                        this.shippingTimesBySalesChannel[item.salesChannelId] = item;
                        this.salesChannelsValues[item.salesChannelId] = item.shippingTime;
                    });
                   
                }
                this.shippingItemsLoaded = true;
                this.shippingTimes = shippingTimes;
                this.isLoading = false;
            });
        },

        getSalesChannels() {
            this.isLoading = true;
            const criteria = new Criteria();
            criteria.addAssociation('translations');
            criteria.addFilter(Criteria.equals('active', 1));

            this.salesChannelRepository.search(criteria, Shopware.Context.api).then((salesChannels) => {
                this.salesChannels = salesChannels;
                this.isLoading = false;

            });
        },

        log(object) {
            console.log(object);
        },

        updateShippingTimeForEverySalesChannel(selectedValue) {
            this.isLoading = true;
            const promises = [];
            
            if (selectedValue === null || selectedValue === undefined ) {
                return;
            }

            this.salesChannels.forEach((salesChannel) => {

                let salesChannelId = salesChannel.id;
                let entityToSave = null;
                if (this.shippingTimesBySalesChannel[salesChannelId] !== undefined) {
                    entityToSave = this.shippingTimesBySalesChannel[salesChannelId];
                } else {
    
                    entityToSave = this.repository.create(Shopware.Context.api);
                    entityToSave.productId = this.defaultItem.productId;
                    entityToSave.salesChannelId = salesChannelId;
                }

                entityToSave.shippingTime = parseInt(selectedValue);

                promises.push(this.repository.save(entityToSave, Shopware.Context.api));
            });

            Promise.all(promises).then(() => {
                this.getSalesChannels();
                this.getShippingTimeItems();
                this.isLoading = false;
            });

        },

        optionSelected(selectedValue, salesChannel) {

            if (selectedValue === null || selectedValue === undefined ) {
                return;
            }

            let salesChannelId = salesChannel.id;
            
            if (this.shippingTimesBySalesChannel[salesChannelId] !== undefined) {
                this.newEntity = this.shippingTimesBySalesChannel[salesChannelId];
            } else {

                this.newEntity = this.repository.create(Shopware.Context.api);
                this.newEntity.productId = this.defaultItem.productId;
                this.newEntity.salesChannelId = salesChannelId;
            }
            this.salesChannelsValues[salesChannelId] = selectedValue;
            this.newEntity.shippingTime = parseInt(selectedValue);
                
            this.repository
                .save(this.newEntity, Shopware.Context.api)
                .then(() => {
                    this.getShippingTimeItems();
                });
        },

    }
});