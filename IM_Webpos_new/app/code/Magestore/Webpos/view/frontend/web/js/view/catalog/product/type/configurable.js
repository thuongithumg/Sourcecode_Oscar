/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'underscore',
        'mage/template',
        'Magestore_Webpos/js/model/catalog/product-factory',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/catalog/product/swatch',
        'Magestore_Webpos/js/helper/alert',
        'mage/translate',
        'Magestore_Webpos/js/model/catalog/product',
        'priceUtils',
        'priceBox',
        'jquery/ui',
        'jquery/jquery.parsequery',
        'Magento_Catalog/js/price-box',

    ],
    function ($, ko, Component, _, mageTemplate, ProductFactory, priceHelper, swatchModel, alertHelper, $t, productModel) {
        return {
            element: $('#product_addtocart_form'),
            detailPopup: ko.observable(),
            priceConfig: ko.observable({}),
            options: {
                superSelector: '.super-attribute-select',
                selectSimpleProduct: '[name="selected_configurable_option"]',
                priceHolderSelector: '.price-box',
                spConfig: {},
                state: {},
                priceFormat: {},
                optionTemplate: '',
                mediaGallerySelector: '[data-gallery-role=gallery-placeholder]',
                mediaGalleryInitial: null,
                lableConfig: {}
            },
            swatchOption: $('#swatch-option'),
            defaults: {},
            initialize: function () {
                //this.setAllData();
                this._super();
            },

            createPriceBox: function () {
                var priceBoxes = $('[data-role=priceBox]');

                priceBoxes = priceBoxes.filter(function (index, elem) {
                    return !$(elem).find('.price-from').length;
                });
                priceBoxes.priceBox({'priceConfig': this.priceConfig});
            },

            _create: function () {

                this._resetSwatch();
                // Initial setting of various option values
                this._initializeOptions();

                // Override defaults with URL query parameters and/or inputs values
                this._overrideDefaults();

                // Change events to check select reloads
                this._setupChangeEvents();


                $('body').off('observerSwatch');
                $('body').on('observerSwatch', function () {

                    $('.swatch-option').click(function () {


                        var optionLength = $('#attribute' + $(this).data('attribute')).find('option').length;

                        if (optionLength > 1) {
                            var attributeId = $(this).attr('id');

                            $('.attribute' + $(this).data('attribute')).removeClass('swatch-select');

                            $('#attribute' + $(this).data('attribute')).val(attributeId);

                            $('#attribute' + $(this).data('attribute')).trigger('change', attributeId);
                            $(this).addClass('swatch-select');
                        } else {
                            //alertHelper({title:'Error', content: $t('Please choose previous options')});
                        }

                    });
                });

                // Fill state
                this._fillState();

                // Setup child and prev/next settings
                this._setChildSettings();

                // Setup/configure values to inputs
                this._configureForValues();
            },

            /**
             * Initialize tax configuration, initial settings, and options values.
             * @private
             */
            _initializeOptions: function () {
                var options = this.options,
                    gallery = $(options.mediaGallerySelector),
                    priceBoxOptions = $(this.options.priceHolderSelector).priceBox('option').priceConfig || null;
                if (priceBoxOptions && priceBoxOptions.optionTemplate) {
                    options.optionTemplate = priceBoxOptions.optionTemplate;
                }

                if (priceBoxOptions && priceBoxOptions.priceFormat) {
                    options.priceFormat = priceBoxOptions.priceFormat;
                }
                options.optionTemplate = mageTemplate(options.optionTemplate);

                options.settings = options.spConfig.containerId ?
                    $(options.spConfig.containerId).find(options.superSelector) :
                    $(options.superSelector);

                options.values = options.spConfig.defaultValues || {};
                options.parentImage = $('[data-role=base-image-container] img').attr('src');
                var allOption = options.spConfig.attributes;
                var swatchAllOption = [];
                var allOptionArray = Object.keys(allOption).map(function (key) {
                    return allOption[key];
                });
                allOptionArray = this.sortByKey(allOptionArray, 'position');
                $.each(allOptionArray, function (index, attributeValue) {
                    this._generateSwatchDiv(attributeValue);
                }.bind(this));
                options.swatchOption = swatchAllOption;


                this.inputSimpleProduct = this.element.find(options.selectSimpleProduct);
                gallery.on('gallery:loaded', function () {
                    var galleryObject = gallery.data('gallery');
                    options.mediaGalleryInitial = galleryObject.returnCurrentImages();
                });
            },

            sortByKey: function (array, key) {
                return array.sort(function (a, b) {
                    var x = a[key];
                    var y = b[key];

                    if (typeof x == "string") {
                        x = x.toLowerCase();
                    }
                    if (typeof y == "string") {
                        y = y.toLowerCase();
                    }

                    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
                });
            },

            _generateSwatchDiv: function (attributeValue) {
                var deferred = swatchModel().load(attributeValue.id);
                var optionValue = attributeValue.options;
                deferred.done(function (data) {
                    if (data && typeof data.swatches != 'undefined') {
                        this._breakDiv();
                        this._insertAttributeTitle(attributeValue.label);
                        $('#attribute' + attributeValue.id).parent().parent()
                            .css('visibility', 'hidden')
                            .css('height', '0');

                        $.each(optionValue, function (index, value) {
                            var swatchData = data.swatches[value.id];
                            if (swatchData.type == '1') {
                                this._pushColor(swatchData.value, value.id, attributeValue.id);
                            }

                            if (swatchData.type == '0') {
                                this._pushText(swatchData.value, value.id, attributeValue.id);
                            }
                        }.bind(this));

                        this._breakDiv();
                        $('body').trigger('observerSwatch');
                    }
                    var countOptionNumber = this.countOptionNumber(attributeValue.id);
                    if (countOptionNumber == 1) {
                        $('.attribute' + attributeValue.id).addClass('swatch-hidden');
                    }


                }.bind(this));
            },

            /**
             * Override default options values settings with either URL query parameters or
             * initialized inputs values.
             * @private
             */
            _overrideDefaults: function () {
                var hashIndex = window.location.href.indexOf('#');

                if (hashIndex !== -1) {
                    this._parseQueryParams(window.location.href.substr(hashIndex + 1));
                }

                if (this.options.spConfig.inputsInitialized) {
                    this._setValuesByAttribute();
                }
            },

            /**
             * Parse query parameters from a query string and set options values based on the
             * key value pairs of the parameters.
             * @param {*} queryString - URL query string containing query parameters.
             * @private
             */
            _parseQueryParams: function (queryString) {
                var queryParams = $.parseQuery({
                    query: queryString
                });

                $.each(queryParams, $.proxy(function (key, value) {
                    this.options.values[key] = value;
                }, this));
            },

            /**
             * Override default options values with values based on each element's attribute
             * identifier.
             * @private
             */
            _setValuesByAttribute: function () {
                this.options.values = {};
                $.each(this.options.settings, $.proxy(function (index, element) {
                    var attributeId;

                    if (element.value) {
                        attributeId = element.id.replace(/[a-z]*/, '');
                        this.options.values[attributeId] = element.value;
                    }
                }, this));
            },

            /**
             * Set up .on('change') events for each option element to configure the option.
             * @private
             */
            _setupChangeEvents: function () {
                $.each(this.options.settings, $.proxy(function (index, element) {
                    $(element).on('change', this, this._configure);
                }, this));
            },

            /**
             * Iterate through the option settings and set each option's element configuration,
             * attribute identifier. Set the state based on the attribute identifier.
             * @private
             */
            _fillState: function () {
                $.each(this.options.settings, $.proxy(function (index, element) {
                    var attributeId = element.id.replace(/[a-z]*/, '');

                    if (attributeId && this.options.spConfig.attributes[attributeId]) {
                        element.config = this.options.spConfig.attributes[attributeId];
                        element.attributeId = attributeId;
                        this.options.state[attributeId] = false;
                    }
                }, this));
            },

            /**
             * Set each option's child settings, and next/prev option setting. Fill (initialize)
             * an option's list of selections as needed or disable an option's setting.
             * @private
             */
            _setChildSettings: function () {
                var childSettings = [],
                    settings = this.options.settings,
                    index = settings.length,
                    option;

                while (index--) {
                    option = settings[index];

                    if (index) {
                        option.disabled = true;
                    } else {
                        this._fillSelect(option);
                    }
                    _.extend(option, {
                        childSettings: childSettings.slice(),
                        prevSetting: settings[index - 1],
                        nextSetting: settings[index + 1]
                    });

                    childSettings.push(option);
                }
            },

            /**
             * Setup for all configurable option settings. Set the value of the option and configure
             * the option, which sets its state, and initializes the option's choices, etc.
             * @private
             */
            _configureForValues: function () {
                if (this.options.values) {
                    this.options.settings.each($.proxy(function (index, element) {
                        var attributeId = element.attributeId;
                        element.value = this.options.values[attributeId] || '';
                        this._configureElement(element);
                    }, this));
                }
            },

            _pushOpenDiv: function () {
                var swatchOption = $('#swatch-option');
                var oldHtml = swatchOption.html();
                swatchOption.html(oldHtml + "<div>");
            },

            _pushCloseDiv: function () {
                var swatchOption = $('#swatch-option');
                var oldHtml = swatchOption.html();
                swatchOption.html(oldHtml + "</div>");
            },

            _pushColor: function (color, optionValue, attributeValue) {
                var swatchOption = $('#swatch-option');
                var oldHtml = swatchOption.html();
                swatchOption.html(oldHtml + "<div id='" + optionValue + "' class='swatch-option attribute" + attributeValue + "' data-attribute= '" + attributeValue + "' style='background-color:" + color + "'></div>");
            },


            _pushText: function (text, optionValue, attributeValue) {
                var swatchOption = $('#swatch-option');
                var oldHtml = swatchOption.html();
                swatchOption.html(oldHtml + "<div id='" + optionValue + "' class='swatch-option attribute" + attributeValue + "' data-attribute= '" + attributeValue + "'><span>" + text + "</span></div>");
            },

            _breakDiv: function () {
                var swatchOption = $('#swatch-option');
                var oldHtml = swatchOption.html();
                swatchOption.html(oldHtml + "<div style='clear:both'></div>");
            },

            _insertAttributeTitle: function (title) {
                var swatchOption = $('#swatch-option');
                var oldHtml = swatchOption.html();
                swatchOption.html(oldHtml + "<div style='clear:both'>" + title + "</div>");
            },

            _resetSwatch: function () {
                $('#swatch-option').html('');
            },

            /**
             * Event handler for configuring an option.
             * @private
             * @param {Object} event - Event triggered to configure an option.
             */
            _configure: function (event) {
                event.data._configureElement(this);
            },

            /**
             * Configure an option, initializing it's state and enabling related options, which
             * populates the related option's selection and resets child option selections.
             * @private
             * @param {*} element - The element associated with a configurable option.
             */
            _configureElement: function (element) {
                this.simpleProduct = this._getSimpleProductId(element);

                if (element.value) {
                    this.options.state[element.config.id] = element.value;
                    if (element.nextSetting) {
                        element.nextSetting.disabled = false;
                        this._fillSelect(element.nextSetting);
                        this._resetChildren(element.nextSetting);

                    } else {
                        if (!!document.documentMode) {
                            document.getElementsByName('selected_configurable_option')[0].value = element.options[element.selectedIndex].config.allowedProducts[0];
                        } else {
                            document.getElementsByName('selected_configurable_option')[0].value = element.selectedOptions[0].config.allowedProducts[0];
                        }

                        var spConfigData = $.parseJSON(this.detailPopup().itemData().json_config);
                        var defaultPrice = spConfigData.prices.finalPrice.amount;

                        var priceOption = this._getPrices();
                        var finalPrice = defaultPrice;
                        var cachePrice = defaultPrice;
                        $.map(priceOption, function (el) {
                            if (el) {
                                if (el.finalPrice) {
                                    if (el.finalPrice.amount) {
                                        finalPrice = parseFloat(cachePrice) + parseFloat(el.finalPrice.amount);
                                    }
                                }
                            }
                        });
                    }
                    itemData = this.detailPopup().itemData();

                    if ($('.screen-shoot-detail').is(":visible")) {
                        var productDeferred = ProductFactory.get().load(parseInt(this.simpleProduct));
                        productDeferred.done(function (data) {
                            if (data && typeof data.image != 'undefined') {
                                itemData.image = data.image;
                                this.detailPopup().itemData(itemData);
                            }

                        }.bind(this));
                    }

                    this.detailPopup().configurableProductIdResult(this.simpleProduct);
                    var optionObjectResult = this.options.state;
                    var optionObjectResultArr = [];
                    $.map(optionObjectResult, function (val, index) {
                        optionObjectResultArr.push({id: index, value: val});
                    });
                    this.detailPopup().configurableOptionsResult(optionObjectResultArr);
                    if (this.simpleProduct) {
                        this.detailPopup().basePriceAmount(this.options.spConfig.optionPrices[this.simpleProduct].finalPrice.amount);
                        this.detailPopup().defaultPriceAmount(priceHelper.convertAndFormat(this.options.spConfig.optionPrices[this.simpleProduct].finalPrice.amount));
                    }
                    this.options.lableConfig[element.config.id] = element.selectedOptions[0].config.label;
                    var labelObject = this.options.lableConfig;
                    var lableObjectArr = [];
                    $.map(labelObject, function (val, index) {
                        lableObjectArr.push({id: index, value: val});
                    });
                    this.detailPopup().configurableLabelResult(lableObjectArr);
                } else {
                    this._resetChildren(element);
                }
                this._reloadPrice();
            },

            /**
             * For a given option element, reset all of its selectable options. Clear any selected
             * index, disable the option choice, and reset the option's state if necessary.
             * @private
             * @param {*} element - The element associated with a configurable option.
             */
            _resetChildren: function (element) {

                $('.' + element.id).removeClass('swatch-select');
                if (element.childSettings) {
                    _.each(element.childSettings, function (set) {
                        set.selectedIndex = 0;
                        set.disabled = true;
                    });

                    if (element.config) {
                        this.options.state[element.config.id] = false;
                    }
                }
            },

            /**
             * Populates an option's selectable choices.
             * @private
             * @param {*} element - Element associated with a configurable option.
             */
            _fillSelect: function (element) {
                var attributeId = element.id.replace(/[a-z]*/, ''),
                    options = this._getAttributeOptions(attributeId),
                    prevConfig,
                    index = 1,
                    allowedProducts,
                    i,
                    j;

                this._clearSelect(element);
                element.options[0] = new Option('', '');
                element.options[0].innerHTML = this.options.spConfig.chooseText;
                prevConfig = false;

                if (element.prevSetting) {
                    prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
                }

                if (options) {
                    for (i = 0; i < options.length; i++) {
                        allowedProducts = [];

                        if (prevConfig) {
                            for (j = 0; j < options[i].products.length; j++) {
                                // prevConfig.config can be undefined
                                if (prevConfig.config &&
                                    prevConfig.config.allowedProducts &&
                                    prevConfig.config.allowedProducts.indexOf(options[i].products[j]) > -1) {
                                    allowedProducts.push(options[i].products[j]);
                                }
                            }
                        } else {
                            allowedProducts = options[i].products.slice(0);
                        }

                        if (allowedProducts.length > 0) {
                            options[i].allowedProducts = allowedProducts;
                            element.options[index] = new Option(this._getOptionLabel(options[i]), options[i].id);

                            if (typeof options[i].price !== 'undefined') {
                                element.options[index].setAttribute('price', options[i].prices);
                            }

                            element.options[index].config = options[i];
                            index++;
                        }
                    }
                }


                var allOption = element.options;
                var allOptionValue = [];
                $('#attribute' + attributeId).find('option').each(function (index, value) {
                    if ($(this).val()) {
                        allOptionValue.push($(this).val());
                    }
                });
                $('.attribute' + attributeId).each(function (index, value) {
                    var id = ($(this).attr('id'));
                    if ($.inArray(id, allOptionValue) == -1) {
                        $(this).addClass('.swatch-hidden');
                    }
                });
                var optionCount = this.countOptionNumber(attributeId);
                if (optionCount > 1) {
                    $('.attribute' + attributeId).removeClass('swatch-hidden');
                }

            },

            countOptionNumber: function (attributeId) {
                return $('#attribute' + attributeId).find('option').length;
            },

            /**
             * Generate the label associated with a configurable option. This includes the option's
             * label or value and the option's price.
             * @private
             * @param {*} option - A single choice among a group of choices for a configurable option.
             * @return {String} The option label with option value and price (e.g. Black +1.99)
             */
            _getOptionLabel: function (option) {
                return option.label;
            },

            /**
             * Removes an option's selections.
             * @private
             * @param {*} element - The element associated with a configurable option.
             */
            _clearSelect: function (element) {
                var i;

                for (i = element.options.length - 1; i >= 0; i--) {
                    element.remove(i);
                }
            },

            /**
             * Retrieve the attribute options associated with a specific attribute Id.
             * @private
             * @param {Number} attributeId - The id of the attribute whose configurable options are sought.
             * @return {Object} Object containing the attribute options.
             */
            _getAttributeOptions: function (attributeId) {
                if (this.options.spConfig.attributes[attributeId]) {
                    return this.options.spConfig.attributes[attributeId].options;
                }
            },

            /**
             * Reload the price of the configurable product incorporating the prices of all of the
             * configurable product's option selections.
             */
            _reloadPrice: function () {
                $(this.options.priceHolderSelector).trigger('updatePrice', this._getPrices());
            },

            /**
             * Get product various prices
             * @returns {{}}
             * @private
             */
            _getPrices: function () {
                var prices = {},
                    elements = _.toArray(this.options.settings),
                    hasProductPrice = false;

                _.each(elements, function (element) {
                    var selected = element.options[element.selectedIndex],
                        config = selected && selected.config,
                        priceValue = {};
                    if (config && config.allowedProducts.length === 1 && !hasProductPrice) {
                        priceValue = this._calculatePrice(config);
                        hasProductPrice = true;
                    }
                    prices[element.attributeId] = priceValue;
                }, this);
                return prices;
            },

            /**
             * Returns pracies for configured products
             *
             * @param {*} config - Products configuration
             * @returns {*}
             * @private
             */
            _calculatePrice: function (config) {
                var displayPrices = $(this.options.priceHolderSelector).priceBox('option').prices,
                    newPrices = this.options.spConfig.optionPrices[_.first(config.allowedProducts)];
                _.each(displayPrices, function (price, code) {
                    if (newPrices[code]) {
                        displayPrices[code].amount = newPrices[code].amount - displayPrices[code].amount;
                    }
                });

                return displayPrices;
            },

            /**
             * Returns Simple product Id
             *  depending on current selected option.
             *
             * @private
             * @param {HTMLElement} element
             * @returns {String|undefined}
             */
            _getSimpleProductId: function (element) {
                // TODO: Rewrite algorithm. It should return ID of
                //        simple product based on selected options.
                if (!element.config)
                    element.config = {};
                var allOptions = element.config.options,
                    value = element.value,
                    config;

                config = _.filter(allOptions, function (option) {
                    return option.id === value;
                });
                config = _.first(config);

                return _.isEmpty(config) || config.allowedProducts.length > 1 ?
                    undefined :
                    _.first(config.allowedProducts);

            }

        };
    }
);