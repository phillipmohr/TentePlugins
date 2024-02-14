import CadDownloadPlugin from './plugin/cad-download.plugin';

const PluginManager = window.PluginManager;

PluginManager.register('CadDownloadPlugin', CadDownloadPlugin, '[data-cad-download]');

if (module.hot) {
    module.hot.accept();
}
