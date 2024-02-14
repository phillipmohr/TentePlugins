import template from './sw-cms-el-nwgncy-products-configurator.html.twig';
import './sw-cms-el-nwgncy-products-configurator.scss';

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
            this.initElementConfig('nwgncy-products-configurator');
            this.initElementData('nwgncy-products-configurator');
        },
    },
};
