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
        'ui/components/layout',
        'view/base/abstract',
        'helper/price',
        'helper/datetime',
        'eventManager'
    ],
    
    function ($, ko, ViewManager, Component,  priceHelper, datetimeHelper, eventManager) {
        "use strict";

        return Component.extend({
            orderData: ko.observable(null),
            parentView: ko.observable(''),
            isVisible: ko.observable(false),
            classIn: ko.observable(''),
            stypeDisplay: ko.observable('none'),

            initialize: function () {
                this._super();
            },

            setData: function(data, object){
                var viewManager = require('ui/components/layout');
                this.orderData(data);
                this.parentView(object);
                eventManager.dispatch('sales_order_set_data_view_action', {'order': data});
                // viewManager.getSingleton('view/sales/order/view/payment').orderData(data);
                // viewManager.getSingleton('view/sales/order/view/payment').parentView(object);
            },

            getJsObject: function(){
                return {
                    parentView: this.parentView(),
                }
            },
            
            getAddressType: function(type){
                switch (type) {
                    case 'billing':
                        return this.orderData.call().billing_address;
                        break;
                    case 'shipping':
                        return this.orderData.call().extension_attributes.shipping_assignments[0].shipping.address;
                        break;
                }
            },

            getCustomerName: function(type) {
                var address = this.getAddressType(type);
                return address.firstname + ' ' + address.lastname;
            },

            getAddress: function(type){
                var address = this.getAddressType(type);
                var city = address.city ? address.city + ', ': '';
                var region = address.region ? address.region + ', ' : '';
                var postcode = address.postcode ? address.postcode + ', ' : '';
                return city + region + postcode + address.country_id;
            },
            
            display: function (isShow) {
                if(isShow) {
                    this.isVisible(true);
                    this.stypeDisplay('block');
                    this.classIn('in');
                    $('.notification-bell').hide();
                    $('#c-button--push-left').hide();
                }else {
                    this.isVisible(false);
                    this.stypeDisplay('none');
                    this.classIn('');
                    $('.notification-bell').show();
                    $('#c-button--push-left').show();
                }
            },

            convertAndFormatPrice: function(price, from, to){
                return priceHelper.convertAndFormat(price, from, to);
            },

            currencyConvert: function(price, from, to){
                return priceHelper.currencyConvert(price, from, to);
            },
            
            toBasePrice: function(price){
                return priceHelper.toBasePrice(price);
            },
            
            getPriceHelper: function (){
                return priceHelper;
            },

            show: function(actionName){
                eventManager.dispatch('sales_order_start_action', {'action': actionName});
            }
        });
    }
);