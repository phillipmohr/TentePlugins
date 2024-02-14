import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';

export default class ProductConfiguratorPlugin extends Plugin {
     static options = {
          blockContainerSelector: '.cms-block-nwgncy-products-configurator',
          parentFilterPanelSelector: '.cms-element-product-listing-wrapper',
          categoriesSelector: '.js-configurator-category',
          measuredPropertySelectSelector: '.js-configurator-measured-property',
          measuredPropertySelectContainerSelector: '.js-configurator-measured-container',
          measuredPropertyMinSelectSelector: '.js-configurator-measured-property.min',
          measuredPropertyMaxSelectSelector: '.js-configurator-measured-property.max',
          propertySelectSelector: '.js-configurator-property',
          propertyCheckboxSelector: '.js-configurator-property-checkbox',
          measuredPropertySelectOptionMinSelector: '.js-configurator-measured-property.min option',
          measuredPropertySelectOptionMaxSelector: '.js-configurator-measured-property.max option',
          measuredPropertySelectOptionsExceptResetSelector: '.js-configurator-measured-property option:not(.reset)',
          propertySelectOptionSelector: '.js-configurator-property option',
          propertySelectOptionExceptResetSelector: '.js-configurator-property option:not(.reset)',
          propertySelectOptionResetSelector: '.js-configurator-property option.reset',
          productsConfiguratorContainerSelector: '.cms-element-product-listing-wrapper',
          toggleButtonSelector: '.js-products-configurator-toggle',
          contentContainerSelector: '.cms-block-nwgncy-products-configurator .content',
          loaderContainerSelector: '.cms-block-nwgncy-products-configurator .loading',
          checkboxesAndRadiosSelector: 'input[type="checkbox"], input[type="radio"]',
          selectInputsSelector: 'select',
          resetButtonSelector: '.js-btn-reset-filters',
          searchInputSelector: '.js-configurator-search-input',
          searchButtonSelector: '.js-configurator-search-button',
          configuratorFormSelector: '#configurator-form',
     };

     init() {
          this._client = new HttpClient();
          const parentFilterPanelElement = DomAccess.querySelector(document, this.options.parentFilterPanelSelector);
          this.listing = window.PluginManager.getPluginInstanceFromElement(
              parentFilterPanelElement,
              'Listing'
          );

          this.listingPagination = window.PluginManager.getPluginInstanceFromElement(
               parentFilterPanelElement,
               'ListingPagination'
          );

          this._searchUrls = {
               filter: "/widgets/search/filter",
               data: "/widgets/search"
          }
          this._defaultDataAndFilterUrls = {
               filter: this.listing.options?.filterUrl,
               data: this.listing.options?.dataUrl,
          }

          this.blockContainer = DomAccess.querySelector(document, this.options.blockContainerSelector);
          this.listingElement = parentFilterPanelElement;
          
          this._selectedPropertyOptions = {};
          this._selectedMinMeasuredPropertyOptions = {};
          this._selectedMaxMeasuredPropertyOptions = {};
          this._selectedPropertyCheckboxOptions = [];
          this._selectedCategoryOption = null;
          this._searchQuery = "";

          this._toggleButton = DomAccess.querySelector(document, this.options.toggleButtonSelector);
          this._contentContainer = DomAccess.querySelector(document, this.options.contentContainerSelector);
          this._loaderContainer = DomAccess.querySelector(document, this.options.loaderContainerSelector);
          this._categories = DomAccess.querySelectorAll(this._contentContainer, this.options.categoriesSelector);
          this._measuredPropertySelectContainer = DomAccess.querySelector(this._contentContainer, this.options.measuredPropertySelectContainerSelector);
          this._measuredPropertySelects = DomAccess.querySelectorAll(this._contentContainer, this.options.measuredPropertySelectSelector);
          this._measuredPropertyMinSelects = DomAccess.querySelectorAll(this._contentContainer, this.options.measuredPropertyMinSelectSelector);
          this._measuredPropertyMaxSelects = DomAccess.querySelectorAll(this._contentContainer, this.options.measuredPropertyMaxSelectSelector);
          this._propertySelects = DomAccess.querySelectorAll(this._contentContainer, this.options.propertySelectSelector);
          this._propertyCheckboxes = DomAccess.querySelectorAll(this._contentContainer, this.options.propertyCheckboxSelector);
          this._measuredPropertySelectsOptionsMin = DomAccess.querySelectorAll(this._contentContainer, this.options.measuredPropertySelectOptionMinSelector);
          this._measuredPropertySelectsOptionsMax = DomAccess.querySelectorAll(this._contentContainer, this.options.measuredPropertySelectOptionMaxSelector);
          this._propertySelectsOptions = DomAccess.querySelectorAll(this._contentContainer, this.options.propertySelectOptionSelector);
          this._propertySelectsOptionsResets = DomAccess.querySelectorAll(this._contentContainer, this.options.propertySelectOptionResetSelector);
          this._propertySelectsOptionsExceptReset = DomAccess.querySelectorAll(this._contentContainer, this.options.propertySelectOptionExceptResetSelector);
          this._measuredPropertySelectsOptionsExceptReset = DomAccess.querySelectorAll(this._contentContainer, this.options.measuredPropertySelectOptionsExceptResetSelector);
          this._checkboxesAndRadios = DomAccess.querySelectorAll(this._contentContainer, this.options.checkboxesAndRadiosSelector);
          this._selectInputs = DomAccess.querySelectorAll(this._contentContainer, this.options.selectInputsSelector);
          this._resetButton = DomAccess.querySelector(this._contentContainer, this.options.resetButtonSelector);
          this._searchInput = DomAccess.querySelector(this._contentContainer, this.options.searchInputSelector);
          this._searchButton = DomAccess.querySelector(this._contentContainer, this.options.searchButtonSelector);
          this._configuratorForm = DomAccess.querySelector(this._contentContainer, this.options.configuratorFormSelector);
          this._registerEvents();

          if (this.listing?._urlFilterParams) {
               this._initUrlSelectedProperties(this.listing._urlFilterParams);
          }

          this.fetchAvailableOptions();
     }

