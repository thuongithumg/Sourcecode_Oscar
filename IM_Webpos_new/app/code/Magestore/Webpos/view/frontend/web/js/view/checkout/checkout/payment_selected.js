/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'mage/translate',
        'uiComponent',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
        'Magestore_WebposPaynl/js/model/paynl'
    ],
    function ($, ko, $t, Component, CheckoutModel, PriceHelper, Helper, TotalsFactory, PaynlInstoreModel) {
        "use strict";
        return Component.extend({
            DATA:{
                VISIBILITY_PAYMENT_TYPE: '1',
                VALID: '1',
                INVALID: '0'
            },
            FREE_PAYMENT_CODE:'free',
            MULTIPLE_PAYMENT_CODE:'multipaymentforpos',
            WEBPOS_PAYMENTS:['cashforpos','ccforpos','codforpos','cp1forpos','cp2forpos', 'paypal_integration', 'payflowpro_integration'],
            currentTotal: ko.observable(),
            cartTotal: ko.pureComputed(function(){
                return TotalsFactory.get().getTotalValue('grand_total');
            }),
            baseCartTotal: ko.pureComputed(function(){
                return TotalsFactory.get().getBaseTotalValue('grand_total');
            }),
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/payment_selected'
            },
            initialize: function () {
                this._super();
                this.selectedPayment = '#payment_selected';
                this.creditCardPayment = '#payment_creditcard';
                this.paymentList = '#payment_list';
                this.addPaymentButton = '#add_payment_button';
                this.rightPopup = '.popup-for-right';
                this.wrapBackover = '.wrap-backover';
                if(this.currentTotal() == undefined){
                    CheckoutModel.remainTotal(this.cartTotal());
                    CheckoutModel.baseRemainTotal(this.baseCartTotal());
                }
            },
            initPayments: function () {
                CheckoutModel.selectedPayments([]);
                CheckoutModel.remainTotal(0);
                CheckoutModel.baseRemainTotal(0);
                this.currentTotal(undefined);
                if($(this.creditCardPayment) !== undefined){
                    $(this.creditCardPayment).hide();
                }
                if($(this.selectedPayment) !== undefined){
                    $(this.selectedPayment).hide();
                }
                if($(this.paymentList) !== undefined){
                    $(this.paymentList).show();
                }
            },
            setPayments: function (payments) {
                CheckoutModel.selectedPayments(payments);
            },
            getPayments: function () {
                return CheckoutModel.selectedPayments();
            },
            visibilityPaymentType: function () {
                return [0,2];
            },
            addPayment: function(data){
                this.setPaymentMethod(data);
                var iconClass = "icon-iconPOS-payment-"+data.code;
                if(
                    (data.code == 'authorizenet_directpost') ||
                    (data.code == 'paynl_payment_instore') ||
                    (CheckoutModel.selectedPayments().length <= 0)
                ){
                    CheckoutModel.paymentCode(data.code);
                    iconClass = "icon-iconPOS-payment-ccforpos";
                }else{
                    CheckoutModel.paymentCode('multipaymentforpos');
                }
                // }
                if(data.is_pay_later == '1'){
                    CheckoutModel.selectedPayments.push({
                        id:CheckoutModel.selectedPayments().length,
                        code:data.code,
                        iconClass:iconClass,
                        title:data.title,
                        price:data.price,
                        type:data.type,
                        is_pay_later:data.is_pay_later,
                        is_reference_number:data.is_reference_number,
                        card_type: '',
                        cart_total:0,
                        base_cart_total:0,
                        paid_amount:0,
                        base_paid_amount:0,
                        cart_total_formated: PriceHelper.convertAndFormat(0),
                        is_extension_method: false,
                        template: data.template,
                        form_data: data.form_data
                    });
                    // this.disableAddPaymentButton();
                }else{
                    this.removePayLaterPayment();
                    var paymentData = {
                        id:CheckoutModel.selectedPayments().length,
                        code:data.code,
                        iconClass:iconClass,
                        title:data.title,
                        price:data.price,
                        type:data.type,
                        is_pay_later:data.is_pay_later,
                        is_reference_number:data.is_reference_number,
                        card_type: '',
                        cart_total:CheckoutModel.remainTotal(),
                        base_cart_total:CheckoutModel.baseRemainTotal(),
                        paid_amount:CheckoutModel.remainTotal(),
                        base_paid_amount:CheckoutModel.baseRemainTotal(),
                        cart_total_formated: PriceHelper.formatPrice(CheckoutModel.remainTotal()),
                        is_extension_method: false,
                        template: data.template,
                        form_data: data.form_data
                    }
                    if(data.is_extension){
                        paymentData.paid_amount = data.paid_amount;
                        paymentData.cart_total = data.cart_total;
                    }
                    var eventData = {
                        params: data,
                        paymentData: paymentData
                    };
                    Helper.dispatchEvent('add_webpos_payment_before', eventData);
                    CheckoutModel.selectedPayments.push(eventData.paymentData);
                    CheckoutModel.remainTotal(0);
                    CheckoutModel.baseRemainTotal(0);
                    this.reCalculateTotal(-1);
                    this.disableAddPaymentButton();
                }
                this.hidePaymentPopup();
            },
            getExtensionPayment: function(code){
                if(code){
                    var item = ko.utils.arrayFirst(CheckoutModel.selectedPayments(), function (item) {
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
                        self.updatePaymentPrice(data.code, data.price, data.base_price, item);
                    }else{
                        this.removePayLaterPayment();
                        var paymentData = {
                            id:CheckoutModel.selectedPayments().length,
                            code:data.code,
                            iconClass:"icon-iconPOS-payment-"+data.code,
                            title:data.title,
                            price:data.price,
                            base_price:data.base_price,
                            type:data.type,
                            is_pay_later:data.is_pay_later,
                            is_reference_number:data.is_reference_number,
                            is_extension_method:data.is_extension_method,
                            cart_total:data.cart_total,
                            base_cart_total:data.base_cart_total,
                            paid_amount:data.paid_amount,
                            base_paid_amount:data.base_paid_amount,
                            cart_total_formated: ko.observable(PriceHelper.formatPrice(data.cart_total)),
                            actions: data.actions,
                            template: data.template,
                            form_data: data.form_data
                        }
                        CheckoutModel.selectedPayments.push(paymentData);
                        this.reCalculateTotal(paymentData.id);
                    }
                }
            },
            updatePaymentPrice: function(code, amount, baseAmount, item){
                if(code){
                    var self = this;
                    item = (item)?item:self.getExtensionPayment(code);
                    if(item !== false) {
                        CheckoutModel.selectedPayments()[item.id].cart_total = amount;
                        CheckoutModel.selectedPayments()[item.id].base_cart_total = baseAmount;
                        CheckoutModel.selectedPayments()[item.id].paid_amount = amount;
                        CheckoutModel.selectedPayments()[item.id].base_paid_amount = baseAmount;
                        CheckoutModel.selectedPayments()[item.id].cart_total_formated(PriceHelper.formatPrice(amount));
                        self.reCalculateTotal(item.id);
                    }
                }
            },
            editPaymentPrice: function (data, event) {
                var eventData = {
                    paymentData: data,
                    newValue: PriceHelper.toNumber(event.target.value)
                };
                Helper.dispatchEvent('edit_payment_price', eventData);
                var seletctedId = data.id;
                var paymentPrice = PriceHelper.toNumber(event.target.value);
                event.target.value = PriceHelper.formatPrice(PriceHelper.toNumber(event.target.value));
                CheckoutModel.selectedPayments()[seletctedId].cart_total = paymentPrice;
                CheckoutModel.selectedPayments()[seletctedId].base_cart_total = PriceHelper.toBasePrice(paymentPrice);
                CheckoutModel.selectedPayments()[seletctedId].paid_amount = paymentPrice;
                CheckoutModel.selectedPayments()[seletctedId].base_paid_amount = PriceHelper.toBasePrice(paymentPrice);
                this.reCalculateTotal(seletctedId);
            },
            reCalculateTotal: function (seletctedId) {
                var currenTotal = 0;
                var currenBaseTotal = 0;
                ko.utils.arrayForEach(CheckoutModel.selectedPayments(), function(item, index) {
                    item.id = index;
                    currenTotal += PriceHelper.toNumber(item.cart_total);
                    currenBaseTotal += PriceHelper.toNumber(item.base_cart_total);
                });
                this.currentTotal(currenTotal);
                this.checkAddPayment(currenTotal);
                CheckoutModel.remainTotal(this.cartTotal() - currenTotal);
                CheckoutModel.baseRemainTotal(this.baseCartTotal() - currenBaseTotal);
                if(seletctedId >= 0 && CheckoutModel.remainTotal() < 0) {
                    this.setTotalWithoutChange(seletctedId, CheckoutModel.remainTotal(), CheckoutModel.baseRemainTotal());
                }
            },
            setTotalWithoutChange: function (seletctedId, remailTotal, baseRemainTotal) {
                var cartTotal = CheckoutModel.selectedPayments()[seletctedId].cart_total;
                var baseCartTotal = CheckoutModel.selectedPayments()[seletctedId].base_cart_total;
                CheckoutModel.selectedPayments()[seletctedId].paid_amount = cartTotal + remailTotal;
                CheckoutModel.selectedPayments()[seletctedId].base_paid_amount = baseCartTotal + baseRemainTotal;
            },
            checkAddPayment: function (currenTotal) {
                if(currenTotal < this.cartTotal()){
                    $(this.addPaymentButton).prop('disabled', false);
                }else{
                    $(this.addPaymentButton).prop('disabled', true);
                }
            },
            disableAddPaymentButton: function () {
                $(this.addPaymentButton).prop('disabled', true);
            },
            hidePaymentPopup:function () {
                $(this.rightPopup).hide();
                $(this.rightPopup).removeClass('fade-in');
                $(this.wrapBackover).hide()
            },
            removeSelectedPayment: function (data, event) {
                CheckoutModel.selectedPayments.remove(data);
                this.reCalculateTotal(-1);
                this.showActivePayments();
                if(data.is_extension_method == true && data.actions && data.actions.remove){
                    data.actions.remove();
                }
            },
            removePayLaterPayment: function (data, event) {
                ko.utils.arrayForEach(CheckoutModel.selectedPayments(), function(item) {
                    if(item != undefined) {
                        if (item.is_pay_later == '1') {
                            CheckoutModel.selectedPayments.remove(item);
                        }
                    }
                });
            },
            showActivePayments: function () {
                if(CheckoutModel.selectedPayments().length == 0){
                    if($(this.selectedPayment) !== undefined){
                        $(this.selectedPayment).hide();
                    }
                    if($(this.creditCardPayment) !== undefined){
                        $(this.creditCardPayment).hide();
                    }
                    if($(this.paymentList) !== undefined){
                        $(this.paymentList).show();
                    }
                    $(this.addPaymentButton).prop('disabled', false);
                }
            },
            isShowReferenceNumber: function (check) {
                if(check == '1'){
                    return true;
                }
                return false;
            },
            editReferenceNumber: function (data, event) {
                var seletctedId = data.id;
                CheckoutModel.selectedPayments()[seletctedId].reference_number = event.target.value;
            },
            getRefenceNumberText: function () {
                return $t('Reference Number');
            },
            getRefenrenceNumberId: function (code) {
                return code+'_reference_number';
            },
            renewPayments: function () {
                CheckoutModel.selectedPayments([]);
                this.currentTotal(undefined);
                if (Helper.isUseOnline('checkout')) {
                    CheckoutModel.remainTotal(TotalsFactory.get().getOnlineValue('grand_total'));
                    CheckoutModel.baseRemainTotal(Helper.toBasePrice(TotalsFactory.get().getOnlineValue('grand_total')));
                }else{
                    CheckoutModel.remainTotal(TotalsFactory.get().getTotalValue('grand_total'));
                    CheckoutModel.baseRemainTotal(TotalsFactory.get().getBaseTotalValue('grand_total'));
                }
                if($(this.paymentList) !== undefined){
                    $(this.paymentList).show();
                }
                Helper.dispatchEvent('payments_reset_after', '');
            },
            checkVisibleInputBox: function (check) {
                if(check == '1'){
                    return false;
                }
                return true;
            },
            setPaymentMethod: function(data){
                var self = this;
                if(data && data.code){
                    var paymentMethod = ($.inArray(data.code, self.WEBPOS_PAYMENTS) >= 0)?self.MULTIPLE_PAYMENT_CODE:data.code;
                    if(Helper.isUseOnline('checkout') && (paymentMethod != CheckoutModel.paymentCode())){
                        CheckoutModel.savePaymentMethodOnline(paymentMethod);
                    }else{
                        CheckoutModel.paymentCode(paymentMethod);
                    }
                }
            },
            getFormId: function (code) {
                return 'payment_form_'+code;
            },

            isShowForm: function (code) {
                var isShow = false;
                var output = this.showFormPayment(code);
                if (output) {
                    return true;
                }
            },
            showFormPayment: function (code) {
                var output = '';
                switch(code){
                    case PaynlInstoreModel.PAYMENT_METHOD_CODE:
                        output = _.map(window.webposConfig.paynlBank, function(value, key) {
                            return {
                                'value': value.id,
                                'text': value.visibleName
                            }
                        });
                        break;
                }
                return output;
            }
        });
    }
);