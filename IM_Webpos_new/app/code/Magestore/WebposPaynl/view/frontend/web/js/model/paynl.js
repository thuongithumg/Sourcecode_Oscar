/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'mage/translate',
        'Magestore_Webpos/js/helper/alert',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/view/checkout/checkout/payment_selected',
        'Magestore_Webpos/js/view/checkout/checkout/payment_popup',
        'Magestore_Webpos/js/view/checkout/checkout/payment',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
        'Magestore_WebposPaynl/js/resource/paynl',
        'Magestore_Webpos/js/action/notification/add-notification'
    ],
    function ($, ko, __, Alert, PriceHelper, Helper, Event, SelectedPayment, PopupPayment, Payment, CheckoutModel, CartModel, Items, TotalsFactory, ApiResource, addNotification) {
        "use strict";

        var PaynlService = {
            /**
             * Logo url
             */
            logoUrl: ko.observable(''),
            /**
             * Paynl authorize url
             */
            authorizeUrl: ko.observable(''),
            /**
             * show ajax loader
             */
            loading: ko.observable(false),
            /**
             * Flag to check the popup has been blocked
             */
            blocked: ko.observable(false),
            /**
             * Flag to check if allow customer pay via email
             */
            enableSendInvoice: ko.observable(false),
            /**
             * Save invoice number sent to customer email
             */
            lastInvoiceData: ko.observable(),
            /**
             * authorize window
             */
            authorizeWindow: ko.observable(),
            /**
             * columns
             */
            renderPaymentColumns: ko.observable(false),
            /**
             * Payment method code
             */
            PAYMENT_METHOD_CODE: 'paynl_payment_instore',
            /**
             * Initialize
             */
            initialize: function () {
                var self = this;
                self.initCallbacks();
                self.initPaymentMethod();
                self.initEvents();
                return self;
            },
            /**
             * Start create payment
             */
            start: function(){
                var self = this;
                var deferred = $.Deferred();
                var params = {
                    transaction:self.getTransactionData()
                };
                // ApiResource().setPush(true).setLog(false).startPayment(params, deferred);
                self.loading(true);
                deferred.done(function (response) {
                    if(response){
                        self.openAuthorizeWindow(response);
                    }else{
                        Event.dispatch('close_paynl_integration', '');
                    }
                    self.showMessage(response);
                }).always(function () {
                    self.loading(false);
                });
            },
            /**
             * Show message by process response
             * @param response
             */
            showMessage: function(response){
                if(response && response.message){
                    var priority = (response.success)?'success':'danger';
                    var title = (response.success)?'Message':'Error';
                    Alert({
                        priority: priority,
                        title: __(title),
                        message: __(response.message)
                    });
                }
            },
            /**
             * Open authorize window
             * @param url
             */
            openAuthorizeWindow: function(url){
                var newWin = window.open(url);
                if(!newWin || newWin.closed || typeof newWin.closed=='undefined')
                {
                    alert(__('Your browser has blocked the automatic popup, please change your browser setting and place order with Paynl'));
                }
                var self = this;
                if(self.authorizeWindow()){
                    self.authorizeWindow().close();
                }
                self.authorizeUrl(url);
                var authorizeWindow = window.open(url, 'authorize_window', 'status=1,width=700,height=700');
                if(authorizeWindow){
                    self.blocked(false);
                    self.authorizeWindow(authorizeWindow);
                }else{
                    self.blocked(true);
                }
            },
            /**
             * Close authorize window
             */
            closeAuthorizeWindow: function(){
                var self = this;
                if(self.authorizeWindow()){
                    self.authorizeWindow().close();
                    self.authorizeWindow('');
                }
            },
            /**
             * Close paynl integration popup
             */
            cancel: function(){
                Event.dispatch('close_paynl_integration', '');
            },
            /**
             * Update reference number and place order
             * @param responseText
             */
            success: function(responseText){
                var self = this;
                if(responseText){
                    var response = JSON.parse(responseText);
                    if(response){
                        self.showMessage(response);
                        if(response.success){
                            Event.dispatch('paynl_integration_payment_complete', '');
                            if(response.transactionId){
                                self.updateRefNumber(response.transactionId);
                                if(Helper.isUseOnline('checkout')){
                                    CheckoutModel.placeOrderOnline();
                                }else{
                                    CheckoutModel.createOrder();
                                }
                            }
                        }
                    }
                }
                Event.dispatch('close_paynl_integration', '');
            },
            /**
             * Set init object to call from childs window
             */
            initCallbacks: function(){
                var self = this;
                if(typeof window.paynlService == 'undefined'){
                    window.paynlService = {
                        cancel:self.cancel,
                        success:$.proxy(self.success, self)
                    };
                }
            },
            /**
             * Get paynl method from selected payment list
             * @returns {*}
             */
            getSelectedPaynlMethod: function(){
                var self = this;
                var payments = CheckoutModel.selectedPayments();
                var paynl = ko.utils.arrayFirst(payments, function(method){
                    return method.code == self.PAYMENT_METHOD_CODE;
                });
                return paynl;
            },
            /**
             * Subscribe the list to add paynl payment method
             */
            initPaymentMethod: function(){

            },
            /**
             * Add paynl payment method
             * @param items
             * @returns {*}
             */
            checkPaymentList: function(items){
                var self = this;
                var inserted = false;
                if(items.length > 0){
                    var paynl = ko.utils.arrayFirst(items, function(method){
                        return method.code == self.PAYMENT_METHOD_CODE;
                    });
                    if(paynl){
                        inserted = true;
                        paynl.type = "0";
                    }
                }
                if(!inserted){
                    var firstMethod = ko.utils.arrayFirst(items, function(method){
                        return method.code != '';
                    });
                    if(firstMethod || self.renderPaymentColumns() != false){
                        if(self.renderPaymentColumns() == false || firstMethod){
                            self.renderPaymentColumns(firstMethod.columns);
                        }
                        items.push({
                            code:self.PAYMENT_METHOD_CODE,
                            columns: self.renderPaymentColumns(),
                            information:"",
                            is_default:"0",
                            is_pay_later:0,
                            is_reference_number:"0",
                            title:__("Web POS - Paynl Integration"),
                            type:"0"
                        });
                    }
                }
                return items;
            },
            /**
             * Init some events, change event when place order
             */
            initEvents: function(){
                var self = this;
                // CheckoutModel.selectedPayments.subscribe(function(){
                //     // $('#checkout_button').unbind('click');
                //     $('#checkout_button').click(function(){
                //         $.proxy(self.placeOrder(), self);
                //     });
                // });
                Event.observer('webpos_place_order_before', function (event, data) {
                    self.placeOrderBefore(data);
                });
                Helper.observerEvent('webpos_place_order_online_before', function (event, data) {
                    self.placeOrderAfter(data);
                });
            },
            /**
             * Rewrite place order function
             * @returns {boolean}
             */
            placeOrder: function(){
                var self = this;
                var paynl = self.getSelectedPaynlMethod();
                if(paynl){
                    Event.dispatch('open_paynl_integration', '');
                    if(!self.enableSendInvoice()){
                        self.start();
                    }
                }else{
                    Event.dispatch('start_place_order', '');
                }
                return false;
            },
            /**
             * Save reference number after process paynl payment
             * @param transactionId
             */
            updateRefNumber: function(transactionId){
                var self = this;
                var paynl = self.getSelectedPaynlMethod();
                if(paynl){
                    paynl.reference_number = __("Paynl Transaction ID ")+'#'+transactionId;
                    paynl.is_pay_later = 0;
                }
            },
            /**
             * Get transaction params to create paynl payment
             * @returns {{total: *, currency: string, description: string}}
             */
            getTransactionData: function(){
                var self = this;
                var paynl = self.getSelectedPaynlMethod();
                var amount = (paynl)?paynl.cart_total:0;
                var transaction = {
                    total: PriceHelper.currencyConvert(amount),
                    currency: (window.webposConfig)?window.webposConfig.currentCurrencyCode:'',
                    description: ''
                }
                return transaction;
            },
            /**
             * Get paynl method from selected payment list
             * @returns {*}
             */
            // getSelectedPaynlMethod: function(){
            //     var self = this;
            //     var payments = CheckoutModel.selectedPayments();
            //     var paynl = ko.utils.arrayFirst(payments, function(method){
            //         return method.code == self.PAYMENT_METHOD_CODE;;
            //     });
            //     return paynl;
            // },

            /**
             * Check if customer has been selected
             * @returns {boolean}
             */
            validateCustomerData: function(){
                return (CartModel.customerId())?true:false;
            },
            /**
             * Add params to save data to order
             * @param data
             */
            placeOrderBefore: function(data){
                var self = this;
                var lastInvoiceData = self.lastInvoiceData();
                if (data && data.increment_id && CartModel.customerId() && lastInvoiceData && lastInvoiceData.id) {
                    data.sync_params.extension_data.push({
                        key:"webpos_paynl_invoice_number",
                        value: lastInvoiceData.number
                    });
                    data.sync_params.extension_data.push({
                        key:"webpos_paynl_invoice_id",
                        value: lastInvoiceData.id
                    });
                }
            },
            /**
             * Reset invoice data
             * @param data
             */
            placeOrderAfter: function(data){
                var self = this;
                var deferred = $.Deferred();
                var orderId = data.increment_id;
                var transactionData = self.getTransactionData();
                transactionData.quote_id = data.quote_id;
                transactionData.bank_id = $('#payment-select').val();
                var params = {
                    transaction: transactionData
                };
                if (data.payment.method == 'paynl_payment_instore') {
                    CheckoutModel.loading(true);
                    $('#authorizenet-directpost-progress-html').removeClass('hide');
                    ApiResource().setPush(true).setLog(false).startPayment(params, deferred);
                    deferred.done(function (response) {
                        if(response){
                            self.openAuthorizeWindow(response);
                        }else{
                            Event.dispatch('close_paynl_integration', '');
                        }
                        self.showMessage(response);
                    }).always(function () {
                        $('#authorizenet-directpost-progress-html').addClass('hide');
                    });
                }
            }
        };
        return PaynlService.initialize();
    }
);