     /**
     *
     * @private
     */
     _registerEvents() {
          this._toggleButton.addEventListener('click', event => {
               this._toggleButtonTrigger();
          });

          this._categories.forEach(item => {
               item.addEventListener('change', event => {
                    if (event.target.checked) {
                         const value = event.target.value;
                         this._selectedCategoryOption = value;
                         this.refreshListing();
                         this.fetchAvailableOptions();
                    }
               });
          });

          this._measuredPropertyMinSelects.forEach(item => {
               item.addEventListener('change', event => {
                    const selectedOption = item.options[item.selectedIndex];
                    const selectedValue = selectedOption.value;
                    const fullId = item.id;
                    const id = fullId.replace('configurator-property-group-', '').replace('-min', '');

                    if (selectedOption.classList.contains('reset')) {

                         item.querySelectorAll('option:not(.reset)').forEach(function(option) {
                              option.selected = false;
                         });

                         delete this._selectedMinMeasuredPropertyOptions[id];

                    } else {
                         const parsedSelectedValue = this.parseNumberFromString(selectedValue);
                         if (parsedSelectedValue !== null) {
                              this._selectedMinMeasuredPropertyOptions[id] = parsedSelectedValue;
                         }
                    }
                    
                    this.refreshListing();
                    this.fetchAvailableOptions();
               });
          });

          this._measuredPropertyMaxSelects.forEach(item => {
               item.addEventListener('change', event => {
                    const selectedOption = item.options[item.selectedIndex];
                    const selectedValue = selectedOption.value;
                    const fullId = item.id;
                    const id = fullId.replace('configurator-property-group-', '').replace('-max', '');

                    if (selectedOption.classList.contains('reset')) {

                         item.querySelectorAll('option:not(.reset)').forEach(function(option) {
                              option.selected = false;
                         });

                         delete this._selectedMaxMeasuredPropertyOptions[id];

                    } else {
                         const parsedSelectedValue = this.parseNumberFromString(selectedValue);
                         if (parsedSelectedValue !== null) {
                              this._selectedMaxMeasuredPropertyOptions[id] = parsedSelectedValue;
                         }
                    }
                    this.refreshListing();
                    this.fetchAvailableOptions();
               });
          });

          this._propertySelects.forEach(item => {
               item.addEventListener('change', event => {
                    const selectedOption = item.options[item.selectedIndex];
                    const selectedValue = selectedOption.value;
                    const fullId = item.id;
                    const id = fullId.replace('configurator-property-group-', '');

                    if (selectedOption.classList.contains('reset')) {

                         item.querySelectorAll('option:not(.reset)').forEach(function(option) {
                              option.selected = false;
                         });

                         delete this._selectedPropertyOptions[id];

                    } else {
                         this._selectedPropertyOptions[id] = selectedValue;
                    }
                    
                    this.refreshListing();
                    this.fetchAvailableOptions();
               });
          });

          this._propertyCheckboxes.forEach(item => {
               item.addEventListener('change', event => {
                    const value = item.value;
                    if (item.checked) {
                         if (!this._selectedPropertyCheckboxOptions.includes(value)) {
                              this._selectedPropertyCheckboxOptions.push(value);
                         }
                    } else {
                         this._selectedPropertyCheckboxOptions = this._selectedPropertyCheckboxOptions.filter(option => option !== value);
                    }
                    this.refreshListing();
                    this.fetchAvailableOptions();
               });
          });
          
          this._configuratorForm.addEventListener('submit', event => {
               event.preventDefault();
          });
          this._resetButton.addEventListener('click', event => {
               this._resetFilters();
          });

          this._searchButton.addEventListener('click', event => this._searchHandler());
          this._searchInput.addEventListener('keyup', event => {
               if (event.key === 'Enter') {
                    this._searchHandler();
               }
          });
          this._searchInput.addEventListener('blur', event => {
               this._searchHandler();
          });
     }
     _resetFilters() {
          this._selectInputs.forEach(select => {
               select.selectedIndex = 0;
          });
          this._propertyCheckboxes.forEach(checkbox => {
               checkbox.checked = false;
          });
          this._categories.forEach(radio => {
               radio.checked = false;
          });

          this._selectedPropertyOptions = {};
          this._selectedMinMeasuredPropertyOptions = {};
          this._selectedMaxMeasuredPropertyOptions = {};
          this._selectedPropertyCheckboxOptions = [];
          this._selectedCategoryOption = null;
          this._searchQuery = "";
          this._searchInput.value = "";
          

          this.listing.options.filterUrl = this._defaultDataAndFilterUrls.filter;
          this.listing.options.dataUrl = this._defaultDataAndFilterUrls.data;


          this.fetchAvailableOptions();
          this.refreshListing();
     }

