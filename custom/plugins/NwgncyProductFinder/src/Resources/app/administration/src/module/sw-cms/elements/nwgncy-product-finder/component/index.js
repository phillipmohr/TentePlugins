import template from './sw-cms-el-nwgncy-product-finder.html.twig';
import './sw-cms-el-nwgncy-product-finder.scss';

const { Mixin } = Shopware;

/**
 * @private
 * @package content
 */
export default {
    template,

    inject: ['feature'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('nwgncy-product-finder');
            this.initElementData('nwgncy-product-finder');
        },
    },
};
