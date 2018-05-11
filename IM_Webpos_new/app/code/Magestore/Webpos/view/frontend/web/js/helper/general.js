/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'jquery',
    'Magestore_Webpos/js/helper/staff',
    'Magestore_Webpos/js/helper/datetime',
    'mage/translate',
    'Magestore_Webpos/js/helper/price',
    'Magestore_Webpos/js/helper/alert',
    'Magestore_Webpos/js/helper/datetime',
    'Magestore_Webpos/js/action/notification/add-notification',
    'Magestore_Webpos/js/model/event-manager',
    'Magestore_Webpos/js/model/config/local-config',
    'Magestore_Webpos/js/lib/cookie'

], function (ko, $, Staff, HelperDateTime, Translate, HelperPrice, Alert, HelperDatetime, AddNotification, EventManager, LocalConfig, Cookies) {
    'use strict';
    var Helper = {
        isOnlineCheckout: ko.observable(),
        isOnCheckoutPage: ko.observable(false),
        isSynchronization: ko.observable(false),
        initialize: function () {
            var self = this;
            self.initCheckoutMode();
            EventManager.observer('checkout_mode_configuration_change', function(){
                self.initCheckoutMode();
                EventManager.dispatch('checkout_mode_configuration_change_after', '');
            });
            return self;
        },
        initCheckoutMode: function(){
            var self = this;
            var checkoutMode = self.getLocalConfig('os_checkout/enable_online_mode');
            self.isOnlineCheckout((checkoutMode == 1) ? true : false);
        },
        alert: function (priority, title, message) {
            if (typeof priority == 'string') {
                Alert({
                    priority: priority,
                    title: title,
                    message: message
                });
            } else {
                Alert(priority);
            }
        },
        convertAndFormatPrice: function (amount, from, to) {
            return HelperPrice.convertAndFormat(amount, from, to);
        },
        convertAndFormatWithoutSymbol: function (amount, from, to) {
            return HelperPrice.convertAndFormatWithoutSymbol(amount, from, to);
        },
        convertPrice: function (amount, from, to) {
            return HelperPrice.currencyConvert(amount, from, to);
        },
        formatPrice: function (value) {
            return HelperPrice.formatPrice(value);
        },
        formatPriceWithoutSymbol: function (value) {
            return HelperPrice.formatPriceWithoutSymbol(value);
        },
        toBasePrice: function (value) {
            return HelperPrice.toBasePrice(value);
        },
        toNumber: function (value) {
            return HelperPrice.toNumber(value);
        },
        correctPrice: function (value) {
            return HelperPrice.correctPrice(value);
        },
        roundPrice: function (value) {
            return HelperPrice.roundPrice(value);
        },
        getPriceHelper: function () {
            return HelperPrice;
        },
        __: function (string) {
            return Translate(string);
        },
        getDatetimeHelper: function () {
            return HelperDatetime;
        },
        addNotification: function (message, showAlert, alertPriority, alertTitle) {
            AddNotification(message, showAlert, alertPriority, alertTitle);
        },
        dispatchEvent: function (eventName, data, timeout) {
            EventManager.dispatch(eventName, data, timeout);
        },
        observerEvent: function (eventName, function_callback) {
            EventManager.observer(eventName, function_callback);
        },
        getObject: function (objectPath) {

        },
        getBrowserConfig: function (path) {
            return (window.webposConfig[path]) ? window.webposConfig[path] : "";
        },
        saveBrowserConfig: function (path, value) {
            window.webposConfig[path] = value;
        },
        isHavePermission: function (resource) {
            return Staff.isHavePermission(resource);
        },
        saveLocalConfig: function (configPath, value) {
            LocalConfig.save(configPath, value);
        },
        getLocalConfig: function (configPath) {
            return LocalConfig.get(configPath);
        },
        isStoreCreditEnable: function () {
            var plugin = this.getBrowserConfig('plugins');
            var plugins_config = this.getBrowserConfig('plugins_config');
            if (plugin && plugin.length > 0 && $.inArray('os_store_credit', plugin) !== -1) {
                if (plugins_config && plugins_config['os_store_credit']) {
                    return (plugins_config['os_store_credit']['customercredit/general/enable']) ? true : false;
                }
            }
            return false;
        },
        isRewardPointsEnable: function () {
            var plugin = this.getBrowserConfig('plugins');
            var plugins_config = this.getBrowserConfig('plugins_config');
            if (plugin && plugin.length > 0 && $.inArray('os_reward_points', plugin) !== -1) {
                if (plugins_config && plugins_config['os_reward_points']) {
                    return (plugins_config['os_reward_points']['rewardpoints/general/enable']) ? true : false;
                }
            }
            return false;
        },
        isGiftCardEnable: function () {
            var plugin = this.getBrowserConfig('plugins');
            var plugins_config = this.getBrowserConfig('plugins_config');
            if (plugin && plugin.length > 0 && $.inArray('os_gift_card', plugin) !== -1) {
                if (plugins_config && plugins_config['os_gift_card']) {
                    return (plugins_config['os_gift_card']['giftvoucher/general/active']) ? true : false;
                }
            }
            return false;
        },
        isGiftCardM2EEEnable: function () {
            var plugin = this.getBrowserConfig('plugins');
            if (plugin && plugin.length > 0 && $.inArray('giftCardAccount', plugin) !== -1) {
                return true;
            }
            return false;
        },
        isStoreCreditEEEnable: function () {
            var plugin = this.getBrowserConfig('plugins');
            var plugins_config = this.getBrowserConfig('plugins_config');
            if (plugin && plugin.length > 0 && $.inArray('os_storecredit_ee', plugin) !== -1) {
                return true;
            }
            return false;
        },
        getPluginConfig: function (pluginCode, path) {
            var plugin = this.getBrowserConfig('plugins');
            var plugins_config = this.getBrowserConfig('plugins_config');
            if (plugin && plugin.length > 0 && $.inArray(pluginCode, plugin) !== -1) {
                if (plugins_config && plugins_config[pluginCode]) {
                    return plugins_config[pluginCode][path];
                }
            }
            return false;
        },
        isProductPriceIncludesTax: function () {
            return (window.webposConfig['tax/calculation/price_includes_tax'] == '1') ? true : false;
        },
        isDiscountOnPriceIncludesTax: function () {
            return (window.webposConfig['tax/calculation/discount_tax'] == '1') ? true : false;
        },
        isCartDisplayIncludeTax: function (type) {
            if (type) {
                var EXCLUDE = '1';
                var INCLUDE = '2';
                var BOTH = '3';
                switch (type) {
                    case 'price':
                        return (window.webposConfig['tax/cart_display/price'] == EXCLUDE) ? false : true;
                        break;
                    case 'subtotal':
                        return (window.webposConfig['tax/cart_display/subtotal'] == EXCLUDE) ? false : true;
                        break;
                }
            }
            return true;
        },
        /**
         * Check cross border trade
         * @returns {boolean}
         */
        isEnableCrossBorderTrade: function () {
            var self = this;
            var cross_border_trade_enabled = self.getBrowserConfig('tax/calculation/cross_border_trade_enabled');
            if (self.isProductPriceIncludesTax() && typeof cross_border_trade_enabled != 'undefined' && cross_border_trade_enabled == 1) {
                return true;
            }
            return false;
        },
        /**
         * Check cross border trade
         * @returns {boolean}
         */
        isShowTaxFinal: function () {
            var self = this;
            var apply_after_discount = self.getBrowserConfig('tax/calculation/apply_after_discount');
            var showFinal = (apply_after_discount == 1 && self.isProductPriceIncludesTax());
            return showFinal ? true : false;
        },
        /**
         * @param key
         * @returns {*}
         */
        getOnlineConfig: function (key) {
            var data = this.getBrowserConfig('online_data');
            if (data && typeof data[key] != 'undefined') {
                return data[key];
            }
            return false;
        },
        /**
         * @param key
         * @param value
         */
        saveOnlineConfig: function (key, value) {
            var data = this.getBrowserConfig('online_data');
            if (data) {
                data[key] = value;
            }
        },
        /**
         * @param section
         * @returns {boolean}
         */
        isUseOnline: function(section){
            var self = this;
            var sections = self.getOnlineConfig('sections');
            var isAll = (($.inArray('all', sections.split(',')) >= 0) || self.isSynchronization());
            var isInArray = ($.inArray(section.toString(), sections.split(',')) >= 0);
            return ((isAll || isInArray));/* && self.isOnlineCheckout());*/
        },
        /**
         * Return true if use stocks online
         */
        isStockOnline: function(){
            return (this.isUseOnline('products') && this.isUseOnline('stocks'));
        },
        /**
         * Get current staff ID
         * @returns {*}
         */
        getCurrentStaffId: function(){
            return Staff.getStaffId();
        }
    };
    return Helper.initialize();
});