     /**
     * @param pushHistory
     * @param overrideParams
     * @public
     */
     changeListing(pushHistory = true, overrideParams = {}) {
          this._buildRequest(pushHistory, overrideParams);
     }

     refreshListing() {
          const minPropertiesObject = {};
          const maxPropertiesObject = {};
          const allParamsObject = {};

          const allPropertySelectValues = Object.values(this._selectedPropertyOptions).flat();
          const allPropertyValuesSet = new Set(allPropertySelectValues.concat(this._selectedPropertyCheckboxOptions));
          
          if (this._selectedCategoryOption !== null) {
               allPropertyValuesSet.add(this._selectedCategoryOption);
          }

          const allPropertyValues = Array.from(allPropertyValuesSet);

          const delimitedPropertiesString = allPropertyValues.filter(value => value !== '').join('|');
      
          if (delimitedPropertiesString) {
              allParamsObject.properties = delimitedPropertiesString;
          }
      
          if (this._selectedMinMeasuredPropertyOptions) {
               const minMeasuredOptionsParams = Object.entries(this._selectedMinMeasuredPropertyOptions)
                    .map(([id, value]) => [`min-property[${id}]`, value]);
          
               Object.assign(minPropertiesObject, Object.fromEntries(minMeasuredOptionsParams));
          }
      
          if (this._selectedMaxMeasuredPropertyOptions) {
               const maxMeasuredOptionsParams = Object.entries(this._selectedMaxMeasuredPropertyOptions)
                    .map(([id, value]) => [`max-property[${id}]`, value]);
          
               Object.assign(maxPropertiesObject, Object.fromEntries(maxMeasuredOptionsParams));
          }
          if (this._searchQuery) {
               const searchQueryObject = {
                    search: this._searchQuery
               }
               Object.assign(allParamsObject, searchQueryObject);
          }
      
          Object.assign(allParamsObject, minPropertiesObject, maxPropertiesObject);

          this.listing.changeListing(true, { p: 1, ...allParamsObject });
     }      

