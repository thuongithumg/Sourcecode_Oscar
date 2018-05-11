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
        'mage/translate',
        'ui/components/order/action',
        'eventManager',
        'action/sales/order/payment/create',
        'helper/alert',
        'helper/price',
        'ui/lib/modal/confirm',
        'ui/components/checkout/checkout/renderer/payment-factory',
        'model/checkout/checkout/payment',
        'helper/general',
        'model/checkout/checkout',
        'model/checkout/cart/totals',
        'model/checkout/checkout/payment/form/paynl_instore',
        'model/checkout/checkout/payment/nlpay-instore',
    ],
    function ($, ko, ViewManager,
              $t,
              colGrid, eventmanager, createPaymentAction, Alert, PriceHelper,
              Confirm,
              RenderPaymentFactory,
              PaymentModel,
              Helper,
              CheckoutModel,
              Totals,
              PaynlInstoreModel,
              PaynlInstore
    ) {
        "use strict";

        return colGrid.extend({
            items: ko.observableArray([]),
            columns: ko.observableArray([]),
            isVisible: ko.observable(false),
            classIn: ko.observable(''),
            stypeDisplay: ko.observable('none'),
            formId: 'payment_popup_form',
            addMorePaymentBtn: 'add_more_payment_btn',
            morePaymentFormId: 'order_more_payment_container',
            isShowMorePayment: ko.observable(false),
            // orderData: ko.observable(null),
            parentView: ko.observable(''),
            filterAttribute: 'code',
            // selectedPayments: ko.observableArray([]),
            currentTotal: ko.observable(),
            baseRemainTotal: ko.observable(),
            isAllPaymentSelected: ko.observable(true),
            baseTotalDue: 0,
            selectedPayments: PaymentModel.selectedPayments,
            selectedAmount: PaymentModel.selectedAmount,
            ccMethods: CheckoutModel.ccMethods,
            remainTotal: CheckoutModel.remainTotal,
            cartTotal: Totals.grandTotal,
            visible: ko.observable(true),

            defaults: {
                template: 'ui/order/view/payment',
                templatePaymentSelected: 'ui/order/view/payment/payment_selected',
                templatePaymentList: 'ui/order/view/payment/payment_list',
                templatePaymentListPopup: 'ui/order/view/payment/payment_list_popup'
            },
            initialize: function () {
                var self = this;
                this._super();
                // this._render();
                // this._prepareItems();
                this.initObserver();
                self.items = PaymentModel.items;
                self.showSelectedPayment = ko.pureComputed(function(){
                    return (PaymentModel.hasSelectedPayment() && !PaymentModel.showCcForm())?true:false;
                });
                ko.pureComputed(function () {
                    return self.orderData();
                }).subscribe(function () {
                    if (self.orderData()) {
                        self.selectedPayments([]);
                        self.isShowMorePayment(false);
                        self.isAllPaymentSelected(false);
                        self.baseTotalDue = self.orderData().base_total_due ? self.orderData().base_total_due : 0;
                        self.baseRemainTotal(self.baseTotalDue);
                        self.remainTotal(self.baseRemainTotal());
                        // self._prepareCollection();
                        // self.showActivePayments();
                    }
                });
            },
            initObserver: function(){
                var self = this;
                Helper.observerEvent('go_to_checkout_page', function(){
                    PaymentModel.saveDefaultPaymentMethod();
                });
                Helper.observerEvent('reset_payments_data', function(){
                    PaymentModel.renewPayments();
                });
                Helper.observerEvent('save_default_payment', function(){
                    PaymentModel.saveDefaultPaymentMethod();
                });
                PaymentModel.hasSelectedPayment.subscribe(function(selected){
                    self.visible((selected == true)?false:true);
                });
                PaymentModel.showCcForm.subscribe(function(showCCform){
                    self.visible((showCCform == true)?false:true);
                });
            },
            remainTotalOrder: ko.pureComputed(function(){
                var self = this;
                var remainMoney = CheckoutModel.remainTotal();
                if (isNaN(remainMoney))
                    remainMoney = PriceHelper.toNumber(remainMoney);
                if (remainMoney < 0) remainMoney = 0;
                return Helper.convertAndFormatPrice((PriceHelper.toNumber(remainMoney)) ? Math.abs(PriceHelper.toNumber(remainMoney)) : 0);
            }),
            
            display: function (isShow) {
                if (isShow) {
                    $('#order_more_payment_container').hide();
                    this.isVisible(true);
                    this.stypeDisplay('block');
                    this.classIn('in');
                    $('.notification-bell').hide();
                    $('#c-button--push-left').hide();

                } else {
                    this.isVisible(false);
                    this.stypeDisplay('none');
                    this.classIn('');
                    $('.notification-bell').show();
                    $('#c-button--push-left').show();
                }
            },

            closeListPayment: function (isShow) {
                $('#order_more_payment_container').hide();
            },
            
            resetViewData: function (data) {
                this.orderData(null);
                this.parentView().updateOrderListData(data);
            },

            _prepareCollection: function () {
                this.filterAttribute = 'code';
                this.collection = PaymentModel.getWebposPayments();

            },

            _prepareColumns: function () {
                this.addColumn({
                    headerText: $t("Title"),
                    rowText: "title",
                    renderer: RenderPaymentFactory.get()
                });
            },

            setPaymentMethod: function (data) {
                var self = this;
                PaymentModel.addTakePayment(data);
                $('#add_more_payment_btn').prop('disabled', true);
                $('#order_more_payment_container').hide();
                // Helper.dispatchEvent('hide_payment_popup', '');
            },

            addPayment: function(data){
                var self = this;
                if(data.is_pay_later == 1){
                    PaymentModel.addPaylaterPayment(data);
                }else{
                    PaymentModel.addPaidPayment(data);
                }
            },

            addExtensionMethod: function (data) {
                PaymentModel.addExtensionPayment(data);
                // if($('#payment_selected') !== undefined){
                //     $('#payment_selected').show();
                // }
                // if($('#payment_creditcard') !== undefined){
                //     $('#payment_creditcard').hide();
                // }
                // if($('#payment_list') !== undefined){
                //     $('#payment_list').hide();
                // }
                // window.webposLayout.getSingleton('view/checkout/checkout/payment_popup')._prepareItems();
            },

            checkPaymentCollection: function () {
                if(this.items().length > 0){
                    return false;
                }
                return true;
            },

            isShowReferenceNumber: function (check) {
                if(check == PaymentModel.DATA.VALID){
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
            editPaymentPrice: function (data, event) {
                $.each(PaymentModel.selectedPayments(),  function (index, item) {
                    $('#order_more_payment_container .' + item.code).hide();
                });
                var price = Math.abs(PriceHelper.toNumber(event.target.value));
                var remainTotal = this.baseRemainTotal();
                var selectedId = data.id;
                var paymentPrice = PriceHelper.currencyConvert(
                    price,
                    window.webposConfig.currentCurrencyCode,
                    this.orderData().base_currency_code
                );
                event.target.value = PriceHelper.formatPrice(price);
                this.selectedPayments()[selectedId].cart_total = paymentPrice;
                this.selectedPayments()[selectedId].paid_amount = paymentPrice;
                this.reCalculateTotal(selectedId);
            },

            removeSelectedPayment: function (data, event) {
                var self = this;
                this.selectedPayments.remove(data);
                this.isAllPaymentSelected(false);
                $('#order_more_payment_container .' + data.code).show();
                self.reCalculateTotal(-1);
                this.showActivePayments();
                this.isShowMorePayment(false);
            },

            reCalculateTotal: function (selectedId) {
                var currentTotal = 0;
                $.each(this.selectedPayments(), function (index, item) {
                    currentTotal += PriceHelper.toNumber(item.cart_total);
                });
                this.currentTotal(currentTotal);
                this.baseRemainTotal(this.baseTotalDue - currentTotal);
                this.remainTotal(PriceHelper.formatPrice(
                    Math.abs(this.baseRemainTotal()),
                    this.orderData().base_currency_code,
                    window.webposConfig.currentCurrencyCode
                ));
                if (selectedId && this.baseRemainTotal() < 0) {
                    this.selectedPayments()[selectedId].paid_amount += PriceHelper.currencyConvert(
                        this.baseRemainTotal(),
                        this.orderData().base_currency_code,
                        window.webposConfig.currentCurrencyCode
                    );
                }
                ;
                this.checkAddPayment(this.baseRemainTotal());
            },

            checkAddPayment: function (baseRemainTotal) {
                if (baseRemainTotal > 0) {
                    $('#' + this.addMorePaymentBtn).prop('disabled', false);
                } else {
                    $('#' + this.addMorePaymentBtn).prop('disabled', true);
                }
            },

            showActivePayments: function () {
                if (this.selectedPayments().length > 0)
                    $('#' + this.addMorePaymentBtn).prop('disabled', false);
                else
                    $('#' + this.addMorePaymentBtn).prop('disabled', true);
            },

            addMorePayment: function () {
                $('#order_more_payment_container').show();

            },
            
            setPaymentMethodPopup: function (data, event) {
                this.setPaymentMethod(data, event);
                this.addMorePayment();
            },

            formatPrice: function (price) {
                PriceHelper.formatPrice(
                    price,
                    this.orderData().base_currency_code,
                    window.webposConfig.currentCurrencyCode
                );
            },

            getFormId: function (code) {
                return 'payment_form_' + code;
            },

            getPaymentForm: function (code, form_data) {
                var FormModel = '';
                switch (code) {
                    case PaynlInstoreModel.CODE:
                        FormModel = PaynlInstoreModel.initData(form_data);
                        break;
                }
                return FormModel;
            },
            submit: function (data, event) {
                event.target.disabled = true;
                var self = this;
                Confirm({
                    content: $t('Are you sure you want to take payment on this order?'),
                    actions: {
                        confirm: function (confirmEvent) {
                            let deferred = $.Deferred();
                            confirmEvent.target.disabled = true;

                            var useNlPayInStorePayment = !!self.selectedPayments().find((payment) => {
                                return payment.code === PaynlInstoreModel.CODE;
                            });

                            if (useNlPayInStorePayment) {
                                PaynlInstore.openPopup();
                            }

                            createPaymentAction.execute(self.selectedPayments(), self.orderData(), deferred, self);

                            deferred
                                .done(
                                    function (response) {
                                        if (useNlPayInStorePayment) {
                                            PaynlInstore.saveOrderSuccess(response, function (orderData) {
                                                Alert({priority: 'success', title: $t('Success'), message: $t('Create payment successfully!')});
                                                eventmanager.dispatch('sales_order_afterSave', {'response': orderData});
                                                eventmanager.dispatch('orders_history_show_container_after', '');
                                            });
                                            return;
                                        }
                                        Alert({priority: 'success', title: $t('Success'), message: $t('Create payment successfully!')})
                                    }
                                )
                                .fail(
                                    reason => Alert({priority: 'warning', title: $t('Error'), message: reason})
                                );
                        },
                        always: function (confirmEvent) {
                            event.target.disabled = false;
                            confirmEvent.stopImmediatePropagation();
                        }
                    }
                });

            }
        });
    }
);