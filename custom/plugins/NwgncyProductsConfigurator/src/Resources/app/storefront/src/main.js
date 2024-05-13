import ProductConfiguratorPlugin from './products-configurator-plugin/products-configurator.plugin';
import NwgncyListingPaginationPlugin from './nwgncy-listing-pagination/nwgncy-listing-pagination'

const PluginManager = window.PluginManager;
PluginManager.register('ProductConfiguratorPlugin', ProductConfiguratorPlugin, '[data-products-configurator]');
PluginManager.override('ListingPagination', NwgncyListingPaginationPlugin, '[data-listing-pagination]');