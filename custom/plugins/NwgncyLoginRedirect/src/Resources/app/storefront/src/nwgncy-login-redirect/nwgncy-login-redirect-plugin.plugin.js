import Plugin from 'src/plugin-system/plugin.class';
// import Storage from 'src/helper/storage/storage.helper';
import DomAccess from 'src/helper/dom-access.helper';
import CookieStorageHelper from 'src/helper/storage/cookie-storage.helper';

export default class NwgncyLoginRedirectPlugin extends Plugin {

    init() {
        
        if (window.redirectData) {

            this.storage = CookieStorageHelper;
            
            if (this._isLoginPage() || this._isRegisterAccountPage()) {
                this._setRedirectDataToForm();
            } else if (!this._isAccountHomePage() && !this._isLogoutPage() && !this._isForgotPasswordPage()) {
                this._saveCurrentRoute();
            }
        }
    }

    _saveCurrentRoute() {
        const data = {
            currentUrl: window.redirectData['currentUrl'],
            currentUrlParams: window.redirectData['currentUrlParams']
        }
        this.storage.setItem(this._getStorageKey(), JSON.stringify(data))
    }

    _getUrlData() {
        return this.storage.getItem(this._getStorageKey());
    }

    _setRedirectDataToForm() {
        let currentUrlData = this._getUrlData();

        if (!currentUrlData) {
            return;
        }

        currentUrlData = JSON.parse(currentUrlData);
        
        const redirectParamInput = DomAccess.querySelectorAll(document, 'input[name="redirectParameters"]', false);
        const redirectToInput = DomAccess.querySelectorAll(document, 'input[name="redirectTo"]', false);
        
        if (redirectParamInput && redirectToInput && currentUrlData) {
            
            let redirectParams;
            if (currentUrlData['currentUrlParams'] && currentUrlData['currentUrlParams'] !== '') {
                 redirectParams = currentUrlData['currentUrlParams'];
            } else {
                redirectParams = '[]';
            }

            redirectParamInput.forEach(input => {
                input.value = JSON.stringify(redirectParams);
            });

            redirectToInput.forEach(input => {
                input.value = currentUrlData['currentUrl'];
            });
        }
    }

    _getStorageKey() { 
        return 'nwgncy-login-redirect-data';
    }

    _isLoginPage() {
        return window.redirectData['currentUrl'] === 'frontend.account.login.page';
    }

    _isLogoutPage() {
        return window.redirectData['currentUrl'] === 'frontend.account.logout.page';
    }

    _isAccountHomePage() {
        return window.redirectData['currentUrl'] === 'frontend.account.home.page';
    }

    _isRegisterAccountPage() {
        return window.redirectData['currentUrl'] === 'frontend.account.register.page';
    }

    _isForgotPasswordPage() {
        return window.redirectData['currentUrl'] === 'frontend.account.recover.page';
    }
}