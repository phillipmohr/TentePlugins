import OffcanvasMenuPlugin from 'src/plugin/main-menu/offcanvas-menu.plugin';
import OffCanvas from 'src/plugin/offcanvas/offcanvas.plugin';

export default class CustomOffCanvasMenuPlugin extends OffcanvasMenuPlugin {

    init() {
        super.init();
    }

    _updateOverlay(animationType, content) {
        this._content = content;

        if (OffCanvas.exists()) {
            const offcanvasMenu = OffcanvasMenuPlugin._getOffcanvasMenu();

            if (!offcanvasMenu) {
                this._replaceOffcanvasContent(content);
            }

            this._createOverlayElements();
            const currentContent = OffcanvasMenuPlugin._getOverlayContent('');
            const menuContent = OffcanvasMenuPlugin._getMenuContentFromResponse(content);

            this._replaceOffcanvasMenuContent(animationType, menuContent, currentContent);
            this._registerEvents();
        }

        this.$emitter.publish('updateOverlay');
    }
}
