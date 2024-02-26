import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';

export default class PriceRequestSubmitLoaderPlugin extends Plugin {
    static options = {
        requestFormSelector: '.tente-pricerequest-modal-form',
        requestModalTextSelector: '.tente-pricerequest-request-popup-text',
        requestModalProductListSelector: '.tente-pricerequest-request-product'
    };

    init() {
        this.requestForm = DomAccess.querySelector(document, this.options.requestFormSelector);
        this._registerEvents();
    }

    _registerEvents() {
        this.requestForm.addEventListener('submit', e => {
            // Prevent form submission if not all required fields are filled
            if (!this._areAllRequiredFieldsFilled()) {
                e.preventDefault(); // Prevent the form from submitting
                alert('Please fill in all required fields.'); // Inform the user (Consider using a more user-friendly notification method)
                return; // Stop further execution
            }

            const formContent = this.requestForm.querySelector('.form-content');
            const loadingComponent = this.requestForm.querySelector('.loading-component-container');
            const requestModalText = DomAccess.querySelector(document, this.options.requestModalTextSelector);
            const requestModalProductList = DomAccess.querySelector(document, this.options.requestModalProductListSelector);
            
            if (formContent) {
                formContent.classList.add('d-none');
            }
            if (requestModalText) {
                requestModalText.classList.add('d-none');
            }
            if (requestModalProductList) {
                requestModalProductList.classList.add('d-none');
            }
            if (loadingComponent) {
                loadingComponent.classList.remove('d-none');
            }
        });
    }

    _areAllRequiredFieldsFilled() {
        const requiredInputs = this.requestForm.querySelectorAll('[required]');
        for (let input of requiredInputs) {
            if (!input.value.trim()) {
                return false;
            }
        }
        return true;
    }
}
