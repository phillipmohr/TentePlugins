import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import AjaxOffCanvas from 'src/plugin/offcanvas/ajax-offcanvas.plugin';

/**
 * This plugin handels the price request window activities
 */
export default class PriceRequestsWindow extends Plugin {
    static options = {
        requestButtonCart: '.btn-nb-pricerequest-cart',
        requestButtonOffcanvasCart: '.btn-nb-pricerequest-offcanvascart',
        requestButtonListing: '.btn-nb-pricerequest-listing',
        requestButtonDetail: '.btn-nb-pricerequest-detail',
        lineItemsSource: 'input[name="nb_pricerequest_lineitems"]',
        lineItemsDest: 'input[name="nb_pricerequest_form_lineitems"]',
        quantityProducttitleSeperator: 'input[name="nb_priceonrequest_quantity_producttitle_seperator"]',
        detailPageQuantitySelector: '.product-detail-quantity-select,.product-detail-quantity-input',
        detailPageQuantitySelectorCustomTemplate: '.quantity-selector-group-input'
    };

    init() {
        this.bindPriceRequestButtonCart();
        this.bindPriceRequestButtonOffcanvasCart();
        this.bindPriceRequestButtonListing();
        this.bindPriceRequestButtonDetail();
        this.showSuccessError();
    }

//bindings

    bindPriceRequestButtonCart() {
        //if the element exists on this page we are allowed to do a binding
        if(document.querySelector(this.options.requestButtonCart) != null){
            const plugin = window.PluginManager.getPluginInstanceFromElement(document.querySelector(this.options.requestButtonCart), 'AjaxModal');
            plugin.$emitter.subscribe('ajaxModalOpen', this.onRequestCartClickedModalOpen.bind(this));
        }
    }


    //OffCanvasCart', OffCanvasCartPlugin, '');
    bindPriceRequestButtonOffcanvasCart() {
        //subscribe the offcanvas cart opened and bind our binding event
        let bindingElem = document.querySelector('[data-offcanvas-cart]');
        if(bindingElem !== null){
            const plugin = window.PluginManager.getPluginInstanceFromElement(document.querySelector('[data-offcanvas-cart]'), 'OffCanvasCart');
            plugin.$emitter.subscribe('offCanvasOpened', this.onOffcavasCartOpened.bind(this));
        }
    }

    //helper to bind our button as soon as the offcanvas was opened
    onOffcavasCartOpened() {
        //if the element exists on this page we are allowed to do a binding
        if(document.querySelector(this.options.requestButtonOffcanvasCart) != null) {
            setTimeout(function(){
                window.PluginManager.initializePlugin('AjaxModal', this.options.requestButtonOffcanvasCart);
                const plugin = window.PluginManager.getPluginInstanceFromElement(document.querySelector(this.options.requestButtonOffcanvasCart), 'AjaxModal');

                plugin.$emitter.subscribe('ajaxModalOpen', this.onRequestOffcanvasCartClickedModalOpen.bind(this));
            }.bind(this), 200);

        }
    }

    bindPriceRequestButtonListing() {
        //if the element exists on this page we are allowed to do a binding
        let requestButtons = document.querySelectorAll(this.options.requestButtonListing);

        for(const requestButton of requestButtons)
        {
            const plugin = window.PluginManager.getPluginInstanceFromElement(requestButton, 'AjaxModal');
            plugin.$emitter.subscribe('ajaxModalOpen', this.onRequestListingClickedModalOpen.bind(this));
        }
    }

    bindPriceRequestButtonDetail() {
        //if the element exists on this page we are allowed to do a binding
        if(document.querySelector(this.options.requestButtonDetail) != null) {
            const plugin = window.PluginManager.getPluginInstanceFromElement(document.querySelector(this.options.requestButtonDetail), 'AjaxModal');
            plugin.$emitter.subscribe('ajaxModalOpen', this.onRequestDetailClickedModalOpen.bind(this));
        }
    }

    setSourceUrl() {

        
        var formSourceUrl = document.querySelector(".nbpr_form_sourceurl");
        if(formSourceUrl) {
            formSourceUrl.value = window.location.href;
        }
        
        var errorRoute = document.querySelector(".nb_por_error_route");
        if(errorRoute) {
            errorRoute.value = window.location.href + "?nb_por_success=0";
        }
        
    }

