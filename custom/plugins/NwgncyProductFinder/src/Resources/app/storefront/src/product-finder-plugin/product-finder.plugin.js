import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';

export default class ProductFinderPlugin extends Plugin {
     static options = { 
          blockContainerSelector: '.cms-block-nwgncy-product-finder',
          finderFormSelector: '#product-finder-form',
          categoryFieldSelector: '.js-product-finder-category-field',
          activeCategoryFieldSelector: '.js-product-finder-category-field.active',
          propertyOptionsGroupsContainerSelector: '.section.property-options',
          propertyOptionsGroupsFieldSelector: '.section.property-options .js-product-finder-property-option-group-field',
          propertyOptionsFieldSelector: '.options-list .js-option-field',
          findButton: '.js-btn-find-products',
          propertyOptionSelector: '.js-finder-property-option-input',
          minMaxOptionsGroupsFieldSelector: '.section.property-options .js-product-finder-property-option-group-slider-field',
          loadinContainerSelector: '.cms-block-nwgncy-product-finder .loading'
     };

     init() {
          this._client = new HttpClient();
          const blockContainer = DomAccess.querySelector(document, this.options.blockContainerSelector);
          this._finderForm = DomAccess.querySelector(blockContainer, this.options.finderFormSelector);
          this._categoryFields = DomAccess.querySelectorAll(blockContainer, this.options.categoryFieldSelector);
          this._propertyOptionsGroupsContainer = DomAccess.querySelector(blockContainer, this.options.propertyOptionsGroupsContainerSelector);
          this._loadingContainer = DomAccess.querySelector(blockContainer, this.options.loadinContainerSelector);
          this._selectedCategoryOption = DomAccess.querySelector(blockContainer, this.options.activeCategoryFieldSelector);
          this._selectedCategoryOptionId = this._selectedCategoryOption?.querySelector('input')?.value ?? null;
          this._propertyOptionsGroups = DomAccess.querySelectorAll(blockContainer, this.options.propertyOptionsGroupsFieldSelector);
          this._propertyOptions = DomAccess.querySelectorAll(blockContainer, this.options.propertyOptionsFieldSelector, false);
          this._propertyOptionsInputs = DomAccess.querySelectorAll(blockContainer, this.options.propertyOptionSelector, false);
          this._findButton = DomAccess.querySelector(blockContainer, this.options.findButton);
          this._minMaxOptionsGroups = DomAccess.querySelectorAll(blockContainer, this.options.minMaxOptionsGroupsFieldSelector);

          this._selectedMinMeasuredPropertyOptions = {};
          this._selectedMaxMeasuredPropertyOptions = {};

          const defaultActiveCategoryField = DomAccess.querySelector(blockContainer, this.options.activeCategoryFieldSelector);
          this._selectedCategoryOptionValue = null;
          if (defaultActiveCategoryField) {
               const defaultActiveCategoryFieldInput = defaultActiveCategoryField.querySelector('input');
               if (defaultActiveCategoryFieldInput) {
                    this._selectedCategoryOptionValue = defaultActiveCategoryFieldInput.value;
               }
          }
          this._selectedPropertyOptions = {};

          this._registerEvents();

          this._isLoading = true;

          this.fetchAvailableOptions();
     }

