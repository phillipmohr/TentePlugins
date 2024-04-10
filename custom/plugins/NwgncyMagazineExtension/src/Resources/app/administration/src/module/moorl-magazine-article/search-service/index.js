const {Component, Application} = Shopware;

import template from './index.html.twig';

Component.override('sw-search-bar-item', {
    template
});

Application.addServiceProviderDecorator('searchTypeService', searchTypeService => {
    searchTypeService.upsertType('moorl_magazine_article', {
        entityName: 'moorl_magazine_article',
        entityService: 'moorlMagazineArticleService',
        placeholderSnippet: 'moorl-magazine.general.placeholderSearchBar',
        listingRoute: 'moorl.magazine.article.list'
    });

    return searchTypeService;
});