    showSuccessError() {
        var url_string = window.location;
        var url = new URL(url_string);
        var nb_por_success = url.searchParams.get("nb_por_success");

        let $_toast = document.querySelector("#toast");

        if (nb_por_success == 1) {
            $_toast.classList.add('nb-alert-success');
            $_toast.classList.remove('nb-alert-failure');
            this.showToast();
        }

        if (nb_por_success == 0) {
            $_toast.classList.add('nb-alert-failure');
            $_toast.classList.remove('nb-alert-success');
            this.showToast();
        }
    }


//modal listeners

    onRequestCartClickedModalOpen(){
        this.setSourceUrl();

        var lineitemsSource = document.querySelector('input[name="nb_pricerequest_lineitems"]');
        var lineitemsDest = document.querySelector('input[name="nb_pricerequest_form_lineitems"]');

        lineitemsDest.value = lineitemsSource.value;

        document.querySelector('.nb_pricerequest_form_lineitems_div').innerHTML = this.printablelineitems(lineitemsSource.value);

        var closeCartButton = document.body.querySelector('.js-offcanvas-close');

        document.querySelector('.nbpr_deletecart').value = 1;
    }

    onRequestOffcanvasCartClickedModalOpen() {

        this.setSourceUrl();

        //handle other information
        var lineitemsSource = DomAccess.querySelector(
            document.body,
            '.offcanvas-cart input[name="nb_pricerequest_lineitems"]'
        );

        var lineitemsDest = document.querySelector('input[name="nb_pricerequest_form_lineitems"]');
        var lineitemsSourceDec = JSON.parse(lineitemsSource.value);

        var lineitemsSourceCleaned = [];
        for (var i = 0; i < lineitemsSourceDec.length; i++) {
            lineitemsSourceCleaned.push(lineitemsSourceDec[i]);
        }
        var lineitemsSourceCleanedEncoded = JSON.stringify(lineitemsSourceCleaned);

        lineitemsDest.value = lineitemsSourceCleanedEncoded;

        document.querySelector('.nb_pricerequest_form_lineitems_div').innerHTML = this.printablelineitems(lineitemsSourceCleanedEncoded);

// Emit event after everything is written before cart close
        this.$emitter.publish('priceRequestFormWritten', {});

        var closeCartButton = document.querySelector('.js-offcanvas-close');

        AjaxOffCanvas.close(100);

        document.querySelector('.nbpr_deletecart').value = 1;
    }

    onRequestListingClickedModalOpen(event) {
        this.setSourceUrl();

        var eventTarget = event.target;
        var lineitemsSource = eventTarget.querySelector('input[name="nb_pricerequest_lineitems"]').value;

        var lineitemsDest = document.querySelector('input[name="nb_pricerequest_form_lineitems"]');

        lineitemsDest.value = lineitemsSource;

        document.querySelector('.nb_pricerequest_form_lineitems_div').innerHTML = this.printablelineitems(lineitemsSource);

        document.querySelector('.nbpr_deletecart').value = 0;
    }

    onRequestDetailClickedModalOpen() {
        this.setSourceUrl();

        var lineitemsSource = document.querySelector('input[name="nb_pricerequest_lineitems"]');
        var lineitemsDest = document.querySelector('input[name="nb_pricerequest_form_lineitems"]');
        var quantitySelector = document.querySelector(this.options.detailPageQuantitySelector);
        if(quantitySelector == null){
            quantitySelector = document.querySelector(this.options.detailPageQuantitySelectorCustomTemplate);
        }

        var quantity = quantitySelector.value;

        if(lineitemsDest) {
            lineitemsDest.value = lineitemsSource.value.replace('"quantity":0', '"quantity":' + quantity);
            document.querySelector('.nb_pricerequest_form_lineitems_div').innerHTML = this.printablelineitems(lineitemsSource.value.replace('"quantity":0', '"quantity":' + quantity));
            document.querySelector('.nbpr_deletecart').value = 0;
        }



    }

//helpers

