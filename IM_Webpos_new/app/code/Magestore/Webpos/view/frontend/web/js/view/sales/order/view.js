/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
    
define(
    [
        'require',
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/view/layout',
        'underscore',
        'mage/translate',
        'Magestore_Webpos/js/view/base/abstract',
        'Magestore_Webpos/js/model/sales/order/total',
        'Magestore_Webpos/js/model/sales/order/status',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/action/cart/checkout',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/action/cart/reorder',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/action/hardware/printer',
        'Magestore_Webpos/js/model/config/local-config',
        'Magestore_Webpos/js/model/checkout/shopping-cart'
    ],
    function (
              require,
              $,
              ko,
              OrderFactory,
              ViewManager,
              _,
              $t,
              Component,
              orderTotal,
              orderStatus,
              CheckoutModel,
              eventmanager,
              Checkout,
              priceHelper,
              datetimeHelper,
              ReOrder,
              Helper,
              PrintPosHub,
              localConfig,
              ShoppingCartModel
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
                template: 'Magestore_Webpos/sales/order/view',
                templateTop: 'Magestore_Webpos/sales/order/view/top',
                templateBilling: 'Magestore_Webpos/sales/order/view/billing',
                templateShipping: 'Magestore_Webpos/sales/order/view/shipping',
                templateShippingMethod: 'Magestore_Webpos/sales/order/view/shipping-method',
                templatePaymentMethod: 'Magestore_Webpos/sales/order/view/payment-method',
                templateTotal: 'Magestore_Webpos/sales/order/view/total',
                templateItems: 'Magestore_Webpos/sales/order/view/items',
                templateComments: 'Magestore_Webpos/sales/order/view/comments',
            },

            initialize: function () {
                this._super();
                var self = this;
                ko.pureComputed(function () {
                    return this.orderData();
                }, this).subscribe(function () {
                    if (this.orderData()) {
                        this.isCanceled(OrderFactory.get().setData(this.orderData()).isCanceled());
                        this.canInvoice(OrderFactory.get().get().setData(this.orderData()).canInvoice());
                        this.canCancel(OrderFactory.get().setData(this.orderData()).canCancel());
                        this.canShip(OrderFactory.get().setData(this.orderData()).canShip());
                        this.canCreditmemo(OrderFactory.get().setData(this.orderData()).canCreditmemo());
                        this.canSync(OrderFactory.get().setData(this.orderData()).canSync());
                        this.canTakePayment(OrderFactory.get().setData(this.orderData()).canTakePayment())
                        this.canUnhold(OrderFactory.get().setData(this.orderData()).canUnhold())
                    }
                }, this);
                this.cannotSync = ko.pureComputed(function () {
                    return (this.orderData() && this.orderData().state) ? this.orderData().state != 'notsync' : false;
                }, this);

                this.showInvoiceButton = ko.pureComputed(function () {
                    return (this.canInvoice() && this.cannotSync());
                }, this);
                eventmanager.observer('sales_order_afterSave', function (event, data) {
                    if (data.response && data.response.entity_id > 0) {
                        var deferedSave = $.Deferred();
                        OrderFactory.get().setData(data.response).setMode('offline').save(deferedSave);
                        this.orderListView().updateOrderListData(data.response);
                    }
                }.bind(this));
                if (this.isFirstLoad) {
                    $("body").click(function () {
                        this.isShowActionPopup(false);
                    }.bind(this));
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
                                if(totalCode == 'subtotal_incl_tax' && window.webposConfig['tax/sales_display/subtotal'] && window.webposConfig['tax/sales_display/subtotal'] == '1'){
                                    totalCode = 'subtotal';
                                }
                                if(totalCode !== 'webpos_change') {
                                    self.totalValues.push(
                                        {
                                            totalValue: (value.isPrice) ? priceHelper.formatPrice(self.orderData()[totalCode]) : self.orderData()[totalCode] + ' ' + value.valueLabel,
                                            totalLabel: value.totalName == 'base_discount_amount' &&
                                            (self.orderData().discount_description || self.orderData().coupon_code) ?
                                                $t(value.totalLabel) + ' (' + (self.orderData().discount_description ?
                                                    self.orderData().discount_description : self.orderData().coupon_code) +
                                                ')' : $t(value.totalLabel)
                                        }
                                    );
                                }
                            }
                        } else {
                            if ((self.orderData()[value.totalName] && self.orderData()[value.totalName] != 0) || value.required) {
                                var totalCode = value.totalName.replace("base_", "");
                                if(totalCode == 'subtotal_incl_tax' && window.webposConfig['tax/sales_display/subtotal'] && window.webposConfig['tax/sales_display/subtotal'] == '1'){
                                    totalCode = 'subtotal';
                                }
                                if(totalCode !== 'webpos_change') {
                                    self.totalValues.push(
                                        {
                                            totalValue: (value.isPrice) ? self.convertAndFormatPrice(self.orderData()[value.totalName]) : self.orderData()[value.totalName] + ' ' + value.valueLabel,
                                            totalLabel: value.totalName == 'base_discount_amount' &&
                                            (self.orderData().discount_description || self.orderData().coupon_code) ?
                                                $t(value.totalLabel) + ' (' + (self.orderData().discount_description ?
                                                    self.orderData().discount_description : self.orderData().coupon_code) +
                                                ')' : $t(value.totalLabel)
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
                var viewManager = require('Magestore_Webpos/js/view/layout');
                this.isShowActionPopup(false);
                if (!this.popupArray) {
                    this.popupArray = {
                        sendemail: viewManager.getSingleton('view/sales/order/sendemail'),
                        comment: viewManager.getSingleton('view/sales/order/comment'),
                        invoice: viewManager.getSingleton('view/sales/order/invoice'),
                        shipment: viewManager.getSingleton('view/sales/order/shipment'),
                        refund: viewManager.getSingleton('view/sales/order/creditmemo'),
                        cancel: viewManager.getSingleton('view/sales/order/cancel'),
                        payment: viewManager.getSingleton('view/sales/order/view/payment')
                    }
                }
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

            getAddress: function (type) {
                var address = this.getAddressType(type);
                var city = address.city ? address.city + ', ' : '';
                var region = address.region && typeof address.region == 'string' ? address.region + ', ' : '';
                var postcode = address.postcode ? address.postcode + ', ' : '';
                return city + region + postcode + address.country_id;
            },

            getStatus: function () {
                var self = this;
                return _.find(self.statusObject, function (obj) {
                    return obj.statusClass == self.orderData().status
                }).statusLabel;
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
                var html = $('#container-print-order')[0].innerHTML;
                if (localConfig.get('hardware/printer') === '1') {
                    $('.action-button .print').prop('disabled', true);
                    PrintPosHub(html);
                } else {
                    var print_window = window.open('', 'print_offline', 'status=1,width=700,height=700');
                    print_window.document.write(html);
                    html = print_window.document.getElementsByTagName('html')[0].innerHTML;

                    print_window.print();
                }
                var orderData = self.orderData();
                eventmanager.dispatch('webpos_printed_receipt_from_order_detail', {
                    order: orderData
                })
            },
            
            syncOrder: function(){
                CheckoutModel.syncOrder(this.orderData(),"orderlist");
            },

            reOrder: function () {
                this.isShowActionPopup(false);
                ReOrder(this.orderData());
                ShoppingCartModel.reoder_customer_id = this.orderData().customer_id;
                ShoppingCartModel.refresh();
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
                var hasGiftcard = this.orderData().base_gift_voucher_discount && this.orderData().base_gift_voucher_discount < 0;
                var hasRewardpoints = this.orderData().rewardpoints_base_discount && this.orderData().rewardpoints_base_discount < 0;
                var isPosPayment = this.orderData().payment && this.orderData().payment.method == 'multipaymentforpos';
                return ((hasGiftcard || hasRewardpoints) && isPosPayment);
            },

            getWebposPaymentAmount: function (data) {
                var order_currency_code = this.orderData().order_currency_code;
                var current_currency_code = window.webposConfig.currentCurrencyCode;
                var amount = priceHelper.currencyConvert(
                    data.base_payment_amount,
                    this.orderData().base_currency_code
                );
                if (order_currency_code == current_currency_code) {
                    amount = data.payment_amount;
                }
                return (data.base_payment_amount == 0) ? this.convertAndFormatPrice(0) : priceHelper.formatPrice(amount);
            },

            getPaidPayment: function () {
                var payments = [];
                if (this.showWebposPayment()) {
                    if(this.hasWebposPayment()){
                        var allPayments = this.orderData().webpos_order_payments;
                        $.each(allPayments, function (index, payment) {
                            if (priceHelper.toNumber(payment.base_payment_amount) > 0) {
                                if(payment.reference_number && !payment.addedRef && payment.card_type){
                                    payment.addedRef = true;
                                    payment.addCardType = true;
                                    payment.method_title += ' ( ' + payment.reference_number + ' - ' + payment.card_type + ')';
                                } else if(payment.reference_number && !payment.addedRef){
                                    payment.addedRef = true;
                                    payment.method_title += ' ( ' + payment.reference_number + ' )';
                                }
                                payments.push(payment);
                            }
                        });
                    }
                    if(this.showIntegration()){
                        var hasGiftcard = this.orderData().base_gift_voucher_discount && this.orderData().base_gift_voucher_discount < 0;
                        if(hasGiftcard){
                            var baseAmount = this.orderData().base_gift_voucher_discount;
                            var amount = this.orderData().gift_voucher_discount;
                            payments.push({
                                base_payment_amount:-baseAmount,
                                payment_amount:-amount,
                                method_title: $t('Gift Voucher')
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

            childOfBundleOrGroup: function(orderItem){
                var childOfBundleOrGroup = false;
                $.each(this.orderData().items,function (index, el) {
                    if (el.item_id == orderItem.parent_item_id && ['bundle','grouped'].indexOf(el.product_type)!=-1){
                        childOfBundleOrGroup = true;
                        return false;
                    }
                });
                return childOfBundleOrGroup;
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
                            if(payment.reference_number && !payment.addedRef && payment.card_type){
                                payment.addedRef = true;
                                payment.addCardType = true;
                                payment.method_title += ' ( ' + payment.reference_number + ' - ' + payment.card_type + ')';
                            } else if(payment.reference_number && !payment.addedRef){
                                payment.addedRef = true;
                                payment.method_title += ' ( ' + payment.reference_number + ' )';
                            }
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
            getItemRowTotalFormated: function(item){
                var self = this;
                var formated = ''
                if(item){
                    if(window.webposConfig.currentCurrencyCode == self.orderData().order_currency_code){
                        formated = item.row_total+item.tax_amount-item.discount_amount;
                    }else{
                        if(window.webposConfig.currentCurrencyCode == self.orderData().base_currency_code){
                            formated = item.base_row_total+item.base_tax_amount-item.base_discount_amount;
                        } else{
                            formated = item.base_row_total+item.base_tax_amount-item.base_discount_amount;
                            formated = priceHelper.currencyConvert(formated,self.orderData().base_currency_code, window.webposConfig.currentCurrencyCode);
                        }
                    }
                }
                return priceHelper.formatPrice(formated);
            },
            getItemPriceFormated: function(item){
                var self = this;
                var formated = ''
                if(item){
                    var displayIncludeTax = Helper.isCartDisplayIncludeTax('price');
                    var displayIncludeTax = false;
                    if(window.webposConfig.currentCurrencyCode == self.orderData().order_currency_code){
                        formated = (displayIncludeTax)?item.price_incl_tax:item.price;
                    }else{
                        if(window.webposConfig.currentCurrencyCode == self.orderData().base_currency_code){
                            formated = (displayIncludeTax)?item.base_price_incl_tax:item.base_price;
                        } else{
                            formated = (displayIncludeTax)?item.base_price_incl_tax:item.base_price;
                            formated = priceHelper.currencyConvert(formated,self.orderData().base_currency_code, window.webposConfig.currentCurrencyCode);
                        }
                    }
                }
                return priceHelper.formatPrice(formated);
            }
        });
    }
);