     /**
     *
     * @private
     */
     _registerEvents() {
          this._categoryFields.forEach((element) => {
               element.addEventListener('click', () => {
                    if (this._selectedCategoryOption !== element) {
                         this._categoryFields.forEach((el) => {
                              el.classList.remove('active');
                         });
     
                         element.classList.add('active');
     
                         var clickedValue = element.querySelector('input').value;
                         this._selectedCategoryOption = element;
                         this._selectedCategoryOptionId = clickedValue;
                         this._selectedCategoryOptionValue = clickedValue;
                         this.startLoading();
                         this.fetchAvailableOptions();
                    }
               });
          });

          this._propertyOptionsGroups.forEach(element => {
               const label = element.querySelector('.property-group-label');
               label.addEventListener('click', () => {
                    this._propertyOptionsGroups.forEach(el => {
                         if (el !== element) {
                              el.classList.remove('open');
                         }
                    });
               
                    element.classList.toggle('open');
               });
          });
          
          if (this._propertyOptions) {
               
               this._propertyOptions.forEach(element => {
                    const input = element.querySelector('input');
                    input.addEventListener('change', event => {
                         if (input.checked) {
                              const selectedValue = input.value;
                              const propertyGroupId = input.name;
                              const selectedInputName = input.getAttribute('data-option-name');
                              this._selectedPropertyOptions[propertyGroupId] = selectedValue;

                              const parentPropertyGroupLabel = document.querySelector('.js-property-group-label-name[data-property-group="' + propertyGroupId + '"]');
                              if (parentPropertyGroupLabel) {
                                   parentPropertyGroupLabel.innerHTML = selectedInputName;
                              }
                              const parentPropertyContainer = document.querySelector('.js-product-finder-property-option-group-field[data-property-group="' + propertyGroupId + '"]');
                              if (parentPropertyContainer) {
                                   parentPropertyContainer.classList.remove('open');
                              }
                         }
                    });
               });
          }
          this._minMaxOptionsGroups.forEach(element => {
               const groupId = element.getAttribute('data-property-group');
               let sliderOne = element.querySelector(".nwgncy-finder-slider-1");
               let sliderTwo = element.querySelector(".nwgncy-finder-slider-2");

               let displayValOne = element.querySelector(".nwgncy-finder-slider-range1");
               let displayValTwo = element.querySelector(".nwgncy-finder-slider-range2");


               let dash = element.querySelector(".nwgncy-finder-slider-dash");
               let sliderOne1 = element.querySelectorAll('.nwgncy-finder-slider-buttons span');


               let sliderTrack = element.querySelector(".nwgncy-finder-slider-track");

               const stepVal = Array.from(element.querySelector(".nwgncy-finder-slider-number").options, option => option.value);
               displayValOne.textContent = stepVal[sliderOne.value];
               displayValTwo.textContent = stepVal[sliderTwo.value];

               sliderOne1.forEach(button => {
                    button.addEventListener('click', () => {
                         if (this.isCloserToSlider(parseInt(button.getAttribute('data-value')), sliderOne.value, sliderTwo.value)) {
                              sliderOne.value = parseInt(button.getAttribute('data-value'));
                              displayValOne.textContent = stepVal[sliderOne.value];
                         } else {
                              sliderTwo.value = parseInt(button.getAttribute('data-value'));
                              displayValTwo.textContent = stepVal[sliderTwo.value];
                         }
                         this.fillColor(sliderOne, sliderTwo, sliderOne1, dash, sliderTrack, displayValOne, displayValTwo);

                         const rangeValue1 = element.querySelector('.nwgncy-finder-slider-range1');
                         const rangeValue2 = element.querySelector('.nwgncy-finder-slider-range2');

                         const rangeValue1Order = rangeValue1?.style?.order;

                         var minValue = '';
                         var maxValue = '';

                         if (rangeValue1Order == '1') {
                              minValue = rangeValue1?.textContent;
                              maxValue = rangeValue2?.textContent;
                         } else if (rangeValue1Order == '3') {
                              maxValue = rangeValue1?.textContent;
                              minValue = rangeValue2?.textContent;
                         }

                         if (minValue) {
                              this._selectedMinMeasuredPropertyOptions[groupId] = minValue;
                         }
                         if (maxValue) {
                              this._selectedMaxMeasuredPropertyOptions[groupId] = maxValue;
                         }
                    });
               });

               sliderOne.addEventListener('input', e => {
                    this.slideOne(element, e, 'nwgncy-finder-slider-range1', dash, sliderTrack, sliderOne, sliderTwo, sliderOne1, stepVal, displayValTwo);
                    const rangeValue1 = element.querySelector('.nwgncy-finder-slider-range1');
                    const rangeValue2 = element.querySelector('.nwgncy-finder-slider-range2');

                    const rangeValue1Order = rangeValue1?.style?.order;

                    var minValue = '';
                    var maxValue = '';

                    if (rangeValue1Order == '1') {
                         minValue = rangeValue1?.textContent;
                         maxValue = rangeValue2?.textContent;
                    } else if (rangeValue1Order == '3') {
                         maxValue = rangeValue1?.textContent;
                         minValue = rangeValue2?.textContent;
                    }

                    if (minValue) {
                         this._selectedMinMeasuredPropertyOptions[groupId] = minValue;
                    }
                    if (maxValue) {
                         this._selectedMaxMeasuredPropertyOptions[groupId] = maxValue;
                    }
               });
               sliderTwo.addEventListener('input', e => {
                    this.slideTwo(element, e, 'nwgncy-finder-slider-range2', dash, sliderTrack, sliderOne, sliderTwo, sliderOne1, stepVal, displayValOne);
                    const rangeValue1 = element.querySelector('.nwgncy-finder-slider-range1');
                    const rangeValue2 = element.querySelector('.nwgncy-finder-slider-range2');

                    const rangeValue1Order = rangeValue1?.style?.order;

                    var minValue = '';
                    var maxValue = '';

                    if (rangeValue1Order == '1') {
                         minValue = rangeValue1?.textContent;
                         maxValue = rangeValue2?.textContent;
                    } else if (rangeValue1Order == '3') {
                         maxValue = rangeValue1?.textContent;
                         minValue = rangeValue2?.textContent;
                    }

                    if (minValue) {
                         this._selectedMinMeasuredPropertyOptions[groupId] = minValue;
                    }
                    if (maxValue) {
                         this._selectedMaxMeasuredPropertyOptions[groupId] = maxValue;
                    }
               });
               this.setElementOrder(displayValOne, dash, displayValTwo);
          });

          this._findButton.addEventListener('click', event => {
               var queryString = "";
               const allPropertySelectValues = Object.values(this._selectedPropertyOptions).flat();
               if (this._selectedCategoryOptionValue) {
                    allPropertySelectValues.push(this._selectedCategoryOptionValue);
               }
               const delimitedPropertiesString = allPropertySelectValues.filter(value => value !== '').join('|');
               if (delimitedPropertiesString) {
                    queryString = 'properties=' + encodeURIComponent(delimitedPropertiesString);
               }

               var minMaxParams = [];

               for (var id in this._selectedMinMeasuredPropertyOptions) {
                    if (this._selectedMinMeasuredPropertyOptions.hasOwnProperty(id)) {
                         minMaxParams.push(encodeURIComponent('min-property[' + id + ']') + '=' + this._selectedMinMeasuredPropertyOptions[id]);
                    }
               }
               for (var id in this._selectedMaxMeasuredPropertyOptions) {
                    if (this._selectedMaxMeasuredPropertyOptions.hasOwnProperty(id)) {
                         minMaxParams.push(encodeURIComponent('max-property[' + id + ']') + '=' + this._selectedMaxMeasuredPropertyOptions[id]);
                    }
               }
               
               var minMaxParamsUrl = minMaxParams.join('&');
               if (minMaxParamsUrl) {
                    if (queryString) {
                         queryString += '&' + minMaxParamsUrl;
                    } else {
                         queryString = minMaxParamsUrl;
                    }
               }
               const salesChannelBaseUrl = this._finderForm.getAttribute('data-sc-base-url');
               var baseUrl = "";
               if (salesChannelBaseUrl) {
                    baseUrl = salesChannelBaseUrl;
               }
               
               var mainCategoryName = 'Products';
               if (this._findButton.getAttribute('data-target-cat')) {
                    mainCategoryName = this._findButton.getAttribute('data-target-cat');
               }

               const targetUrl = `${baseUrl}/${mainCategoryName}/?${queryString}`;

               window.location.href = targetUrl;
          });
     }
     fetchAvailableOptions() {
          var queryString = "";
          var categoryOptionIdEncoded = "";
          if (this._selectedCategoryOptionValue) {
               categoryOptionIdEncoded = encodeURIComponent(this._selectedCategoryOptionValue);
          }
          if (categoryOptionIdEncoded) {
               var queryString = `options=${categoryOptionIdEncoded}`;
          }

          const salesChannelBaseUrl = this._finderForm.getAttribute('data-sc-base-url');
          var baseUrl = "";
          if (salesChannelBaseUrl) {
               baseUrl += salesChannelBaseUrl;
          }
          const formAction = this._finderForm.getAttribute('data-available-options-route');
          var controllerUrl = "/";
          if (formAction) {
               controllerUrl = formAction;
          }
          const url = `${baseUrl}${controllerUrl}?${queryString}`;

          this._client.get(url, this.availableOptionsHandler.bind(this));
     }

