"use strict";(self.webpackChunk=self.webpackChunk||[]).push([["nwgncy-tente-theme"],{4948:(e,t,n)=>{var i=n(3175),s=n(3637);class r extends i.Z{init(){super.init()}_updateOverlay(e,t){if(this._content=t,s.Z.exists()){i.Z._getOffcanvasMenu()||this._replaceOffcanvasContent(t),this._createOverlayElements();const n=i.Z._getOverlayContent(""),s=i.Z._getMenuContentFromResponse(t);this._replaceOffcanvasMenuContent(e,s,n),this._registerEvents()}this.$emitter.publish("updateOverlay")}}var a,o,l,c=n(6285),u=n(3206);class d extends c.Z{init(){this.requestForm=u.Z.querySelector(document,this.options.requestFormSelector),this._registerEvents()}_registerEvents(){this.requestForm.addEventListener("submit",(e=>{if(!this._areAllRequiredFieldsFilled())return void e.preventDefault();const t=this.requestForm.querySelector(".form-content"),n=this.requestForm.querySelector(".loading-component-container"),i=u.Z.querySelector(document,this.options.requestModalTextSelector),s=u.Z.querySelector(document,this.options.requestModalProductListSelector);t&&t.classList.add("d-none"),i&&i.classList.add("d-none"),s&&s.classList.add("d-none"),n&&n.classList.remove("d-none")}))}_areAllRequiredFieldsFilled(){const e=this.requestForm.querySelectorAll("[required]");for(let t of e)if(!t.value.trim())return!1;return!0}}a=d,l={requestFormSelector:".tente-pricerequest-modal-form",requestModalTextSelector:".tente-pricerequest-request-popup-text",requestModalProductListSelector:".tente-pricerequest-request-product"},(o=function(e){var t=function(e,t){if("object"!=typeof e||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var i=n.call(e,t||"default");if("object"!=typeof i)return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"==typeof t?t:String(t)}(o="options"))in a?Object.defineProperty(a,o,{value:l,enumerable:!0,configurable:!0,writable:!0}):a[o]=l;var h=n(2615);class f extends c.Z{init(){this.bindPriceRequestButtonCart(),this.bindPriceRequestButtonOffcanvasCart(),this.bindPriceRequestButtonListing(),this.bindPriceRequestButtonDetail(),this.showSuccessError()}bindPriceRequestButtonCart(){if(null!=document.querySelector(this.options.requestButtonCart)){window.PluginManager.getPluginInstanceFromElement(document.querySelector(this.options.requestButtonCart),"AjaxModal").$emitter.subscribe("ajaxModalOpen",this.onRequestCartClickedModalOpen.bind(this))}}bindPriceRequestButtonOffcanvasCart(){if(null!==document.querySelector("[data-offcanvas-cart]")){window.PluginManager.getPluginInstanceFromElement(document.querySelector("[data-offcanvas-cart]"),"OffCanvasCart").$emitter.subscribe("offCanvasOpened",this.onOffcavasCartOpened.bind(this))}}onOffcavasCartOpened(){null!=document.querySelector(this.options.requestButtonOffcanvasCart)&&setTimeout(function(){window.PluginManager.initializePlugin("AjaxModal",this.options.requestButtonOffcanvasCart);window.PluginManager.getPluginInstanceFromElement(document.querySelector(this.options.requestButtonOffcanvasCart),"AjaxModal").$emitter.subscribe("ajaxModalOpen",this.onRequestOffcanvasCartClickedModalOpen.bind(this))}.bind(this),200)}bindPriceRequestButtonListing(){let e=document.querySelectorAll(this.options.requestButtonListing);for(const t of e){window.PluginManager.getPluginInstanceFromElement(t,"AjaxModal").$emitter.subscribe("ajaxModalOpen",this.onRequestListingClickedModalOpen.bind(this))}}bindPriceRequestButtonDetail(){if(null!=document.querySelector(this.options.requestButtonDetail)){window.PluginManager.getPluginInstanceFromElement(document.querySelector(this.options.requestButtonDetail),"AjaxModal").$emitter.subscribe("ajaxModalOpen",this.onRequestDetailClickedModalOpen.bind(this))}}setSourceUrl(){var e=document.querySelector(".nbpr_form_sourceurl");e&&(e.value=window.location.href);var t=document.querySelector(".nb_por_error_route");t&&(t.value=window.location.href+"?nb_por_success=0")}showSuccessError(){var e=window.location,t=new URL(e).searchParams.get("nb_por_success");let n=document.querySelector("#toast");1==t&&(n.classList.add("nb-alert-success"),n.classList.remove("nb-alert-failure"),this.showToast()),0==t&&(n.classList.add("nb-alert-failure"),n.classList.remove("nb-alert-success"),this.showToast())}onRequestCartClickedModalOpen(){this.setSourceUrl();var e=document.querySelector('input[name="nb_pricerequest_lineitems"]');document.querySelector('input[name="nb_pricerequest_form_lineitems"]').value=e.value,document.querySelector(".nb_pricerequest_form_lineitems_div").innerHTML=this.printablelineitems(e.value);document.body.querySelector(".js-offcanvas-close");document.querySelector(".nbpr_deletecart").value=1}onRequestOffcanvasCartClickedModalOpen(){this.setSourceUrl();for(var e=u.Z.querySelector(document.body,'.offcanvas-cart input[name="nb_pricerequest_lineitems"]'),t=document.querySelector('input[name="nb_pricerequest_form_lineitems"]'),n=JSON.parse(e.value),i=[],s=0;s<n.length;s++)i.push(n[s]);var r=JSON.stringify(i);t.value=r,document.querySelector(".nb_pricerequest_form_lineitems_div").innerHTML=this.printablelineitems(r),this.$emitter.publish("priceRequestFormWritten",{});document.querySelector(".js-offcanvas-close");h.Z.close(100),document.querySelector(".nbpr_deletecart").value=1}onRequestListingClickedModalOpen(e){this.setSourceUrl();var t=e.target.querySelector('input[name="nb_pricerequest_lineitems"]').value;document.querySelector('input[name="nb_pricerequest_form_lineitems"]').value=t,document.querySelector(".nb_pricerequest_form_lineitems_div").innerHTML=this.printablelineitems(t),document.querySelector(".nbpr_deletecart").value=0}onRequestDetailClickedModalOpen(){this.setSourceUrl();var e=document.querySelector('input[name="nb_pricerequest_lineitems"]'),t=document.querySelector('input[name="nb_pricerequest_form_lineitems"]'),n=document.querySelector(this.options.detailPageQuantitySelector);null==n&&(n=document.querySelector(this.options.detailPageQuantitySelectorCustomTemplate));var i=n.value;t&&(t.value=e.value.replace('"quantity":0','"quantity":'+i),document.querySelector(".nb_pricerequest_form_lineitems_div").innerHTML=this.printablelineitems(e.value.replace('"quantity":0','"quantity":'+i)),document.querySelector(".nbpr_deletecart").value=0)}showToast(){var e=document.getElementById("toast");clearTimeout(t),e.style.transform="translateX(0)";var t=setTimeout((()=>{e.style.transform="translateX(400px)"}),4e3)}closeToast(){document.getElementById("toast").style.transform="translateX(400px)"}printablelineitems(e){if(document.querySelector(this.options.quantityProducttitleSeperator))var t=document.querySelector(this.options.quantityProducttitleSeperator).value;for(var n="",i=JSON.parse(e),s=0;s<i.length;s++)if("*"==i[s].number)n+="<b>",n+=i[s].children[0].quantity,i[s].children[0].quantity>1&&null!=i[s].children[0].packUnitPlural?n+=" "+i[s].children[0].packUnitPlural+" ":1==i[s].children[0].quantity&&null!=i[s].children[0].packUnit?n+=" "+i[s].children[0].packUnit+" ":n+="x ",n+=i[s].children[0].name,""!=i[s].children[0].variant&&null!=i[s].variant&&void 0!==i[s].variant?(n+="</b> (",n+=i[s].children[0].variant,n+=") ("):n+="</b> (",n+=i[s].children[0].number,n+=")",n+="</br>";else if(n+="<b>",n+=i[s].quantity,i[s].quantity>1&&null!=i[s].packUnitPlural?n+=" "+i[s].packUnitPlural+" ":1==i[s].quantity&&null!=i[s].packUnit?n+=" "+i[s].packUnit+" ":n+=t,n+=i[s].name,""!=i[s].variant&&null!=i[s].variant&&void 0!==i[s].variant?(n+="</b> (",n+=i[s].variant,n+=") ("):n+="</b> (",n+=i[s].number,n+=")",n+="</br>",void 0!==i[s].children){n+="<ul>";for(var r=0;r<i[s].children.length;r++)n+="<li>",n+=i[s].children[r].quantity,i[s].children[r].quantity>1&&null!=i[s].children[r].packUnitPlural?n+=" "+i[s].children[r].packUnitPlural+" ":1==i[s].children[r].quantity&&null!=i[s].children[r].packUnit?n+=" "+i[s].children[r].packUnit+" ":n+="x ",n+=i[s].children[r].name,""!=i[s].children[r].variant&&null!=i[s].variant&&void 0!==i[s].variant?(n+=" (",n+=i[s].children[r].variant,n+=") ("):n+=" (",n+=i[s].children[r].number,n+=")",n+="</li>";n+="</ul>"}return n}}!function(e,t,n){(t=function(e){var t=function(e,t){if("object"!=typeof e||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var i=n.call(e,t||"default");if("object"!=typeof i)return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"==typeof t?t:String(t)}(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n}(f,"options",{requestButtonCart:".btn-nb-pricerequest-cart",requestButtonOffcanvasCart:".btn-nb-pricerequest-offcanvascart",requestButtonListing:".btn-nb-pricerequest-listing",requestButtonDetail:".btn-nb-pricerequest-detail",lineItemsSource:'input[name="nb_pricerequest_lineitems"]',lineItemsDest:'input[name="nb_pricerequest_form_lineitems"]',quantityProducttitleSeperator:'input[name="nb_priceonrequest_quantity_producttitle_seperator"]',detailPageQuantitySelector:".product-detail-quantity-select,.product-detail-quantity-input",detailPageQuantitySelectorCustomTemplate:".quantity-selector-group-input"});var m=n(8254);class v extends c.Z{init(){this._client=new m.Z,this._getButton(),this._getHiddenSubmit(),this._registerEvents(),this._getCmsBlock(),this._getConfirmationText()}sendAjaxFormSubmit(){const{_client:e,el:t,options:n}=this,i=new FormData(t);e.post(t.action,i,this._handleResponse.bind(this),n.contentType)}_registerEvents(){this.el.addEventListener("submit",this._handleSubmit.bind(this)),this._button&&(this._button.addEventListener("submit",this._handleSubmit.bind(this)),this._button.addEventListener("click",this._handleSubmit.bind(this)))}_getConfirmationText(){const e=this.el.querySelector('input[name="confirmationText"]');e&&(this._confirmationText=e.value)}_getButton(){this._button=this.el.querySelector("button")}_getCmsBlock(){this._block=this.el.closest(this.options.cmsBlock)}_getHiddenSubmit(){this._hiddenSubmit=this.el.querySelector(this.options.hiddenSubmitSelector)}_handleSubmit(e){e.preventDefault(),this.el.checkValidity()?this._submitForm():this._showValidation()}_showValidation(){this._hiddenSubmit.click()}_submitForm(){this.$emitter.publish("beforeSubmit"),this.sendAjaxFormSubmit(),this.el.classList.contains("contact-form")&&this._validateForm()&&this._showLoadingComponent()}_showLoadingComponent(){const e=this.el.querySelector(".form-content"),t=this.el.querySelector(".loading-component-container");this.el.classList.add("is-loading"),e&&t&&(e.classList.add("d-none"),t.classList.remove("d-none"));const n=this._block.querySelector(".confirm-alert");n&&n.remove()}_hideLoadingComponent(){const e=this.el.querySelector(".form-content"),t=this.el.querySelector(".loading-component-container");this.el.classList.remove("is-loading"),e&&t&&(e.classList.remove("d-none"),t.classList.add("d-none"))}_handleResponse(e){const t=JSON.parse(e);if(this.$emitter.publish("onFormResponse",e),t.length>0){let e=!0,n="";for(let i=0;i<t.length;i+=1)"danger"!==t[i].type&&"info"!==t[i].type||(e=!1),n+=t[i].alert;!e&&this.el.classList.contains("contact-form")&&this._hideLoadingComponent(),this._createResponse(e,n)}else window.location.reload()}_createResponse(e,t){if(e)this._confirmationText&&(t=this._confirmationText),this._block.innerHTML=`<div class="confirm-message">${t}</div>`;else{const e=this._block.querySelector(".confirm-alert");e&&e.remove();const n=`<div class="confirm-alert">${t}</div>`;this._block.insertAdjacentHTML("beforeend",n)}this._block.scrollIntoView({behavior:"smooth",block:"end"})}_validateForm(){const e=this.el,t=["email","subject","comment","company","country","state","city","zip","street","firstName","lastName"];for(var n=0;n<t.length;n++){var i=t[n],s=e.querySelector('[name="'+i+'"]');if(!s)return!1;if("checkbox"===s.type){if(!s.checked)return!1}else if(""===s.value.trim())return!1}return!0}}!function(e,t,n){(t=function(e){var t=function(e,t){if("object"!=typeof e||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var i=n.call(e,t||"default");if("object"!=typeof i)return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"==typeof t?t:String(t)}(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n}(v,"options",{hiddenClass:"d-none",hiddenSubmitSelector:".submit--hidden",formContentSelector:".form-content",cmsBlock:".cms-block",contentType:"application/x-www-form-urlencoded"});const p=window.PluginManager;p.override("OffcanvasMenu",r,"[data-offcanvas-menu]"),p.override("PriceRequestsWindow",f,"body"),p.register("PriceRequestSubmitLoader",d,"[data-price-request-submit-loader]"),p.override("FormCmsHandler",v,".cms-element-form form")},3175:(e,t,n)=>{n.d(t,{Z:()=>h});var i,s,r,a=n(6285),o=n(3637),l=n(7906),c=n(8254),u=n(3206),d=n(1966);class h extends a.Z{init(){this._cache={},this._client=new c.Z,this._content=l.Z.getTemplate(),this._registerEvents()}_registerEvents(){if(this.el.removeEventListener(this.options.tiggerEvent,this._getLinkEventHandler.bind(this)),this.el.addEventListener(this.options.tiggerEvent,this._getLinkEventHandler.bind(this)),o.Z.exists()){const e=o.Z.getOffCanvas();d.Z.iterate(e,(e=>{const t=e.querySelectorAll(this.options.linkSelector);d.Z.iterate(t,(e=>{h._resetLoader(e),e.addEventListener("click",(t=>{this._getLinkEventHandler(t,e)}))}))}))}}_openMenu(e){h._stopEvent(e),o.Z.open(this._content,this._registerEvents.bind(this),this.options.position),o.Z.setAdditionalClassName(this.options.additionalOffcanvasClass),this.$emitter.publish("openMenu")}_getLinkEventHandler(e,t){if(!t){const t=u.Z.querySelector(document,this.options.initialContentSelector);return this._content=t.innerHTML,t.classList.contains("is-root")?this._cache[this.options.navigationUrl]=this._content:this._fetchMenu(this.options.navigationUrl),this._openMenu(e)}if(h._stopEvent(e),t.classList.contains(this.options.linkLoadingClass))return;h._setLoader(t);const n=u.Z.getAttribute(t,"data-href",!1)||u.Z.getAttribute(t,"href",!1);if(!n)return;let i=this.options.forwardAnimationType;(t.classList.contains(this.options.homeBtnClass)||t.classList.contains(this.options.backBtnClass))&&(i=this.options.backwardAnimationType),this.$emitter.publish("getLinkEventHandler"),this._fetchMenu(n,this._updateOverlay.bind(this,i))}static _setLoader(e){e.classList.add(this.options.linkLoadingClass);const t=e.querySelector(this.options.loadingIconSelector);t&&(t._linkIcon=t.innerHTML,t.innerHTML=l.Z.getTemplate())}static _resetLoader(e){e.classList.remove(this.options.linkLoadingClass);const t=e.querySelector(this.options.loadingIconSelector);t&&t._linkIcon&&(t.innerHTML=t._linkIcon)}_updateOverlay(e,t){if(this._content=t,o.Z.exists()){const n=h._getOffcanvasMenu();n||this._replaceOffcanvasContent(t),this._createOverlayElements();const i=h._getOverlayContent(n),s=h._getMenuContentFromResponse(t);this._replaceOffcanvasMenuContent(e,s,i),this._registerEvents()}this.$emitter.publish("updateOverlay")}_replaceOffcanvasMenuContent(e,t,n){e!==this.options.forwardAnimationType?e!==this.options.backwardAnimationType?(this._animateInstant(t,n),this.$emitter.publish("replaceOffcanvasMenuContent")):this._animateBackward(t,n):this._animateForward(t,n)}_animateInstant(e){this._overlay.innerHTML=e,this.$emitter.publish("animateInstant")}_animateForward(e,t){""===this._placeholder.innerHTML&&(this._placeholder.innerHTML=t),this._overlay.classList.remove(this.options.transitionClass),this._overlay.style.left="100%",this._overlay.innerHTML=e,setTimeout((()=>{this._overlay.classList.add(this.options.transitionClass),this._overlay.style.left="0%"}),1),this.$emitter.publish("animateForward")}_animateBackward(e,t){""===this._overlay.innerHTML&&(this._overlay.innerHTML=t),this._placeholder.innerHTML=e,this._overlay.classList.remove(this.options.transitionClass),this._overlay.style.left="0%",setTimeout((()=>{this._overlay.classList.add(this.options.transitionClass),this._overlay.style.left="100%"}),1),this.$emitter.publish("animateBackward")}static _getMenuContentFromResponse(e){const t=(new DOMParser).parseFromString(e,"text/html");return h._getOverlayContent(t)}static _getOverlayContent(e){if(!e)return"";const t=e.querySelector(this.options.overlayContentSelector);return t?t.innerHTML:""}_createOverlayElements(){const e=h._getOffcanvasMenu();e&&(this._placeholder=h._createPlaceholder(e),this._overlay=h._createNavigationOverlay(e)),this.$emitter.publish("createOverlayElements")}static _createNavigationOverlay(e){const t=h._getOffcanvas(),n=t.querySelector(this.options.overlayClass);if(n)return n;const i=document.createElement("div");return i.classList.add(this.options.overlayClass.substr(1)),i.style.minHeight=`${t.clientHeight}px`,e.appendChild(i),i}static _createPlaceholder(e){const t=h._getOffcanvas(),n=t.querySelector(this.options.placeholderClass);if(n)return n;const i=document.createElement("div");return i.classList.add(this.options.placeholderClass.substr(1)),i.style.minHeight=`${t.clientHeight}px`,e.appendChild(i),i}_fetchMenu(e,t){return!!e&&(this._cache[e]&&"function"==typeof t?t(this._cache[e]):(this.$emitter.publish("beforeFetchMenu"),void this._client.get(e,(n=>{this._cache[e]=n,"function"==typeof t&&t(n)}))))}_replaceOffcanvasContent(e){this._content=e,o.Z.setContent(this._content),this._registerEvents(),this.$emitter.publish("replaceOffcanvasContent")}static _stopEvent(e){e.preventDefault(),e.stopImmediatePropagation()}static _getOffcanvas(){return o.Z.getOffCanvas()[0]}static _getOffcanvasMenu(){return h._getOffcanvas().querySelector(this.options.menuSelector)}}i=h,s="options",r={navigationUrl:window.router["frontend.menu.offcanvas"],position:"left",tiggerEvent:"click",additionalOffcanvasClass:"navigation-offcanvas",linkSelector:".js-navigation-offcanvas-link",loadingIconSelector:".js-navigation-offcanvas-loading-icon",linkLoadingClass:"is-loading",menuSelector:".js-navigation-offcanvas",overlayContentSelector:".js-navigation-offcanvas-overlay-content",initialContentSelector:".js-navigation-offcanvas-initial-content",homeBtnClass:"is-home-link",backBtnClass:"is-back-link",transitionClass:"has-transition",overlayClass:".navigation-offcanvas-overlay",placeholderClass:".navigation-offcanvas-placeholder",forwardAnimationType:"forwards",backwardAnimationType:"backwards"},(s=function(e){var t=function(e,t){if("object"!=typeof e||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var i=n.call(e,t||"default");if("object"!=typeof i)return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"==typeof t?t:String(t)}(s))in i?Object.defineProperty(i,s,{value:r,enumerable:!0,configurable:!0,writable:!0}):i[s]=r},2615:(e,t,n)=>{n.d(t,{Z:()=>o});var i=n(3637),s=n(8254),r=n(7906);let a=null;class o extends i.Z{static open(e=!1,t=!1,n=null,s="left",r=!0,a=i.Z.REMOVE_OFF_CANVAS_DELAY(),o=!1,l=""){if(!e)throw new Error("A url must be given!");i.r._removeExistingOffCanvas();const c=i.r._createOffCanvas(s,o,l,r);this.setContent(e,t,n,r,a),i.r._openOffcanvas(c)}static setContent(e,t,n,i,l){const c=new s.Z;super.setContent(`<div class="offcanvas-body">${r.Z.getTemplate()}</div>`,i,l),a&&a.abort();const u=e=>{super.setContent(e,i,l),"function"==typeof n&&n(e)};a=t?c.post(e,t,o.executeCallback.bind(this,u)):c.get(e,o.executeCallback.bind(this,u))}static executeCallback(e,t){"function"==typeof e&&e(t),window.PluginManager.initializePlugins()}}},3637:(e,t,n)=>{n.d(t,{Z:()=>u,r:()=>c});var i=n(9658),s=n(2005),r=n(1966);const a="offcanvas",o=350;class l{constructor(){this.$emitter=new s.Z}open(e,t,n,i,s,r,a){this._removeExistingOffCanvas();const o=this._createOffCanvas(n,r,a,i);this.setContent(e,i,s),this._openOffcanvas(o,t)}setContent(e,t,n){const i=this.getOffCanvas();i[0]&&(i[0].innerHTML=e,this._registerEvents(n))}setAdditionalClassName(e){this.getOffCanvas()[0].classList.add(e)}getOffCanvas(){return document.querySelectorAll(`.${a}`)}close(e){const t=this.getOffCanvas();r.Z.iterate(t,(e=>{bootstrap.Offcanvas.getInstance(e).hide()})),setTimeout((()=>{this.$emitter.publish("onCloseOffcanvas",{offCanvasContent:t})}),e)}goBackInHistory(){window.history.back()}exists(){return this.getOffCanvas().length>0}_openOffcanvas(e,t){l.bsOffcanvas.show(),window.history.pushState("offcanvas-open",""),"function"==typeof t&&t()}_registerEvents(e){const t=i.Z.isTouchDevice()?"touchend":"click",n=this.getOffCanvas();r.Z.iterate(n,(t=>{const i=()=>{setTimeout((()=>{t.remove(),this.$emitter.publish("onCloseOffcanvas",{offCanvasContent:n})}),e),t.removeEventListener("hide.bs.offcanvas",i)};t.addEventListener("hide.bs.offcanvas",i)})),window.addEventListener("popstate",this.close.bind(this,e),{once:!0});const s=document.querySelectorAll(".js-offcanvas-close");r.Z.iterate(s,(n=>n.addEventListener(t,this.close.bind(this,e))))}_removeExistingOffCanvas(){l.bsOffcanvas=null;const e=this.getOffCanvas();return r.Z.iterate(e,(e=>e.remove()))}_getPositionClass(e){return"left"===e?"offcanvas-start":"right"===e?"offcanvas-end":`offcanvas-${e}`}_createOffCanvas(e,t,n,i){const s=document.createElement("div");if(s.classList.add(a),s.classList.add(this._getPositionClass(e)),!0===t&&s.classList.add("is-fullwidth"),n){const e=typeof n;if("string"===e)s.classList.add(n);else{if(!Array.isArray(n))throw new Error(`The type "${e}" is not supported. Please pass an array or a string.`);n.forEach((e=>{s.classList.add(e)}))}}return document.body.appendChild(s),l.bsOffcanvas=new bootstrap.Offcanvas(s,{backdrop:!1!==i||"static"}),s}}const c=Object.freeze(new l);class u{static open(e,t=null,n="left",i=!0,s=350,r=!1,a=""){c.open(e,t,n,i,s,r,a)}static setContent(e,t=!0,n=350){c.setContent(e,t,n)}static setAdditionalClassName(e){c.setAdditionalClassName(e)}static close(e=350){c.close(e)}static exists(){return c.exists()}static getOffCanvas(){return c.getOffCanvas()}static REMOVE_OFF_CANVAS_DELAY(){return o}}}},e=>{e.O(0,["vendor-node","vendor-shared"],(()=>{return t=4948,e(e.s=t);var t}));e.O()}]);