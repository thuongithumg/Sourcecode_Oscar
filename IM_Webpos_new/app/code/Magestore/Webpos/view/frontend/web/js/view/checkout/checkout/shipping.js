/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'require',
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/view/base/list/collection-list',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/shipping-factory',
        'Magestore_Webpos/js/helper/general',
        'mage/calendar',
    ],
    function (require, $, ko, ViewManager, colGrid, CheckoutModel, PriceHelper, Items, CartModel, ShippingFactory, Helper) {
        "use strict";
        return colGrid.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/shipping',
            },
            onlineItems: ko.observableArray([]),
            initialize: function () {
                this.isShowHeader = true;
                this.model = ShippingFactory.get().setMode('offline');
                this.isCheck = ko.pureComputed(function(){
                    return CheckoutModel.selectedShippingCode();
                });
                this._super();
                this._render();
                this.initObserver();
            },
            initObserver: function(){
                var self = this;
                Helper.observerEvent('go_to_checkout_page', function(){
                    if(!Helper.isUseOnline('checkout')){
                        self._render();
                    }
                });
                Helper.observerEvent('load_shipping_online_after', function(event, data){
                    if(data && data.items){
                        self.onlineItems(data.items);
                        self._prepareItems();
                        self.initShippingMethod();
                    }
                });
            },
            _prepareCollection: function () {
                if(this.collection == null) {
                    this.collection = this.model.getCollection().setOrder('price','DESC');
                }
                this.collection.setPageSize(10);
                this.collection.setCurPage(1);
            },
            _prepareItems: function () {
                var self = this;
                if (Helper.isUseOnline('checkout')) {
                    self.items(self.onlineItems());
                }else{
                    this._super();
                }
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
                    self.saveDefaultShippingMethod();
                }
            },
            /**
             * Save shipping method
             * @param data
             */
            saveShippingMethod: function (data) {
                CheckoutModel.saveShipping(data);
                Helper.observerEvent('save_default_payment', '');
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
                    var defaultShippingCode = Helper.getBrowserConfig('default_shipping');
                    defaultShippingCode = (defaultShippingCode == 'webpos_shipping')?'webpos_shipping_storepickup':defaultShippingCode+'_'+defaultShippingCode;
                    var defaultMethod = false;
                    for(var i = 0; i < shippingList.length; i++){
                        if(shippingList[i].code == defaultShippingCode) {
                            defaultMethod = shippingList[i];
                            break;
                        }
                        if(shippingList[i].code == 'webpos_shipping_storepickup' && defaultShippingCode == ''){
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
                if(!Helper.isUseOnline('checkout')){
                    self._prepareItems();
                    self.saveDefaultShippingMethod();
                }
            },
            setShippingMethod: function (data, event) {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                CheckoutModel.saveShipping(data);
                viewManager.getSingleton('view/checkout/checkout/payment').saveDefaultPaymentMethod();
            },
            getShippingPrice: function(price, priceType){
                var shippingFee = 0;
                shippingFee = price;
                if(typeof priceType != "undefined"){
                    shippingFee = (priceType == "I")?(shippingFee * Items.totalShipableItems()):shippingFee;
                }
                return PriceHelper.convertAndFormat(shippingFee);
            },
            checkDefaultMethod: function (code) {
                if(CheckoutModel.selectedShippingCode()) {
                    if($('#'+code).length && CheckoutModel.selectedShippingCode() == code) {
                        $('#'+code).prop("checked", true);
                    }
                }else {
                    if (this.getDefaultShippingMethod() && this.getDefaultShippingMethod().code == code) {
                        $('#' + code).prop("checked", true);
                    }
                }
            },
            initDate: function () {
                var currentDate = new Date();
                var year = currentDate.getFullYear();
                var month = currentDate.getMonth();
                var day = currentDate.getDate();
                $("#delivery_date").calendar({
                    showsTime: true,
                    controlType: 'select',
                    timeFormat: 'HH:mm',
                    showTime: false,
                    minDate: new Date(year, month, day, '00', '00', '00', '00'),
                });
            }
        });
    }
);