     availableOptionsHandler(response) {
          try {
               const responseObject = JSON.parse(response);
               if (responseObject && responseObject.availableOptionIds && Array.isArray(responseObject.availableOptionIds)) {
                    const availableOptionIds = responseObject.availableOptionIds;
                    this._propertyOptionsGroups.forEach(propertyGroupField => {
                         var availableOptionsCount = 0;
                         const groupId = propertyGroupField.getAttribute('data-property-group');
                         const propertyGroupFieldOptions = propertyGroupField.querySelectorAll('.js-finder-property-option-input');
                         propertyGroupFieldOptions.forEach(inputElement => {
                              const inputElementValue = inputElement.value;
                              if (availableOptionIds.includes(inputElementValue)) {
                                   availableOptionsCount++;
                              }
                         });
                         if (availableOptionsCount == 0) {
                              propertyGroupField.style.display = 'none';
                              delete this._selectedPropertyOptions[groupId];
                         } else {
                              propertyGroupField.style.display = 'flex';
                         }
                    });
                    this._minMaxOptionsGroups.forEach(propertyGroupField => {
                         var availableOptionsCount = 0;
                         const groupId = propertyGroupField.getAttribute('data-property-group');
                         const propertyGroupFieldOptions = propertyGroupField.querySelectorAll('.nwgncy-finder-slider-number option');
                         propertyGroupFieldOptions.forEach(inputElement => {
                              const inputElementId = inputElement.id;
                              if (availableOptionIds.includes(inputElementId)) {
                                   availableOptionsCount++;
                              }
                         });
                         if (availableOptionsCount == 0) {
                              propertyGroupField.style.display = 'none';
                              delete this._selectedMinMeasuredPropertyOptions[groupId];
                              delete this._selectedMaxMeasuredPropertyOptions[groupId];
                         } else {
                              propertyGroupField.style.display = 'flex';
                         }
                    });
               }
          } catch (error) {
          }
          this.finishLoading();
     }

