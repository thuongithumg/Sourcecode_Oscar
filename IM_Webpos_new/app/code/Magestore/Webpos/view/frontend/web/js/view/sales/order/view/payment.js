/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'require',
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/checkout/payment-factory',
        'Magestore_Webpos/js/view/layout',
        'mage/translate',
        'Magestore_Webpos/js/view/base/list/collection-list',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/action/sales/order/payment/create',
        'Magestore_Webpos/js/helper/alert',
        'Magestore_Webpos/js/helper/price',
        'Magento_Ui/js/modal/confirm',
        'Magestore_Webpos/js/view/checkout/checkout/renderer/payment-factory',
    ],
    function (require, $, ko, PaymentFactory, ViewManager, $t, colGrid, eventmanager, createPaymentAction, Alert, PriceHelper, Confirm, RenderPaymentFactory) {
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
            orderData: ko.observable(null),
            parentView: ko.observable(''),
            filterAttribute: 'code',
            selectedPayments: ko.observableArray([]),
            currentTotal: ko.observable(),
            baseRemainTotal: ko.observable(),
            remainTotalValue: ko.observable(),
            remainTotal: ko.observable(''),
            isAllPaymentSelected: ko.observable(true),
            baseTotalDue: 0,
            totalDue: 0,

            defaults: {
                template: 'Magestore_Webpos/sales/order/view/payment',
                templatePaymentSelected: 'Magestore_Webpos/sales/order/view/payment/payment_selected',
                templatePaymentList: 'Magestore_Webpos/sales/order/view/payment/payment_list',
                templatePaymentListPopup: 'Magestore_Webpos/sales/order/view/payment/payment_list_popup'
            },
            initialize: function () {
                var self = this;
                this._super();
                this._render();
                this._prepareItems();
                ko.pureComputed(function () {
                    return self.orderData();
                }).subscribe(function () {
                    if (self.orderData()) {
                        self.selectedPayments([]);
                        self.isShowMorePayment(false);
                        self.isAllPaymentSelected(false);
                        self.totalDue = self.orderData().total_due ? self.orderData().total_due : 0
                        self.baseTotalDue = self.orderData().base_total_due ? self.orderData().base_total_due : 0
                        self.baseRemainTotal(self.baseTotalDue);
                        if (window.webposConfig.currentCurrencyCode == self.orderData().order_currency_code) {
                            self.remainTotalValue(self.totalDue);
                        } else {
                            if (window.webposConfig.currentCurrencyCode == self.orderData().base_currency_code) {
                                self.remainTotalValue(self.baseTotalDue);
                                self.totalDue = self.baseTotalDue;
                            } else {
                                var remainValue = PriceHelper.currencyConvert(self.baseTotalDue, self.orderData().base_currency_code, window.webposConfig.currentCurrencyCode);
                                self.remainTotalValue(remainValue);

                                var totalDue = PriceHelper.currencyConvert(self.baseTotalDue, self.orderData().base_currency_code, window.webposConfig.currentCurrencyCode);
                                self.totalDue = totalDue;
                            }
                        }
                        self.remainTotal(PriceHelper.formatPrice(self.remainTotalValue()));
                        self._prepareCollection();
                        self.showActivePayments();
                    }
                    ;
                });
            },

            display: function (isShow) {
                if (isShow) {
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

            resetViewData: function (data) {
                this.orderData(null);
                this.parentView().updateOrderListData(data);
            },

            _prepareCollection: function () {
                this.filterAttribute = 'code';
                this.collection = PaymentFactory.get().setMode('offline').getCollection()
                    .addFieldToFilter('type', 0, 'eq')
                    .addFieldToFilter('code', PaymentFactory.noneTakePaymentList(), 'nin')
                ;

            },

            _prepareColumns: function () {
                this.addColumn({
                    headerText: "Title",
                    rowText: "title",
                    renderer: RenderPaymentFactory.get()
                });
            },

            setPaymentMethod: function (data, event) {
                this.addPayment(data);
            },

            addPayment: function (data) {
                var self = this;
                this.isAllPaymentSelected(true);
                $.each(self.items(), function (index, item) {
                    if (item.code == data.code) {
                        item.hide = true;
                    }
                    if (typeof item.hide == 'undefined' || !item.hide) {
                        self.isAllPaymentSelected(false);
                    }
                });
                var paymentData = {
                    id: this.selectedPayments().length,
                    code: data.code,
                    iconClass: "icon-iconPOS-payment-" + data.code,
                    title: data.title,
                    price: data.price,
                    type: data.type,
                    is_pay_later: data.is_pay_later,
                    is_reference_number: data.is_reference_number,
                    cart_total: this.remainTotalValue(),
                    base_cart_total: this.baseRemainTotal(),
                    paid_amount: this.remainTotalValue(),
                    base_paid_amount: this.baseRemainTotal(),
                    cart_total_formated: PriceHelper.formatPrice(this.remainTotalValue()),
                };
                var eventData = {
                    params: data,
                    paymentData: paymentData
                };
                eventmanager.dispatch('add_webpos_payment_before', eventData);
                this.selectedPayments.push(eventData.paymentData);
                this.reCalculateTotal();
            },

            editReferenceNumber: function (data, event) {
                var seletctedId = data.id;
                this.selectedPayments()[seletctedId].reference_number = event.target.value;
            },

            editPaymentPrice: function (data, event) {
                var price = Math.abs(PriceHelper.toNumber(event.target.value));
                var selectedId = data.id;
                event.target.value = PriceHelper.formatPrice(price);
                this.selectedPayments()[selectedId].base_cart_total = PriceHelper.toBasePrice(price);
                this.selectedPayments()[selectedId].cart_total = price;
                this.selectedPayments()[selectedId].base_paid_amount = PriceHelper.toBasePrice(price);
                this.selectedPayments()[selectedId].paid_amount = price;
                this.reCalculateTotal(selectedId);
            },

            removeSelectedPayment: function (data, event) {
                var self = this;
                this.selectedPayments.remove(data);
                this.isAllPaymentSelected(false);
                $.each(self.items(), function (index, item) {
                    if (item.code == data.code) {
                        item.hide = false;
                    }
                });
                this.reCalculateTotal();
                this.showActivePayments();
                this.isShowMorePayment(false);
            },

            reCalculateTotal: function (selectedId) {
                var currentTotal = 0;
                var currentBaseTotal = 0;
                $.each(this.selectedPayments(), function (index, item) {
                    currentTotal += PriceHelper.toNumber(item.cart_total);
                    currentBaseTotal += PriceHelper.toNumber(item.base_cart_total);
                });
                this.currentTotal(currentTotal);
                this.remainTotalValue(this.totalDue - currentTotal);
                this.baseRemainTotal(this.baseTotalDue - currentBaseTotal);
                if (this.remainTotalValue() < 0) {
                    this.remainTotal(PriceHelper.formatPrice(-this.remainTotalValue()));
                } else {
                    this.remainTotal(PriceHelper.formatPrice(this.remainTotalValue()));
                }
                if (selectedId && this.remainTotalValue() < 0) {
                    this.selectedPayments()[selectedId].paid_amount += this.remainTotalValue();
                    this.selectedPayments()[selectedId].base_paid_amount += this.baseRemainTotal();
                }
                ;
                this.checkAddPayment(this.remainTotalValue());
            },

            checkAddPayment: function (remainTotal) {
                if (remainTotal > 0) {
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
                if (this.isShowMorePayment())
                    this.isShowMorePayment(false);
                else
                    this.isShowMorePayment(true);
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

            submit: function (data, event) {
                event.target.disabled = true;
                var self = this;
                Confirm({
                    content: $t('Are you sure you want to take payment on this order?'),
                    actions: {
                        confirm: function (confirmEvent) {
                            confirmEvent.target.disabled = true;
                            createPaymentAction.execute(self.selectedPayments(), self.orderData(), $.Deferred(), self);
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