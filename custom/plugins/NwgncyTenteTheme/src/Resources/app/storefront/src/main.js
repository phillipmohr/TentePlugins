import CustomOffCanvasMenuPlugin from './custom-offcanvas-menu/offcanvas-menu-plugin';

const PluginManager = window.PluginManager;

PluginManager.override('OffcanvasMenu', CustomOffCanvasMenuPlugin, '[data-offcanvas-menu]');
