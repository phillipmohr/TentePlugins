import CustomOffCanvasMenuPlugin from './custom-offcanvas-menu/offcanvas-menu-plugin';
import PriceRequestSubmitLoader from './price-request-submit-loader/price-request-submit-loader.plugin';
import PriceRequestsWindow from './pricerequests-window/pricerequests-window';
import ContactFormSubmitLoaderPlugin from './contact-form-submit-loader/contact-form-submit-loader.plugin';

const PluginManager = window.PluginManager;

PluginManager.override('OffcanvasMenu', CustomOffCanvasMenuPlugin, '[data-offcanvas-menu]');
PluginManager.override('PriceRequestsWindow', PriceRequestsWindow, 'body');
PluginManager.register('PriceRequestSubmitLoader', PriceRequestSubmitLoader, '[data-price-request-submit-loader]');
// PluginManager.register('ContactFormSubmitLoaderPlugin', ContactFormSubmitLoaderPlugin, '[data-contact-form-submit-loader]');

if (module.hot) {
    module.hot.accept();
}