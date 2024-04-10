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
            searchConfigEntity: 'moorl_magazine_article',

            article: null,
            showImportModal: null,
            wordpress: {
                feedUrl: 'https://myblog.com/wp-json/wp/v2 or https://myblog.com/index.php?rest_route=/wp/v2',
                feedPrefix: 'custom',
                feedStatus: null,
                feedMediaFromContent: null,
                feedMediaFromCover: null,
                feedRemoveAttr: null,
                data: {
                    posts: [],
                    categories: []
                },
                collections: {
                    articles: null,
                    categories: null,
                    media: null,
                },
                mediaFolderId: null,
                activeImportItem: null,
            }
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

        mediaDefaultFolderRepo() {
            return this.repositoryFactory.create('media_default_folder');
        },

        mediaRepo() {
            return this.repositoryFactory.create('media');
        },

        categoryRepo() {
            return this.repositoryFactory.create('moorl_magazine_category');
        },

        creatorRepo() {
            return this.repositoryFactory.create('moorl_creator');
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

        apiRequest(str, entity, page) {
            console.log(str);

            httpClient.get(str + '&page=' + page).then((response) => {
                //console.log(response);

                this.wordpress.data[entity] = [...this.wordpress.data[entity], ...response.data];

                this.wordpress.feedStatus = null;

                let totalPages = parseInt(response.headers['x-wp-totalpages'], 10);
                if (totalPages > page) {
                    page++;
                    this.apiRequest(str, entity, page);
                }
            }).catch((exception) => {
                this.wordpress.feedStatus = exception;
            });
        },

        hasOriginId(collection, originId) {
            if (!collection || !originId) {
                console.log("hasOriginId Error", originId, collection);
                return null;
            }
            let has = null;
            collection.forEach(function(item) {
                if (item.originId === originId) {
                    console.log("hasOriginId Match!", item.originId, originId);
                    has = true;
                }
            });
            return has;
        },

        getIdByFileName(collection, fileName) {
            if (!collection || !fileName) {
                console.log("getIdByFileName Error", fileName, collection);
                return null;
            }
            let id = null;
            collection.forEach(function(item) {
                //console.log("getIdByFileName Check...", fileName, item.fileName);
                if (item.fileName == fileName) {
                    console.log("getIdByFileName Match!", fileName, item.fileName);
                    id = item.id;
                }
            });
            return id;
        },

        getByOriginIds(collection, originIds) {
            originIds = originIds.map(x => this.wordpress.feedPrefix + x);
            return collection.filter(item => originIds.includes(item.originId));
        },

        async getByOriginIdsNew(repo, originIds) {
            originIds = originIds.map(x => this.wordpress.feedPrefix + x);
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equalsAny('originId', originIds));
            let entities = null;
            await repo.search(criteria, Shopware.Context.api).then((result) => {
                entities = result;
            });
            return entities ? entities : null;
        },

        async getMediaByFileName(filename) {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('fileName', filename));
            let media = null;
            await this.mediaRepo.search(criteria, Shopware.Context.api).then((result) => {
                media = result.first();
            });
            return media;
        },

        async getAuthorByName(name) {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('name', name));
            let author = null;
            await this.creatorRepo.search(criteria, Shopware.Context.api).then((result) => {
                author = result.first();
            });
            return author;
        },

        async getMediaById(id) {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('id', id));
            let media = null;
            await this.mediaRepo.search(criteria, Shopware.Context.api).then((result) => {
                media = result.first();
            });
            return media;
        },

        async hasOriginIdNew(repo, originId) {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('originId', originId));
            let entity = null;
            await repo.search(criteria, Shopware.Context.api).then((result) => {
                    entity = result.first();
                });
            return entity ? entity.id : null;
        },

        onClickTestConnection() {
            if (this.wordpress.feedUrl.indexOf("?") === -1) {
                this.apiRequest(this.wordpress.feedUrl + '/posts?per_page=20&_embed', 'posts', 1);
                this.apiRequest(this.wordpress.feedUrl + '/categories?per_page=20', 'categories', 1);
            } else {
                this.apiRequest(this.wordpress.feedUrl + '/posts&per_page=20&_embed', 'posts', 1);
                this.apiRequest(this.wordpress.feedUrl + '/categories&per_page=20', 'categories', 1);
            }

            let criteria = new Criteria();
            criteria.setLimit(1);
            criteria.setPage(1);
            criteria.addAssociation('folder');
            criteria.addFilter(Criteria.equals('entity', 'moorl_magazine_article'));

            this.mediaDefaultFolderRepo
                .search(criteria, Shopware.Context.api)
                .then((result) => {
                    this.wordpress.mediaFolderId = result.first().folder.id;
                });
        },

        onClickStartImport() {
            this.importCategoryItem();
        },

        async importCategoryItem() {
            if (!this.showImportModal) {
                return this.cancelImport();
            }

            if (this.wordpress.data.categories.length === 0) {
                return this.importArticleItem();
            }

            let item = this.wordpress.data.categories.shift();
            let newItem = this.categoryRepo.create(Shopware.Context.api);

            this.wordpress.activeImportItem = item;

            Object.assign(newItem, {
                name: item.name,
                teaser: item.description,
                originId: this.wordpress.feedPrefix + item.id,
            });

            //console.log(newItem);

            if (!await this.hasOriginIdNew(this.categoryRepo, newItem.originId)) {
                //this.wordpress.collections.categories.add(newItem);

                this.categoryRepo.save(newItem, Shopware.Context.api).then(() => {
                    this.importCategoryItem();
                });
            } else {
                this.importCategoryItem();
            }
        },

        async uploadMediaFromUrl(url) {
            console.log("Uploading", url);

            // Check URL is Image

            const newMediaItem = this.mediaRepo.create(Shopware.Context.api);
            const mediaUrl = new URL(url);
            const file = mediaUrl.pathname.split('/').pop().split('.');

            if (file.length === 1) {
                newMediaItem.fileName = file[0].replace(/[^a-zA-Z0-9_\- ]/g, "");
            } else {
                newMediaItem.fileName = file[0].replace(/[^a-zA-Z0-9_\- ]/g, "");
                newMediaItem.fileExtension = file.pop();
            }

            newMediaItem.mediaFolderId = this.wordpress.mediaFolderId;

            //console.log("newMediaItem", newMediaItem);

            let media = await this.getMediaByFileName(newMediaItem.fileName);

            //console.log("media", media);

            if (media) {
                return media;
            } else {
                await this.mediaRepo.save(newMediaItem, Shopware.Context.api).then(async () => {
                    await this.mediaService.uploadMediaFromUrl(
                        newMediaItem.id,
                        mediaUrl,
                        newMediaItem.fileExtension,
                        newMediaItem.fileName
                    ).then(async () => {
                        media = await this.getMediaById(newMediaItem.id);
                    }).catch((err) => {
                        console.log(err);
                        console.log("Item", this.wordpress.activeImportItem);
                    });
                });

                return media;
            }
        },

        async walkTheDom(node, func) {
            await func(node);
            node = node.firstChild;
            while (node) {
                await this.walkTheDom(node, func);
                node = node.nextSibling;
            }
        },

        async sanitizeContent(content) {
            const that = this;

            let wrapper = document.createElement('div');
            wrapper.innerHTML = content;

            await this.walkTheDom(wrapper, async function(element) {
                if (that.wordpress.feedRemoveAttr) {
                    if (element.removeAttribute) {
                        element.removeAttribute('id');
                        element.removeAttribute('style');
                        element.removeAttribute('class');
                        element.removeAttribute('srcset');
                        element.removeAttribute('sizes');
                    }
                }

                if (that.wordpress.feedMediaFromContent) {
                    if (element.tagName === 'IMG' || element.tagName === 'img') {
                        console.log("Found IMG", element);
                        element.removeAttribute('srcset');
                        element.removeAttribute('sizes');
                        let src = element.getAttribute('src');
                        let media = await that.uploadMediaFromUrl(src);

                        if (media) {
                            element.setAttribute('src', media.url);
                            console.log("rewrite src", element);
                        } else {
                            console.log("src not found", src);
                        }
                    }
                }
            });
            content = wrapper.innerHTML;

            return content;
        },

        cancelImport() {
            this.wordpress.data.categories = [];
            this.wordpress.data.posts = [];
        },

        async importArticleItem() {
            if (!this.showImportModal) {
                return this.cancelImport();
            }

            if (this.wordpress.data.posts.length === 0) {
                return;
            }

            let item = this.wordpress.data.posts.shift();
            let newItem = this.repository.create(Shopware.Context.api);

            this.wordpress.activeImportItem = item;

            Object.assign(newItem, {
                name: item.title.rendered,
                author: (typeof item._embedded.author[0].name != 'undefined' ? item._embedded.author[0].name : null),
                teaser: item.excerpt.rendered.replace(/<[^>]*>?/gm, ''),
                date: item.date,
                content: await this.sanitizeContent(item.content.rendered),
                hideComments: (item.comment_status === 'open'),
                active: (item.status === 'publish'),
                invisible: (item.type !== 'post'),
                sticky: item.sticky,
                originId: this.wordpress.feedPrefix + item.id,
                categories: await this.getByOriginIdsNew(this.categoryRepo, item.categories)
            });

            //console.log(newItem);

            if (!await this.hasOriginIdNew(this.repo, newItem.originId)) {
                if (this.wordpress.feedMediaFromCover) {
                    if (typeof item._embedded["wp:featuredmedia"] != 'undefined') {
                        if (typeof item._embedded["wp:featuredmedia"][0].source_url != 'undefined') {
                            let media = await this.uploadMediaFromUrl(item._embedded["wp:featuredmedia"][0].source_url)
                            console.log(item._embedded["wp:featuredmedia"][0].source_url);
                            console.log("Media new", media);
                            newItem.mediaId = media ? media.id : null;
                        }
                    }
                }

                // https://dixeno.de/wp-json/wp/v2
                if (typeof item._embedded.author[0].name != 'undefined') {
                    const author = item._embedded.author[0];

                    let magazineAuthor = await this.getAuthorByName(author.name);

                    if (magazineAuthor) {
                        newItem.creatorId = magazineAuthor.id;
                    } else {
                        magazineAuthor = this.creatorRepo.create(Shopware.Context.api);
                        magazineAuthor.name = author.name;
                        magazineAuthor.description = author.description;
                        if (typeof author.avatar_urls === 'object') {
                            if (typeof author.avatar_urls['96'] === 'string') {
                                let media = await this.uploadMediaFromUrl(author.avatar_urls['96'])
                                magazineAuthor.avatarId = media ? media.id : null;
                            }
                        }
                        newItem.creatorId = magazineAuthor.id;

                        await this.creatorRepo.save(magazineAuthor, Shopware.Context.api);
                    }
                }

                //this.wordpress.collections.articles.add(newItem);

                this.repository.save(newItem, Shopware.Context.api).then(() => {
                    this.importArticleItem();
                });
            } else {
                this.importArticleItem();
            }
        },

        onOpenImportModal () {
            this.showImportModal = true;
        },

        onCloseImportModal() {
            this.showImportModal = null;
        },

        changeLanguage() {
            this.getList();
        },

        onDuplicate(reference) {
            this.repository.clone(reference.id, Shopware.Context.api, {
                name: `${reference.name} ${this.$tc('sw-product.general.copy')}`,
                locked: false
            }).then((duplicate) => {
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