     _initUrlSelectedProperties(urlFilterParams) {
          var refreshListing = false;
          if (urlFilterParams?.properties) {
               const initialUrlPropertiesOptionsArr = urlFilterParams.properties.split('|');
               if (initialUrlPropertiesOptionsArr) {
                    const propertySelectsOptions = this._propertySelectsOptions;
                    const matchingPropertyOptions = Array.from(propertySelectsOptions).filter(option => initialUrlPropertiesOptionsArr.includes(option.value));
                    matchingPropertyOptions.forEach(option => {
                         const parentSelect = option.parentNode;
                         if (parentSelect) {
                              const parentSelectId = parentSelect.id.replace('configurator-property-group-', '');
                              option.selected = true;
                              this._selectedPropertyOptions[parentSelectId] = option.value;
                         }
                    });

                    const propertyCheckboxOptions = this._propertyCheckboxes;
                    const matchingPropertyCheckboxOptions = Array.from(propertyCheckboxOptions).filter(checkbox => initialUrlPropertiesOptionsArr.includes(checkbox.value));
                    matchingPropertyCheckboxOptions.forEach(checkbox => {
                         const value = checkbox.value;
                         checkbox.checked = true;
                         if (!this._selectedPropertyCheckboxOptions.includes(value)) {
                              this._selectedPropertyCheckboxOptions.push(value);
                         }
                    });


                    const categoryOptions = this._categories;
                    const matchingCategoryOption = Array.from(categoryOptions).find(radio => initialUrlPropertiesOptionsArr.includes(radio.value));
                    if (matchingCategoryOption) {
                         const value = matchingCategoryOption.value;
                         matchingCategoryOption.checked = true;
                         this._selectedCategoryOption = value;
                    }
               }
          }
          if (urlFilterParams?.search) {
               const paramsSearch = urlFilterParams.search;
               this.listing.options.filterUrl = this._searchUrls.filter;
               this.listing.options.dataUrl = this._searchUrls.data;
               this._searchQuery = paramsSearch;
               this._searchInput.value = paramsSearch;
               refreshListing = true;
          }
          this._measuredPropertySelects.forEach((select) => {
               const selectId = select.id.replace('configurator-property-group-', '');
               const selectIdWithoutSuffix = selectId.replace(/-(min|max)$/, '');
               
               const minParamName = `min-property[${selectIdWithoutSuffix}]`;
               const maxParamName = `max-property[${selectIdWithoutSuffix}]`;
             
               if (select.classList.contains('min') && urlFilterParams[minParamName]) {
                    const selectValue = urlFilterParams[minParamName];
                    select.value = selectValue;
                    this._selectedMinMeasuredPropertyOptions[selectIdWithoutSuffix] = selectValue;
               } else if (select.classList.contains('max') && urlFilterParams[maxParamName]) {
                    const selectValue = urlFilterParams[maxParamName];
                    select.value = selectValue;
                    this._selectedMaxMeasuredPropertyOptions[selectIdWithoutSuffix] = selectValue;
               }
          });
          if (refreshListing) {
               this.refreshListing();
          }
     }

     _toggleButtonTrigger() {
          if (this._contentContainer.style.display !== 'none') {
               this._toggleButton.classList.remove('open');
               this._contentContainer.style.display = 'none';
          } else {
               this._toggleButton.classList.add('open');
               this._contentContainer.style.display = 'block';
          }
     }

     fetchAvailableOptions() {
          const minValuesObj = {};
          const maxValuesObj = {};

          const allOptionSelectValues = Object.values(this._selectedPropertyOptions).flat();
          const allOptionValuesSet = new Set(allOptionSelectValues.concat(this._selectedPropertyCheckboxOptions));
          
          if (this._selectedCategoryOption !== null) {
               allOptionValuesSet.add(this._selectedCategoryOption);
          }

          const allOptionValues = Array.from(allOptionValuesSet);
          const delimitedOptionsString = allOptionValues.filter(value => value !== '').join('|');

          for (const id in this._selectedMinMeasuredPropertyOptions) {
               if (this._selectedMinMeasuredPropertyOptions.hasOwnProperty(id)) {
                    minValuesObj[id] = this._selectedMinMeasuredPropertyOptions[id][0];
               }
          }

          for (const id in this._selectedMaxMeasuredPropertyOptions) {
               if (this._selectedMaxMeasuredPropertyOptions.hasOwnProperty(id)) {
                    maxValuesObj[id] = this._selectedMaxMeasuredPropertyOptions[id][0];
               }
          }

          var queryParams = [];
          
          if (delimitedOptionsString) {
               const optionsStringEncoded = encodeURIComponent(delimitedOptionsString);
               queryParams.push(`options=${optionsStringEncoded}`);
          }

          if (this._selectedMinMeasuredPropertyOptions && !this.isEmptyObject(minValuesObj)) {
               const minValuesJSON = JSON.stringify(minValuesObj);
               const encodedMinValues = encodeURIComponent(minValuesJSON);
               queryParams.push(`minValues=${encodedMinValues}`);
          }

          if (this._selectedMaxMeasuredPropertyOptions && !this.isEmptyObject(maxValuesObj)) {
               const maxValuesJSON = JSON.stringify(maxValuesObj);
               const encodedMaxValues = encodeURIComponent(maxValuesJSON);
               queryParams.push(`maxValues=${encodedMaxValues}`);
          }
          
          var queryString = queryParams.join('&');
          
          //uncomment to show all options when the page loads (even those without products)
          /*if (!queryString) {
               return;
          }*/
          
          const salesChannelBaseUrl = this._configuratorForm.getAttribute('data-sc-base-url');
          var baseUrl = "";
          if (salesChannelBaseUrl) {
               baseUrl = salesChannelBaseUrl;
          }
          const formAction = this._configuratorForm.getAttribute('action');
          var controllerUrl = "/";
          if (formAction) {
               controllerUrl = formAction;
          }
          const url = `${baseUrl}${controllerUrl}?${queryString}`;

          this.startLoading();
          this._client.get(url, this.availableOptionsHandler.bind(this));
     }

