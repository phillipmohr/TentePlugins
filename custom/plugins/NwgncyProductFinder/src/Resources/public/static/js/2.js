(this["webpackJsonpPluginnwgncy-product-finder"]=this["webpackJsonpPluginnwgncy-product-finder"]||[]).push([[2],{"7WCU":function(n,s,i){"use strict";i.r(s);i("Qgu4"),s.default={template:'\n{% block sw_cms_element_nwgncy_product_finder_preview %}\n<div class="cms-section nwgncy-quick-finder-block pos-0 cms-section-default" style="">\n     <div class="cms-section-default full-width">\n          <div class="cms-block  pos-0 cms-block-nwgncy-product-finder-preview" style="">\n               <div class="cms-block-container" >\n                    <div class="cms-block-container-row row cms-row ">\n                         <div class="container">\n                              <div class="content">\n                                   <h3>Quick-find your products</h3>\n                                   <form id="product-finder-form" name="product-finder">\n                                        <div class="finder-row">\n                                             <div class="section categories">\n                                                  <div class="field js-product-finder-category-field active">\n                                                       <label class="category-label">Category 1</label>\n                                                       <input name="finder-category" type="radio" >\n                                                  </div>\n                                                  <div class="field js-product-finder-category-field">\n                                                       <label class="category-label">Category 2</label>\n                                                       <input name="finder-category" type="radio">\n                                                  </div>\n                                             </div>\n                                             <div class="properties-container">\n                                                  <div class="loading" style="display: none;">\n                                                       <div class="loader"></div>\n                                                  </div>\n                                                  <div class="section property-options" style="display: flex;">\n\n\n                                                       <div class="field js-product-finder-property-option-group-field" style="display: flex;">\n                                                            <label class="property-group-label">\n                                                                 <span>\n                                                                      <span class="js-property-group-label-name">Property group 1</span>\n                                                                      <span class="icon icon-arrow-medium-down icon-xs">\n                                                                           <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewbox="0 0 16 16">\n                                                                                <use xlink:href="#icons-solid-arrow-medium-down" fill="#758CA3" fill-rule="evenodd"></use>\n                                                                           </svg>\n                                                                      </span>\n                                                                 </span>\n                                                            </label>\n                                                            <div class="dropdown-content">\n                                                                 <div class="options-list">\n                                                                      <div class="js-option-field">\n                                                                           <label class="property-group-option-label">-</label>\n                                                                           <input class="js-finder-property-option-input" data-option-name="-" type="radio">\n                                                                      </div>\n                                                                      <div class="js-option-field">\n                                                                           <label class="property-group-option-label">-</label>\n                                                                           <input class="js-finder-property-option-input" data-option-name="-" type="radio">\n                                                                      </div>\n                                                                 </div>\n                                                            </div>\n                                                       </div>\n\n                                                       <div class="field js-product-finder-property-option-group-field js-product-finder-property-option-group-slider-field" style="display: flex;">\n                                                            <label class="property-group-label">\n                                                                 <span>Property group 2\n                                                                      <span class="icon icon-arrow-medium-down icon-xs">\n                                                                           <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewbox="0 0 16 16">\n                                                                                <use xlink:href="#icons-solid-arrow-medium-down" fill="#758CA3" fill-rule="evenodd"></use>\n                                                                           </svg>\n                                                                      </span>\n                                                                 </span>\n                                                            </label>\n                                                            <div class="dropdown-content slider">\n                                                                 <div class="min-max">\n                                                                      <div class="nwgncy-finder-slider-values">\n                                                                           <div class="nwgncy-finder-slider-range1" style="order: 1;">-</div>\n                                                                           <div class="nwgncy-finder-slider-dash" style="order: 2;">\n                                                                                ‐\n                                                                           </div>\n                                                                           <div class="nwgncy-finder-slider-range2" style="order: 3;">-</div>\n                                                                           <div class="nwgncy-finder-slider-unit">\n                                                                                unit\n                                                                           </div>\n                                                                      </div>\n                                                                      <div class="nwgncy-finder-slider-container">\n                                                                           <div class="nwgncy-finder-slider-track"></div>\n                                                                           <input type="range" min="0" max="21" value="0" class="nwgncy-finder-slider-1" step="1">\n                                                                           <input type="range" min="0" max="21" value="21" class="nwgncy-finder-slider-2" step="1">\n\n                                                                           <div class="nwgncy-finder-slider-buttons">\n                                                                                <span data-value="0"></span>\n                                                                                <span data-value="1"></span>\n                                                                                <span data-value="2"></span>\n                                                                                <span data-value="3"></span>\n                                                                                <span data-value="4"></span>\n                                                                                <span data-value="5"></span>\n                                                                                <span data-value="6"></span>\n                                                                                <span data-value="7"></span>\n                                                                                <span data-value="8"></span>\n                                                                                <span data-value="9"></span>\n                                                                                <span data-value="10"></span>\n                                                                                <span data-value="11"></span>\n                                                                                <span data-value="12"></span>\n                                                                                <span data-value="13"></span>\n                                                                                <span data-value="14"></span>\n                                                                                <span data-value="15"></span>\n                                                                                <span data-value="16"></span>\n                                                                                <span data-value="17"></span>\n                                                                                <span data-value="18"></span>\n                                                                                <span data-value="19"></span>\n                                                                                <span data-value="20"></span>\n                                                                                <span data-value="21"></span>\n                                                                           </div>\n\n                                                                           <datalist class="nwgncy-finder-slider-number">\n                                                                                <option value="-"></option>\n                                                                                <option value="-"></option>\n                                                                           </datalist>\n                                                                      </div>\n                                                                 </div>\n                                                            </div>\n                                                       </div>\n                                                       <div class="field js-product-finder-property-option-group-field js-product-finder-property-option-group-slider-field" style="display: flex;">\n                                                            <label class="property-group-label">\n                                                                 <span>Property group 3\n                                                                      <span class="icon icon-arrow-medium-down icon-xs">\n                                                                           <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewbox="0 0 16 16">\n                                                                                <use xlink:href="#icons-solid-arrow-medium-down" fill="#758CA3" fill-rule="evenodd"></use>\n                                                                           </svg>\n                                                                      </span>\n                                                                 </span>\n                                                            </label>\n                                                            <div class="dropdown-content slider">\n                                                                 <div class="min-max">\n                                                                      <div class="nwgncy-finder-slider-values">\n                                                                           <div class="nwgncy-finder-slider-range1" style="order: 1;">-</div>\n                                                                           <div class="nwgncy-finder-slider-dash" style="order: 2;">\n                                                                                ‐\n                                                                           </div>\n                                                                           <div class="nwgncy-finder-slider-range2" style="order: 3;">-</div>\n                                                                           <div class="nwgncy-finder-slider-unit">\n                                                                                -\n                                                                           </div>\n                                                                      </div>\n                                                                      <div class="nwgncy-finder-slider-container">\n                                                                           <div class="nwgncy-finder-slider-track"></div>\n                                                                           <input type="range" min="0" max="1" value="0" class="nwgncy-finder-slider-1" step="1">\n                                                                           <input type="range" min="0" max="1" value="1" class="nwgncy-finder-slider-2" step="1">\n\n                                                                           <div class="nwgncy-finder-slider-buttons">\n                                                                                <span data-value="0"></span>\n                                                                                <span data-value="1"></span>\n                                                                           </div>\n\n                                                                           <datalist class="nwgncy-finder-slider-number">\n                                                                                <option value="-"></option>\n                                                                                <option value="-"></option>\n                                                                           </datalist>\n                                                                      </div>\n                                                                 </div>\n                                                            </div>\n                                                       </div>\n                                                       <div class="field js-product-finder-property-option-group-field js-product-finder-property-option-group-slider-field" style="display: none;">\n                                                            <label class="property-group-label">\n                                                                 <span>Property group 4\n                                                                      <span class="icon icon-arrow-medium-down icon-xs">\n                                                                           <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewbox="0 0 16 16">\n                                                                                <use xlink:href="#icons-solid-arrow-medium-down" fill="#758CA3" fill-rule="evenodd"></use>\n                                                                           </svg>\n                                                                      </span>\n                                                                 </span>\n                                                            </label>\n                                                            <div class="dropdown-content slider">\n                                                                 <div class="min-max">\n                                                                      <div class="nwgncy-finder-slider-values">\n                                                                           <div class="nwgncy-finder-slider-range1" style="order: 1;">-</div>\n                                                                           <div class="nwgncy-finder-slider-dash" style="order: 2;">\n                                                                                ‐\n                                                                           </div>\n                                                                           <div class="nwgncy-finder-slider-range2" style="order: 3;">-</div>\n                                                                           <div class="nwgncy-finder-slider-unit">\n                                                                                -\n                                                                           </div>\n                                                                      </div>\n                                                                      <div class="nwgncy-finder-slider-container">\n                                                                           <div class="nwgncy-finder-slider-track"></div>\n                                                                           <input type="range" min="0" max="0" value="0" class="nwgncy-finder-slider-1" step="1">\n                                                                           <input type="range" min="0" max="0" value="0" class="nwgncy-finder-slider-2" step="1">\n\n                                                                           <div class="nwgncy-finder-slider-buttons">\n                                                                                <span data-value="0"></span>\n                                                                           </div>\n\n                                                                           <datalist class="nwgncy-finder-slider-number">\n                                                                                <option value="8"></option>\n                                                                           </datalist>\n                                                                      </div>\n                                                                 </div>\n                                                            </div>\n                                                       </div>\n                                                  </div>\n                                             </div>\n                                             <button class="btn btn-primary js-btn-find-products" type="button" data-target-lang="en-GB">\n                                                  <span class="btn-label">Find Products</span>\n                                             </button>\n                                        </div>\n                                   </form>\n                              </div>\n                         </div>\n                    </div>\n               </div>\n          </div>\n     </div>\n</div>\n{% endblock %}\n'}},Qgu4:function(n,s,i){var a=i("hQDv");a.__esModule&&(a=a.default),"string"==typeof a&&(a=[[n.i,a,""]]),a.locals&&(n.exports=a.locals);(0,i("P8hj").default)("477de0d8",a,!0,{})},hQDv:function(n,s,i){}}]);