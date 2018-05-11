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
        'model/checkout/checkout',
        'model/checkout/checkout/payment',
        'helper/general',
    ],
    function ($, ko, CheckoutModel, Payment, Helper) {
        "use strict";
         var PaynlInstoreModel = {
             CODE: 'pay_payment_instore',
             SELECTED_TERMINAL_PATH: 'pay_payment_instore.selected_terminal',
             selectedTerminal: ko.observable(),
             terminals: ko.observableArray([]),
             initialize: function () {
                 var self = this;
                 self.initEvents();
                 return self;
             },
             initEvents: function(){
                 var self = this;
                 Helper.observerEvent('webpos_place_order_before', function (event, data) {
                     self.placeOrderBefore(data);
                 });
                 Helper.observerEvent('webpos_order_take_payment_before_save', function (event, data) {
                     self.takePaymentOrderBefore(data);
                 });
                 Helper.observerEvent('webpos_place_order_online_before', function (event, data) {
                     self.placeOrderOnlineBefore(data);
                 });
                 self.selectedTerminal.subscribe(function(terminalId){
                     Helper.saveLocalConfig(self.SELECTED_TERMINAL_PATH, terminalId);
                 });
                 return self;
             },
             initData: function(form_data){
                 var self = this;
                 var form_data = JSON.parse(form_data);
                 if(form_data.terminals){
                     self.terminals(form_data.terminals);
                     self.selectedTerminal(self.getDefaultSelectedTerminal());
                 }
                 return self;
             },
             getDefaultSelectedTerminal: function(){
                 var self = this;
                 var terminals = self.terminals();
                 var defaultSelected = '';
                 if(terminals && (terminals.length > 0)){
                     var selectedTerminal = Helper.getLocalConfig(self.SELECTED_TERMINAL_PATH);
                     if(selectedTerminal){
                        $.each(terminals, function(index, terminal){
                            if(terminal.value == selectedTerminal){
                                defaultSelected =  selectedTerminal;
                            }
                        });
                     }else{
                         var terminal = terminals[0];
                         defaultSelected = terminal.value;
                     }
                 }
                 return defaultSelected;
             },
             insertTerminalIDToObject: function(object) {
                 let self = this;
                 if (CheckoutModel.paymentCode() === self.CODE) {
                     object.payment.terminalId = self.selectedTerminal();
                 }

                 if (CheckoutModel.paymentCode() === Payment.MULTIPLE_PAYMENT_CODE) {
                     object.payment.method_data = object.payment.method_data.map((payment) => {
                         if (payment.code === self.CODE) {
                             payment.additional_data = {
                                 terminalId : self.selectedTerminal(),
                                 amount : payment.amount
                             }
                         }

                         return payment;
                     });
                 }
             },
             placeOrderBefore: function(data){
                 if (!data && !data.increment_id) {
                    return;
                 }
                 this.insertTerminalIDToObject(data.sync_params);
             },
             placeOrderOnlineBefore: function(params){
                 this.insertTerminalIDToObject(params);
             },
             takePaymentOrderBefore: function(data){
                 var self = this;
                 if (data && data.order_increment_id) {
                     data.payment.method_data = data.payment.method_data.map((payment) => {
                         if (payment.code === self.CODE) {
                             payment.additional_data = {
                                 terminalId : self.selectedTerminal(),
                                 amount : payment.amount
                             }
                         }

                         return payment;
                     });

                 }
             }
         };
         return PaynlInstoreModel.initialize();
    }
);