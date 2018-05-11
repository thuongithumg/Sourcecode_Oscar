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

define([
    'ko',
    'jquery',
    'helper/price',
    'helper/datetime',
    'helper/alert',
    'helper/staff',
    'eventManager',
    'model/config/local-config',
    'model/appConfig',
    'dataManager'
], function (ko, $, HelperPrice, HelperDatetime, Alert, Staff, EventManager, LocalConfig, AppConfig, DataManager) {
    'use strict';
    var Helper = {
        isOnlineCheckout: ko.observable(),
        isOnCheckoutPage: ko.observable(false),
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
            var checkoutMode = self.getLocalConfig(AppConfig.CONFIG_PATH.IS_USE_ONLINE);
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
        getPriceHelper: function () {
            return HelperPrice;
        },
        __: function (string) {
            return Translator.translate(string);
        },
        getDatetimeHelper: function () {
            return HelperDatetime;
        },
        addNotification: function (message, showAlert, alertPriority, alertTitle) {
            // AddNoti(message, showAlert, alertPriority, alertTitle);
        },
        dispatchEvent: function (eventName, data, timeout) {
            EventManager.dispatch(eventName, data, timeout);
        },
        observerEvent: function (eventName, function_callback) {
            EventManager.observer(eventName, function_callback);
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
        isPdfInvoicePlusEnable: function () {
            var plugin = this.getBrowserConfig('plugins');
            var plugins_config = this.getBrowserConfig('plugins_config');
            if (plugin && plugin.length > 0 && $.inArray('os_pdf_invoice_plus', plugin) !== -1) {
                if (plugins_config && plugins_config['os_pdf_invoice_plus']) {
                    return true;
                }
            }
            return false;
        },
        isInventorySuccessEnable: function () {
            var plugin = this.getBrowserConfig('plugins');
            var plugins_config = this.getBrowserConfig('plugins_config');
            if (plugin && plugin.length > 0 && $.inArray('os_inventory_success', plugin) !== -1) {
                if (plugins_config && plugins_config['os_inventory_success']) {
                    return true;
                }
            }
            return false;
        },
        isStorePickupEnable: function () {
            var plugin = this.getBrowserConfig('plugins');
            var plugins_config = this.getBrowserConfig('plugins_config');
            if (plugin && plugin.length > 0 && $.inArray('os_storepickup', plugin) !== -1) {
                if (plugins_config && plugins_config['os_storepickup']['enable']) {
                    return true;
                }
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
        getQuoteInitParams: function () {
            var self = this;
            return {
                quote_id: DataManager.getData(self.KEY.QUOTE_ID),
                store_id: DataManager.getData(self.KEY.STORE_ID),
                customer_id: DataManager.getData(self.KEY.CUSTOMER_ID),
                currency_id: DataManager.getData(self.KEY.CURRENCY_ID)
            };
        },
        isAutoCheckPromotion: function () {
            var useOnline = this.getLocalConfig(AppConfig.CONFIG_PATH.IS_USE_ONLINE);
            var autoCheckPromotion = this.getLocalConfig(AppConfig.CONFIG_PATH.AUTO_CHECK_PROMOTION);
            return (useOnline == false) && (autoCheckPromotion == true);
        },
        isAutoSyncRewardPointsBalance: function () {
            var config = this.getLocalConfig(AppConfig.CONFIG_PATH.AUTO_SYNC_POINTS_BALANCE);
            return (config == true)?true:false;
        },
        isAutoSyncCreditBalance: function () {
            var config = this.getLocalConfig(AppConfig.CONFIG_PATH.AUTO_SYNC_CREDIT_BALANCE);
            return (config == true)?true:false;
        },
        isShowCreditBalanceOnReceipt: function () {
            var config = this.getLocalConfig(AppConfig.CONFIG_PATH.SHOW_CUSTOMER_CREDIT_BALANCE_ON_RECEIPT);
            return (config == true)?true:false;
        },
        isShowPointsBalanceOnReceipt: function () {
            var config = this.getLocalConfig(AppConfig.CONFIG_PATH.SHOW_CUSTOMER_POINTS_BALANCE_ON_RECEIPT);
            return (config == true)?true:false;
        },
        /**
         * Check cross border trade
         * @returns {boolean}
         */
        isEnableCrossBorderTrade: function(){
            var self = this;
            var cross_border_trade_enabled = self.getBrowserConfig('tax/calculation/cross_border_trade_enabled');
            if(self.isProductPriceIncludesTax()  && typeof cross_border_trade_enabled != 'undefined' && cross_border_trade_enabled == 1){
                return true;
            }
            return false;
        },
        /**
         * Check cross border trade
         * @returns {boolean}
         */
        isShowTaxFinal: function(){
            var self = this;
            var apply_after_discount = self.getBrowserConfig('tax/calculation/apply_after_discount');
            var showFinal = (apply_after_discount == 1 && self.isProductPriceIncludesTax());
            return showFinal?true:false;
        },
        /**
        * Round down number
        */
        round: function(tax){
            var priceFormat = window.webposConfig.priceFormat;
            var nAdj = Math.pow(10, priceFormat.precision);
            tax = Math.floor(tax * nAdj) / nAdj;
            return tax;
        },
        /**
         * Get PDF invoice template
         * @returns {boolean}
         */
        getPdfInvoiceTemplate: function () {
            var config = this.getLocalConfig(AppConfig.CONFIG_PATH.PDF_INVOICE_PLUS_ORDERS_PRINTING_TEMPLATE);
            return (config == 0)?false:config;
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
         * Get current staff ID
         * @returns {*}
         */
        getCurrentStaffId: function(){
            return Staff.getStaffId();
        },

        isUseShiftOnline: function () {
            return true;
        }
    };
    return Helper.initialize();
});