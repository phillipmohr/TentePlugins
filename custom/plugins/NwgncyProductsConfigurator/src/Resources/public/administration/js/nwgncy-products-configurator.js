!function(e){function n(n){for(var t,r,s=n[0],i=n[1],a=0,c=[];a<s.length;a++)r=s[a],Object.prototype.hasOwnProperty.call(o,r)&&o[r]&&c.push(o[r][0]),o[r]=0;for(t in i)Object.prototype.hasOwnProperty.call(i,t)&&(e[t]=i[t]);for(l&&l(n);c.length;)c.shift()()}var t={},r={"nwgncy-products-configurator":0},o={"nwgncy-products-configurator":0};function s(n){if(t[n])return t[n].exports;var r=t[n]={i:n,l:!1,exports:{}};return e[n].call(r.exports,r,r.exports,s),r.l=!0,r.exports}s.e=function(e){var n=[];r[e]?n.push(r[e]):0!==r[e]&&{0:1,1:1,2:1}[e]&&n.push(r[e]=new Promise((function(n,t){for(var o="static/css/"+({}[e]||e)+".css",i=s.p+o,a=document.getElementsByTagName("link"),c=0;c<a.length;c++){var l=(p=a[c]).getAttribute("data-href")||p.getAttribute("href");if("stylesheet"===p.rel&&(l===o||l===i))return n()}var u=document.getElementsByTagName("style");for(c=0;c<u.length;c++){var p;if((l=(p=u[c]).getAttribute("data-href"))===o||l===i)return n()}var d=document.createElement("link");d.rel="stylesheet",d.type="text/css";d.onerror=d.onload=function(o){if(d.onerror=d.onload=null,"load"===o.type)n();else{var s=o&&("load"===o.type?"missing":o.type),a=o&&o.target&&o.target.href||i,c=new Error("Loading CSS chunk "+e+" failed.\n("+a+")");c.code="CSS_CHUNK_LOAD_FAILED",c.type=s,c.request=a,delete r[e],d.parentNode.removeChild(d),t(c)}},d.href=i,document.head.appendChild(d)})).then((function(){r[e]=0})));var t=o[e];if(0!==t)if(t)n.push(t[2]);else{var i=new Promise((function(n,r){t=o[e]=[n,r]}));n.push(t[2]=i);var a,c=document.createElement("script");c.charset="utf-8",c.timeout=120,s.nc&&c.setAttribute("nonce",s.nc),c.src=function(e){return s.p+"static/js/"+({}[e]||e)+".js"}(e);var l=new Error;a=function(n){c.onerror=c.onload=null,clearTimeout(u);var t=o[e];if(0!==t){if(t){var r=n&&("load"===n.type?"missing":n.type),s=n&&n.target&&n.target.src;l.message="Loading chunk "+e+" failed.\n("+r+": "+s+")",l.name="ChunkLoadError",l.type=r,l.request=s,t[1](l)}o[e]=void 0}};var u=setTimeout((function(){a({type:"timeout",target:c})}),12e4);c.onerror=c.onload=a,document.head.appendChild(c)}return Promise.all(n)},s.m=e,s.c=t,s.d=function(e,n,t){s.o(e,n)||Object.defineProperty(e,n,{enumerable:!0,get:t})},s.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},s.t=function(e,n){if(1&n&&(e=s(e)),8&n)return e;if(4&n&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(s.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&n&&"string"!=typeof e)for(var r in e)s.d(t,r,function(n){return e[n]}.bind(null,r));return t},s.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return s.d(n,"a",n),n},s.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},s.p=(window.__sw__.assetPath + '/bundles/nwgncyproductsconfigurator/'),s.oe=function(e){throw console.error(e),e};var i=this["webpackJsonpPluginnwgncy-products-configurator"]=this["webpackJsonpPluginnwgncy-products-configurator"]||[],a=i.push.bind(i);i.push=n,i=i.slice();for(var c=0;c<i.length;c++)n(i[c]);var l=a;s(s.s="WkLe")}({MsQ7:function(e,n,t){var r=t("k/o5");r.__esModule&&(r=r.default),"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals);(0,t("P8hj").default)("585a021e",r,!0,{})},NG4b:function(e,n,t){var r=t("x1Gm");r.__esModule&&(r=r.default),"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals);(0,t("P8hj").default)("58e33169",r,!0,{})},P8hj:function(e,n,t){"use strict";function r(e,n){for(var t=[],r={},o=0;o<n.length;o++){var s=n[o],i=s[0],a={id:e+":"+o,css:s[1],media:s[2],sourceMap:s[3]};r[i]?r[i].parts.push(a):t.push(r[i]={id:i,parts:[a]})}return t}t.r(n),t.d(n,"default",(function(){return g}));var o="undefined"!=typeof document;if("undefined"!=typeof DEBUG&&DEBUG&&!o)throw new Error("vue-style-loader cannot be used in a non-browser environment. Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");var s={},i=o&&(document.head||document.getElementsByTagName("head")[0]),a=null,c=0,l=!1,u=function(){},p=null,d="data-vue-ssr-id",f="undefined"!=typeof navigator&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());function g(e,n,t,o){l=t,p=o||{};var i=r(e,n);return m(i),function(n){for(var t=[],o=0;o<i.length;o++){var a=i[o];(c=s[a.id]).refs--,t.push(c)}n?m(i=r(e,n)):i=[];for(o=0;o<t.length;o++){var c;if(0===(c=t[o]).refs){for(var l=0;l<c.parts.length;l++)c.parts[l]();delete s[c.id]}}}}function m(e){for(var n=0;n<e.length;n++){var t=e[n],r=s[t.id];if(r){r.refs++;for(var o=0;o<r.parts.length;o++)r.parts[o](t.parts[o]);for(;o<t.parts.length;o++)r.parts.push(h(t.parts[o]));r.parts.length>t.parts.length&&(r.parts.length=t.parts.length)}else{var i=[];for(o=0;o<t.parts.length;o++)i.push(h(t.parts[o]));s[t.id]={id:t.id,refs:1,parts:i}}}}function v(){var e=document.createElement("style");return e.type="text/css",i.appendChild(e),e}function h(e){var n,t,r=document.querySelector("style["+d+'~="'+e.id+'"]');if(r){if(l)return u;r.parentNode.removeChild(r)}if(f){var o=c++;r=a||(a=v()),n=w.bind(null,r,o,!1),t=w.bind(null,r,o,!0)}else r=v(),n=x.bind(null,r),t=function(){r.parentNode.removeChild(r)};return n(e),function(r){if(r){if(r.css===e.css&&r.media===e.media&&r.sourceMap===e.sourceMap)return;n(e=r)}else t()}}var y,b=(y=[],function(e,n){return y[e]=n,y.filter(Boolean).join("\n")});function w(e,n,t,r){var o=t?"":r.css;if(e.styleSheet)e.styleSheet.cssText=b(n,o);else{var s=document.createTextNode(o),i=e.childNodes;i[n]&&e.removeChild(i[n]),i.length?e.insertBefore(s,i[n]):e.appendChild(s)}}function x(e,n){var t=n.css,r=n.media,o=n.sourceMap;if(r&&e.setAttribute("media",r),p.ssrId&&e.setAttribute(d,n.id),o&&(t+="\n/*# sourceURL="+o.sources[0]+" */",t+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(o))))+" */"),e.styleSheet)e.styleSheet.cssText=t;else{for(;e.firstChild;)e.removeChild(e.firstChild);e.appendChild(document.createTextNode(t))}}},WkLe:function(e,n,t){"use strict";t.r(n);t("NG4b");Shopware.Component.register("sw-cms-block-nwgncy-products-configurator",{template:'{% block sw_cms_block_nwgncy_products_configurator %}\n    <div class="sw-cms-block-nwgncy-products-configurator">\n        <slot name="productsConfigurator">\n        </slot>\n    </div>\n{% endblock %}'});t("MsQ7");Shopware.Component.register("sw-cms-preview-nwgncy-products-configurator",{template:'\n{% block sw_cms_block_nwgncy_products_configurator_preview %}\n<div class="cms-block  pos-0 cms-block-nwgncy-products-configurator-small-preview preview">\n  <div class="cms-block-container">\n    <div class="cms-block-container-row row cms-row ">\n      <div class="container">\n        <div class="filter-toggle-container">\n          <button class="btn filter-toggle js-products-configurator-toggle open">\n            <svg xmlns="http://www.w3.org/2000/svg" height="17px" viewBox="0 0 512 512" class="chevron-down">\n              <path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path>\n            </svg>\n            <span>Products Configurator</span>\n          </button>\n        </div>\n        <div class="content">\n          <form name="products-configurator" action="" method="get">\n            <div class="categories-and-search">\n              <div class="categories">\n                  <div class="fields">\n                <span class="category">Category</span>\n                <div class="field">\n                  <input name="categories" type="radio" class="js-configurator-category">\n                  <label>Example</label>\n                </div>\n                <div class="field">\n                  <input name="categories" type="radio" class="js-configurator-category">\n                  <label>Example</label>\n                </div>\n                  </div>\n              </div>\n          </div>\n            <div class="filters">\n              <div class="field">\n                <label>Example</label>\n                <select multiple class="js-configurator-property">\n                  <option class="reset" value="">Reset filter</option>\n                  <option value="Example">Example</option>\n                  <option value="Example">Example</option>\n                </select>\n              </div>\n              <div class="field">\n                <label>Example</label>\n                <select multiple class="js-configurator-property">\n                  <option class="reset" value="">Reset filter</option>\n                  <option value="Example">Example</option>\n                  <option value="Example">Example</option>\n                </select>\n              </div>\n              <div class="field">\n                <label>Example</label>\n                <select multiple class="js-configurator-property">\n                  <option class="reset" value="">Reset filter</option>\n                  <option value="Example">Example</option>\n                  <option value="Example">Example</option>\n                </select>\n              </div>\n            </div>\n            <div class="checkboxes">\n              <div class="fields">\n                <span>Show only products</span>\n                <div class="field">\n                  <input class="js-configurator-property-checkbox" type="checkbox">\n                  <label>Example</label>\n                </div>\n                <div class="field">\n                  <input class="js-configurator-property-checkbox" type="checkbox">\n                  <label>Example</label>\n                </div>\n              </div>\n            </div>\n          </form>\n          <button class="btn btn-primary js-btn-reset-filters">\n            <span class="btn-label">Reset filters</span>\n          </button>\n        </div>\n        <div class="loading">\n          <div class="loader"></div>\n        </div>\n      </div>\n    </div>\n  </div>\n</div>\n{% endblock %}\n'}),Shopware.Service("cmsService").registerCmsBlock({name:"nwgncy-products-configurator",category:"commerce",label:"Products Configurator",component:"sw-cms-block-nwgncy-products-configurator",previewComponent:"sw-cms-preview-nwgncy-products-configurator",defaultConfig:{marginBottom:"0",marginTop:"0",marginLeft:"0",marginRight:"0",sizingMode:"full_width"},slots:{productsConfigurator:"nwgncy-products-configurator"}});t("ym3I")},"k/o5":function(e,n,t){},x1Gm:function(e,n,t){},ym3I:function(e,n,t){Shopware.Component.register("sw-cms-el-preview-nwgncy-products-configurator",(function(){return t.e(2).then(t.bind(null,"c44E"))})),Shopware.Component.register("sw-cms-el-config-nwgncy-products-configurator",(function(){return t.e(1).then(t.bind(null,"m/EJ"))})),Shopware.Component.register("sw-cms-el-nwgncy-products-configurator",(function(){return t.e(0).then(t.bind(null,"h75d"))}));var r=Shopware.Data.Criteria,o=new r(1,2e3),s=new r(1,2e3);Shopware.Service("cmsService").registerCmsElement({name:"nwgncy-products-configurator",label:"sw-cms.elements.productSlider.label",component:"sw-cms-el-nwgncy-products-configurator",configComponent:"sw-cms-el-config-nwgncy-products-configurator",previewComponent:"sw-cms-el-preview-nwgncy-products-configurator",defaultConfig:{categoryPropertyGroup:{source:"static",value:null,required:!0,entity:{name:"property_group",criteria:o}},propertyGroups:{source:"static",value:[],required:!0,entity:{name:"property_group",criteria:o}},measuredPropertyGroups:{source:"static",value:[],required:!1,entity:{name:"property_group",criteria:o}},propertyGroupOptions:{source:"static",value:[],required:!0,entity:{name:"property_group_option",criteria:s}}},collect:Shopware.Service("cmsService").getCollectFunction()})}});