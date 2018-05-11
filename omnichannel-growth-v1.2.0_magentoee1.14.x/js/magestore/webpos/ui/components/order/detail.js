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
        'require',
        'jquery',
        'ko',
        'model/sales/order-factory',
        // 'view/layout',
        'underscore',
        'mage/translate',
        'view/base/abstract',
        'model/sales/order/total',
        'model/sales/order/status',
        'model/checkout/checkout',
        'eventManager',
        // 'action/cart/checkout',
        'helper/price',
        'helper/datetime',
        'action/cart/reorder',
        'helper/general',
        'model/checkout/integration/pdf-invoice-plus'
    ],
    function (
        require,
        $,
        ko,
        OrderFactory,
        // ViewManager,
        _,
        $t,
        Component,
        orderTotal,
        orderStatus,
        CheckoutModel,
        eventManager,
        // Checkout,
        priceHelper,
        datetimeHelper,
        ReOrder,
        Helper,
        PdfInvoicePlusModel
    ) {
        "use strict";

        return Component.extend({
            orderData: ko.observable(null),
            orderListView: ko.observable(''),
            isShowActionPopup: ko.observable(false),
            totalValues: ko.observableArray([]),
            statusObject: orderStatus.getStatusObjectView(),
            isCanceled: ko.observable(true),
            canInvoice: ko.observable(true),
            canCancel: ko.observable(true),
            canShip: ko.observable(true),
            canCreditmemo: ko.observable(true),
            canSync: ko.observable(true),
            canTakePayment: ko.observable(false),
            canUnhold: ko.observable(false),
            isFirstLoad: true,
            defaults: {
                template: 'ui/order/detail',
                templateTop: 'ui/order/view/top',
                templateBilling: 'ui/order/view/billing',
                templateShipping: 'ui/order/view/shipping',
                templateShippingMethod: 'ui/order/view/shipping-method',
                templatePaymentMethod: 'ui/order/view/payment-method',
                templateTotal: 'ui/order/view/total',
                templateItems: 'ui/order/view/items',
                templateComments: 'ui/order/view/comments'
            },

            initialize: function () {
                this._super();
                var self = this;
                ko.pureComputed(function () {
                    return self.orderData();
                }).subscribe(function () {
                    if (self.orderData()) {
                        var orderModel = OrderFactory.get();
                        self.isCanceled(orderModel.setData(self.orderData()).isCanceled());
                        self.canInvoice(orderModel.setData(self.orderData()).canInvoice());
                        self.canCancel(orderModel.setData(self.orderData()).canCancel());
                        self.canShip(orderModel.setData(self.orderData()).canShip());
                        self.canCreditmemo(orderModel.setData(self.orderData()).canCreditmemo());
                        self.canSync(orderModel.setData(self.orderData()).canSync());
                        self.canTakePayment(orderModel.setData(self.orderData()).canTakePayment());
                        self.canUnhold(orderModel.setData(self.orderData()).canUnhold());
                    }
                });
                self.cannotSync = ko.pureComputed(function () {
                    return (self.orderData() && self.orderData().state) ? self.orderData().state != 'notsync' : false;
                });

                self.showInvoiceButton = ko.pureComputed(function () {
                    return (self.canInvoice() && self.cannotSync());
                });
                eventManager.observer('sales_order_afterSave', function (event, data) {
                    if (data.response && data.response.entity_id > 0) {
                        var deferedSave = $.Deferred();
                        OrderFactory.get().setData(data.response).setMode('offline').save(deferedSave);
                        self.orderListView().updateOrderListData(data.response);
                    }
                });
                eventManager.observer('sales_order_start_action', function (event, data) {
                    if (data && data.action) {
                        self.showPopup(data.action);
                    }
                });
                if (this.isFirstLoad) {
                    $("body").click(function () {
                        self.isShowActionPopup(false);
                    });
                    this.isFirstLoad = false;
                }
            },

            afterRender: function () {
                var calheight, heightfooter, heightheader, heighttop, heightsumtotal;
                heightfooter = $('.footer-order').height();
                heightheader = $('#webpos_order_view_container .o-header-nav').height();
                heighttop = $('#webpos_order_view_container .sum-info-top').height();
                heightsumtotal = $('#webpos_order_view_container .total-due').height();
                calheight = heightfooter + heightheader + heightsumtotal + heighttop + 60;
                $('#webpos_order_view_container main').height('calc(100vh - '+ calheight +'px)');
            },
            setData: function (data, object) {
                this.orderData(data);
                this.orderListView(object);
                this.isShowActionPopup(false);
                var self = this;
                this.totalValues([]);
                var totalArray = orderTotal.getTotalOrderView();
                if (self.orderData())
                    $.each(totalArray, function (index, value) {
                        var order_currency_code = self.orderData().order_currency_code;
                        var current_currency_code = window.webposConfig.currentCurrencyCode;
                        if (
                            order_currency_code == current_currency_code
                        ) {
                            if ((self.orderData()[value.totalName] && self.orderData()[value.totalName] != 0) || value.required) {
                                var totalCode = value.totalName.replace("base_", "");
                                if(totalCode ==  'subtotal' && window.webposConfig['tax/cart_display/subtotal'] == 2){
                                    totalCode = 'subtotal_incl_tax';
                                }
                                if(totalCode !== 'webpos_change') {
                                    self.totalValues.push(
                                        {
                                            totalValue: (value.isPrice) ? priceHelper.formatPrice(self.orderData()[totalCode]) : self.orderData()[totalCode] + ' ' + value.valueLabel,
                                            totalLabel: value.totalName == 'base_discount_amount' &&
                                            (self.orderData().discount_description || self.orderData().coupon_code) ?
                                                (value.totalLabel) + ' (' + (self.orderData().discount_description ?
                                                    self.orderData().discount_description : self.orderData().coupon_code) +
                                                ')' : (value.totalLabel)
                                        }
                                    );
                                }
                            }
                        } else {
                            if ((self.orderData()[value.totalName] && self.orderData()[value.totalName] != 0) || value.required) {
                                var totalCode = value.totalName.replace("base_", "");
                                if(totalCode !== 'webpos_change') {
                                    self.totalValues.push(
                                        {
                                            totalValue: (value.isPrice) ? self.convertAndFormatPrice(self.orderData()[value.totalName]) : self.orderData()[value.totalName] + ' ' + value.valueLabel,
                                            totalLabel: value.totalName == 'base_discount_amount' &&
                                            (self.orderData().discount_description || self.orderData().coupon_code) ?
                                                (value.totalLabel) + ' (' + (self.orderData().discount_description ?
                                                    self.orderData().discount_description : self.orderData().coupon_code) +
                                                ')' : (value.totalLabel)
                                        }
                                    );
                                }
                            }
                        }
                    });
            },

            showActionPopup: function (data, event) {
                event.stopPropagation();
                if (this.orderViewObject.isShowActionPopup.call())
                    this.orderViewObject.isShowActionPopup(false);
                else
                    this.orderViewObject.isShowActionPopup(true);
            },

            showPopup: function (type) {
                var viewManager = require('ui/components/layout');
                this.isShowActionPopup(false);
                if (!this.popupArray) {
                    this.popupArray = {
                        sendemail: viewManager.getSingleton('ui/components/order/sendemail'),
                        comment: viewManager.getSingleton('ui/components/order/comment'),
                        invoice: viewManager.getSingleton('ui/components/order/invoice'),
                        shipment: viewManager.getSingleton('ui/components/order/shipment'),
                        refund: viewManager.getSingleton('ui/components/order/creditmemo'),
                        cancel: viewManager.getSingleton('ui/components/order/cancel'),
                        payment: viewManager.getSingleton('ui/components/order/view/payment')
                    }
                }
                eventManager.dispatch('sales_order_prepare_actions_component', {'popups': this.popupArray});
                this.popupArray[type].display(true);
            },

            getAddressType: function (type) {
                switch (type) {
                    case 'billing':
                        return this.orderData.call().billing_address;
                        break;
                    case 'shipping':
                        return this.orderData.call().extension_attributes.shipping_assignments[0].shipping.address;
                        break;
                }
            },

            getCustomerName: function (type) {
                var address = this.getAddressType(type);
                return address.firstname + ' ' + address.lastname;
            },

            checkValue: function (data) {
                if(data.totalLabel == $t('Gift Card')){
                    data.totalValue = priceHelper.formatPrice(-priceHelper.toNumber(data.totalValue));
                }

                if (priceHelper.toNumber(data.totalValue) == 0 && data.totalLabel == $t('Change')) {
                    return false;
                } else {
                    return true;
                }
            },

            getAddress: function (type) {
                var address = this.getAddressType(type);
                var city = address.city ? address.city + ', ' : '';
                var region = address.region && typeof address.region == 'string' ? address.region + ', ' : '';
                var postcode = address.postcode ? address.postcode + ', ' : '';
                return city + region + postcode + address.country_id;
            },

            getStatus: function () {
                var self = this;
                var status = _.find(self.statusObject, function (obj) {
                    return obj.statusClass == self.orderData().status
                });
                return (status)?status.statusLabel:self.orderData().status;
            },

            getJsObject: function () {
                return {
                    orderView: this,
                    orderListView: this.orderListView.call(),
                }
            },

            getPrice: function (label) {
                if (this.orderData().order_currency_code == window.webposConfig.currentCurrencyCode) {
                    return priceHelper.formatPrice(this.orderData()[label]);
                }
                return this.convertAndFormatPrice(
                    this.orderData()['base_' + label],
                    this.orderData().base_currency_code
                );
            },

            getGrandTotal: function () {
                if (this.orderData().order_currency_code == window.webposConfig.currentCurrencyCode) {
                    return priceHelper.formatPrice(this.orderData().grand_total);
                }
                return this.convertAndFormatPrice(this.orderData().base_grand_total, this.orderData().base_currency_code)
            },

            convertAndFormatPrice: function (price, from, to) {
                return priceHelper.convertAndFormat(price, from, to);
            },

            canShowComment: function () {
                var canShowComment = false;
                if (this.orderData().status_histories) {
                    $.each(this.orderData().status_histories, function (index, value) {
                        if (value.comment && value.comment != '') canShowComment = true;
                    });
                }
                return canShowComment;
            },

            printOrder: function () {
                var self = this;
                if(Helper.isPdfInvoicePlusEnable() && Helper.getPdfInvoiceTemplate()){
                    var orderId = self.orderData().entity_id;
                    PdfInvoicePlusModel.startPrint(orderId);
                    return true;
                }
                var html = $('#container-print-order')[0].innerHTML;
                var print_window = window.open('', 'print_offline', 'status=1,width=700,height=700');
                if(print_window){
                    print_window.document.open();
                    print_window.document.write(html);
                    print_window.print();
                }
            },

            syncOrder: function(){
                CheckoutModel.syncOrder(this.orderData(),"orderlist");
            },

            reOrder: function () {
                this.isShowActionPopup(false);
                ReOrder(this.orderData());
            },

            unhold: function () {

            },

            showWebposPayment: function () {
                var hasPayment = this.hasWebposPayment();
                var showIntegration = this.showIntegration();
                return (hasPayment || showIntegration);
            },

            hasWebposPayment: function () {
                var hasPayment = this.orderData().webpos_order_payments && this.orderData().webpos_order_payments.length > 0;
                return hasPayment;
            },

            showIntegration: function(){
                var hasGiftcard = this.orderData().base_gift_voucher_discount && this.orderData().base_gift_voucher_discount != 0;
                var hasRewardpoints = this.orderData().rewardpoints_base_discount && this.orderData().rewardpoints_base_discount < 0;
                var isPosPayment = this.orderData().payment; // && this.orderData().payment.method == 'multipaymentforpos';
                return ((hasGiftcard || hasRewardpoints) && isPosPayment);
            },

            getWebposPaymentAmount: function (data) {
                var order_currency_code = this.orderData().order_currency_code;
                var current_currency_code = window.webposConfig.currentCurrencyCode;
                var amount = priceHelper.currencyConvert(
                    data.base_real_amount,
                    this.orderData().base_currency_code
                );
                if (order_currency_code == current_currency_code) {
                    amount = data.real_amount;
                }
                var formatedAmount = priceHelper.formatPrice(amount);
                if(data.reference_number){
                    formatedAmount = '('+data.reference_number+') '+formatedAmount;
                }
                return (data.base_real_amount == 0) ? this.convertAndFormatPrice(0) : formatedAmount;
            },

            getPaidPayment: function () {
                var payments = [];
                if (this.showWebposPayment()) {
                    if(this.hasWebposPayment()){
                        var allPayments = this.orderData().webpos_order_payments;
                        $.each(allPayments, function (index, payment) {
                            if (priceHelper.toNumber(payment.base_payment_amount) > 0) {
                                payments.push(payment);
                            }
                        });
                    }

                    if(this.showIntegration()){
                        var hasGiftcard = this.orderData().base_gift_voucher_discount && this.orderData().base_gift_voucher_discount != 0;
                        if(hasGiftcard){
                            var baseAmount = this.orderData().base_gift_voucher_discount;
                            var amount = this.orderData().gift_voucher_discount;
                            payments.push({
                                base_payment_amount:-baseAmount,
                                payment_amount:-amount,
                                method_title: $t('Gift Card')
                            });
                        }
                        var hasRewardpoints = this.orderData().rewardpoints_base_discount && this.orderData().rewardpoints_base_discount < 0;
                        if(hasRewardpoints){
                            var baseAmount = this.orderData().rewardpoints_base_discount;
                            var amount = this.orderData().rewardpoints_discount;
                            payments.push({
                                base_payment_amount:-baseAmount,
                                payment_amount:-amount,
                                method_title: $t("Customer's Reward Points")
                            });
                        }
                    }
                }
                return payments;
            },

            getDeliveryDate: function (date) {
                return datetimeHelper.getFullDatetime(this.orderData().webpos_delivery_date);
                if (date)
                    return datetimeHelper.getFullDatetime(date);
                return datetimeHelper.getFullDatetime(this.orderData().webpos_delivery_date);
            },

            getPayLaterPayment: function () {
                var payments = [];
                if (this.showWebposPayment() && this.orderData().base_total_due > 0) {
                    var allPayments = this.orderData().webpos_order_payments;
                    $.each(allPayments, function (index, payment) {
                        if (priceHelper.toNumber(payment.base_payment_amount) == 0) {
                            payments.push(payment);
                        }
                    });
                }
                return payments;
            },
            showPayLater: function () {
                var payments = this.getPayLaterPayment();
                return (payments.length > 0) ? true : false;
            },
            /**
             * return a date time with format: Thursday 4 May, 2016 15:26PM
             * @param dateString
             * @returns {string}
             */
            getFullDatetime: function (dateString) {
                return datetimeHelper.getFullDatetime(dateString);
            },

            /**
             * return a date time with format: Thursday 4 May, 2016 15:26PM
             * @param dateString
             * @returns {string}
             */
            getFullCurrentDatetime: function (dateString) {
                var currentTime = datetimeHelper.stringToCurrentTime(dateString);
                return datetimeHelper.getFullDatetime(currentTime);
            },
            convertToCurrentTime: function(dateString){
                return (typeof dateString == 'string')?datetimeHelper.stringToCurrentTime(dateString):datetimeHelper.toCurrentTime(dateString);
            }
        });
    }
);