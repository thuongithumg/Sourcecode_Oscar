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
        'posComponent',
        'model/checkout/checkout',
        'helper/price',
        'helper/alert',
        'eventManager',
        'action/notification/add-notification'
    ],
    function ($,ko, Component, CheckoutModel,  PriceHelper, Alert, Event, AddNoti) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'ui/checkout/success'
            },
            successMessage: ko.observable(),
            successImageUrl: ko.observable(),
            initialize: function () {
                this._super();
                this.orderData = ko.pureComputed(function(){
                    var result = CheckoutModel.createOrderResult();
                    return (result && result.increment_id)?result:false;
                });
                this.createdOrder = ko.pureComputed(function(){
                    var result = CheckoutModel.createOrderResult();
                    return (result && result.increment_id)?true:false;
                })
            },
            getOrderData: function(key){
                var orderData = this.orderData();
                var data = "";
                if(orderData){
                    data = orderData;
                    if(key){
                        if(typeof data[key] != "undefined"){
                            data = data[key];
                        }else{
                            data = "";
                        }
                    }
                }
                return data;
            },
            getCustomerEmail: function(){
                return this.getOrderData('customer_email');
            },
            getGrandTotal: function(){
                return PriceHelper.formatPrice(this.getOrderData('grand_total'));
            },
            getOrderIdMessage: function(){
                return "#"+this.getOrderData('increment_id');
            },
            printReceipt: function(){
                Event.dispatch('print_receipt', '');
            },
            startNewOrder: function(){
                CheckoutModel.resetCheckoutData();
                Event.dispatch('start_new_order', '');
            },
            sendEmail: function(){
                var self = this;
                if(self.getCustomerEmail()){
                    CheckoutModel.sendEmail(self.getCustomerEmail(),self.getOrderData('increment_id'));
                }else{
                    Alert({
                        priority:"warning",
                        title: self.__("Warning"),
                        message: self.__("Please enter the email address")
                    });
                }
            },
            saveEmail: function(data,event){
                if(!this.orderData()){
                    this.orderData({});
                }
                this.orderData().customer_email = event.target.value;
            }
        });
    }
);