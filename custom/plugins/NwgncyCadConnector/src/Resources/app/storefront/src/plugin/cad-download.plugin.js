import Plugin from 'src/plugin-system/plugin.class';

export default class CadDownloadPlugin extends Plugin {
    static options = {
        tenteBtnGroup: '.tente-downloads-btn-group',
        tenteBtnGroupToggle: '.tente-downloads-dropdown-toggle',
        tentePdfBtn: '.tente-downloads-pdf-btn',
        tenteStpBtn: '.tente-downloads-stp-btn',
        tenteStpDownloadForm: '.tente-stp-downloads-form',
        tentePdfDownloadForm: '.tente-pdf-downloads-form'
    };

    init() {
        this.changeFileFormat();
    }

    changeFileType(fileType) {
        const tenteBtnGroupToggle = document.querySelector(this.options.tenteBtnGroupToggle);
        tenteBtnGroupToggle.textContent = fileType;
    }

    showPdfForm() {
        const tentePdfDownloadForm = document.querySelector(this.options.tentePdfDownloadForm);
        tentePdfDownloadForm.classList.remove('d-none');
    }

    hidePdfForm() {
        const tentePdfDownloadForm = document.querySelector(this.options.tentePdfDownloadForm);
        tentePdfDownloadForm.classList.add('d-none');
    }

    showStpForm() {
        const tenteStpDownloadForm = document.querySelector(this.options.tenteStpDownloadForm);
        tenteStpDownloadForm.classList.remove('d-none');
    }

    hideStpForm() {
        const tenteStpDownloadForm = document.querySelector(this.options.tenteStpDownloadForm);
        tenteStpDownloadForm.classList.add('d-none');
    }

    changeFileFormat() {
        const tentePdfBtn = document.querySelector(this.options.tentePdfBtn);
        tentePdfBtn.addEventListener('click', (event) => {
            event.preventDefault();
            this.changeFileType('PDF');
            this.showPdfForm();
            this.hideStpForm();
        });

        const tenteStpBtn = document.querySelector(this.options.tenteStpBtn);
        tenteStpBtn.addEventListener('click', (event) => {
            event.preventDefault();
            this.changeFileType('STEP-2.14');
            this.showStpForm();
            this.hidePdfForm();
        });
    }
}
