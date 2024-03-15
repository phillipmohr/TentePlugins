import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';

/**
 * @package content
 */
export default class FormCmsHandler extends Plugin {

    static options = {
        hiddenClass: 'd-none',
        hiddenSubmitSelector: '.submit--hidden',
        formContentSelector: '.form-content',
        cmsBlock: '.cms-block',
        contentType: 'application/x-www-form-urlencoded',
    };

    init() {
        this._client = new HttpClient();
        this._getButton();
        this._getHiddenSubmit();
        this._registerEvents();
        this._getCmsBlock();
        this._getConfirmationText();
    }

    sendAjaxFormSubmit() {
        const { _client, el, options } = this;
        const _data = new FormData(el);

        _client.post(el.action, _data, this._handleResponse.bind(this), options.contentType);
    }

    _registerEvents() {
        this.el.addEventListener('submit', this._handleSubmit.bind(this));

        if (this._button) {
            this._button.addEventListener('submit', this._handleSubmit.bind(this));
            this._button.addEventListener('click', this._handleSubmit.bind(this));
        }
    }

    _getConfirmationText() {
        const input = this.el.querySelector('input[name="confirmationText"]');
        if (input) {
            this._confirmationText = input.value;
        }
    }

    _getButton() {
        this._button = this.el.querySelector('button');
    }

    _getCmsBlock() {
        this._block = this.el.closest(this.options.cmsBlock);
    }

    _getHiddenSubmit() {
        this._hiddenSubmit = this.el.querySelector(this.options.hiddenSubmitSelector);
    }

    _handleSubmit(event) {
        event.preventDefault();

        if (this.el.checkValidity()) {
            this._submitForm();
        } else {
            this._showValidation();
        }
    }

    _showValidation() {
        this._hiddenSubmit.click();
    }

    _submitForm() {
        this.$emitter.publish('beforeSubmit');
        this.sendAjaxFormSubmit();
        if (this.el.classList.contains('contact-form')) {
            if (this._validateForm()) {
                this._showLoadingComponent();
            }
        }
    }

    _showLoadingComponent() {
        const formContent = this.el.querySelector('.form-content');
        const loadingComponent = this.el.querySelector('.loading-component-container');
        this.el.classList.add('is-loading');
        if (formContent && loadingComponent) {
            formContent.classList.add('d-none');
            loadingComponent.classList.remove('d-none');
        }
        const confirmDiv = this._block.querySelector('.confirm-alert');
        if (confirmDiv) {
            confirmDiv.remove();
        }
    }

    _hideLoadingComponent() {
        const formContent = this.el.querySelector('.form-content');
        const loadingComponent = this.el.querySelector('.loading-component-container');
        this.el.classList.remove('is-loading');
        if (formContent && loadingComponent) {
            formContent.classList.remove('d-none');
            loadingComponent.classList.add('d-none');
        }
    }

    _handleResponse(res) {
        const response = JSON.parse(res);
        this.$emitter.publish('onFormResponse', res);

        if (response.length > 0) {
            let changeContent = true;
            let content = '';
            for (let i = 0; i < response.length; i += 1) {
                if (response[i].type === 'danger' || response[i].type === 'info') {
                    changeContent = false;
                }
                content += response[i].alert;
            }
            if (!changeContent && this.el.classList.contains('contact-form')) {
                this._hideLoadingComponent();
            }

            this._createResponse(changeContent, content);
        } else {
            window.location.reload();
        }
    }

    _createResponse(changeContent, content) {
        if (changeContent) {
            if (this._confirmationText) {
                content = this._confirmationText;
            }
            this._block.innerHTML = `<div class="confirm-message">${content}</div>`;
        } else {
            const confirmDiv = this._block.querySelector('.confirm-alert');
            if (confirmDiv) {
                confirmDiv.remove();
            }
            const html = `<div class="confirm-alert">${content}</div>`;
            this._block.insertAdjacentHTML('beforeend', html);
        }

        this._block.scrollIntoView({
            behavior: 'smooth',
            block: 'end',
        });
    }

    _validateForm() {
        const form = this.el;
        const fieldsToValidate = ['email', 'subject', 'comment', 'company', 'country', 'state', 'city', 'zip', 'street', 'firstName', 'lastName'];
        
        for (var i = 0; i < fieldsToValidate.length; i++) {
            var fieldName = fieldsToValidate[i];
            var fieldElement = form.querySelector('[name="' + fieldName + '"]');
            
            if (fieldElement) {
                var fieldType = fieldElement.type;
                if (fieldType === 'checkbox') {
                    if (!fieldElement.checked) {
                        return false;
                    }
                } else {
                    var fieldValue = fieldElement.value.trim();
                    if (fieldValue === '') {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }
        return true;
    }
    
}
