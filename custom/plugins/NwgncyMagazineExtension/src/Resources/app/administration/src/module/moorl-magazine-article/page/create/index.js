import template from '../detail/index.html.twig';

const {Component} = Shopware;

Component.extend('moorl-magazine-article-create', 'moorl-magazine-article-detail', {
    template,
    methods: {
        getItem() {
            this.item = this.repository.create(Shopware.Context.api);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.item, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({name: 'moorl.magazine.article.detail', params: {id: this.item.id}});
                })
                .catch((exception) => {
                    this.isLoading = false;
                    if (exception.response.data && exception.response.data.errors) {
                        exception.response.data.errors.forEach((error) => {
                            this.createNotificationWarning({
                                title: this.$tc('moorl-magazine.detail.titleError'),
                                message: error.detail
                            });
                        });
                    }
                });
        }
    }
});
