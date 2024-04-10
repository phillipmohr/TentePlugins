const { Application, Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

const initContainer = Application.getContainer('init');
const httpClient = initContainer.httpClient;

import template from './index.html.twig';

Component.register('moorl-magazine-article-list', {
    template,

    inject: [
        'repositoryFactory',
        'filterFactory',
        'mediaService'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            items: null,
            selectedItems: null,
            sortBy: 'autoIncrement',
            sortDirection: 'DESC',
            filterCriteria: [],
            naturalSorting: false,
            isLoading: true,
            storeKey: 'grid.filter.moorl_magazine_article',
            activeFilterNumber: 0,
            searchConfigEntity: 'moorl_magazine_article'
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        repository() {
            return this.repositoryFactory.create('moorl_magazine_article');
        },

        defaultCriteria() {
            const defaultCriteria  = new Criteria(this.page, this.limit);
            this.naturalSorting = this.sortBy === 'autoIncrement';

            defaultCriteria.setTerm(this.term);

            this.sortBy.split(',').forEach(sortBy => {
                defaultCriteria.addSorting(Criteria.sort(sortBy, this.sortDirection, this.naturalSorting));
            });

            this.filterCriteria.forEach(filter => {
                defaultCriteria.addFilter(filter);
            });

            return defaultCriteria ;
        },

        columns() {
            return [
                {
                    property: 'active',
                    dataIndex: 'active',
                    label: this.$tc('moorl-magazine.properties.active'),
                    inlineEdit: 'boolean',
                    align: 'center'
                },
                {
                    property: 'autoIncrement',
                    dataIndex: 'autoIncrement',
                    label: '#',
                    align: 'center'
                },
                {
                    property: 'date',
                    dataIndex: 'date',
                    label: this.$tc('moorl-magazine.properties.date'),
                    align: 'center'
                },
                {
                    property: 'name',
                    dataIndex: 'name',
                    label: this.$tc('moorl-magazine.properties.name'),
                    routerLink: 'moorl.magazine.article.detail',
                    inlineEdit: 'string',
                    allowResize: true,
                    primary: true
                },
                {
                    property: 'teaser',
                    dataIndex: 'teaser',
                    label: this.$tc('moorl-magazine.properties.teaser'),
                    allowResize: true,
                },
                {
                    property: 'originId',
                    dataIndex: 'originId',
                    label: this.$tc('moorl-magazine.properties.originId'),
                    inlineEdit: 'string',
                    allowResize: true,
                }
            ]
        }
    },

    methods: {
        async getList() {
            this.isLoading = true;

            const criteria = await this.addQueryScores(this.term, this.defaultCriteria);

            if (!this.entitySearchable) {
                this.isLoading = false;
                this.total = 0;

                return false;
            }

            if (this.freshSearchTerm) {
                criteria.resetSorting();
            }

            return this.repository.search(criteria)
                .then(searchResult => {
                    this.items = searchResult;
                    this.total = searchResult.total;
                    this.isLoading = false;
                });
        },

        changeLanguage() {
            this.getList();
        },

        onDuplicate(reference) {
            const behavior = {
                cloneChildren: true,
                overwrites: {
                    name: `${reference.name} ${this.$tc('global.default.duplicate')}`,
                    locked: false
                }
            };

            this.repository.clone(reference.id, Shopware.Context.api, behavior).then((duplicate) => {
                this.$router.push({ name: 'moorl.magazine.article.detail', params: { id: duplicate.id } });
            });
        },

        updateSelection() {
            console.log("ok");
        },

        updateTotal({total}) {
            this.total = total;
        },
    },

    created() {
        this.getList();
    }
});
