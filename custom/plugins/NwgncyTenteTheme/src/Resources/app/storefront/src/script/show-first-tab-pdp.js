document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.product-detail-tabs .nav-tabs a');
    const firstTabLink = document.querySelector('.product-detail-tabs .nav-tabs a:first-child');
    const firstTabPane = document.querySelector('.product-detail-tabs .tab-content .accordion-item:first-child .tab-pane');

    if (tabs) {
        tabs.forEach(tab => tab.classList.remove('active'));
    }

    if (firstTabLink) {
        firstTabLink.classList.add('active');

    }
    if (firstTabPane) {
        firstTabPane.classList.add('active', 'show');

    }
});