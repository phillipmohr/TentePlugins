const {Module} = Shopware;

import './page/list';
import './page/detail';
import './page/create';
import './style/main.scss';

import defaultSearchConfiguration from './default-search-configuration';

Module.register('moorl-magazine-article', {
    type: 'plugin',
    name: 'moorl-magazine-article',
    title: 'moorl-magazine.general.article',
    color: '#ff3d58',
    icon: 'default-documentation-file',
    entity: 'moorl_magazine_article',

    routes: {
        list: {
            component: 'moorl-magazine-article-list',
            path: 'list',
            meta: {
                privilege: 'moorl_magazine_article:read'
            }
        },
        detail: {
            component: 'moorl-magazine-article-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'moorl.magazine.article.list',
                privilege: 'moorl_magazine_article:read'
            }
        },
        create: {
            component: 'moorl-magazine-article-create',
            path: 'create',
            meta: {
                parentPath: 'moorl.magazine.article.list',
                privilege: 'moorl_magazine_article:read'
            }
        }
    },

    navigation: [{
        label: 'moorl-magazine.general.article',
        color: '#ff3d58',
        icon: 'default-documentation-file',
        path: 'moorl.magazine.article.list',
        position: 201,
        parent: 'sw-content'
    }],

    defaultSearchConfiguration
});

const SearchTypeService = Shopware.Service('searchTypeService');

SearchTypeService.upsertType('moorl_magazine_article', {
    entityName: 'moorl_magazine_article',
    placeholderSnippet: 'moorl-magazine.general.placeholderSearchBar',
    listingRoute: 'moorl.magazine.article.list'
});
