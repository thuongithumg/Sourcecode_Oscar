/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/checkout/taxcalculator',
        'mage/translate',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
        'Magestore_Webpos/js/model/catalog/product-factory',
        'Magestore_Webpos/js/action/hardware/pole',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/data/cart',
        'Magestore_Webpos/js/model/resource-model/magento-rest/checkout/cart',
        'Magestore_Webpos/js/model/checkout/cart/discountpopup'
    ],
    function ($, ko, Items, TaxCalculator, __, Event, TotalsFactory, ProductFactory, poleHelper, priceHelper, Helper, CartData, CartResource, DiscountModel) {
        "use strict";

        /**
         * Cart Model
         * @type {{loading: *, customerId: *, customerGroup: *, customerData: *, currentPage: *, GUEST_CUSTOMER_NAME: *, BACK_CART_BUTTON_CODE: string, CHECKOUT_BUTTON_CODE: string, HOLD_BUTTON_CODE: string, CheckoutModel: *, CartCustomerModel: *, initialize: initialize, initObserver: initObserver, emptyCart: emptyCart, addCustomer: addCustomer, removeCustomer: removeCustomer, removeItem: removeItem, addProduct: addProduct, updateItem: updateItem, getItemData: getItemData, getItemsInfo: getItemsInfo, getItemsDataForOrder: getItemsDataForOrder, getItemsInitData: getItemsInitData, isVirtual: true, totalItems: totalItems, totalShipableItems: totalShipableItems, collectTaxRate: collectTaxRate, reCollectTaxRate: reCollectTaxRate, collectTierPrice: collectTierPrice, validateItemsQty: validateItemsQty, getItemChildsQty: getItemChildsQty, getQtyInCart: getQtyInCart, hasStorecredit: hasStorecredit, canCheckoutStorecredit: canCheckoutStorecredit, applyCatalogRules: applyCatalogRules, getQuoteCustomerParams: getQuoteCustomerParams, resetQuoteInitData: resetQuoteInitData, getCustomerInitParams: getCustomerInitParams, getQuoteInitParams: getQuoteInitParams, saveCartOnline: saveCartOnline, saveCartBeforeCheckoutOnline: saveCartBeforeCheckoutOnline, removeCartOnline: removeCartOnline, removeItemOnline: removeItemOnline, hasOnlineQuote: hasOnlineQuote, saveQuoteData: saveQuoteData}}
         */
        var CartModel = {
            loading: ko.observable(),
            customerId: ko.observable(''),
            customerGroup: ko.observable(''),
            customerData: ko.observable({}),
            currentPage: ko.observable(),
            GUEST_CUSTOMER_NAME: __("Guest"),
            BACK_CART_BUTTON_CODE: "back_to_cart",
            CHECKOUT_BUTTON_CODE: "checkout",
            HOLD_BUTTON_CODE: "hold",
            CheckoutModel: ko.observable(),
            CartCustomerModel: ko.observable(),
            /**
             * Initialize
             * @returns {CartModel}
             */
            initialize: function () {
                var self = this;
                self.initObserver();
                TotalsFactory.get().CartModel(this);
                return self;
            },
            /**
             * Init events and variables
             */
            initObserver: function () {
                var self = this;
                self.isOnCheckoutPage = ko.pureComputed(function () {
                    return (self.currentPage() == CartData.PAGE.CHECKOUT) ? true : false;
                });
                self.isOnCheckoutPage.subscribe(function (value) {
                    Helper.isOnCheckoutPage(value);
                });
                Event.observer('init_quote_online_after', function (event, response) {
                    if (response && response.data) {
                        self.saveQuoteData(response.data);
                    }
                });
            },
            /**
             * Clear cart
             */
            emptyCart: function () {
                Items.items.removeAll();
                this.removeCustomer();
                this.CheckoutModel().useDefaultAddress(this.CheckoutModel().ADDRESS_TYPE.BILLING);
                this.CheckoutModel().useDefaultAddress(this.CheckoutModel().ADDRESS_TYPE.SHIPPING);
                this.CheckoutModel().selectedShippingCode("");
                this.CheckoutModel().selectedShippingPrice(0);
                TotalsFactory.get().shippingData("");
                TotalsFactory.get().shippingFee(0);
                TotalsFactory.get().updateShippingAmount(0);
                Event.dispatch('cart_empty_after', '');
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().getTotalValue('grand_total')));
            },

            removeAllCartItem: function () {
                Items.items.removeAll();
            },
            /**
             * Add customer to cart
             * @param data
             */
            addCustomer: function (data) {
                this.customerData(data);
                this.customerId(data.id);
                this.customerGroup(data.group_id);
                this.collectTierPrice();
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().getTotalValue('grand_total')));
            },
            /**
             * Remove customer
             */
            removeCustomer: function () {
                this.CartCustomerModel().setCustomerId("");
                this.CartCustomerModel().setFullName(this.GUEST_CUSTOMER_NAME);
                this.customerId("");
                this.customerGroup("");
                this.customerData({});
                this.collectTierPrice();
                Event.dispatch('cart_remove_customer_after', {guest_customer_name: this.GUEST_CUSTOMER_NAME});
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().getTotalValue('grand_total')));
            },
            /**
             * Remove cart item
             * @param itemId
             */
            removeItem: function (itemId) {
                Items.removeItem(itemId);
                if (Items.items().length == 0) {
                    TotalsFactory.get().updateShippingAmount(0);
                }
                TotalsFactory.get().collectShippingTotal();
                TotalsFactory.get().collectTaxTotal();
                Event.dispatch('cart_item_remove_after', Items.items());
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().getTotalValue('grand_total')));
            },
            /**
             * Add product to cart
             * @param data
             */
            addProduct: function (data) {
                var validate = true;
                var item = Items.getAddedItem(data);
                if (item !== false) {
                    var dataToValidate = item.getData();
                    if (dataToValidate.product_id != "customsale" && data.product_type != "bundle") {
                        dataToValidate.qty += data.qty;
                        dataToValidate.customer_group = this.customerGroup();
                        validate = ProductFactory.get().validateQtyInCart(dataToValidate);
                    }
                } else {
                    if (data.product_id != "customsale" && data.product_type != "bundle") {
                        data.customer_group = this.customerGroup();
                        if (data.minimum_qty && data.qty < data.minimum_qty) {
                            data.qty = data.minimum_qty;
                        }
                        if (data.maximum_qty && data.maximum_qty > 0 && data.qty > data.maximum_qty) {
                            data.qty = data.maximum_qty;
                        }
                        validate = ProductFactory.get().validateQtyInCart(data);
                    }
                }
                if (validate) {
                    data = this.collectTaxRate(data);
                    Items.addItem(data);
                    TotalsFactory.get().collectShippingTotal();
                    TotalsFactory.get().collectTaxTotal();
                    this.collectTierPrice();
                }
                poleHelper(data.sku + ' +' + priceHelper.convertAndFormat(parseFloat(data.unit_price) * parseFloat(data.qty)), 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().getTotalValue('grand_total')));
            },
            /**
             * Update cart item data
             * @param itemId
             * @param key
             * @param value
             */
            updateItem: function (itemId, key, value) {
                var validate = true;
                var item = Items.getItem(itemId);
                if (item) {
                    if (key == "qty") {
                        var data = item.getData();
                        data.qty = value;
                        if (data.product_id != "customsale" && data.product_type != "bundle") {
                            data.customer_group = this.customerGroup();
                            validate = ProductFactory.get().validateQtyInCart(data);
                        }
                        if (data.product_id == "customsale") {
                            value = (value > 0) ? value : 1;
                        }
                    }
                    if (validate) {
                        Items.setItemData(itemId, key, value);
                        TotalsFactory.get().collectShippingTotal();
                        TotalsFactory.get().collectTaxTotal();
                    }
                }
            },
            /**
             * Get cart item data
             * @param itemId
             * @param key
             * @returns {*}
             */
            getItemData: function (itemId, key) {
                return Items.getItemData(itemId, key);
            },
            /**
             * Get cart item
             * @param itemId
             * @returns {*}
             */
            getItem: function (itemId) {
                return Items.getItem(itemId);
            },
            /**
             * Get items info buy request
             * @returns {Array}
             */
            getItemsInfo: function () {
                var itemsInfo = [];
                if (Items.items().length > 0) {
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        itemsInfo.push(item.getInfoBuyRequest());
                    });
                }
                return itemsInfo;
            },
            /**
             * Get items data for offline order
             * @returns {Array}
             */
            getItemsDataForOrder: function () {
                var itemsData = [];
                if (Items.items().length > 0) {
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        itemsData.push(item.getDataForOrder());
                    });
                }
                return itemsData;
            },
            /**
             * Get items init data
             * @returns {Array}
             */
            getItemsInitData: function () {
                var itemsData = [];
                if (Items.items().length > 0) {
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        itemsData.push(item.getData());
                    });
                }
                return itemsData;
            },
            /**
             * Is cart virtual
             * @returns {boolean}
             */
            isVirtual: function () {
                var isVirtual = true;
                if (Items.items().length > 0) {
                    var notVirtualItem = ko.utils.arrayFilter(Items.items(), function (item) {
                        return item.is_virtual() == false;
                    });
                    isVirtual = (notVirtualItem.length > 0) ? false : true;
                }
                return isVirtual;
            },
            /**
             * Get total number items
             * @returns {*}
             */
            totalItems: function () {
                return Items.totalItems();
            },
            /**
             * Get total shipable items
             * @returns {*}
             */
            totalShipableItems: function () {
                return Items.totalShipableItems();
            },
            /**
             * Collect tax rates for cart item before add
             * @param data
             * @returns {*}
             */
            collectTaxRate: function (data) {
                var self = this;
                var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                var address = {
                    country_id: window.webposConfig["shipping/origin/country_id"],
                    postcode: window.webposConfig["shipping/origin/postcode"],
                    region_id: window.webposConfig["shipping/origin/region_id"]
                };
                switch (calculateTaxBaseOn) {
                    case 'shipping':
                        address = self.CheckoutModel().shippingAddress();
                        break;
                    case 'billing':
                        address = self.CheckoutModel().billingAddress();
                        break;
                }
                data.tax_rates = TaxCalculator().getProductTaxRate(data.tax_class_id, this.customerGroup(), address).values;
                data.tax_rates_data = TaxCalculator().getProductTaxRate(data.tax_class_id, this.customerGroup(), address).rates;
                data.tax_origin_rates = TaxCalculator().getOriginRate(data.tax_class_id, this.customerGroup()).values;
                data.tax_origin_rates_data = TaxCalculator().getOriginRate(data.tax_class_id, this.customerGroup()).rates;
                return data;
            },
            /**
             * Re collect tax rates for all cart items
             */
            reCollectTaxRate: function () {
                var self = this;
                if (Items.items().length > 0) {
                    var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                    var address = {
                        country_id: window.webposConfig["shipping/origin/country_id"],
                        postcode: window.webposConfig["shipping/origin/postcode"],
                        region_id: window.webposConfig["shipping/origin/region_id"]
                    };
                    switch (calculateTaxBaseOn) {
                        case 'shipping':
                            address = self.CheckoutModel().shippingAddress();
                            break;
                        case 'billing':
                            address = self.CheckoutModel().billingAddress();
                            break;
                    }
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        var taxrate = TaxCalculator().getProductTaxRate(item.tax_class_id(), self.customerGroup(), address).values;
                        var taxrateData = TaxCalculator().getProductTaxRate(item.tax_class_id(), self.customerGroup(), address).rates;
                        var taxOriginRate = TaxCalculator().getOriginRate(item.tax_class_id(), self.customerGroup()).values;
                        var taxOriginRateData = TaxCalculator().getOriginRate(item.tax_class_id(), self.customerGroup()).rates;
                        self.updateItem(item.item_id(), 'tax_rates', taxrate);
                        self.updateItem(item.item_id(), 'tax_rates_data', taxrateData);
                        self.updateItem(item.item_id(), 'tax_origin_rates', taxOriginRate);
                        self.updateItem(item.item_id(), 'tax_origin_rates_data', taxOriginRateData);
                    });
                }
            },
            /**
             * Calculate tier price, group price for cart items
             */
            collectTierPrice: function () {
                var self = this;
                if (Items.items().length > 0) {
                    var hasTierPriceItems = ko.utils.arrayFilter(Items.items(), function (item) {
                        return (item.tier_prices() && item.tier_prices().length > 0) ? true : false;
                    });
                    ko.utils.arrayForEach(hasTierPriceItems, function (item) {

                        /** variables for tier_price with child_items of bundle product */
                        var temp_price = 0;
                        var has_child_tier_price = false;
                        /** */

                        var unit_price = item.unit_price();
                        var tier_prices = item.tier_prices();
                        var itemQty = item.qty();
                        var tier_price = false;
                        if (tier_prices) {

                            /** get correct price for bundle product with tier_price of child_items */
                            if(typeof item.bundle_childs_item != "undefined"){
                                ko.utils.arrayFilter(item.bundle_childs_qty(), function (childQty) {
                                    var product_id = childQty.code;
                                    var product_qty = childQty.value;
                                    ko.utils.arrayFilter(item.bundle_childs_item(), function (childData) {

                                        if(product_id == childData.items[0].product_id) {
                                            temp_price += parseFloat(childData.items[0].price);

                                            if ((childData.items[0].tier_price)) {
                                                var validTierPrices = ko.utils.arrayFilter(childData.items[0].tier_price, function (data) {
                                                    return (((self.customerGroup() == data.cust_group ) || (data.cust_group == 32000)) && itemQty >= data.price_qty) ? true : false;
                                                });
                                                if (validTierPrices && validTierPrices.length > 0) {

                                                    has_child_tier_price = true;
                                                    var real_price = Math.min.apply(false, validTierPrices.map(function (el) {
                                                        return el['price'];
                                                    }));
                                                    temp_price = temp_price - parseFloat(childData.items[0].price) + parseFloat(real_price)*product_qty;
                                                }
                                            }
                                        }
                                    });
                                });
                            }

                            /**  format currency */
                            if(has_child_tier_price) {
                                temp_price = priceHelper.currencyConvert(temp_price);
                            }

                            var validTierPrices = ko.utils.arrayFilter(tier_prices, function (data) {
                                return (((self.customerGroup() == data.customer_group_id ) || (data.customer_group_id == 32000)) && itemQty >= data.qty) ? true : false;
                            });
                            if (validTierPrices && validTierPrices.length > 0) {
                                tier_price = Math.min.apply(false, validTierPrices.map(function (el) {

                                    /* fix tier_price */
                                    if(el.extension_attributes){
                                        return unit_price*el.extension_attributes.percentage_value/100;
                                    }
                                    if( has_child_tier_price ){
                                        return temp_price*el['value']/100;
                                    }

                                    return el['value'];
                                }));
                            }else{
                                if( has_child_tier_price ){
                                    tier_price = temp_price;
                                }
                            }
                        }

                        self.updateItem(item.item_id(), 'tier_price', tier_price);
                    });

                    var hasNoTierPriceItems = ko.utils.arrayFilter(Items.items(), function (item) {
                        return (item.tier_prices() && item.tier_prices().length > 0) ? false : true;
                    });
                    ko.utils.arrayForEach(hasNoTierPriceItems, function (item) {
                        if (item.child_product()) {
                            self.collectChildProductTierPrice(item, item.child_product());
                        } else {
                            var collection = ProductFactory.get(); // .getCollection();
                            // collection.reset();
                            // collection.addFieldToFilter('id', child_id, 'eq');
                            var cdeferred = collection.load(parseInt(item.child_id()));
                            cdeferred.done(function (data) {
                                if (data) {
                                    self.collectChildProductTierPrice(item, data);
                                }
                            });
                        }

                    });
                }
            },
            /**
             * Calculate tier price, group price for cart children items
             */
            collectChildProductTierPrice: function (item, child) {
                var self = this,
                    tier_price = false;
                if (child && child.tier_prices) {
                    item.tier_prices(child.tier_prices);
                    var validTierPrices = ko.utils.arrayFilter(child.tier_prices, function (data) {
                        return (((self.customerGroup() == data.customer_group_id ) || (data.customer_group_id == 32000)) && item.qty() >= data.qty);
                    });
                    if (validTierPrices && validTierPrices.length > 0) {
                        tier_price = Math.min.apply(false, validTierPrices.map(function (el) {
                            return el['value']
                        }));
                    }
                }
                self.updateItem(item.item_id(), 'tier_price', tier_price);
            },
            /**
             * Validate qty
             * @returns {*}
             */
            validateItemsQty: function () {
                var self = this;
                var error = [];
                if (Items.items().length > 0) {
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        var data = item.getData();
                        if (data.product_id != "customsale" && data.product_type != "bundle") {
                            data.customer_group = self.customerGroup();
                            var validate = ProductFactory.get().checkStockItemsInCart(data);
                            if (validate !== true) {
                                error.push(validate);
                            }
                        }
                    });
                }
                return (error.length > 0) ? error : true;
            },
            /**
             * Get items qty in cart
             * @returns {Array}
             */
            getItemChildsQty: function () {
                var qtys = [];
                if (Items.items().length > 0) {
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        var data = item.getData();
                        if (data.product_id != "customsale") {
                            if (data.product_type == "bundle") {
                                if (data.bundle_childs_qty) {
                                    ko.utils.arrayForEach(data.bundle_childs_qty, function (option) {
                                        qtys.push({id: option.code, qty: option.value});
                                    });
                                }
                            } else {
                                if (data.child_id) {
                                    qtys.push({id: data.child_id, qty: data.qty});
                                } else {
                                    qtys.push({id: data.product_id, qty: data.qty});
                                }
                            }
                        }
                    });
                }
                return qtys;
            },
            /**
             * Get total qty in cart
             * @param productId
             * @returns {number}
             */
            getQtyInCart: function (productId) {
                var qty = 0;
                if (productId && Items.items().length > 0) {
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        if (item.getData('product_id') == productId) {
                            qty += item.getData('qty');
                        }
                    });
                }
                return qty;
            },
            /**
             * Check if cart has store credit product
             * @returns {boolean}
             */
            hasStorecredit: function () {
                if (Items.items().length > 0) {
                    var storecreditItem = ko.utils.arrayFirst(Items.items(), function (item) {
                        return (item.product_type() == "customercredit");
                    });
                    if (storecreditItem) {
                        return true;
                    }
                }
                return false;
            },
            /**
             * Check if cart can buy store credit
             * @returns {boolean}
             */
            canCheckoutStorecredit: function () {
                var hasStorecredit = this.hasStorecredit();
                if (hasStorecredit && this.customerId() == '') {
                    return false;
                }
                return true;
            },
            /**
             * Apply catalog rules
             * @param itemsData
             */
            applyCatalogRules: function (itemsData) {
                var self = this;
                if (itemsData && Items.items().length > 0) {
                    var allowedTypes = ['simple', 'virtual'];
                    $.each(itemsData, function (itemId, itemData) {
                        var item = ko.utils.arrayFirst(Items.items(), function (item) {
                            return (($.inArray(item.type_id(), allowedTypes) >= 0) && (item.item_id() == itemId));
                        });
                        if (item) {
                            var base_original_price = parseFloat(itemData.base_original_price);
                            var base_calculation_price = parseFloat(itemData.base_calculation_price);
                            var custom_price = parseFloat(itemData.custom_price);
                            if (((base_original_price != base_calculation_price) && !custom_price) || item.applied_catalog_rules()) {
                                item.applied_catalog_rules(true);
                                item.base_original_price(itemData.base_original_price);
                                item.tax_rates([itemData.tax_percent]);
                                item.unit_price((Helper.isProductPriceIncludesTax()) ? itemData.base_calculation_price : itemData.base_price);
                            }
                            item.qty(itemData.qty);
                        }
                    });
                }
            },
            /**
             * Get quote customer params
             * @returns {{customer_id: *, billing_address: *, shipping_address: *}}
             */
            getQuoteCustomerParams: function () {
                var self = this;
                return {
                    customer_id: self.customerId(),
                    billing_address: self.CheckoutModel().billingAddress(),
                    shipping_address: self.CheckoutModel().shippingAddress()
                };
            },
            /**
             * Reset quote data
             */
            resetQuoteInitData: function () {
                var self = this;
                var data = {
                    quote_id: '',
                    customer_id: self.customerId()
                };
                self.saveQuoteData(data);
            },
            /**
             * Get customer params
             * @returns {{customer_id: *, billing_address: *, shipping_address: *, data: *}}
             */
            getCustomerInitParams: function () {
                var self = this;
                return {
                    customer_id: Helper.getOnlineConfig(CartData.KEY.CUSTOMER_ID),
                    billing_address: Helper.getOnlineConfig(CartData.KEY.BILLING_ADDRESS),
                    shipping_address: Helper.getOnlineConfig(CartData.KEY.SHIPPING_ADDRESS),
                    data: Helper.getOnlineConfig(CartData.KEY.CUSTOMER_DATA)
                };
            },
            /**
             * Get quote init params
             * @returns {{quote_id: *, store_id: *, customer_id: *, currency_id: *, till_id: *}}
             */
            getQuoteInitParams: function () {
                var self = this;
                var params = {
                    quote_id: Helper.getOnlineConfig(CartData.KEY.QUOTE_ID),
                    quote_id_mask: Helper.getOnlineConfig(CartData.KEY.QUOTE_ID_MASK),
                    store_id: Helper.getOnlineConfig(CartData.KEY.STORE_ID),
                    customer_id: Helper.getOnlineConfig(CartData.KEY.CUSTOMER_ID),
                    currency_id: Helper.getOnlineConfig(CartData.KEY.CURRENCY_ID),
                    till_id: Helper.getOnlineConfig(CartData.KEY.TILL_ID)
                };
                var customer = self.getQuoteCustomerParams();
                if (customer.customer_id) {
                    params.customer_id = customer.customer_id;
                }
                return params;
            },
            /**
             * Save cart only - not distch events
             * @returns {*}
             */
            saveCartOnline: function () {
                var self = this;
                var params = self.getQuoteInitParams();
                params.items = self.getItemsInfo();
                params.customer = self.getQuoteCustomerParams();
                params.section = CartData.KEY.QUOTE_INIT;
                self.loading(true);
                var apiRequest = $.Deferred();

                CartResource().setPush(true).setLog(false).saveCart(params, apiRequest);
                apiRequest.done(function (response) {
                    // if(response.status == CartData.DATA.STATUS.ERROR && response.messages){
                    //     CartData.errorMessages(response.messages);
                    // }else{
                    //     CartData.hasErrors(false);
                    //     CartData.errorMessages('');
                    // }
                    Event.dispatch('affter_save_cart_online', '');
                }).always(function () {
                    self.loading(false);

                });
                return apiRequest;
            },
            /**
             * Save cart and dispatch events
             * @param saveBeforeRemove
             * @returns {*}
             */
            saveCartBeforeCheckoutOnline: function (saveBeforeRemove) {
                var self = this;
                var params = self.getQuoteInitParams();
                params.items = self.getItemsInfo();
                params.customer = self.getQuoteCustomerParams();
                if (saveBeforeRemove == true) {
                    params.section = CartData.KEY.QUOTE_INIT;
                }
                var viewManager = require('Magestore_Webpos/js/view/layout');
                viewManager.getSingleton('view/checkout/checkout/integration/storecredit-ee').getBalance(params.customer.customer_id);
                self.loading(true);
                var apiRequest = $.Deferred();
                CartResource().setPush(true).setLog(false).saveCartBeforeCheckout(params, apiRequest);

                apiRequest.done(function (response) {
                    if (response&&response.totals&&response.totals.length){
                        $.each(response.totals, function (index, el) {
                            if ( (el.code == 'subtotal') && DiscountModel.appliedDiscount()) {
                                var viewManager = require('Magestore_Webpos/js/view/layout');
                                viewManager.getSingleton('view/sales/order/hold-view').applyCartDiscountAfterCheckoutHoldOrder();
                                return false;
                            }
                        })
                    }
                }).always(function () {
                    self.loading(false);
                    poleHelper('', 'Total: ' + Helper.convertAndFormatPrice(TotalsFactory.get().grandTotal()));
                });
                return apiRequest;
            },

            /**
             * Save cart and dispatch events
             * @param saveBeforeRemove
             * @returns {*}
             */
            saveCartMultipleOrder: function (saveBeforeRemove) {
                var self = this;
                var params = self.getQuoteInitParams();
                params.items = self.getItemsInfo();
                params.customer = self.getQuoteCustomerParams();
                if (saveBeforeRemove === true) {
                    params.quote_id = '';
                }
                self.loading(true);
                var apiRequest = $.Deferred();
                params.isSwitchToCheckout = true;
                CartResource().setPush(true).setLog(false).saveCartBeforeCheckout(params, apiRequest);

                apiRequest.done(function (response) {
                    var viewManager = require('Magestore_Webpos/js/view/layout');
                    // viewManager.getSingleton('view/checkout/cart').switchToCheckout();
                    viewManager.getSingleton('view/checkout/cart/discountpopup').apply();
                }).always(function () {
                    self.loading(false);
                    poleHelper('', 'Total: ' + Helper.convertAndFormatPrice(TotalsFactory.get().grandTotal()));
                });
                return apiRequest;
            },
            /**
             * Call API to empty cart - remove quote
             * @returns {*}
             */
            removeCartOnline: function () {
                var self = this;
                var initParams = self.getQuoteInitParams();
                var params = {quote_id: initParams.quote_id};
                self.loading(true);
                var apiRequest = $.Deferred();
                CartResource().setPush(true).setLog(false).removeCart(params, apiRequest);

                apiRequest.done(
                    function (response) {
                        if (typeof response.quote_init != undefined) {
                            self.emptyCart();
                        }
                    }
                ).always(function () {
                    self.loading(false);
                    poleHelper('', 'Total: ' + Helper.convertAndFormatPrice(TotalsFactory.get().grandTotal()));
                });
                return apiRequest;
            },
            isCurrentOnlineCartItem: function (item_id) {
                var quote_id = 0;
                $.each(this.currentOnlineCartItem(), function (index, itemData) {
                    if (itemData.item_id == item_id) {
                        quote_id = itemData.quote_id;
                        return false;
                    }
                }.bind(this));
                $.each(Items.items(), function (index, itemData) {
                    if (itemData.item_id == item_id) {
                        quote_id = itemData.quote_id;
                        return false;
                    }
                }.bind(this));
                return quote_id;
            },
            /**
             * Call API to remove cart item online
             * @param itemId
             * @returns {*}
             */
            removeItemOnline: function (itemId) {
                var self = this;
                if (Items.items().length == 1) {
                    return self.removeCartOnline();
                }

                var params = self.getQuoteInitParams();
                params.item_id = itemId;

                // var onlineQuoteId = this.isCurrentOnlineCartItem(itemId);
                // if (onlineQuoteId) {
                //     params.quote_id = onlineQuoteId;
                // }

                self.loading(true);
                var apiRequest = $.Deferred();
                CartResource().setPush(true).setLog(false).removeItem(params, apiRequest);

                apiRequest.done(
                    function (response) {
                        if (typeof response.quote_init != undefined) {
                            self.removeItem(itemId);
                        }
                    }
                ).always(function () {
                    self.loading(false);
                    poleHelper('', 'Total: ' +
                        Helper.convertAndFormatPrice(TotalsFactory.get().grandTotal()));
                });
                return apiRequest;
            },
            /**
             * Check if cart has been saved online or not
             * @returns {boolean}
             */
            hasOnlineQuote: function () {
                var self = this;
                return (Helper.getOnlineConfig(CartData.KEY.QUOTE_ID)) ? true : false;
            },
            /**
             * Save quote init data to data manager
             * @param quoteData
             */
            saveQuoteData: function (quoteData) {
                if (quoteData) {
                    $.each(quoteData, function (key, value) {
                        value = (value) ? value : '';
                        Helper.saveOnlineConfig(key, value);
                    })
                }
            },
            /**
             * Get applied taxes
             * @returns {Array}
             */
            getAppliedTaxes: function () {
                var appliedTaxes = [];
                ko.utils.arrayForEach(Items.items(), function (item) {
                    var item_applied_taxes = item.applied_taxes();
                    ko.utils.arrayForEach(item_applied_taxes, function (item_applied_tax) {
                        var calculatedTax = ko.utils.arrayFirst(appliedTaxes, function (appliedTaxe) {
                            return (appliedTaxe.id == item_applied_tax.id);
                        });
                        if (calculatedTax) {
                            calculatedTax.amount += item_applied_tax.amount;
                            calculatedTax.base_amount += item_applied_tax.base_amount;
                        } else {
                            appliedTaxes.push($.extend(true, {}, item_applied_tax));
                        }
                    });
                });
                return appliedTaxes;
            },
            /**
             * Get item applied taxes
             * @returns {Array}
             */
            getItemAppliedTaxes: function () {
                var itemAppliedTaxes = [];
                ko.utils.arrayForEach(Items.items(), function (item) {
                    itemAppliedTaxes.push({
                        applied_taxes: item.applied_taxes(),
                        item_id: item.item_id(),
                        type: "product"
                    });
                });
                return itemAppliedTaxes;
            }
        };
        return CartModel.initialize();
    }
);