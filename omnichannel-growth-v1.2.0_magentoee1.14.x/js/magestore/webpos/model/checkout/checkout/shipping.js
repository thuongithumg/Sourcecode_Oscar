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
        'eventManager',
        'dataManager',
        'helper/general',
        'model/checkout/checkout',
        'model/checkout/cart',
        'model/checkout/cart/items'
    ],
    function ($, ko, Event, DataManager, Helper, CheckoutModel, CartModel, Items) {
        "use strict";
        var ShippingModel = {
            /**
             * Shipping methods
             */
            items: ko.observableArray(DataManager.getData('shipping')),
            /**
             * Check selected shipping method
             */
            isSelected: ko.pureComputed(function(){
                return CheckoutModel.selectedShippingCode();
            }),
            /**
             * Initialize
             */
            initialize: function(){
                var self = this;
                Event.observer('load_shipping_online_after', function(event, data){
                    if(data && data.items){
                        self.items(data.items);
                        self.initShippingMethod();
                    }
                });
            },
            /**
             * Init default method if not selected, resave shipping method if selected
             */
            initShippingMethod: function(){
                var self = this;
                var selectedCode = CheckoutModel.selectedShippingCode();
                var method = self.getShippingMethodByCode(selectedCode);
                if(method){
                    self.reSaveShippingMethod();
                }else{
                    if(Helper.isOnlineCheckout()){
                        self.saveDefaultShippingMethod();
                    }
                }
            },
            /**
             * Save shipping method
             * @param data
             */
            saveShippingMethod: function (data) {
                CheckoutModel.saveShipping(data);
                Event.dispatch('save_default_payment', '');
            },
            /**
             * Format shipping price
             * @param price
             * @param priceType
             * @returns {*}
             */
            formatShippingPrice: function(price, priceType){
                var shippingFee = 0;
                shippingFee = price;
                if(typeof priceType != "undefined"){
                    shippingFee = (priceType == "I")?(shippingFee * Items.totalShipableItems()):shippingFee;
                }
                return Helper.convertAndFormatPrice(shippingFee);
            },
            /**
             * get selected shipping method
             * @returns {*}
             */
            getSelectedShippingMethod: function () {
                var shippingList = this.items();
                if(shippingList.length > 0){
                    var selectedShippingCode = CheckoutModel.selectedShippingCode();
                    var method = false;
                    for(var i = 0; i < shippingList.length; i++){
                        if(shippingList[i].code == selectedShippingCode) {
                            method = shippingList[i];
                            break;
                        }
                    }
                    if(method == false){
                        CheckoutModel.selectedShippingCode('');
                        CheckoutModel.selectedShippingTitle('');
                    }else{
                        return method;
                    }
                }
                return false;
            },
            /**
             * get shipping method by code
             * @returns {*}
             */
            getShippingMethodByCode: function (code) {
                var shippingList = this.items();
                if(shippingList.length > 0){
                    var method = false;
                    for(var i = 0; i < shippingList.length; i++){
                        if(shippingList[i].code == code) {
                            method = shippingList[i];
                            break;
                        }
                    }
                    return method;
                }
                return false;
            },
            /**
             *
             */
            reSaveShippingMethod: function(){
                var self = this;
                var selectedMethod = self.getSelectedShippingMethod();
                if(selectedMethod && !CartModel.isVirtual()){
                    self.saveShippingMethod(selectedMethod);
                }
            },
            /**
             * get default shipping method
             * @returns {*}
             */
            getDefaultShippingMethod: function () {
                var self = this;
                var shippingList = this.items();
                if(shippingList.length > 0){
                    var defaultShippingCode = DataManager.getData('default_shipping_method');
                    var defaultMethod = false;
                    for(var i = 0; i < shippingList.length; i++){
                        if(shippingList[i].code == defaultShippingCode) {
                            defaultMethod = shippingList[i];
                            break;
                        }
                    }
                    if(defaultMethod == false){
                        CheckoutModel.useWebposShipping();
                        return self.getShippingMethodByCode('webpos_shipping_storepickup');
                    }else{
                        return defaultMethod;
                    }
                }
                return false;
            },
            /**
             * Save default shipping method
             */
            saveDefaultShippingMethod: function () {
                var self = this;
                var defaultMethod = self.getDefaultShippingMethod();
                if(defaultMethod && !CartModel.isVirtual()){
                    self.saveShippingMethod(defaultMethod);
                }
            },
            /**
             * Check default method
             * @param code
             * @returns {boolean}
             */
            isDefaultMethod: function (code) {
                var self = this;
                return (self.getDefaultShippingMethod() && self.getDefaultShippingMethod().code == code)?true:false;
            },
            /**
             * Check config enable delivery data
             * @returns {boolean}
             */
            useDeliveryTime: function () {
                return (Helper.getBrowserConfig('webpos/general/enable_delivery_date') == 1) ? true : false;
            },
            /**
             * Reset shipping list
             */
            resetShipping: function () {
                var self = this;
                if(!Helper.isOnlineCheckout()){
                    var posShipping = DataManager.getData('shipping');
                    self.items(posShipping);
                    self.saveDefaultShippingMethod();
                }
            }
        };
        ShippingModel.initialize();
        return ShippingModel;
    }
);