     startLoading () {
          this._propertyOptionsGroupsContainer.style.display = 'none';
          this._loadingContainer.style.display = 'flex';
          this._isLoading = true;
     }
     finishLoading () {
          this._propertyOptionsGroupsContainer.style.display = 'flex';
          this._loadingContainer.style.display = 'none';
          this._isLoading = false;
     }

     /* slider functions */

     fillColor(min, max, sliderOne1, dash, sliderTrack, range1, range2) {
          let percent1 = (min.value / max.max) * 100;
          let percent2 = (max.value / max.max) * 100;
          sliderTrack.style.boxShadow = "";
          sliderTrack.style.background = `linear-gradient(to right, #f5f5f5 ${percent1}% , #2a77da ${percent1}% , #2a77da ${percent2}%, #f5f5f5 ${percent2}%)`;
          if (percent1 >= percent2) {
               sliderTrack.style.background = `linear-gradient(to right, #f5f5f5 ${percent2}% , #2a77da ${percent2}% , #2a77da ${percent1}%, #f5f5f5 ${percent1}%)`;
               sliderOne1.forEach(function (button) {
                    if (max.value < parseInt(button.getAttribute('data-value')) && min.value > parseInt(button.getAttribute('data-value'))) {
                         button.style.background = "#2a77da";
                    } else {
                         button.style.background = `linear-gradient(180deg,#f5f5f5,#f9f9f9)`;
                    }
               });
               this.setElementOrder(range2,dash,range1);

          } else {
               this.setElementOrder(range1,dash,range2);
               sliderOne1.forEach(function (button) {

                    if (min.value < parseInt(button.getAttribute('data-value')) && max.value > parseInt(button.getAttribute('data-value'))) {
                         button.style.background = "#2a77da";

                    } else button.style.background = `linear-gradient(180deg,#f5f5f5,#f9f9f9)`;

               });
          }
     }

     setElementOrder(order1, order2, order3) {
          order1.style.order = 1;
          order2.style.order = 2;
          order3.style.order = 3
     }

     isCloserToSlider(value, sliderOneValue, sliderTwoValue) {
          const diffToOne = Math.abs(value - sliderOneValue);
          const diffToTwo = Math.abs(value - sliderTwoValue);
          return diffToOne < diffToTwo;
     }

     slideOne(element, event, param, dash, sliderTrack, sliderOne, sliderTwo,  sliderOne1, stepVal, displayValTwo) {
          let displayValOne = element.querySelector("." + param);
          displayValOne.textContent = stepVal[event.target.value];
          this.fillColor(sliderOne, sliderTwo, sliderOne1, dash, sliderTrack, displayValOne, displayValTwo);
     }
     slideTwo(element, event, param, dash, sliderTrack, sliderOne, sliderTwo, sliderOne1, stepVal, displayValOne) {
          let displayValTwo = element.querySelector("." + param);
          displayValTwo.textContent = stepVal[event.target.value];
          this.fillColor(sliderOne, sliderTwo, sliderOne1, dash, sliderTrack, displayValOne, displayValTwo);
     }
}