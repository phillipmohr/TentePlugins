import ProductFinderPlugin from './product-finder-plugin/product-finder.plugin';

const PluginManager = window.PluginManager;
PluginManager.register('ProductFinderPlugin', ProductFinderPlugin, '[data-product-finder]');