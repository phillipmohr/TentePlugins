import CustomOffCanvasMenuPlugin from './custom-offcanvas-menu/offcanvas-menu-plugin';
import PriceRequestSubmitLoader from './price-request-submit-loader/price-request-submit-loader.plugin';
import PriceRequestsWindow from './pricerequests-window/pricerequests-window';
import FormCmsHandlerPlugin from './plugin/forms/form-cms-handler.plugin';
import './script/show-first-tab-pdp';

const PluginManager = window.PluginManager;

PluginManager.override('OffcanvasMenu', CustomOffCanvasMenuPlugin, '[data-offcanvas-menu]');
PluginManager.override('PriceRequestsWindow', PriceRequestsWindow, 'body');
PluginManager.register('PriceRequestSubmitLoader', PriceRequestSubmitLoader, '[data-price-request-submit-loader]');
PluginManager.override('FormCmsHandler', FormCmsHandlerPlugin, '.cms-element-form form');
if (module.hot) {
    module.hot.accept();
}