     availableOptionsHandler(response) {
          try {
               const responseObject = JSON.parse(response);
               if (responseObject) {
                    const availableOptionIds = responseObject.availableOptionIds;
                    if (availableOptionIds && Array.isArray(availableOptionIds)) {
                         this._propertySelectsOptionsExceptReset.forEach(option => {
                              if (!availableOptionIds.includes(option.value)) {
                                   option.style.display = 'none';
                                   option.selected = false;
                              } else {
                                   option.style.display = 'block'; 
                              }
                         });
                         this._measuredPropertySelectsOptionsExceptReset.forEach(option => {
                              if (!availableOptionIds.includes(option.id)) {
                                   option.style.display = 'none';
                                   option.selected = false;
                              } else {
                                   option.style.display = 'block'; 
                              }
                         });
                         this.toggleMeasuredSelectsVisibility();
                    }
               }
          } catch (error) {
          }
          this.stopLoading();
     }
     startLoading() {
          this._configuratorForm.style.opacity = 0;
          this._configuratorForm.style.pointerEvents = 'none';
          this._loaderContainer.style.opacity = 1;
          
     }
     stopLoading() {
          setTimeout(() => {
               this._configuratorForm.style.opacity = 1;
               this._configuratorForm.style.pointerEvents = 'unset';
               this._loaderContainer.style.opacity = 0;
          }, 200);
     }

     parseNumberFromString(input) {
          const matches = input.match(/[-+]?[0-9]*\.?[0-9]+|[0-9]*,?[0-9]+/g);
          if (matches) {
               return matches.map(match => {
                    const sanitizedMatch = match.replace(',', '.');
                    if (sanitizedMatch.includes('.')) {
                         return parseFloat(sanitizedMatch);
                    } else {
                         return parseInt(sanitizedMatch, 10);
                    }
               });
          } else {
               return null;
          }
     }

     isEmptyObject(obj) {
          return Object.keys(obj).length === 0;
     }
     
     toggleMeasuredSelectsVisibility() {
          this._measuredPropertySelects.forEach(select => {
               select.parentNode.parentNode.style.display = Array.from(select.options).filter(option => !option.classList.contains("reset"))
               .every(option => option.style.display == 'none') ? 'none' : 'block';
          });
          const measuredFields = this._measuredPropertySelectContainer.querySelectorAll('.field');
          this._measuredPropertySelectContainer.style.display =  Array.from(measuredFields).every(field => field.style.display == 'none') ? 'none' : 'flex';
     }

     _searchHandler() {
          const searchQuery = this._searchInput.value;
          this._searchQuery = searchQuery;
          if (searchQuery) {
               this.listing.options.filterUrl = this._searchUrls.filter;
               this.listing.options.dataUrl = this._searchUrls.data;

          } else {
               this.listing.options.filterUrl = this._defaultDataAndFilterUrls.filter;
               this.listing.options.dataUrl = this._defaultDataAndFilterUrls.data;
          }
          this.startLoading();

          this.refreshListing();
          
          setTimeout(() => {
               this.stopLoading();
          }, 200);    
     }
        
}