    showToast() {
        var toast = document.getElementById("toast");
        clearTimeout(x);
        toast.style.transform = "translateX(0)";
        var x = setTimeout(() => {
            toast.style.transform = "translateX(400px)"
        }, 4000);
    }

    closeToast() {
        var toast = document.getElementById("toast");
        toast.style.transform = "translateX(400px)";
    }

    printablelineitems(lineitems) {

        if(document.querySelector(this.options.quantityProducttitleSeperator)) {
            var quantityProducttitleSeperator = document.querySelector(this.options.quantityProducttitleSeperator).value;
        }


        var str = '';

        var lineitemsarr = JSON.parse(lineitems);


        for (var i = 0; i < lineitemsarr.length; i++) {
            //custom products
            if (lineitemsarr[i].number == "*") {
                str += '<b>';
                str += lineitemsarr[i].children[0].quantity;

                //if plural
                if(lineitemsarr[i].children[0].quantity > 1 && lineitemsarr[i].children[0].packUnitPlural != null ){
                    str += ' ' + lineitemsarr[i].children[0].packUnitPlural + ' ';
                }//singular
                else if(lineitemsarr[i].children[0].quantity == 1 && lineitemsarr[i].children[0].packUnit != null ){
                    str += ' ' + lineitemsarr[i].children[0].packUnit + ' ';
                }//no unit
                else{
                    str += 'x ';
                }


                str += lineitemsarr[i].children[0].name;

                if (lineitemsarr[i].children[0].variant != "" && lineitemsarr[i].variant != null && typeof (lineitemsarr[i].variant) != "undefined") {
                    str += '</b> (';
                    str += lineitemsarr[i].children[0].variant;
                    str += ') (';
                } else {
                    str += '</b> (';
                }
                str += lineitemsarr[i].children[0].number;
                str += ')';
                str += '</br>';
            } else {
                str += '<b>';
                str += lineitemsarr[i].quantity;

                //if plural
                if(lineitemsarr[i].quantity > 1 && lineitemsarr[i].packUnitPlural != null ){
                    str += ' ' + lineitemsarr[i].packUnitPlural + ' ';
                }//singular
                else if(lineitemsarr[i].quantity == 1 && lineitemsarr[i].packUnit != null ){
                    str += ' ' + lineitemsarr[i].packUnit + ' ';
                }//no unit
                else{
                    str += quantityProducttitleSeperator;
                }



                str += lineitemsarr[i].name;

                if (lineitemsarr[i].variant != "" && lineitemsarr[i].variant != null && typeof (lineitemsarr[i].variant) != "undefined") {
                    str += '</b> (';
                    str += lineitemsarr[i].variant;
                    str += ') (';
                } else {
                    str += '</b> (';
                }
                str += lineitemsarr[i].number;
                str += ')';
                str += '</br>';

                //check for children

                if (lineitemsarr[i].children !== undefined) {
                    str += '<ul>';
                    for (var z = 0; z < lineitemsarr[i].children.length; z++) {

                        str += "<li>";
                        str += lineitemsarr[i].children[z].quantity;

                        //if plural
                        if(lineitemsarr[i].children[z].quantity > 1 && lineitemsarr[i].children[z].packUnitPlural != null ){
                            str += ' ' + lineitemsarr[i].children[z].packUnitPlural + ' ';
                        }//singular
                        else if(lineitemsarr[i].children[z].quantity == 1 && lineitemsarr[i].children[z].packUnit != null ){
                            str += ' ' + lineitemsarr[i].children[z].packUnit + ' ';
                        }//no unit
                        else{
                            str += 'x ';
                        }

                        str += lineitemsarr[i].children[z].name;

                        if (lineitemsarr[i].children[z].variant != "" && lineitemsarr[i].variant != null && typeof (lineitemsarr[i].variant) != "undefined") {
                            str += ' (';
                            str += lineitemsarr[i].children[z].variant;
                            str += ') (';
                        } else {
                            str += ' (';
                        }
                        str += lineitemsarr[i].children[z].number;
                        str += ')';
                        str += '</li>';
                    }
                    str += '</ul>';
                }

            }
        }


        return str;
    }

}