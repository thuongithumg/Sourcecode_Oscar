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

/*global define*/
define(
    [
        'jquery',
        'ko',
        'mage/translate',
        'model/sales/order/payment',
        'eventManager',
        'helper/alert',
        'helper/price',
        'action/notification/add-notification'
    ],
    function($, ko,
             $t,
             Payment, eventmanager, alertHelper, PriceHelper, notification) {
        'use strict';
        return {
            isValid: false,
            orderData: {},
            submitData: {
                "payment":{
                    "method_data":[]
                }
            },

            execute: function(datas, orderData, deferred, parent){
                var self = this;
                this.orderData = orderData;
                this.isValid = false;
                if(datas.length>0)
                    this.isValid = true;
                if(!this.isValid){
                    deferred.reject('Please select a payment method!');
                    return;
                }
                var paymentsData = [];
                $.each(datas,function(){
                    var data = {};
                    data.code = this.code;
                    data.title = this.title;
                    data.base_amount = Math.round(this.cart_total*100)/100;
                    data.amount = PriceHelper.currencyConvert(
                        this.cart_total,
                        orderData.base_currency_code,
                        window.webposConfig.currentCurrencyCode
                    );
                    if ($.isNumeric(data.base_amount) == false && $.isNumeric(data.amount) == false ) {
                        data.amount = PriceHelper.toNumber(this.paid_amount);
                        data.base_amount = data.amount;
                    }
                    if(orderData.base_grand_total > this.paid_amount) {
                        data.base_real_amount = Math.round(this.paid_amount * 100) / 100;
                        data.real_amount = PriceHelper.currencyConvert(
                            this.paid_amount,
                            orderData.base_currency_code,
                            window.webposConfig.currentCurrencyCode
                        );
                    }else{
                        // data.real_amount = orderData.grand_total;
                        // data.base_real_amount = orderData.base_grand_total;
                        data.real_amount = data.amount;
                        data.base_real_amount = data.base_amount;
                    }
                    data.reference_number = this.reference_number;
                    data.is_pay_later = this.is_pay_later;
                    data.shift_id = window.webposConfig.shiftId?window.webposConfig.shiftId:'';
                    paymentsData.push(data);
                });
                this.submitData = {
                    "payment":{
                        "method_data": paymentsData
                    },
                    "order_increment_id": orderData.increment_id,
                    'order_id': orderData.entity_id
                };
                if(this.submitData.payment.method_data.length>0){
                    eventmanager.dispatch('webpos_order_take_payment_before_save', this.submitData);
                    this.saveOrderOffline(this.submitData);
                    Payment().setData(this.orderData).setPostData(this.submitData).setMode('online').save()
                        .done(function (response) {
                            if (response && response.increment_id) {
                                eventmanager.dispatch('sales_order_afterSave', {'response': response});
                                eventmanager.dispatch('orders_history_show_container_after', '');
                            }
                            deferred.resolve(response);
                        })
                        .fail(error => {
                            error = JSON.parse(error.responseText);

                            let message = `Have error while syncing ${self.id}.`;

                            if(error && error.messages !== undefined){
                                message = error.messages.error[0].message;
                            }


                            deferred.reject(message)
                        })
                        .always(parent.display(false));
                }                
            },

            saveOrderOffline: function(submitData){
                var self = this;
                if(this.submitData.payment.method_data.length>0){
                    if(!this.orderData.webpos_order_payments)
                        this.orderData.webpos_order_payments = [];
                    $.each(this.submitData.payment.method_data, function(index, value){
                        self.orderData.webpos_order_payments.push({
                            base_payment_amount: value.base_amount,
                            method: value.code,
                            method_title: value.title,
                            payment_amount: value.amount,
                            base_display_amount: value.base_real_amount,
                            display_amount: value.real_amount,
                        });
                        self.orderData.base_total_paid+=value.base_amount;
                        var amount = PriceHelper.currencyConvert(
                            value.base_amount,
                            self.orderData.base_currency_code,
                            self.orderData.order_currency_code
                        );
                        self.orderData.total_paid+=amount;
                        
                        self.orderData.base_total_due-=value.base_amount;
                        self.orderData.total_due-=amount;
                    });
                    if(self.orderData.base_total_paid-self.orderData.base_grand_total>0){
                        self.orderData.webpos_base_change=self.orderData.base_total_paid-self.orderData.base_grand_total;
                        self.orderData.webpos_change=self.orderData.total_paid-self.orderData.grand_total;
                    }
                }
                eventmanager.dispatch('sales_order_take_payment_beforeSave', {'response': this.submitData});
                eventmanager.dispatch('sales_order_afterSave', {'response': this.orderData});
            },
        }
    }
);
