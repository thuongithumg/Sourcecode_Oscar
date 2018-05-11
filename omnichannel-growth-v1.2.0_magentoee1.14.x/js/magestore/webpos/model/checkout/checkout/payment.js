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
        'model/checkout/cart',
        'model/checkout/checkout',
        'helper/price',
        'helper/general',
        'model/checkout/cart/totals',
        'model/checkout/checkout/payment/creditcard'
    ],
    function ($, ko, Event, DataManager, CartModel, CheckoutModel, PriceHelper, Helper, Totals, CreditCard) {
        "use strict";
        var PaymentModel = {
            DATA:{
                VISIBILITY_PAYMENT_TYPE: '1',
                VALID: '1',
                INVALID: '0'
            },
            FREE_PAYMENT_CODE:'free',
            MULTIPLE_PAYMENT_CODE:'multipaymentforpos',
            WEBPOS_PAYMENTS:['cashforpos','ccforpos','codforpos','cp1forpos','cp2forpos'],
            MULTIABLE_PAYMENTS:['cashforpos','ccforpos','codforpos','cp1forpos','cp2forpos', 'pay_payment_instore'],
            items: ko.observableArray(DataManager.getData('payment')),
            selectedAmount: ko.observable(),
            selectedPayments: CheckoutModel.selectedPayments,
            ccMethods: CheckoutModel.ccMethods,
            remainTotal: CheckoutModel.remainTotal,
            cartTotal: Totals.grandTotal,
            initialize: function(){
                var self = this;
                this.selectedPayment = '#payment_selected';
                this.creditCardPayment = '#payment_creditcard';
                this.paymentList = '#payment_list';
                self.showCcForm = ko.pureComputed(function(){
                    return (self.ccMethods().length > 0)?true:false;
                });
                self.isStripeIntegration = ko.pureComputed(function () {
                    return (CheckoutModel.paymentCode() === 'stripe_integration');
                });
                self.hasSelectedPayment = ko.pureComputed(function(){
                    return (self.selectedPayments().length > 0)?true:false;
                });
                Event.observer('load_payment_online_after', function(event, data){
                    if(data && data.items){
                        var posPayments = DataManager.getData('payment');
                        var items = data.items;
                        // var items = posPayments.concat(data.items);
                        self.items(items);
                    }
                });
                Totals.grandTotal.subscribe(function(value){
                    if(self.hasSelectedPayment()){
                        self.selectedPayments([]);
                        self.selectedAmount(undefined);
                        self.remainTotal(value);
                    }
                    if(CartModel.isOnCheckoutPage()){
                        self.saveDefaultPaymentMethod();
                    }
                    if(value <= 0){
                        CheckoutModel.paymentCode(self.getPaymentCodeZeroTotal());
                        self.renewPayments();
                    }
                });
            },
            setPayments: function (payments) {
                this.selectedPayments(payments);
            },
            getPayments: function () {
                var self = this;
                var selectedPayments = self.selectedPayments();
                selectedPayments = $.grep(selectedPayments, function(data) {
                    return data.type == self.DATA.VISIBILITY_PAYMENT_TYPE;
                });
                return selectedPayments;
            },
            addPayment: function(data){
                var self = this;
                if(data.is_pay_later == self.DATA.VALID){
                    self.addPaylaterPayment(data);
                }else{
                    self.addPaidPayment(data);
                }
            },
            addTakePayment: function(data){
                var self = this;
                self.addPaidTakePayment(data);
            },
            getPaymentIconClass: function(code){
                var self = this;
                return ($.inArray(code, self.WEBPOS_PAYMENTS) >= 0)?"icon-iconPOS-payment-"+code:"icon-iconPOS-payment-cp1forpos";
            },
            setPaymentMethod: function(data){
                var self = this;
                let paymentMethod = data.code;
                if(data && data.code){
                    if (
                            CheckoutModel.selectedPayments().length > 1 &&
                            ($.inArray(data.code, self.MULTIABLE_PAYMENTS) >= 0)
                        ) {
                        paymentMethod = self.MULTIPLE_PAYMENT_CODE;
                    }

                    if(Helper.isOnlineCheckout() && (paymentMethod != CheckoutModel.paymentCode())){
                        CheckoutModel.savePaymentMethodOnline(paymentMethod);
                    }else{
                        CheckoutModel.paymentCode(paymentMethod);
                    }
                }
            },
            addPaylaterPayment: function(data){
                var self = this;
                self.setPaymentMethod(data);
                if(data.is_pay_later == self.DATA.VALID){
                    self.selectedPayments.push({
                        id:self.selectedPayments().length,
                        code:data.code,
                        iconClass:self.getPaymentIconClass(data.code),
                        title:data.title,
                        price:data.price,
                        type:data.type,
                        multiable:data.multiable,
                        is_pay_later:data.is_pay_later,
                        is_reference_number:data.is_reference_number,
                        cart_total:0,
                        paid_amount:0,
                        cart_total_formated: PriceHelper.convertAndFormat(0),
                        is_extension_method: false,
                        template: data.template,
                        form_data: data.form_data
                    });
                }
            },
            addPaidTakePayment: function(data){
                var self = this;
                    var paymentData = {
                        id:self.selectedPayments().length,
                        code:data.code,
                        iconClass:self.getPaymentIconClass(data.code),
                        title:data.title,
                        price:data.price,
                        type:data.type,
                        multiable:data.multiable,
                        is_pay_later:data.is_pay_later,
                        is_reference_number:data.is_reference_number,
                        cart_total:PriceHelper.toNumber(self.remainTotal()),
                        paid_amount:self.remainTotal(),
                        cart_total_formated: PriceHelper.convertAndFormat(PriceHelper.toNumber(self.remainTotal())),
                        is_extension_method: false,
                        template: data.template,
                        form_data: data.form_data
                    }
                    if(data.is_extension){
                        paymentData.paid_amount = data.paid_amount;
                        paymentData.cart_total = data.cart_total;
                    }
                    self.selectedPayments.push(paymentData);
                    if(data.type == self.DATA.INVALID){
                        self.setPaymentMethod(data);
                    }else{
                        self.ccMethods.push(paymentData);
                        CheckoutModel.paymentCode(data.code);
                    }
                    self.remainTotal(0);
                    self.reCalculateTotal(-1);
            },
            addPaidPayment: function(data){
                var self = this;
                if(data.is_pay_later == self.DATA.INVALID){
                    var paymentData = {
                        id:self.selectedPayments().length,
                        code:data.code,
                        iconClass:self.getPaymentIconClass(data.code),
                        title:data.title,
                        price:data.price,
                        type:data.type,
                        multiable:data.multiable,
                        is_pay_later:data.is_pay_later,
                        is_reference_number:data.is_reference_number,
                        cart_total:PriceHelper.toNumber(self.remainTotal()),
                        paid_amount:self.remainTotal(),
                        cart_total_formated: PriceHelper.convertAndFormat(PriceHelper.toNumber(self.remainTotal())),
                        is_extension_method: false,
                        template: data.template,
                        form_data: data.form_data
                    }
                    if(data.is_extension){
                        paymentData.paid_amount = data.paid_amount;
                        paymentData.cart_total = data.cart_total;
                    }
                    self.selectedPayments.push(paymentData);
                    if(data.type == self.DATA.INVALID){
                        self.setPaymentMethod(data);
                    }else{
                        self.ccMethods.push(paymentData);
                        CheckoutModel.paymentCode(data.code);
                    }
                    self.remainTotal(0);
                    self.reCalculateTotal(-1);
                }
            },
            getExtensionPayment: function(code){
                var self = this;
                if(code){
                    var item = ko.utils.arrayFirst(self.selectedPayments(), function (item) {
                        return item.code == code;
                    });
                    return (item) ? item : false;
                }
                return false;
            },
            addExtensionPayment: function(data){
                if(data && data.code){
                    var self = this;
                    var item = self.getExtensionPayment(data.code);
                    if(item !== false) {
                        self.updatePaymentPrice(data.code, data.price, item);
                    }else{
                        CheckoutModel.paymentCode(self.MULTIPLE_PAYMENT_CODE);
                        self.removePayLaterPayment();
                        var paymentData = {
                            id:self.selectedPayments().length,
                            code:data.code,
                            iconClass:self.getPaymentIconClass(data.code),
                            title:data.title,
                            price:data.price,
                            type:data.type,
                            multiable:data.multiable,
                            is_pay_later:data.is_pay_later,
                            is_reference_number:data.is_reference_number,
                            is_extension_method:data.is_extension_method,
                            cart_total:data.cart_total,
                            paid_amount:data.paid_amount,
                            cart_total_formated: ko.observable(PriceHelper.convertAndFormat(data.cart_total)),
                            actions: data.actions,
                            template: data.template,
                            form_data: data.form_data
                        }
                        self.selectedPayments.push(paymentData);
                        self.reCalculateTotal(paymentData.id);
                    }
                }
            },
            updatePaymentPrice: function(code, amount, item){
                if(code){
                    var self = this;
                    item = (item)?item:self.getExtensionPayment(code);
                    if(item !== false) {
                        self.selectedPayments()[item.id].cart_total = amount;
                        self.selectedPayments()[item.id].paid_amount = amount;
                        self.selectedPayments()[item.id].cart_total_formated(PriceHelper.convertAndFormat(amount));
                        self.reCalculateTotal(item.id);
                    }
                }
            },
            editPaymentPrice: function (seletctedId, value) {
                var self = this;
                var paymentPrice = PriceHelper.toBasePrice(PriceHelper.toNumber(value));
                self.selectedPayments()[seletctedId].cart_total = paymentPrice;
                self.selectedPayments()[seletctedId].paid_amount = paymentPrice;
                self.reCalculateTotal(seletctedId);
            },
            reCalculateTotal: function (seletctedId) {
                var self = this;
                var currenTotal = 0;
                ko.utils.arrayForEach(self.selectedPayments(), function(item) {
                    currenTotal += PriceHelper.toNumber(item.cart_total);
                });
                self.selectedAmount(currenTotal);
                self.remainTotal(this.cartTotal() - currenTotal);
                if(seletctedId >= 0 && self.remainTotal() < 0) {
                    this.setTotalWithoutChange(seletctedId, self.remainTotal());
                }
            },
            setTotalWithoutChange: function (seletctedId, remailTotal) {
                var self = this;
                var cartTotal = self.selectedPayments()[seletctedId].cart_total;
                self.selectedPayments()[seletctedId].paid_amount = cartTotal + remailTotal;
            },
            removeSelectedPayment: function (data) {
                var self = this;
                self.selectedPayments.remove(data);
                self.reCalculateTotal(-1);

                if (data.code === 'stripe_integration') {
                    self.removeCCmethod(data);
                }
                if(data.is_extension_method == true && data.actions && data.actions.remove){
                    data.actions.remove();
                }
                self.showActivePayments();
            },
            removePayLaterPayment: function (data, event) {
                var self = this;
                ko.utils.arrayForEach(self.selectedPayments(), function(item) {
                    if(item != undefined) {
                        if (item.is_pay_later == '1') {
                            self.selectedPayments.remove(item);
                        }
                    }
                });
            },
            editReferenceNumber: function (seletctedId, value) {
                var self = this;
                self.selectedPayments()[seletctedId].reference_number = value;
            },
            renewPayments: function () {
                var self = this;
                self.ccMethods([]);
                self.selectedPayments([]);
                self.selectedAmount(undefined);
                self.remainTotal(this.cartTotal());
                if(!Helper.isOnlineCheckout()){
                    var posPayments = DataManager.getData('payment');
                    self.items(posPayments);
                }
            },
            getDefaultPaymentMethod: function () {
                var self = this;
                var paymentList = self.items();
                if(paymentList.length > 0){
                    for(var i = 0; i < paymentList.length; i++){
                        if(paymentList[i].is_default == '1'){
                            return paymentList[i];
                        }
                    }
                }
                return false;
            },
            saveDefaultPaymentMethod: function () {
                var self = this;
                self.renewPayments();
                if(self.getDefaultPaymentMethod()){
                    self.addPayment(self.getDefaultPaymentMethod());
                }
            },
            showActivePayments: function () {
                if(CheckoutModel.selectedPayments().length === 0){
                    if($(this.creditCardPayment) !== undefined){
                        $(this.creditCardPayment).hide();
                    }
                    if($(this.paymentList) !== undefined){
                        $(this.paymentList).show();
                    }
                    $(this.addPaymentButton).prop('disabled', false);
                } else {
                    if($(this.paymentList) !== undefined){
                        $(this.paymentList).hide();
                    }
                }
            },
            removeCCmethod: function(method){
                var self = this;
                self.ccMethods.remove(method);
                if(method && method.code && self.selectedPayments().length > 0){
                    ko.utils.arrayForEach(self.selectedPayments(), function(item) {
                        if(item.code == method.code) {
                            CheckoutModel.selectedPayments.remove(item);
                        }
                    });
                }
                CreditCard.resetData();
                self.reCalculateTotal(-1);
            },
            getWebposPayments: function(){
                var self = this;
                var payments = [];
                ko.utils.arrayForEach(self.items(), function(item) {
                    if($.inArray(item.code, self.WEBPOS_PAYMENTS) >= 0) {
                        payments.push(item);
                    }
                });
                return payments;
            },
            getWebposPaidPayments: function(){
                var self = this;
                var payments = [];
                ko.utils.arrayForEach(self.items(), function(item) {
                    if($.inArray(item.code, self.WEBPOS_PAYMENTS) >= 0 && !item.is_pay_later) {
                        payments.push(item);
                    }
                });
                return payments;
            },
            getPaymentCodeZeroTotal: function () {
                var self = this;
                var paymentList = self.items();
                if(paymentList.length > 0){
                    for(var i = 0; i < paymentList.length; i++){
                        if(paymentList[i].code == self.FREE_PAYMENT_CODE){
                            return self.FREE_PAYMENT_CODE;
                        }
                    }
                }
                return self.MULTIPLE_PAYMENT_CODE;
            }
        };
        PaymentModel.initialize();
        return PaymentModel;
    }
);