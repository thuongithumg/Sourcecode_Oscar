/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

define(
    [
        'jquery',
        'ko',
        'uiClass',
        'helper/general',
        'model/checkout/cart/items/item/interface',
        'dataManager'
    ],
    function ($,ko, UiClass, Helper, ItemInterface, DataManager) {
        "use strict";
        return UiClass.extend({
            initialize: function () {
                this._super();
                /* S: Define the init fields - use to get data for item object */
                this.initFields = [
                    ItemInterface.PRODUCT_ID,ItemInterface.PRODUCT_NAME,ItemInterface.ITEM_ID,ItemInterface.TIER_PRICE,ItemInterface.MAXIMUM_QTY,ItemInterface.MINIMUM_QTY,ItemInterface.QTY_INCREMENT,
                    ItemInterface.QTY,ItemInterface.UNIT_PRICE,ItemInterface.HAS_CUSTOM_PRICE,ItemInterface.CUSTOM_TYPE,ItemInterface.CUSTOM_PRICE_TYPE,ItemInterface.CUSTOM_PRICE_AMOUNT,ItemInterface.IMAGE_URL,
                    ItemInterface.SUPER_ATTRIBUTE,ItemInterface.SUPER_GROUP,ItemInterface.OPTIONS,ItemInterface.BUNDLE_OPTION,ItemInterface.BUNDLE_OPTION_QTY,ItemInterface.IS_OUT_OF_STOCK,
                    ItemInterface.TAX_CLASS_ID,ItemInterface.IS_VIRTUAL, ItemInterface.QTY_TO_SHIP, ItemInterface.TAX_RATES, ItemInterface.STORE_TAX_RATES, ItemInterface.SKU, ItemInterface.PRODUCT_TYPE, ItemInterface.CHILD_ID,
                    ItemInterface.OPTIONS_LABEL, ItemInterface.STOCKS, ItemInterface.STOCK,ItemInterface.ID,ItemInterface.TYPE_ID,ItemInterface.BUNDLE_CHILDS_QTY,ItemInterface.ITEM_BASE_DISCOUNT_AMOUNT,
                    ItemInterface.ITEM_DISCOUNT_AMOUNT, ItemInterface.SAVED_ONLINE_ITEM, ItemInterface.ONLINE_BASE_TAX_AMOUNT, ItemInterface.HAS_ERROR, ItemInterface.GROUP_PRICES,
                    ItemInterface.TAX_ORIGIN_RATE, ItemInterface.TAX_AMOUNT_BEFORE_DISCOUNT, ItemInterface.GROUP_PRICE, ItemInterface.IS_CUSTOM_SALE, ItemInterface.CHILD_DATA
                ];
                if(Helper.isStoreCreditEnable()) {
                    this.initFields.push(ItemInterface.CREDIT_AMOUNT);
                    this.initFields.push(ItemInterface.CREDIT_PRICE_AMOUNT);
                    this.initFields.push(ItemInterface.ITEM_CREDIT_AMOUNT);
                    this.initFields.push(ItemInterface.ITEM_BASE_CREDIT_AMOUNT);
                }
                if(Helper.isRewardPointsEnable()) {
                    this.initFields.push(ItemInterface.ITEM_POINT_EARN);
                    this.initFields.push(ItemInterface.ITEM_POINT_SPENT);
                    this.initFields.push(ItemInterface.ITEM_POINT_DISCOUNT);
                    this.initFields.push(ItemInterface.ITEM_BASE_POINT_DISCOUNT);
                }
                if(Helper.isGiftCardEnable()) {
                    this.initFields.push(ItemInterface.ITEM_GIFTCARD_DISCOUNT);
                    this.initFields.push(ItemInterface.ITEM_BASE_GIFTCARD_DISCOUNT);
                }
                if(Helper.isInventorySuccessEnable()) {
                    this.initFields.push(ItemInterface.WAREHOUSE_ID);
                }
                /* E: Define the init fields */

                /**
                 * Other extension can use this event to add new fields
                 * @type {{fields, item: exports}}
                 */
                var eventData = {fields : this.initFields, item:this};
                Helper.dispatchEvent('webpos_cart_item_initialize_after', eventData);
                this.initFields = eventData.fields;
            },
            init: function(data){
                var self = this;
                /**
                 * Other extension can use this event to add data/computed object
                 * @type {{data: *, item: exports}}
                 */
                var eventData = {data : data, item:this};
                Helper.dispatchEvent('webpos_cart_item_init_data_before', eventData);

                self[ItemInterface.ID] = (typeof data[ItemInterface.ID] != "undefined")?ko.observable(data[ItemInterface.ID]):ko.observable();
                self[ItemInterface.PRODUCT_ID] = (typeof data[ItemInterface.PRODUCT_ID] != "undefined")?ko.observable(data[ItemInterface.PRODUCT_ID]):ko.observable();
                self[ItemInterface.PRODUCT_NAME] = (typeof data[ItemInterface.PRODUCT_NAME] != "undefined")?ko.observable(data[ItemInterface.PRODUCT_NAME]):ko.observable();
                self[ItemInterface.TYPE_ID] = (typeof data[ItemInterface.TYPE_ID] != "undefined")?ko.observable(data[ItemInterface.TYPE_ID]):ko.observable();
                self[ItemInterface.IS_CUSTOM_SALE] = (typeof data[ItemInterface.IS_CUSTOM_SALE] != "undefined")?ko.observable(data[ItemInterface.IS_CUSTOM_SALE]):ko.observable(false);

                self[ItemInterface.SAVED_ONLINE_ITEM] = (typeof data[ItemInterface.SAVED_ONLINE_ITEM] != "undefined")?ko.observable(data[ItemInterface.SAVED_ONLINE_ITEM]):ko.observable(false);
                self[ItemInterface.HAS_ERROR] = (typeof data[ItemInterface.HAS_ERROR] != "undefined")?ko.observable(data[ItemInterface.HAS_ERROR]):ko.observable(false);
                self[ItemInterface.ITEM_ID] = (typeof data[ItemInterface.ITEM_ID] != "undefined")?ko.observable(data[ItemInterface.ITEM_ID]):ko.observable();
                self[ItemInterface.TIER_PRICES] = (typeof data[ItemInterface.TIER_PRICES] != "undefined")?ko.observable(data[ItemInterface.TIER_PRICES]):ko.observable();
                self[ItemInterface.GROUP_PRICES] = (typeof data[ItemInterface.GROUP_PRICES] != "undefined")?ko.observable(data[ItemInterface.GROUP_PRICES]):ko.observable();
                self[ItemInterface.MAXIMUM_QTY] = (typeof data[ItemInterface.MAXIMUM_QTY] != "undefined")?ko.observable(data[ItemInterface.MAXIMUM_QTY]):ko.observable();
                self[ItemInterface.MINIMUM_QTY] = (typeof data[ItemInterface.MINIMUM_QTY] != "undefined")?ko.observable(data[ItemInterface.MINIMUM_QTY]):ko.observable();
                self[ItemInterface.QTY_INCREMENT] = (typeof data[ItemInterface.QTY_INCREMENT] != "undefined")?ko.observable(data[ItemInterface.QTY_INCREMENT]):ko.observable(1);
                self[ItemInterface.QTY] = (typeof data[ItemInterface.QTY] != "undefined")?ko.observable(data[ItemInterface.QTY]):ko.observable();
                self[ItemInterface.QTY_TO_SHIP] = (typeof data[ItemInterface.QTY_TO_SHIP] != "undefined")?ko.observable(data[ItemInterface.QTY_TO_SHIP]):ko.observable(0);
                self[ItemInterface.UNIT_PRICE] = (typeof data[ItemInterface.UNIT_PRICE] != "undefined")?ko.observable(data[ItemInterface.UNIT_PRICE]):ko.observable(0);
                self[ItemInterface.HAS_CUSTOM_PRICE] = (typeof data[ItemInterface.HAS_CUSTOM_PRICE] != "undefined")?ko.observable(data[ItemInterface.HAS_CUSTOM_PRICE]):ko.observable(false);
                self[ItemInterface.CUSTOM_TYPE] = (typeof data[ItemInterface.CUSTOM_TYPE] != "undefined")?ko.observable(data[ItemInterface.CUSTOM_TYPE]):ko.observable();
                self[ItemInterface.CUSTOM_PRICE_TYPE] = (typeof data[ItemInterface.CUSTOM_PRICE_TYPE] != "undefined")?ko.observable(data[ItemInterface.CUSTOM_PRICE_TYPE]):ko.observable();
                self[ItemInterface.CUSTOM_PRICE_AMOUNT] = (typeof data[ItemInterface.CUSTOM_PRICE_AMOUNT] != "undefined")?ko.observable(data[ItemInterface.CUSTOM_PRICE_AMOUNT]):ko.observable();
                self[ItemInterface.IMAGE_URL] = (typeof data[ItemInterface.IMAGE_URL] != "undefined")?ko.observable(data[ItemInterface.IMAGE_URL]):ko.observable();
                self[ItemInterface.SUPER_ATTRIBUTE] = (typeof data[ItemInterface.SUPER_ATTRIBUTE] != "undefined")?ko.observable(data[ItemInterface.SUPER_ATTRIBUTE]):ko.observable();
                self[ItemInterface.SUPER_GROUP] = (typeof data[ItemInterface.SUPER_GROUP] != "undefined")?ko.observable(data[ItemInterface.SUPER_GROUP]):ko.observable();
                self[ItemInterface.OPTIONS] = (typeof data[ItemInterface.OPTIONS] != "undefined")?ko.observable(data[ItemInterface.OPTIONS]):ko.observable();
                self[ItemInterface.BUNDLE_OPTION] = (typeof data[ItemInterface.BUNDLE_OPTION] != "undefined")?ko.observable(data[ItemInterface.BUNDLE_OPTION]):ko.observable();
                self[ItemInterface.BUNDLE_OPTION_QTY] = (typeof data[ItemInterface.BUNDLE_OPTION_QTY] != "undefined")?ko.observable(data[ItemInterface.BUNDLE_OPTION_QTY]):ko.observable();
                self[ItemInterface.IS_OUT_OF_STOCK] = (typeof data[ItemInterface.IS_OUT_OF_STOCK] != "undefined")?ko.observable(data[ItemInterface.IS_OUT_OF_STOCK]):ko.observable(false);
                self[ItemInterface.TAX_CLASS_ID] = (typeof data[ItemInterface.TAX_CLASS_ID] != "undefined")?ko.observable(data[ItemInterface.TAX_CLASS_ID]):ko.observable();
                self[ItemInterface.IS_VIRTUAL] = (typeof data[ItemInterface.IS_VIRTUAL] != "undefined")?ko.observable(data[ItemInterface.IS_VIRTUAL]):ko.observable(false);
                self[ItemInterface.TAX_RATES] = (typeof data[ItemInterface.TAX_RATES] != "undefined")?ko.observable(data[ItemInterface.TAX_RATES]):ko.observable([]);
                self[ItemInterface.STORE_TAX_RATES] = (typeof data[ItemInterface.STORE_TAX_RATES] != "undefined")?ko.observable(data[ItemInterface.STORE_TAX_RATES]):ko.observable([]);
                self[ItemInterface.TAX_ORIGIN_RATE] = (typeof data[ItemInterface.TAX_ORIGIN_RATE] != "undefined")?ko.observable(data[ItemInterface.TAX_ORIGIN_RATE]):ko.observable([]);
                self[ItemInterface.ONLINE_BASE_TAX_AMOUNT] = (typeof data[ItemInterface.ONLINE_BASE_TAX_AMOUNT] != "undefined")?ko.observable(data[ItemInterface.ONLINE_BASE_TAX_AMOUNT]):ko.observable(0);

                self[ItemInterface.SKU] = (typeof data[ItemInterface.SKU] != "undefined")?ko.observable(data[ItemInterface.SKU]):ko.observable();
                self[ItemInterface.PARENT_SKU] = (typeof data[ItemInterface.PARENT_SKU] != "undefined")?ko.observable(data[ItemInterface.PARENT_SKU]):ko.observable();
                self[ItemInterface.PRODUCT_TYPE] = (typeof data[ItemInterface.PRODUCT_TYPE] != "undefined")?ko.observable(data[ItemInterface.PRODUCT_TYPE]):ko.observable();
                self[ItemInterface.CHILD_ID] = (typeof data[ItemInterface.CHILD_ID] != "undefined")?ko.observable(data[ItemInterface.CHILD_ID]):ko.observable();
                self[ItemInterface.CHILD_DATA] = (typeof data[ItemInterface.CHILD_DATA] != "undefined")?ko.observable(data[ItemInterface.CHILD_DATA]):ko.observable();
                self[ItemInterface.OPTIONS_LABEL] = (typeof data[ItemInterface.OPTIONS_LABEL] != "undefined")?ko.observable(data[ItemInterface.OPTIONS_LABEL]):ko.observable();
                self[ItemInterface.OPTIONS_DATA] = (typeof data[ItemInterface.OPTIONS_DATA] != "undefined")?ko.observable(data[ItemInterface.OPTIONS_DATA]):ko.observable();
                self[ItemInterface.TIER_PRICE] = (typeof data[ItemInterface.TIER_PRICE] != "undefined")?ko.observable(data[ItemInterface.TIER_PRICE]):ko.observable();
                self[ItemInterface.GROUP_PRICE] = (typeof data[ItemInterface.GROUP_PRICE] != "undefined")?ko.observable(data[ItemInterface.GROUP_PRICE]):ko.observable();
                self[ItemInterface.STOCK] = (typeof data[ItemInterface.STOCK] != "undefined")?ko.observable(data[ItemInterface.STOCK]):ko.observable();
                self[ItemInterface.STOCKS] = (typeof data[ItemInterface.STOCKS] != "undefined")?ko.observable(data[ItemInterface.STOCKS]):ko.observable();
                self[ItemInterface.BUNDLE_CHILDS_QTY] = (typeof data[ItemInterface.BUNDLE_CHILDS_QTY] != "undefined")?ko.observable(data[ItemInterface.BUNDLE_CHILDS_QTY]):ko.observable();
                self[ItemInterface.ITEM_DISCOUNT_AMOUNT] = (typeof data[ItemInterface.ITEM_DISCOUNT_AMOUNT] != "undefined")?ko.observable(data[ItemInterface.ITEM_DISCOUNT_AMOUNT]):ko.observable();
                self[ItemInterface.ITEM_BASE_DISCOUNT_AMOUNT] = (typeof data[ItemInterface.ITEM_BASE_DISCOUNT_AMOUNT] != "undefined")?ko.observable(data[ItemInterface.ITEM_BASE_DISCOUNT_AMOUNT]):ko.observable();

                /* S: Integration custom discount per item - define variale to store the data */
                if(Helper.isStoreCreditEnable()) {
                    self[ItemInterface.CREDIT_PRICE_AMOUNT] = (typeof data[ItemInterface.CREDIT_PRICE_AMOUNT] != "undefined") ? ko.observable(data[ItemInterface.CREDIT_PRICE_AMOUNT]) : ko.observable();
                    self[ItemInterface.CREDIT_AMOUNT] = (typeof data[ItemInterface.CREDIT_AMOUNT] != "undefined") ? ko.observable(data[ItemInterface.CREDIT_AMOUNT]) : ko.observable();
                    self[ItemInterface.ITEM_CREDIT_AMOUNT] = (typeof data[ItemInterface.ITEM_CREDIT_AMOUNT] != "undefined") ? ko.observable(data[ItemInterface.ITEM_CREDIT_AMOUNT]) : ko.observable();
                    self[ItemInterface.ITEM_BASE_CREDIT_AMOUNT] = (typeof data[ItemInterface.ITEM_BASE_CREDIT_AMOUNT] != "undefined") ? ko.observable(data[ItemInterface.ITEM_BASE_CREDIT_AMOUNT]) : ko.observable();
                }
                if(Helper.isRewardPointsEnable()) {
                    self[ItemInterface.ITEM_POINT_EARN] = (typeof data[ItemInterface.ITEM_POINT_EARN] != "undefined") ? ko.observable(data[ItemInterface.ITEM_POINT_EARN]) : ko.observable();
                    self[ItemInterface.ITEM_POINT_SPENT] = (typeof data[ItemInterface.ITEM_POINT_SPENT] != "undefined") ? ko.observable(data[ItemInterface.ITEM_POINT_SPENT]) : ko.observable();
                    self[ItemInterface.ITEM_POINT_DISCOUNT] = (typeof data[ItemInterface.ITEM_POINT_DISCOUNT] != "undefined") ? ko.observable(data[ItemInterface.ITEM_POINT_DISCOUNT]) : ko.observable();
                    self[ItemInterface.ITEM_BASE_POINT_DISCOUNT] = (typeof data[ItemInterface.ITEM_BASE_POINT_DISCOUNT] != "undefined") ? ko.observable(data[ItemInterface.ITEM_BASE_POINT_DISCOUNT]) : ko.observable();
                }
                if(Helper.isGiftCardEnable()) {
                    self[ItemInterface.ITEM_GIFTCARD_DISCOUNT] = (typeof data[ItemInterface.ITEM_GIFTCARD_DISCOUNT] != "undefined") ? ko.observable(data[ItemInterface.ITEM_GIFTCARD_DISCOUNT]) : ko.observable();
                    self[ItemInterface.ITEM_BASE_GIFTCARD_DISCOUNT] = (typeof data[ItemInterface.ITEM_BASE_GIFTCARD_DISCOUNT] != "undefined") ? ko.observable(data[ItemInterface.ITEM_BASE_GIFTCARD_DISCOUNT]) : ko.observable();

                    self[ItemInterface.ITEM_GIFTCARD_AMOUNT] = (typeof data[ItemInterface.ITEM_GIFTCARD_AMOUNT] != "undefined") ? ko.observable(data[ItemInterface.ITEM_GIFTCARD_AMOUNT]) : ko.observable();
                    self[ItemInterface.ITEM_GIFTCARD_TEMPLATE_ID] = (typeof data[ItemInterface.ITEM_GIFTCARD_TEMPLATE_ID] != "undefined") ? ko.observable(data[ItemInterface.ITEM_GIFTCARD_TEMPLATE_ID]) : ko.observable();
                    self[ItemInterface.ITEM_GIFTCARD_CAN_SHIP] = (typeof data[ItemInterface.ITEM_GIFTCARD_CAN_SHIP] != "undefined") ? ko.observable(data[ItemInterface.ITEM_GIFTCARD_CAN_SHIP]) : ko.observable();
                }
                /* E: Integration custom discount per item */

                if(Helper.isInventorySuccessEnable()) {
                    self[ItemInterface.WAREHOUSE_ID] = (typeof data[ItemInterface.WAREHOUSE_ID] != "undefined") ? ko.observable(data[ItemInterface.WAREHOUSE_ID]) : ko.observable(DataManager.getData('current_warehouse_id'));
                }

                if(self[ItemInterface.MAXIMUM_QTY]() && self[ItemInterface.QTY]() > self[ItemInterface.MAXIMUM_QTY]()){
                    self[ItemInterface.QTY](Helper.toNumber(self[ItemInterface.MAXIMUM_QTY]()));
                    Helper.alert({
                        priority: "warning",
                        title: "Warning",
                        message: self[ItemInterface.PRODUCT_NAME]()+Helper.__(" has maximum quantity allow in cart is ")+Helper.toNumber(self[ItemInterface.MAXIMUM_QTY]())
                    });
                }

                if(self[ItemInterface.MINIMUM_QTY]() && self[ItemInterface.QTY]() < self[ItemInterface.MINIMUM_QTY]()){
                    self[ItemInterface.QTY](Helper.toNumber(self[ItemInterface.MINIMUM_QTY]()));
                    Helper.alert({
                        priority: "warning",
                        title: "Warning",
                        message: self[ItemInterface.PRODUCT_NAME]()+Helper.__(" has minimum quantity allow in cart is ")+Helper.toNumber(self[ItemInterface.MINIMUM_QTY]())
                    });
                }
                if(!self.price) {
                    self.price = ko.pureComputed(function () {
                        var tierPrice = self[ItemInterface.TIER_PRICE]();
                        var groupPrice = self[ItemInterface.GROUP_PRICE]();
                        var price = self[ItemInterface.UNIT_PRICE]();
                        if(tierPrice && groupPrice){
                            tierPrice = parseFloat(tierPrice);
                            groupPrice = parseFloat(groupPrice);
                            var minPrice = Math.min(tierPrice, groupPrice);
                            price = Math.min(price, minPrice);
                        }else{
                            if(tierPrice && (parseFloat(tierPrice) < price)){
                                price = parseFloat(tierPrice);
                            }
                            if(groupPrice && (parseFloat(groupPrice) < price)){
                                price = parseFloat(groupPrice);
                            }
                        }
                        return price;
                    });
                }
                if(!self.item_price) {
                    self.item_price = ko.pureComputed(function () {
                        var itemPrice = self.item_price_origin();
                        var itemPriceTax = itemPrice;
                        if(Helper.isProductPriceIncludesTax()){
                            // var taxRates = (!Helper.isEnableCrossBorderTrade())?self[ItemInterface.TAX_ORIGIN_RATE]():self[ItemInterface.TAX_RATES]();
                            // if(taxRates && taxRates.length > 0){
                            //     $.each(taxRates, function (index, rate) {
                            //         itemPrice = itemPrice / (1 + rate/100);
                            //     });
                            // }
                            if(Helper.isEnableCrossBorderTrade()){
                                var taxRates = self[ItemInterface.TAX_ORIGIN_RATE]();
                                if(taxRates && taxRates.length > 0){
                                    $.each(taxRates, function (index, rate) {
                                        itemPriceTax = self.calcTaxAmount(itemPrice, rate, true, false);
                                    });
                                }
                            }else{
                                var taxRates = self[ItemInterface.TAX_RATES]();
                                var storeTaxRates = self[ItemInterface.STORE_TAX_RATES]();
                                if(!taxRates.length){
                                    taxRates = [0];
                                }
                                if(taxRates && taxRates.length > 0){
                                    $.each(taxRates, function (index, rate) {
                                        itemPriceTax = self._calculatePriceInclTax(itemPrice, storeTaxRates, taxRates)
                                    });
                                }
                            }
                            itemPrice = itemPriceTax;
                        }
                        return Helper.correctPrice(itemPrice);
                    });
                }
                self._calculatePriceInclTax = function(storePriceInclTax, storeRate, customerRate){
                    var storeTax = self.calcTaxAmount(storePriceInclTax, storeRate, Helper.isProductPriceIncludesTax(), false);
                    var priceExclTax = storePriceInclTax - storeTax;
                    var customerTax = self.calcTaxAmount(priceExclTax, customerRate, Helper.isProductPriceIncludesTax(), false);
                    var customerPriceInclTax = Helper.round(priceExclTax + customerTax);
                    return customerPriceInclTax;
                };
                self.calcTaxAmount = function(price, taxRate, priceIncludeTax, round){
                    taxRate = taxRate / 100;

                    if (priceIncludeTax) {
                        var amount = price * (1 - 1 / (1 + taxRate));
                    } else {
                        var amount = price * taxRate;
                    }

                    if (round) {
                        return Helper.round(amount);
                    }

                    return amount;
                };
                if(!self.item_price_origin) {
                    self.item_price_origin = ko.pureComputed(function () {
                        var itemPrice = self.price();
                        var unitPrice = itemPrice;
                        var discountPercentage = 0;
                        var maximumPercent = Helper.toNumber(Helper.getBrowserConfig('maximum_discount_percent'));
                        var customAmount = (self[ItemInterface.CUSTOM_PRICE_TYPE]() == ItemInterface.FIXED_AMOUNT_CODE) ? Helper.toBasePrice(self[ItemInterface.CUSTOM_PRICE_AMOUNT]()) : self[ItemInterface.CUSTOM_PRICE_AMOUNT]();
                        var validAmount = customAmount;
                        if (self[ItemInterface.HAS_CUSTOM_PRICE]() == true && customAmount >= 0 && self[ItemInterface.CUSTOM_PRICE_TYPE]()) {
                            if (self[ItemInterface.CUSTOM_TYPE]() == ItemInterface.CUSTOM_PRICE_CODE) {
                                itemPrice = (self[ItemInterface.CUSTOM_PRICE_TYPE]() == ItemInterface.FIXED_AMOUNT_CODE) ?
                                    customAmount :
                                    (customAmount * unitPrice / 100);
                                if (self[ItemInterface.CUSTOM_PRICE_TYPE]() == ItemInterface.FIXED_AMOUNT_CODE) {
                                    discountPercentage = (100 - itemPrice / unitPrice * 100);
                                } else {
                                    discountPercentage = 100 - customAmount;
                                }
                                if (maximumPercent && discountPercentage > maximumPercent) {
                                    if (self[ItemInterface.CUSTOM_PRICE_TYPE]() == ItemInterface.FIXED_AMOUNT_CODE) {
                                        validAmount = unitPrice - unitPrice * maximumPercent / 100;
                                    }
                                }
                            } else {
                                if (self[ItemInterface.CUSTOM_TYPE]() == ItemInterface.CUSTOM_DISCOUNT_CODE) {
                                    itemPrice = (self[ItemInterface.CUSTOM_PRICE_TYPE]() == ItemInterface.FIXED_AMOUNT_CODE) ?
                                        (unitPrice - customAmount) :
                                        (unitPrice - customAmount * unitPrice / 100);
                                    if (self[ItemInterface.CUSTOM_PRICE_TYPE]() == ItemInterface.FIXED_AMOUNT_CODE) {
                                        discountPercentage = (customAmount / unitPrice * 100);
                                    } else {
                                        discountPercentage = customAmount;
                                    }
                                }
                                if (maximumPercent && discountPercentage > maximumPercent) {
                                    if (self[ItemInterface.CUSTOM_PRICE_TYPE]() == ItemInterface.FIXED_AMOUNT_CODE) {
                                        validAmount = unitPrice * maximumPercent / 100;
                                    }
                                }
                            }
                        }
                        if (maximumPercent && discountPercentage > maximumPercent) {
                            if (self[ItemInterface.CUSTOM_PRICE_TYPE]() == ItemInterface.PERCENTAGE_CODE) {
                                self[ItemInterface.CUSTOM_PRICE_AMOUNT](maximumPercent);
                            } else {
                                self[ItemInterface.CUSTOM_PRICE_AMOUNT](Helper.convertPrice(validAmount));
                            }
                            itemPrice = unitPrice - unitPrice * maximumPercent / 100;
                            Helper.alert({
                                priority: "warning",
                                title: "Warning",
                                message: Helper.__(" You are able to apply discount under ") + maximumPercent + "% " + Helper.__("only")
                            });
                        }
                        return (itemPrice > 0) ? itemPrice : 0;
                    });
                }
                if(!self[ItemInterface.ROW_TOTAL]) {
                    self[ItemInterface.ROW_TOTAL] = ko.pureComputed(function () {
                        var itemPrice = self.item_price();
                        if(Helper.isProductPriceIncludesTax() && (Helper.isEnableCrossBorderTrade() || self.isUseOriginalTax())){
                            // itemPrice = self.item_price_origin() - (self.tax_amount_before_discount() / self.qty());
                            itemPrice -= (self.tax_amount_before_discount() / self.qty());
                        }
                        var rowTotal = self[ItemInterface.QTY]() * itemPrice;
                        return Helper.correctPrice(rowTotal);
                    });
                }
                if(!self.total_discount_item) {
                    self.total_discount_item = ko.pureComputed(function () {
                        var discountItem = 0;
                        var apply_after_discount = window.webposConfig['tax/calculation/apply_after_discount'];
                        if (apply_after_discount == '1' ) {
                            if(self[ItemInterface.ITEM_BASE_DISCOUNT_AMOUNT]() < 0){
                                discountItem -= self[ItemInterface.ITEM_BASE_DISCOUNT_AMOUNT]();
                            }
                        }

                        /* S: Integration custom discount per item - recalculate tax - tax after discount */
                        var allConfig = Helper.getBrowserConfig('plugins_config');
                        if(Helper.isStoreCreditEnable() && allConfig['os_store_credit']){
                            var configs = allConfig['os_store_credit'];
                            if(configs['customercredit/spend/tax'] && configs['customercredit/spend/tax'] == '0'){
                                if (self[ItemInterface.ITEM_BASE_CREDIT_AMOUNT]() > 0) {
                                    discountItem += self[ItemInterface.ITEM_BASE_CREDIT_AMOUNT]();
                                }
                            }
                        }
                        if(Helper.isRewardPointsEnable()  && apply_after_discount == 1){
                            if(self[ItemInterface.ITEM_BASE_POINT_DISCOUNT]() > 0){
                                discountItem += self[ItemInterface.ITEM_BASE_POINT_DISCOUNT]();
                            }
                        }
                        if(Helper.isGiftCardEnable()  &&  allConfig['os_gift_card']){
                            var configs = allConfig['os_gift_card'];
                            if(configs['giftvoucher/general/apply_after_tax'] == '0'){
                                if (self[ItemInterface.ITEM_BASE_GIFTCARD_DISCOUNT]() > 0) {
                                    discountItem += self[ItemInterface.ITEM_BASE_GIFTCARD_DISCOUNT]();
                                }
                            }

                        }
                        /* E: Integration custom discount per item */

                        /**
                         * Other extension can use this event to add discount
                         * @type {{amount: number, item: *}}
                         */
                        var eventData = {amount : discountItem, item:this};
                        Helper.dispatchEvent('webpos_cart_item_calculate_discount_after', eventData);
                        return discountItem;
                    });

                }
                if(!self[ItemInterface.TAX_AMOUNT]) {
                    self[ItemInterface.TAX_AMOUNT] = ko.pureComputed(function () {
                        if(Helper.isOnlineCheckout() && DataManager.getData('quote_id')){
                            return self[ItemInterface.ONLINE_BASE_TAX_AMOUNT]();
                        }
                        var tax = self.tax_amount_before_discount();

                        /* temporary disable this functionality, because magento core is having a bug in here, currently they don't check this setting when creating order from backend also.
                         * ------------- *
                         var apply_tax_on = window.webposConfig['tax/calculation/apply_tax_on'];
                         if(apply_tax_on == self.APPLY_TAX_ON_ORIGINALPRICE){
                         total = self.row_total_without_discount();
                         }
                         * ------------- *
                         */

                        var apply_after_discount = window.webposConfig['tax/calculation/apply_after_discount'];
                        var discountItem = self.total_discount_item();
                        if (discountItem > 0 && apply_after_discount == 1) {
                            if(Helper.isProductPriceIncludesTax()){
                                var discountTax = discountItem/self.row_total_include_tax();
                                //discountTax = Helper.round(discountTax);
                                tax = tax - discountTax*tax;
                            }else{
                                tax = tax - discountItem/self.row_total()*tax;
                            }
                        }
                        return Helper.correctPrice(tax);
                    });

                }
                if(!self.tax_amount_before_discount) {
                    self.tax_amount_before_discount = ko.pureComputed(function () {
                        var price = self.item_price();
                        var tax = 0;
                        var taxRates = self.tax_rates();
                        if(taxRates && taxRates.length > 0){
                            $.each(taxRates, function (index, rate) {
                                var value = self.calcTaxAmount(price, rate, Helper.isProductPriceIncludesTax(), false);
                                // var value = rate * price / 100;
                                tax += value;
                                price += value;
                            });
                        }
                        tax = Helper.correctPrice(tax);
                        tax = self[ItemInterface.QTY]() * tax;
                        return Helper.correctPrice(tax);
                    });
                }
                if(!self.tax_amount_without_discount) {
                    self.tax_amount_without_discount = ko.pureComputed(function () {
                        var price = self.item_price();
                        var tax = 0;
                        var taxRates = self.tax_rates();
                        if(taxRates && taxRates.length > 0){
                            $.each(taxRates, function (index, rate) {
                                var value = self.calcTaxAmount(price, rate, Helper.isProductPriceIncludesTax(), false);
                                // var value = rate * price / 100;
                                tax += value;
                                price += value;
                            });
                        }
                        tax = Helper.correctPrice(tax);
                        tax = self[ItemInterface.QTY]() * tax;
                        return Helper.correctPrice(tax);
                    });
                }
                if(!self.tax_amount_converted) {
                    self.tax_amount_converted = ko.pureComputed(function () {
                        return Helper.convertPrice(self[ItemInterface.TAX_AMOUNT]());
                    });
                }
                if(!self.row_total_include_tax) {
                    self.row_total_include_tax = ko.pureComputed(function () {
                        var rowTotal = self.row_total();
                        if(!Helper.isEnableCrossBorderTrade()){
                            rowTotal += self.tax_amount_without_discount();
                        }else{
                            rowTotal = self.qty() * self.item_price_origin();
                        }
                        return Helper.correctPrice(rowTotal);
                    });
                }
                if(!self.row_total_formated) {
                    self.row_total_formated = ko.pureComputed(function () {
                        var displayIncludeTax = Helper.isCartDisplayIncludeTax('price');
                        var rowTotal = self[ItemInterface.ROW_TOTAL]();
                        if(displayIncludeTax){
                            rowTotal = self.row_total_include_tax();
                        }
                        return Helper.convertAndFormatPrice(rowTotal);
                    });
                }
                if(!self.original_row_total_formated) {
                    self.original_row_total_formated = ko.pureComputed(function () {
                        var displayIncludeTax = Helper.isCartDisplayIncludeTax('price');
                        var rowTotal = self[ItemInterface.QTY]() * self[ItemInterface.UNIT_PRICE]();
                        if(!Helper.isEnableCrossBorderTrade() && Helper.isProductPriceIncludesTax() && !displayIncludeTax){
                            var taxRates = self.tax_origin_rates();
                            if(taxRates && taxRates.length > 0){
                                $.each(taxRates, function (index, rate) {
                                    rowTotal = rowTotal / (1 + rate/100);
                                });
                            }
                        }
                        return "Reg. " + Helper.convertAndFormatPrice(rowTotal);
                    });
                }
                if(!self.show_original_price) {
                    self.show_original_price = ko.pureComputed(function () {
                        return (self[ItemInterface.HAS_CUSTOM_PRICE]() == true && self[ItemInterface.CUSTOM_PRICE_AMOUNT]() >= 0 && self[ItemInterface.CUSTOM_PRICE_TYPE]());
                    });
                }
                /**
                 * Other extension can use this event to add data/computed object
                 * @type {{data: *, item: exports}}
                 */
                var eventData = {data : data, item:this};
                Helper.dispatchEvent('webpos_cart_item_init_data_after', eventData);
            },
            setIndividualData:function(key, value){
                var self = this;
                if (typeof self[key] != "undefined") {
                    if (key == ItemInterface.QTY) {
                        if (self[ItemInterface.MAXIMUM_QTY]() && value > self[ItemInterface.MAXIMUM_QTY]()) {
                            value = Helper.toNumber(self[ItemInterface.MAXIMUM_QTY]());
                            Helper.alert({
                                priority: "warning",
                                title: "Warning",
                                message: self["product_name"]() + Helper.__(" has maximum quantity allow in cart is ") + value
                            });
                        }
                        if (self[ItemInterface.MINIMUM_QTY]() && value < self[ItemInterface.MINIMUM_QTY]()) {
                            value = Helper.toNumber(self[ItemInterface.MINIMUM_QTY]());
                            Helper.alert({
                                priority: "warning",
                                title: "Warning",
                                message: self["product_name"]() + Helper.__(" has minimum quantity allow in cart is ") + value
                            });
                        }
                    }
                    self[key](value);
                }
            },
            setData: function(key, value){
                var self = this;
                if($.type(key) == 'string') {
                    self.setIndividualData(key, value);
                }else{
                    $.each(key, function(index, val){
                        self.setIndividualData(index, val);
                    })
                }
            },
            getData: function(key){
                var self = this;
                var data = {};
                if(typeof key != "undefined"){
                    data = self[key]();
                }else{
                    var data = {};
                    $.each(self.initFields, function(){
                        data[this] = self[this]();
                    });
                }
                return data;
            },
            getInfoBuyRequest: function(){
                var self = this;
                var infobuy = {};
                infobuy.item_id = self.item_id();
                infobuy.id = self[ItemInterface.PRODUCT_ID]();
                infobuy.qty = self[ItemInterface.QTY]();
                infobuy.qty_to_ship = self[ItemInterface.QTY_TO_SHIP]();
                infobuy.use_discount = Helper.isOnlineCheckout()?1:0;

                if((self[ItemInterface.PRODUCT_ID]() == "customsale") || self[ItemInterface.IS_CUSTOM_SALE]()){
                    infobuy.is_custom_sale = true;
                }

                if(self[ItemInterface.HAS_CUSTOM_PRICE]() == true && self[ItemInterface.CUSTOM_PRICE_AMOUNT]() >= 0){
                    // infobuy.custom_price = Helper.convertPrice(self.item_price());
                    infobuy.custom_price = Helper.convertPrice(self.item_price_origin());
                }
                if(self[ItemInterface.SUPER_ATTRIBUTE]()){
                    infobuy.super_attribute = self[ItemInterface.SUPER_ATTRIBUTE]();
                }
                if(self[ItemInterface.OPTIONS]()){
                    infobuy.options = self[ItemInterface.OPTIONS]();
                }else{
                    if((self[ItemInterface.PRODUCT_ID]() == "customsale") || self[ItemInterface.IS_CUSTOM_SALE]()){
                        var customsaleOptions = [
                            {code:"tax_class_id",value:self[ItemInterface.TAX_CLASS_ID]()},
                            {code:"price",value:self[ItemInterface.UNIT_PRICE]()},
                            {code:"is_virtual",value:self[ItemInterface.IS_VIRTUAL]()},
                            {code:"name",value:self[ItemInterface.PRODUCT_NAME]()}
                        ];
                        infobuy.options = customsaleOptions;
                    }
                }
                if(self[ItemInterface.SUPER_GROUP]()){
                    infobuy[ItemInterface.SUPER_GROUP] = self[ItemInterface.SUPER_GROUP]();
                }
                if(self[ItemInterface.BUNDLE_OPTION]() && self[ItemInterface.BUNDLE_OPTION_QTY]()){
                    infobuy.bundle_option = self[ItemInterface.BUNDLE_OPTION]();
                    infobuy.bundle_option_qty = self[ItemInterface.BUNDLE_OPTION_QTY]();
                }
                var itemPrice = Helper.convertPrice(this.item_price());
                var baseItemPrice = this.item_price();
                var rowTotalInclTax = Helper.convertPrice(this.row_total_include_tax());
                var baseRowTotalInclTax = this.row_total_include_tax();
                var priceInclTax = rowTotalInclTax / this.qty();
                var basePriceInclTax = baseRowTotalInclTax / this.qty();
                var baseDiscountTaxCompensationAmount= this.tax_amount_before_discount() - this.tax_amount();
                var discountTaxCompensationAmount = Helper.convertPrice(baseDiscountTaxCompensationAmount);

                infobuy.extension_data = [
                    {key:"row_total",value:Helper.correctPrice(Helper.convertPrice(self[ItemInterface.ROW_TOTAL]()))},
                    {key:"base_row_total",value:Helper.correctPrice(self[ItemInterface.ROW_TOTAL]())},
                    {key:"price",value:Helper.correctPrice(Helper.convertPrice(self.item_price()))},
                    {key:"base_price",value:Helper.correctPrice(self.item_price())},
                    {key:"original_price",value:Helper.correctPrice(Helper.convertPrice(self.item_price()))},
                    {key:"base_original_price",value:Helper.correctPrice(self.item_price())},
                    {key:"discount_amount",value:-Helper.correctPrice(self[ItemInterface.ITEM_DISCOUNT_AMOUNT]())},
                    {key:"base_discount_amount",value:-Helper.correctPrice(self[ItemInterface.ITEM_BASE_DISCOUNT_AMOUNT]())},
                    {key:"tax_amount",value:Helper.correctPrice(self.tax_amount_converted())},
                    {key:"base_tax_amount",value:Helper.correctPrice(self[ItemInterface.TAX_AMOUNT]())},
                    {key:"custom_tax_class_id",value:Helper.correctPrice(self[ItemInterface.TAX_CLASS_ID]())},
                    {key:"price_incl_tax",value:priceInclTax},
                    {key:"base_price_incl_tax",value:basePriceInclTax},
                    {key:"discount_tax_compensation_amount",value:Helper.correctPrice(discountTaxCompensationAmount)},
                    {key:"base_discount_tax_compensation_amount",value:Helper.correctPrice(baseDiscountTaxCompensationAmount)},
                ];
                infobuy.quote_item_data = [];

                /* S: Integration custom discount per item - add item discount data to save on server database */
                if(Helper.isStoreCreditEnable()){
                    infobuy.amount = self[ItemInterface.CREDIT_AMOUNT]();
                    infobuy.credit_price_amount = self[ItemInterface.CREDIT_PRICE_AMOUNT]();
                    infobuy.extension_data.push({
                        key: "customercredit_discount",
                        value: Helper.correctPrice(self[ItemInterface.ITEM_CREDIT_AMOUNT]())
                    });
                    infobuy.extension_data.push({
                        key: "base_customercredit_discount",
                        value: Helper.correctPrice(self[ItemInterface.ITEM_BASE_CREDIT_AMOUNT]())
                    });
                    if(self[ItemInterface.CREDIT_PRICE_AMOUNT]()){
                        infobuy.extension_data.push({
                            key: "original_price",
                            value: Helper.convertPrice(self[ItemInterface.CREDIT_PRICE_AMOUNT]())
                        });
                        infobuy.extension_data.push({
                            key: "base_original_price",
                            value: self[ItemInterface.CREDIT_PRICE_AMOUNT]()
                        });
                    }
                    if(!infobuy.options){
                        infobuy.options = [];
                    }
                    infobuy.options.push({
                        code: "credit_price_amount",
                        value: self[ItemInterface.CREDIT_PRICE_AMOUNT]()
                    });
                    infobuy.options.push({
                        code: "amount",
                        value: self.amount()
                    });
                }
                if(Helper.isRewardPointsEnable()) {
                    infobuy.extension_data.push({
                        key: "rewardpoints_earn",
                        value: Helper.correctPrice(self[ItemInterface.ITEM_POINT_EARN]())
                    });
                    infobuy.extension_data.push({
                        key: "rewardpoints_spent",
                        value: Helper.correctPrice(self[ItemInterface.ITEM_POINT_SPENT]())
                    });
                    infobuy.extension_data.push({
                        key: "rewardpoints_discount",
                        value: Helper.correctPrice(self[ItemInterface.ITEM_POINT_DISCOUNT]())
                    });
                    infobuy.extension_data.push({
                        key: "rewardpoints_base_discount",
                        value: Helper.correctPrice(self[ItemInterface.ITEM_BASE_POINT_DISCOUNT]())
                    });
                }
                if(Helper.isGiftCardEnable()) {
                    infobuy.extension_data.push({
                        key: "gift_voucher_discount",
                        value: Helper.correctPrice(self[ItemInterface.ITEM_GIFTCARD_DISCOUNT]())
                    });
                    infobuy.extension_data.push({
                        key: "base_gift_voucher_discount",
                        value: Helper.correctPrice(self[ItemInterface.ITEM_BASE_GIFTCARD_DISCOUNT]())
                    });

                    if (self[ItemInterface.TYPE_ID]() == 'giftvoucher') {
                        infobuy.amount = self[ItemInterface.ITEM_GIFTCARD_AMOUNT]();
                        infobuy.giftcard_template_id = self[ItemInterface.ITEM_GIFTCARD_TEMPLATE_ID]();
                        if (self[ItemInterface.ITEM_GIFTCARD_CAN_SHIP]()) {
                            infobuy.recipient_ship = "Yes";
                        }
                    }
                }
                /* E: Integration custom discount per item  */

                //if(Helper.isOnlineCheckout()){
                    if(infobuy.options){
                        infobuy.options = self.parseParamsForOnline(infobuy.options);
                    }
                    if(infobuy.super_attribute){
                        infobuy.super_attribute = self.parseParamsForOnline(infobuy.super_attribute);
                    }
                    if(infobuy.bundle_option){
                        infobuy.bundle_option = self.parseParamsForOnline(infobuy.bundle_option);
                    }
                    if(infobuy.bundle_option_qty){
                        infobuy.bundle_option_qty = self.parseParamsForOnline(infobuy.bundle_option_qty);
                    }
                //}

                if(Helper.isInventorySuccessEnable()) {
                    var warehouseId = parseInt(self[ItemInterface.WAREHOUSE_ID]());
                    infobuy.quote_item_data.push({
                        key: "warehouse_id",
                        value: warehouseId
                    });
                    infobuy.quote_item_data.push({
                        key: "ordered_warehouse_id",
                        value: warehouseId
                    });
                }

                /**
                 * Other extensions can use this event to add information to buy request
                 * @type {{buy_request: {}, item: exports}}
                 */
                var eventData = {buy_request : infobuy, item:this};
                Helper.dispatchEvent('webpos_cart_item_prepare_info_buy_request_after', eventData);

                return infobuy;
            },
            getDataForOrder: function(){
                var self = this;
                var rowTotalInclTax = Helper.convertPrice(self.row_total_include_tax());
                var baseRowTotalInclTax = self.row_total_include_tax();
                var priceInclTax = rowTotalInclTax / self.qty();
                var basePriceInclTax = baseRowTotalInclTax / self.qty();
                var baseDiscountTaxCompensationAmount= self.tax_amount_before_discount() - self.tax_amount();
                var discountTaxCompensationAmount = Helper.convertPrice(baseDiscountTaxCompensationAmount);

                var data = {
                    item_id:self[ItemInterface.ITEM_ID](),
                    name:self[ItemInterface.PRODUCT_NAME](),
                    product_id:self[ItemInterface.PRODUCT_ID](),
                    product_type:self[ItemInterface.PRODUCT_TYPE](),
                    sku:self[ItemInterface.SKU](),
                    qty_canceled:0,
                    qty_invoiced:0,
                    qty_ordered:self[ItemInterface.QTY](),
                    qty_refunded:0,
                    qty_shipped:0,
                    discount_amount:Helper.correctPrice(self[ItemInterface.ITEM_DISCOUNT_AMOUNT]()),
                    base_discount_amount:Helper.correctPrice(self[ItemInterface.ITEM_BASE_DISCOUNT_AMOUNT]()),
                    original_price:Helper.convertPrice(self[ItemInterface.UNIT_PRICE]()),
                    base_original_price:self[ItemInterface.UNIT_PRICE](),
                    tax_amount:Helper.convertPrice(self[ItemInterface.TAX_AMOUNT]()),
                    base_tax_amount:self[ItemInterface.TAX_AMOUNT](),
                    price:Helper.convertPrice(self.item_price()),
                    base_price:self.item_price(),
                    row_total:Helper.convertPrice(self[ItemInterface.ROW_TOTAL]()),
                    base_row_total:self[ItemInterface.ROW_TOTAL](),
                    price_incl_tax:priceInclTax,
                    base_price_incl_tax:basePriceInclTax,
                    row_total_incl_tax:rowTotalInclTax,
                    base_row_total_incl_tax:baseRowTotalInclTax,
                    discount_tax_compensation_amount:discountTaxCompensationAmount,
                    base_discount_tax_compensation_amount:baseDiscountTaxCompensationAmount
                };

                /* S: Integration custom discount per item - add item data for offline order */
                if(Helper.isStoreCreditEnable()) {
                    data.amount = self.amount();
                    data.credit_price_amount = self[ItemInterface.CREDIT_PRICE_AMOUNT]();
                    data.customercredit_discount = Helper.correctPrice(self[ItemInterface.ITEM_CREDIT_AMOUNT]());
                    data.base_customercredit_discount = Helper.correctPrice(self[ItemInterface.ITEM_BASE_CREDIT_AMOUNT]());
                    if(self[ItemInterface.CREDIT_PRICE_AMOUNT]()){
                        data.original_price = Helper.convertPrice(self[ItemInterface.CREDIT_PRICE_AMOUNT]());
                        data.base_original_price = self[ItemInterface.CREDIT_PRICE_AMOUNT]();
                    }
                }
                if(Helper.isRewardPointsEnable()) {
                    data.rewardpoints_earn = Helper.correctPrice(self[ItemInterface.ITEM_POINT_EARN]());
                    data.rewardpoints_spent = Helper.correctPrice(self[ItemInterface.ITEM_POINT_SPENT]());
                    data.rewardpoints_discount = Helper.correctPrice(self[ItemInterface.ITEM_POINT_DISCOUNT]());
                    data.rewardpoints_base_discount = Helper.correctPrice(self[ItemInterface.ITEM_BASE_POINT_DISCOUNT]());
                }
                if(Helper.isGiftCardEnable()) {
                    data.gift_voucher_discount = Helper.correctPrice(self[ItemInterface.ITEM_GIFTCARD_DISCOUNT]());
                    data.base_gift_voucher_discount = Helper.correctPrice(self[ItemInterface.ITEM_BASE_GIFTCARD_DISCOUNT]());
                }
                if(Helper.isInventorySuccessEnable()) {
                    data.warehouse_id = self[ItemInterface.WAREHOUSE_ID]();
                }
                /* E: Integration custom discount per item  */

                /**
                 * Other extension can use this event to add new fields for offline order
                 * @type {{data: {item_id: *, name: *, product_id: *, product_type: *, sku: *, qty_canceled: number, qty_invoiced: number, qty_ordered: *, qty_refunded: number, qty_shipped: number, discount_amount: *, base_discount_amount: *, original_price: *, base_original_price: *, tax_amount: *, base_tax_amount: *, price: *, base_price: *, price_incl_tax: number, base_price_incl_tax: number, row_total: *, base_row_total: *, row_total_incl_tax: *, base_row_total_incl_tax: *, discount_tax_compensation_amount: number, base_discount_tax_compensation_amount: number}, item: exports}}
                 */
                var eventData = {data : data, item:this};
                Helper.dispatchEvent('webpos_cart_item_prepare_data_for_offline_order_after', eventData);

                return data;
            },
            parseParamsForOnline: function(options){
                var params = {};
                if($.isArray(options)){
                    $.each(options, function(index, option){
                        if(option.code){
                            params[option.code] = option.value;
                        }
                    });
                }
                return params;
            },
            isUseOriginalTax: function(){
                var self = this;
                var taxOriginalRates = self[ItemInterface.TAX_ORIGIN_RATE]();
                var taxRates = self[ItemInterface.TAX_RATES]();
                if((taxOriginalRates && (taxOriginalRates.length == 0)) && (taxRates && (taxRates.length == 0))){
                    return true;
                }
                if((taxRates && (taxRates.length > 0)) && (taxOriginalRates && (taxOriginalRates.length > 0))){
                    if(taxRates.length == taxOriginalRates.length){
                        var notEqual = false;
                        $.each(taxRates, function (index, rate) {
                            if(!taxOriginalRates[index] || (taxOriginalRates[index] && (taxOriginalRates[index] != rate))){
                                notEqual = true;
                            }
                        });
                        return !notEqual;
                    }
                }
                return false;
            }
        });
    }
);