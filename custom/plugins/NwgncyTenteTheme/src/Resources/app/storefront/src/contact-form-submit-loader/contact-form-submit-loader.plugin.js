import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';

export default class ContactFormSubmitLoaderPlugin extends Plugin {
    static options = {
        contactFormSelector: '.contact-form',
        privacyNoticeSelector: '.privacy-notice input'
    };

    init() {
        this.contactForm = DomAccess.querySelector(document, this.options.contactFormSelector);
        //this.privacyNotice = DomAccess.querySelector(document, this.options.privacyNoticeSelector);
        this._registerEvents();
    }

    _registerEvents() {

       
        this.contactForm.addEventListener('submit', e => {
            alert('submitted');
            //Prevent form submission if not all required fields are filled
            if (!this._areAllRequiredFieldsFilled()) {
                e.preventDefault(); // Prevent the form from submitting
                alert('Please fill in all required fields.'); // Inform the user (Consider using a more user-friendly notification method)
                return; // Stop further execution
            }

            const formContent = this.contactForm.querySelector('.form-content');
            const loadingComponent = this.contactForm.querySelector('.loading-component-container');
            this.contactForm.classList.add('is-loading');
            if (formContent) {
                formContent.classList.add('d-none');
            }
            if (loadingComponent) {
                loadingComponent.classList.remove('d-none');
            }

        });
        
        // this.privacyNotice.addEventListener('change', e => {
        //     if (e.target.checked) {
        //         console.log('checked');
        //         return;
        //     }
        //     console.log('not checked');
        // });
    }
    _areAllRequiredFieldsFilled() {
        const requiredInputs = this.contactForm.querySelectorAll('[required]');
        for (let input of requiredInputs) {
            if (input.type === 'checkbox') {
                if (!input.checked) {
                    return false;
                }
            } else {
                if (!input.value.trim()) {
                    return false;
                }
            }
        }
        return true;
    }    
}
