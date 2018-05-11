/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/view/catalog/product/type/configurable',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/alert',
        'Magestore_Webpos/js/model/catalog/product-factory',
        'Magestore_Webpos/js/model/inventory/stock-item-factory',
        'Magestore_Webpos/js/model/giftvoucher/giftvoucher',
        'Magestore_Webpos/js/model/checkout/integration/giftcard/giftvoucher-template-factory',
        'mage/translate',
        'Magestore_Webpos/js/helper/general',
        'mage/validation',
        'mage/validation/validation',
    ],
    function ($, ko, Component, configurable, CartModel,
              priceHelper, alertHelper, ProductFactory, StockItemFactory, giftvoucherModel, GiftvoucherTemplateFactory, $t, Helper) {
        "use strict";
        window.timeout = 0;
        return Component.extend({
            initialized: ko.observable(false),
            initializeds: false,
            itemData: ko.observable({}),
            styleOfPopup: ko.observable('view_detail'),
            focusQtyInput: true,
            qtyAddToCart: ko.observable(1),
            defaultPriceAmount: ko.observable(),
            basePriceAmount: ko.observable(),
            configurableProductIdResult: ko.observable(),
            configurableOptionsResult: ko.observable(),
            configurableLabelResult: ko.observable(),
            groupedProductResult: ko.observableArray([]),
            bundleOptionsValueResult: ko.observableArray([]),
            bundleOptionsQtyResult: ko.observableArray([]),
            bundleChildsQtyResult: ko.observableArray([]),
            bundleOptionsLableResult: ko.observableArray([]),
            customOptionsValueResult: ko.observableArray([]),
            customOptionsLableResult: ko.observableArray([]),
            creditProductResult: ko.observableArray([]),
            creditValue: ko.observable(),
            bundleItem: ko.observable(),
            groupItem: ko.observable(),
            allChildQtys: null,
            childQty: ko.observable(null),
            defaults: {
                template: 'Magestore_Webpos/catalog/product/detail-popup'
            },
            initialize: function () {
                this._super();
                var self = this;
                this.resetSuperAttributeData();
                configurable.detailPopup(this);
                if (!this.initialized()) {
                    this.initChildrenQty();
                    this.initialized(true);
                }
                this.getProductPrice.subscribe(function () {
                    self.defaultPriceAmount(self.getProductPrice());
                });
                if (parseInt(window.webposConfig.displayImageItem)) {
                    giftvoucherModel.selectedImage.subscribe(function () {
                        if (giftvoucherModel.selectedImage()) {
                            var oldData = self.itemData();
                            oldData.image = giftvoucherModel.selectedImage();
                            self.itemData(oldData);
                        }
                    });
                }

            },

            getProductPrice: ko.computed(function () {
                var giftCardValue = giftvoucherModel.giftCardValue();
                if (giftvoucherModel.selectPriceType() === giftvoucherModel.SELECT_PRICE_TYPE_DROPDOWN ||
                    giftvoucherModel.selectPriceType()  === giftvoucherModel.SELECT_PRICE_TYPE_RANGE) {
                    if (giftvoucherModel.priceType() === giftvoucherModel.PRICE_TYPE_PERCENT) {
                        var percent = giftvoucherModel.giftCardPrice();
                        return priceHelper.convertAndFormat(giftCardValue * percent / 100);
                    } else if (giftvoucherModel.priceType() === giftvoucherModel.PRICE_TYPE_FIXED) {
                        return priceHelper.convertAndFormat(parseFloat(giftvoucherModel.giftCardPrice()));
                    } else {
                        return priceHelper.convertAndFormat(giftCardValue);
                    }
                }
                else if (giftvoucherModel.priceType() === giftvoucherModel.PRICE_TYPE_FIXED) {
                    return priceHelper.convertAndFormat(giftvoucherModel.giftCardPrice());
                }

            }),

            getTypeId: function () {
                return this.itemData().type_id;
            },
            getQtyIncrement: function () {
                return parseFloat(this.getProductData().qty_increment);
            },
            isQtyDecimal: function () {
                return (parseInt(this.getProductData().is_qty_decimal) == 1) ? true : false;
            },
            incQty: function () {
                var qty = this.getQtyAddToCart();
                var increment = this.getQtyIncrement();
                increment = (increment > 0) ? increment : 1;
                qty += increment;
                this.qtyAddToCart(qty);
            },
            descQty: function () {
                var qty = this.getQtyAddToCart();
                var increment = this.getQtyIncrement();
                increment = (increment > 0) ? increment : 1;
                qty -= increment;
                this.qtyAddToCart(qty);
            },
            getQtyAddToCart: function () {
                var increment = this.getQtyIncrement();
                if (this.qtyAddToCart() <= increment || isNaN(this.qtyAddToCart())) {
                    return increment;
                }
                return this.qtyAddToCart();
            },
            modifyQty: function (data, event) {
                var increment = this.getQtyIncrement();
                var isQtyDecimal = this.isQtyDecimal();
                var qty = (isQtyDecimal) ? parseFloat(event.target.value) : parseInt(event.target.value);
                if ((increment > 0) && qty % increment > 0) {
                    qty -= (isQtyDecimal) ? parseFloat(qty % increment) : parseInt(qty % increment);
                    qty = (qty > 0) ? qty : increment;
                }
                event.target.value = qty;
                this.qtyAddToCart(qty);
            },
            setAllData: function () {
                this.qtyAddToCart(1);
                if (this.itemData().images) {
                    this.reloadJs();
                }
                /* set config */
                this.defaultPriceAmount('');
                //configurable.priceConfig($.parseJSON(self.getItemData().price_config));
                if (this.getTypeId() == 'configurable') {
                    if (window.timeout != 0) {
                        var timeout = 0;
                    } else {
                        window.timeout = 1;
                        var timeout = 1000;
                    }
                    var spConfigData = $.parseJSON(this.itemData().json_config);
                    this.defaultPriceAmount(priceHelper.convertAndFormat(spConfigData.prices.finalPrice.amount));
                    setTimeout(function () {
                        configurable.priceConfig($.parseJSON(this.itemData().price_config));
                        configurable.options.spConfig = spConfigData;
                        configurable.options.optionTemplate = '<%- data.label %>' +
                            '<% if (data.finalPrice.value) { %>' +
                            ' <%- data.finalPrice.formatted %>' +
                            '<% } %>';
                        configurable.createPriceBox();
                        configurable._create();
                    }.bind(this), timeout);
                } else {
                    this.defaultPriceAmount(priceHelper.convertAndFormat(this.itemData().final_price));
                    //configurable.priceConfig($.parseJSON(self.itemData().price_config));
                }

                if (this.getTypeId() === 'giftcard') {

                }

                if (this.getTypeId() === 'giftvoucher') {
                    giftvoucherModel.templates([]);
                    var data = this.itemData();
                    giftvoucherModel.giftAmountStatic(data.giftvoucher_value);
                    giftvoucherModel.giftAmountFrom(data.giftvoucher_from);
                    giftvoucherModel.giftAmountTo(data.giftvoucher_to);
                    if (data.giftvoucher_type !== giftvoucherModel.TYPE_VIRTUAL &&
                        data.giftvoucher_select_price_type === giftvoucherModel.SELECT_PRICE_TYPE_FIX ) {
                        data.giftvoucher_price = data.giftvoucher_value;
                    }
                    giftvoucherModel.giftCardPrice(data.giftvoucher_price);
                    console.log(data.giftvoucher_price);
                    var giftDropDown;
                    if (data.giftvoucher_dropdown) {
                        giftDropDown = data.giftvoucher_dropdown.split(",").map(Number);
                        giftvoucherModel.giftAmountOption(giftDropDown);
                    }


                    giftvoucherModel.selectPriceType(data.giftvoucher_select_price_type);
                    giftvoucherModel.type(data.giftvoucher_type);
                    giftvoucherModel.priceType(data.giftvoucher_price_type);
                    giftvoucherModel.giftCardValue(data.giftvoucher_value);
                    if (data.giftvoucher_type === giftvoucherModel.TYPE_PHYSICAL) {
                        giftvoucherModel.defaultCheckedPostal(true);
                    } else {
                        giftvoucherModel.defaultCheckedPostal(false);
                    }


                    if (data.giftvoucher_type === giftvoucherModel.TYPE_VIRTUAL) {
                        giftvoucherModel.defaultCheckedSender(true);
                        giftvoucherModel.sendToFriend(true);
                    } else {
                        giftvoucherModel.defaultCheckedSender(false);
                        giftvoucherModel.sendToFriend(false);
                    }

                    if (data.giftvoucher_select_price_type === giftvoucherModel.SELECT_PRICE_TYPE_FIX) {
                        giftvoucherModel.giftCardValue(data.giftvoucher_value);
                    } else if (data.giftvoucher_select_price_type === giftvoucherModel.SELECT_PRICE_TYPE_RANGE) {
                        giftvoucherModel.giftCardValue(data.giftvoucher_from);
                    } else {

                        giftDropDown = data.giftvoucher_dropdown.split(",").map(Number);
                        if (typeof giftDropDown[0] !== 'undefined') {
                            giftvoucherModel.giftCardValue(giftDropDown[0]);
                            giftvoucherModel.choosePrice(giftDropDown[0]);
                        }

                    }

                    if (data.giftvoucher_type !== giftvoucherModel.TYPE_PHYSICAL) {
                        var giftvoucher_template = data.giftvoucher_template;
                        var array = giftvoucher_template.split(",").map(Number);

                        $.each(array, function (index, value) {
                            var deferred = GiftvoucherTemplateFactory.get().setMode('offline').load(value.toString());
                            deferred.done(function (response) {
                                var giftTemplateInfo = {
                                    'giftcard_template_id': response.giftcard_template_id,
                                    'template_name': response.template_name,
                                    'images': response.images
                                };
                                giftvoucherModel.templates.push(giftTemplateInfo);
                                if (index === 0) {
                                    giftvoucherModel.selectedTemplate(giftTemplateInfo);
                                    var templateId = giftTemplateInfo.giftcard_template_id;
                                    giftvoucherModel.selectedTemplateImage(templateId);
                                    giftvoucherModel.selectedTemplate(giftTemplateInfo);
                                    giftvoucherModel.chooseImage(templateId);
                                    giftvoucherModel.chooseFirstImageOfTemplate(giftvoucherModel.selectedTemplate());
                                }

                            });
                        });
                    }
                }
            },
            prepareAddToCart: function () {
                this.updatePrice();

                if (this.validateAddToCartForm()) {
                    var product = this.getProductData();
                    var stocks = product.stocks;
                    if (product.super_group && product.super_group.length > 0) {
                        ko.utils.arrayForEach(product.super_group, function (product) {
                            if (product.id) {
                                for (var i in stocks) {
                                    if (stocks[i].sku === product.sku) {
                                        product.stocks = [stocks[i]];
                                        break;
                                    }
                                }
                                ProductFactory.get().data = product;
                                product.unit_price = ProductFactory.get().getFinalPrice();
                                this.addProduct(product);
                            }
                        }.bind(this));
                    } else if (product.storecredit_type == 2) {
                        var rate = parseFloat(product.storecredit_rate);
                        //if (parseFloat($('#storecredit_' + product.id).val()) < parseFloat(product.storecredit_min) || parseFloat($('#storecredit_' + product.id).val()) > parseFloat(product.storecredit_max)) {
                        if (parseFloat($('#storecredit_' + product.id).val()) < parseFloat(priceHelper.currencyConvert(product.storecredit_min))
                            || parseFloat($('#storecredit_' + product.id).val()) > priceHelper.currencyConvert(product.storecredit_max)) {
                            alertHelper({
                                priority: "danger",
                                title: "Error",
                                message: "Invalid credit value!"
                            });
                        } else {
                            var from = window.webposConfig.currentCurrencyCode;
                            var to = window.webposConfig.baseCurrencyCode;
                            var price = priceHelper.currencyConvert($('#storecredit_' + product.id).val(),from,to);
                            var basePrice = parseFloat(price) * rate;
                            this.basePriceAmount(basePrice);
                            this.creditValue(parseFloat(price));
                            product = this.getProductData();
                            this.addProduct(product);
                            product.credit_price_amount = undefined;
                        }
                    } else if (product.storecredit_type == 3) {
                        product = this.getProductData();
                        this.addProduct(product);
                        product.credit_price_amount = undefined;
                    } else if (product.type_id === 'giftvoucher') {
                        product = this.getProductData();
                        this.addProduct(product);
                        product.credit_price_amount = undefined;
                        //gift voucher
                    } else {
                        this.addProduct(product);
                        product.credit_price_amount = undefined;
                    }
                    this.closeDetailPopup();
                } else {
                    if ($('.swatch-option').length > 0) {
                        alertHelper({title: 'Error', content: $t('Please choose all options')});
                    }
                }
            },
            /* Validate Add Address Form */
            validateAddToCartForm: function () {
                return $('#product_addtocart_form').validation() && $('#product_addtocart_form').validation('isValid');
            },
            giftVoucherField: [
                'customer_name',
                'amount',
                'giftcard_template_id',
                'giftcard_template_image',
                'send_friend',
                'recipient_name',
                'recipient_email',
                'message',
                'day_to_send',
                'timezone_to_send',
                'recipient_address',
                'notify_success',
                'recipient_ship'
            ],
            getProductData: function () {
                var product = this.itemData();
                if (product.type_id == "configurable") {
                    product.super_attribute = this.configurableOptionsResult();
                    product.unit_price = this.basePriceAmount();
                    product.child_id = this.configurableProductIdResult();
                    product.child_product = ko.utils.arrayFirst(product.children_products, function (childProduct) {
                        return (childProduct && childProduct.id == product.child_id);
                    });
                    product.options_label = this.configurableLabelResult();
                }
                if (product.type_id == "grouped") {
                    product.super_group = this.groupedProductResult();
                    product.unit_price = "";
                    product.options_label = "";
                }
                if (product.type_id == "customercredit") {
                    var rate = product.storecredit_rate;
                    if (typeof product.credit_price_amount !== 'undefined') {
                        product.amount = this.creditValue();
                        product.credit_price_amount = parseFloat(product.amount) * parseFloat(rate);
                    } else {
                        if (product.storecredit_type == 3) {
                            var values = product.customercredit_value.split(',');
                            product.credit_price_amount = parseFloat(values[0]) * parseFloat(rate);
                            product.amount = parseFloat(values[0]);
                        } else if (product.storecredit_type == 2) {
                            product.credit_price_amount = parseFloat(product.storecredit_min) * parseFloat(rate);
                            product.amount = parseFloat(product.storecredit_min);
                        } else {
                            product.credit_price_amount = parseFloat(product.customercredit_value) * parseFloat(rate);
                            product.amount = parseFloat(product.customercredit_value);
                        }
                    }
                    this.creditValue(product.amount);
                    this.basePriceAmount(product.credit_price_amount);
                    product.unit_price = this.basePriceAmount();
                    product.options_label = priceHelper.convertAndFormat(this.creditValue());
                    product.hasOption = true;
                }
                if (product.type_id == "bundle") {
                    product.bundle_option = this.bundleOptionsValueResult();
                    product.bundle_option_qty = this.bundleOptionsQtyResult();
                    product.bundle_childs_qty = this.bundleChildsQtyResult();
                    product.unit_price = this.basePriceAmount();
                    product.options_label = this.bundleOptionsLableResult();
                }
                if (this.customOptionsValueResult().length > 0) {
                    product.selected_options = this.customOptionsValueResult();
                    product.unit_price = this.basePriceAmount();
                    product.custom_options_label = this.customOptionsLableResult();
                }
                if (product.type_id == "giftvoucher") {
                    var productDataFromFrom = this.getFormData($('#product_addtocart_form'));
                    $.each(this.giftVoucherField, function (index, value) {
                        if (typeof productDataFromFrom[value] !== 'undefined') {
                            product[value] = productDataFromFrom[value];
                        }
                        if (value === 'recipient_ship' && typeof productDataFromFrom[value] !== 'undefined'
                            && productDataFromFrom[value]) {
                            product.is_virtual = true;
                        }
                    });
                }
                product.qty = this.qtyAddToCart();


                return product;
            },

            getFormData: function ($form) {
                var unindexed_array = $form.serializeArray();
                var indexed_array = {};

                $.map(unindexed_array, function (n, i) {
                    indexed_array[n['name']] = n['value'];
                });

                return indexed_array;
            },

            addProduct: function (product) {
                if (!product.stocks && !Helper.isUseOnline('stocks')) {
                    StockItemFactory.get().loadByProductId(product.id).done(function (stock) {
                        if (stock.data) {
                            product.stocks = [stock.data];
                            ProductFactory.get().setData(product);
                            CartModel.addProduct(ProductFactory.get().getInfoBuyRequest(CartModel.customerGroup()));
                            this.customOptionsValueResult([]);
                            this.customOptionsLableResult([]);
                            $("#search-header-product").val("");
                        }
                    }.bind(this));
                } else {
                    ProductFactory.get().setData(product);
                    CartModel.addProduct(ProductFactory.get().getInfoBuyRequest(CartModel.customerGroup()));
                    ProductFactory.get().resetTempAddData();
                    this.customOptionsValueResult([]);
                    this.customOptionsLableResult([]);
                    $("#search-header-product").val("");
                }
            },
            closeDetailPopup: function () {
                var self = this;
                $("#popup-product-detail").hide();
                $(".wrap-backover").hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
                $('#product-options-wrapper input').val('');
                giftvoucherModel.resetGiftvoucherInfo();
                self.itemData({});
            },

            reloadJs: function () {
                if ($("#product-img-slise").find('div.owl-controls').length > 0) {
                    $("#product-img-slise").find('div.owl-controls')[0].remove();
                }
                setTimeout(function () {
                    $("#product-img-slise").owlCarousels({
                        items: 1,
                        itemsDesktop: [1000, 1],
                        itemsDesktopSmall: [900, 1],
                        itemsTablet: [600, 1],
                        itemsMobile: false,
                        navigation: true,
                        pagination: true,
                        navigationText: ["", ""]
                    });
                }, 50);
            },
            updatePrice: function () {
                if (this.itemData().type_id == "grouped" && this.groupItem()) {
                    this.groupItem().updatePrice();
                }
                if (this.itemData().type_id == "bundle" && this.bundleItem()) {
                    this.bundleItem().updatePrice();
                }

            },
            showPopup: function () {
                this.itemData().super_attribute = [];
                this.itemData().super_group = [];
                this.itemData().bundle_option = [];
                this.itemData().bundle_option_qty = [];
                this.itemData().bundle_childs_qty = [];
                this.itemData().options_label = '';
                this.itemData().selected_options = [];
                this.itemData().custom_options_label = '';
                this.configurableOptionsResult([]);
                this.configurableLabelResult('');
                this.customOptionsValueResult([]);
                this.customOptionsLableResult([]);
                this.creditProductResult([]);
                this.bundleOptionsLableResult([]);
                this.bundleChildsQtyResult([]);
                this.bundleOptionsQtyResult([]);
                this.bundleOptionsValueResult([]);
                this.groupedProductResult([]);
                configurable.options.state = {};

                $("#popup-product-detail").show();
                $("#popup-product-detail").removeClass("fade");
                $(".wrap-backover").show();

                $(document).click(function (e) {
                    if (e.target.id == 'popup-product-detail') {
                        $("#popup-product-detail").hide();
                        $(".wrap-backover").hide();
                        $('.notification-bell').show();
                        $('#c-button--push-left').show();
                    }
                });
                this.updatePrice();
            },

            isShowAvailableQty: function () {
                if (this.itemData().is_virtual) {
                    return false;
                }
                return true;
            },

            getAvailableQty: function (productData) {
                var qty = 0;

                for (var i in productData.stocks) {
                    qty = qty + productData.stocks[i].qty;
                }
                if (Helper.isStockOnline()) {
                    qty = (productData.qty_online) ? productData.qty_online : 0;
                }
                if (productData.type_id == 'configurable'
                    || productData.type_id == 'bundle'
                    || productData.type_id == 'grouped') {
                    return qty + ' ' + $t('child item(s)');
                } else {
                    return qty + ' ' + $t('item(s)');
                }
            },

            resetSuperAttributeData: function () {
                configurable.options.state = {};
            },
            /**
             * Init children qty when choose configurable options
             */
            initChildrenQty: function () {
                ko.computed(function () {
                    return this.itemData();
                }, this).subscribe(function () {
                    this.allChildQtys = [];
                    this.childQty(null);
                    this.configurableProductIdResult(null);
                }, this);
                ko.pureComputed(function () {
                    return this.configurableProductIdResult();
                }, this).subscribe(function () {
                    this.childQty(null);
                    if (this.configurableProductIdResult()) {
                        if (typeof this.allChildQtys[this.configurableProductIdResult()] != 'undefined') {
                            return this.childQty(this.allChildQtys[this.configurableProductIdResult()]);
                        } else {
                            StockItemFactory.get().loadByProductId(this.configurableProductIdResult()).done(function (stock) {
                                if (stock.data) {
                                    if (stock.data.qty) {
                                        this.allChildQtys[this.configurableProductIdResult()] = stock.data.qty + ' ' + $t('item(s)');
                                        return this.childQty(this.allChildQtys[this.configurableProductIdResult()]);
                                    }
                                }
                                this.childQty(null);
                            }.bind(this));
                        }
                    } else {
                        this.childQty(null);
                    }
                }, this);
            }
        });
    }
);