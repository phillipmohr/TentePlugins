!function(e){function n(n){for(var t,r,i=n[0],s=n[1],o=0,l=[];o<i.length;o++)r=i[o],Object.prototype.hasOwnProperty.call(a,r)&&a[r]&&l.push(a[r][0]),a[r]=0;for(t in s)Object.prototype.hasOwnProperty.call(s,t)&&(e[t]=s[t]);for(d&&d(n);l.length;)l.shift()()}var t={},r={"nwgncy-product-finder":0},a={"nwgncy-product-finder":0};function i(n){if(t[n])return t[n].exports;var r=t[n]={i:n,l:!1,exports:{}};return e[n].call(r.exports,r,r.exports,i),r.l=!0,r.exports}i.e=function(e){var n=[];r[e]?n.push(r[e]):0!==r[e]&&{0:1,1:1,2:1}[e]&&n.push(r[e]=new Promise((function(n,t){for(var a="static/css/"+({}[e]||e)+".css",s=i.p+a,o=document.getElementsByTagName("link"),l=0;l<o.length;l++){var d=(p=o[l]).getAttribute("data-href")||p.getAttribute("href");if("stylesheet"===p.rel&&(d===a||d===s))return n()}var c=document.getElementsByTagName("style");for(l=0;l<c.length;l++){var p;if((d=(p=c[l]).getAttribute("data-href"))===a||d===s)return n()}var u=document.createElement("link");u.rel="stylesheet",u.type="text/css";u.onerror=u.onload=function(a){if(u.onerror=u.onload=null,"load"===a.type)n();else{var i=a&&("load"===a.type?"missing":a.type),o=a&&a.target&&a.target.href||s,l=new Error("Loading CSS chunk "+e+" failed.\n("+o+")");l.code="CSS_CHUNK_LOAD_FAILED",l.type=i,l.request=o,delete r[e],u.parentNode.removeChild(u),t(l)}},u.href=s,document.head.appendChild(u)})).then((function(){r[e]=0})));var t=a[e];if(0!==t)if(t)n.push(t[2]);else{var s=new Promise((function(n,r){t=a[e]=[n,r]}));n.push(t[2]=s);var o,l=document.createElement("script");l.charset="utf-8",l.timeout=120,i.nc&&l.setAttribute("nonce",i.nc),l.src=function(e){return i.p+"static/js/"+({}[e]||e)+".js"}(e);var d=new Error;o=function(n){l.onerror=l.onload=null,clearTimeout(c);var t=a[e];if(0!==t){if(t){var r=n&&("load"===n.type?"missing":n.type),i=n&&n.target&&n.target.src;d.message="Loading chunk "+e+" failed.\n("+r+": "+i+")",d.name="ChunkLoadError",d.type=r,d.request=i,t[1](d)}a[e]=void 0}};var c=setTimeout((function(){o({type:"timeout",target:l})}),12e4);l.onerror=l.onload=o,document.head.appendChild(l)}return Promise.all(n)},i.m=e,i.c=t,i.d=function(e,n,t){i.o(e,n)||Object.defineProperty(e,n,{enumerable:!0,get:t})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,n){if(1&n&&(e=i(e)),8&n)return e;if(4&n&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(i.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&n&&"string"!=typeof e)for(var r in e)i.d(t,r,function(n){return e[n]}.bind(null,r));return t},i.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(n,"a",n),n},i.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},i.p=(window.__sw__.assetPath + '/bundles/nwgncyproductfinder/'),i.oe=function(e){throw console.error(e),e};var s=this["webpackJsonpPluginnwgncy-product-finder"]=this["webpackJsonpPluginnwgncy-product-finder"]||[],o=s.push.bind(s);s.push=n,s=s.slice();for(var l=0;l<s.length;l++)n(s[l]);var d=o;i(i.s="GQIw")}({"+r+i":function(e,n,t){},ALPZ:function(e,n,t){var r=t("+r+i");r.__esModule&&(r=r.default),"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals);(0,t("P8hj").default)("be14eee2",r,!0,{})},AkNJ:function(e,n,t){},GQIw:function(e,n,t){"use strict";t.r(n);t("ALPZ");Shopware.Component.register("sw-cms-block-nwgncy-product-finder",{template:'{% block sw_cms_block_nwgncy_product_finder %}\n    <div class="sw-cms-block-nwgncy-product-finder">\n        <slot name="productFinder">\n        </slot>\n    </div>\n{% endblock %}'});t("Iqtk");Shopware.Component.register("sw-cms-preview-nwgncy-product-finder",{template:'\n{% block sw_cms_element_nwgncy_product_finder_preview %}\n<div class="cms-section  pos-0 cms-section-default" style="">\n     <div class="cms-section-default full-width">\n          <div class="cms-block  pos-0 cms-block-nwgncy-product-finder-small-preview" style="">\n               <div class="cms-block-container" >\n                    <div class="cms-block-container-row row cms-row ">\n                         <div class="container">\n                              <div class="content">\n                                   <h3>Quick-find your products</h3>\n                                   <form id="product-finder-form" name="product-finder">\n                                        <div class="finder-row">\n                                             <div class="section categories">\n                                                  <div class="field js-product-finder-category-field active">\n                                                       <label class="category-label">Category 1</label>\n                                                       <input name="finder-category" type="radio" >\n                                                  </div>\n                                                  <div class="field js-product-finder-category-field">\n                                                       <label class="category-label">Category 2</label>\n                                                       <input name="finder-category" type="radio">\n                                                  </div>\n                                             </div>\n                                             <div class="properties-container">\n                                                  <div class="loading" style="display: none;">\n                                                       <div class="loader"></div>\n                                                  </div>\n                                                  <div class="section property-options" style="display: flex;">\n\n\n                                                       <div class="field js-product-finder-property-option-group-field" style="display: flex;">\n                                                            <label class="property-group-label">\n                                                                 <span>\n                                                                      <span class="js-property-group-label-name">Property group 1</span>\n                                                                      <span class="icon icon-arrow-medium-down icon-xs">\n                                                                           <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewbox="0 0 16 16">\n                                                                                <use xlink:href="#icons-solid-arrow-medium-down" fill="#758CA3" fill-rule="evenodd"></use>\n                                                                           </svg>\n                                                                      </span>\n                                                                 </span>\n                                                            </label>\n                                                            <div class="dropdown-content">\n                                                                 <div class="options-list">\n                                                                      <div class="js-option-field">\n                                                                           <label class="property-group-option-label">-</label>\n                                                                           <input class="js-finder-property-option-input" data-option-name="-" type="radio">\n                                                                      </div>\n                                                                      <div class="js-option-field">\n                                                                           <label class="property-group-option-label">-</label>\n                                                                           <input class="js-finder-property-option-input" data-option-name="-" type="radio">\n                                                                      </div>\n                                                                 </div>\n                                                            </div>\n                                                       </div>\n\n                                                       <div class="field js-product-finder-property-option-group-field js-product-finder-property-option-group-slider-field" style="display: flex;">\n                                                            <label class="property-group-label">\n                                                                 <span>Property group 2\n                                                                      <span class="icon icon-arrow-medium-down icon-xs">\n                                                                           <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewbox="0 0 16 16">\n                                                                                <use xlink:href="#icons-solid-arrow-medium-down" fill="#758CA3" fill-rule="evenodd"></use>\n                                                                           </svg>\n                                                                      </span>\n                                                                 </span>\n                                                            </label>\n                                                            <div class="dropdown-content slider">\n                                                                 <div class="min-max">\n                                                                      <div class="nwgncy-finder-slider-values">\n                                                                           <div class="nwgncy-finder-slider-range1" style="order: 1;">-</div>\n                                                                           <div class="nwgncy-finder-slider-dash" style="order: 2;">\n                                                                                ‐\n                                                                           </div>\n                                                                           <div class="nwgncy-finder-slider-range2" style="order: 3;">-</div>\n                                                                           <div class="nwgncy-finder-slider-unit">\n                                                                                unit\n                                                                           </div>\n                                                                      </div>\n                                                                      <div class="nwgncy-finder-slider-container">\n                                                                           <div class="nwgncy-finder-slider-track"></div>\n                                                                           <input type="range" min="0" max="21" value="0" class="nwgncy-finder-slider-1" step="1">\n                                                                           <input type="range" min="0" max="21" value="21" class="nwgncy-finder-slider-2" step="1">\n\n                                                                           <div class="nwgncy-finder-slider-buttons">\n                                                                                <span data-value="0"></span>\n                                                                                <span data-value="1"></span>\n                                                                                <span data-value="2"></span>\n                                                                                <span data-value="3"></span>\n                                                                                <span data-value="4"></span>\n                                                                                <span data-value="5"></span>\n                                                                                <span data-value="6"></span>\n                                                                                <span data-value="7"></span>\n                                                                                <span data-value="8"></span>\n                                                                                <span data-value="9"></span>\n                                                                                <span data-value="10"></span>\n                                                                                <span data-value="11"></span>\n                                                                                <span data-value="12"></span>\n                                                                                <span data-value="13"></span>\n                                                                                <span data-value="14"></span>\n                                                                                <span data-value="15"></span>\n                                                                                <span data-value="16"></span>\n                                                                                <span data-value="17"></span>\n                                                                                <span data-value="18"></span>\n                                                                                <span data-value="19"></span>\n                                                                                <span data-value="20"></span>\n                                                                                <span data-value="21"></span>\n                                                                           </div>\n\n                                                                           <datalist class="nwgncy-finder-slider-number">\n                                                                                <option value="-"></option>\n                                                                                <option value="-"></option>\n                                                                           </datalist>\n                                                                      </div>\n                                                                 </div>\n                                                            </div>\n                                                       </div>\n                                                  </div>\n                                             </div>\n                                             <button class="btn btn-primary js-btn-find-products" type="button" data-target-lang="en-GB">\n                                                  <span class="btn-label">Find Products</span>\n                                             </button>\n                                        </div>\n                                   </form>\n                              </div>\n                         </div>\n                    </div>\n               </div>\n          </div>\n     </div>\n</div>\n{% endblock %}\n'}),Shopware.Service("cmsService").registerCmsBlock({name:"nwgncy-product-finder",category:"commerce",label:"Newwwagency Product Finder",component:"sw-cms-block-nwgncy-product-finder",previewComponent:"sw-cms-preview-nwgncy-product-finder",defaultConfig:{marginBottom:"0",marginTop:"0",marginLeft:"0",marginRight:"0",sizingMode:"full_width"},slots:{productFinder:"nwgncy-product-finder"}});t("rR5t")},Iqtk:function(e,n,t){var r=t("AkNJ");r.__esModule&&(r=r.default),"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals);(0,t("P8hj").default)("7b57c5b8",r,!0,{})},P8hj:function(e,n,t){"use strict";function r(e,n){for(var t=[],r={},a=0;a<n.length;a++){var i=n[a],s=i[0],o={id:e+":"+a,css:i[1],media:i[2],sourceMap:i[3]};r[s]?r[s].parts.push(o):t.push(r[s]={id:s,parts:[o]})}return t}t.r(n),t.d(n,"default",(function(){return v}));var a="undefined"!=typeof document;if("undefined"!=typeof DEBUG&&DEBUG&&!a)throw new Error("vue-style-loader cannot be used in a non-browser environment. Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");var i={},s=a&&(document.head||document.getElementsByTagName("head")[0]),o=null,l=0,d=!1,c=function(){},p=null,u="data-vue-ssr-id",f="undefined"!=typeof navigator&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());function v(e,n,t,a){d=t,p=a||{};var s=r(e,n);return g(s),function(n){for(var t=[],a=0;a<s.length;a++){var o=s[a];(l=i[o.id]).refs--,t.push(l)}n?g(s=r(e,n)):s=[];for(a=0;a<t.length;a++){var l;if(0===(l=t[a]).refs){for(var d=0;d<l.parts.length;d++)l.parts[d]();delete i[l.id]}}}}function g(e){for(var n=0;n<e.length;n++){var t=e[n],r=i[t.id];if(r){r.refs++;for(var a=0;a<r.parts.length;a++)r.parts[a](t.parts[a]);for(;a<t.parts.length;a++)r.parts.push(y(t.parts[a]));r.parts.length>t.parts.length&&(r.parts.length=t.parts.length)}else{var s=[];for(a=0;a<t.parts.length;a++)s.push(y(t.parts[a]));i[t.id]={id:t.id,refs:1,parts:s}}}}function m(){var e=document.createElement("style");return e.type="text/css",s.appendChild(e),e}function y(e){var n,t,r=document.querySelector("style["+u+'~="'+e.id+'"]');if(r){if(d)return c;r.parentNode.removeChild(r)}if(f){var a=l++;r=o||(o=m()),n=b.bind(null,r,a,!1),t=b.bind(null,r,a,!0)}else r=m(),n=C.bind(null,r),t=function(){r.parentNode.removeChild(r)};return n(e),function(r){if(r){if(r.css===e.css&&r.media===e.media&&r.sourceMap===e.sourceMap)return;n(e=r)}else t()}}var w,h=(w=[],function(e,n){return w[e]=n,w.filter(Boolean).join("\n")});function b(e,n,t,r){var a=t?"":r.css;if(e.styleSheet)e.styleSheet.cssText=h(n,a);else{var i=document.createTextNode(a),s=e.childNodes;s[n]&&e.removeChild(s[n]),s.length?e.insertBefore(i,s[n]):e.appendChild(i)}}function C(e,n){var t=n.css,r=n.media,a=n.sourceMap;if(r&&e.setAttribute("media",r),p.ssrId&&e.setAttribute(u,n.id),a&&(t+="\n/*# sourceURL="+a.sources[0]+" */",t+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(a))))+" */"),e.styleSheet)e.styleSheet.cssText=t;else{for(;e.firstChild;)e.removeChild(e.firstChild);e.appendChild(document.createTextNode(t))}}},rR5t:function(e,n,t){Shopware.Component.register("sw-cms-el-preview-nwgncy-product-finder",(function(){return t.e(2).then(t.bind(null,"7WCU"))})),Shopware.Component.register("sw-cms-el-config-nwgncy-product-finder",(function(){return t.e(1).then(t.bind(null,"vIeu"))})),Shopware.Component.register("sw-cms-el-nwgncy-product-finder",(function(){return t.e(0).then(t.bind(null,"21ov"))}));var r=Shopware.Data.Criteria,a=new r(1,1e3),i=new r(1,1e3);Shopware.Service("cmsService").registerCmsElement({name:"nwgncy-product-finder",label:"sw-cms.elements.productSlider.label",component:"sw-cms-el-nwgncy-product-finder",configComponent:"sw-cms-el-config-nwgncy-product-finder",previewComponent:"sw-cms-el-preview-nwgncy-product-finder",defaultConfig:{categoryPropertyGroupOptions:{source:"static",value:[],required:!0,entity:{name:"property_group_option",criteria:i}},propertyGroups:{source:"static",value:[],required:!1,entity:{name:"property_group",criteria:a}},measuredPropertyGroups:{source:"static",value:[],required:!1,entity:{name:"property_group",criteria:a}}},collect:Shopware.Service("cmsService").getCollectFunction()})}});