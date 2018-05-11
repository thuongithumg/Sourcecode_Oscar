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
        'helper/general',
        'model/checkout/checkout/payment',
        'eventManager',
        'model/checkout/checkout/payment/form/paynl_instore'
    ],
    function ($, ko, Component, CheckoutModel, PriceHelper, Helper, PaymentModel, Event, PaynlInstoreModel) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'ui/checkout/checkout/payment_selected'
            },
            selectedPayments: PaymentModel.selectedPayments,
            selectedAmount: PaymentModel.selectedAmount,
            cartTotal: PaymentModel.cartTotal,
            initialize: function () {
                this._super();
                var self = this;
                self.showSelectedPayment = ko.pureComputed(function(){
                    var selectedPayment = self.selectedPayments();
                    var show = true;
                    $.each(selectedPayment, function (index, value) {
                        if (value.code !== 'stripe_integration' && $.inArray(value.code, PaymentModel.MULTIABLE_PAYMENTS) < 0) {
                            show = false;
                        }
                    });
                    if (show) {
                        return true;
                    } else {
                        return (PaymentModel.hasSelectedPayment() && !PaymentModel.showCcForm())?true:false;
                    }
                });
                if(self.selectedAmount() == undefined){
                    CheckoutModel.remainTotal(self.cartTotal());
                }
                Event.observer('start_new_order', function(){
                    self.initPayments();
                });

            },
            initPayments: function () {
                this.selectedPayments([]);
                CheckoutModel.remainTotal(0);
                this.selectedAmount(undefined);
            },
            getPayments: function () {
                return this.selectedPayments();
            },
            editPaymentPrice: function (data, event) {
                var self = this;
                var seletctedId = data.id;
                var value = event.target.value;
                value = (PriceHelper.toNumber(value) > 0)?value:0;
                event.target.value = PriceHelper.formatPrice(PriceHelper.toNumber(value));
                PaymentModel.editPaymentPrice(seletctedId, value);
            },
            removeSelectedPayment: function (data, event) {
                PaymentModel.removeSelectedPayment(data);
            },
            isShowReferenceNumber: function (check) {
                if (check == PaymentModel.DATA.VALID) {
                    return true;
                }
                return false;
            },
            editReferenceNumber: function (data, event) {
                var self = this;
                var seletctedId = data.id;
                var value = event.target.value;
                PaymentModel.editReferenceNumber(seletctedId, value);
            },
            getRefenceNumberText: function () {
                return Helper.__('Reference Number');
            },
            getRefenrenceNumberId: function (code) {
                return code+'_reference_number';
            },
            renewPayments: function () {
                var self = this;
                PaymentModel.renewPayments();
                Helper.dispatchEvent('payments_reset_after', '');
            },
            checkVisibleInputBox: function (check) {
                if(check == PaymentModel.DATA.VALID){
                    return false;
                }
                return true;
            },
            editAble: function(data){
                return data.multiable && !data.is_extension_method;
            },
            getFormId: function (code) {
                return 'payment_form_'+code;
            },
            getPaymentForm: function (code, form_data) {
                var FormModel = '';
                switch(code){
                    case PaynlInstoreModel.CODE:
                        FormModel = PaynlInstoreModel.initData(form_data);
                        break;
                }
                return FormModel;
            }
        });
    }
);