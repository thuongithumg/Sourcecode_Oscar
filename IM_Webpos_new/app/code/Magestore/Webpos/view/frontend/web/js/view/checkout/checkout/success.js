/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/alert',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/translate',
        'Magestore_Webpos/js/model/config/local-config',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/action/hardware/printer',
        'Magestore_Webpos/js/action/checkout/load-order-to-checkout',
        'Magestore_Webpos/js/model/checkout/multiorder'
    ],
    function ($,ko, Component, ViewManager, CheckoutModel,  PriceHelper, Alert, AddNoti, Translate, localConfig, Event, PrintPosHub, Checkout, multiOrder) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/success'
            },
            successMessage: ko.observable(),
            successImageUrl: ko.observable(),
            printWindow: ko.observable(),
            initialize: function () {
                this._super();
                this.orderData = ko.pureComputed(function () {
                    var result = CheckoutModel.createOrderResult();
                    return (result && result.increment_id) ? result : false;
                });
                this.createdOrder = ko.pureComputed(function () {
                    var result = CheckoutModel.createOrderResult();
                    return (result && result.increment_id) ? true : false;
                });
                var self = this;
                Event.observer('webpos_order_save_after', function (event, data) {
                    if (data && data.increment_id) {
                        ViewManager.getSingleton('view/checkout/checkout/receipt').initDefaultData();
                        if (ViewManager.getSingleton('view/checkout/checkout/receipt').isAutoPrint()) {
                            self.printReceipt();
                        }
                    }
                });
                Event.observer('webpos_place_order_online_after',function(event,data){
                    if(data && data.increment_id){
                        ViewManager.getSingleton('view/checkout/checkout/receipt').initDefaultData();
                        if(ViewManager.getSingleton('view/checkout/checkout/receipt').isAutoPrint()){
                            self.printReceipt();
                        }
                    }
                });
            },
            getOrderData: function (key) {
                var orderData = this.orderData();
                var data = "";
                if (orderData) {
                    data = orderData;
                    if (key) {
                        if (typeof data[key] != "undefined") {
                            data = data[key];
                        } else {
                            data = "";
                        }
                    }
                }
                return data;
            },
            getCustomerEmail: function () {
                return this.getOrderData('customer_email');
            },
            getGrandTotal: function () {
                return PriceHelper.formatPrice(this.getOrderData('grand_total'));
            },
            getOrderIdMessage: function () {
                return "#" + this.getOrderData('increment_id');
            },
            printReceipt: function () {
                ViewManager.getSingleton('view/checkout/checkout/receipt').initDefaultData();

                var html = ViewManager.getSingleton('view/checkout/checkout/receipt').toHtml();
                if (localConfig.get('hardware/printer') === '1') {
                    PrintPosHub(html);
                } else {
                    var print_window = window.open('', 'print_offline', 'status=1,width=500,height=700');
                    if(print_window){

                        this.printWindow(print_window);
                        print_window.document.write(html);
                        print_window.print();
                    }else{
                        AddNoti(Translate("Your browser has blocked the automatic popup, please change your browser setting or print the receipt manually"), true, "warning", Translate('Message'));
                    }
                }
                var orderData = this.orderData();
                Event.dispatch('webpos_printed_receipt_after_place_order', {
                    order: orderData
                })
            },
            startNewOrder: function () {
                ViewManager.getSingleton('view/checkout/checkout/payment_selected').initPayments();
                ViewManager.getSingleton('view/checkout/cart').switchToCart();
                var deferred = ViewManager.getSingleton('view/checkout/cart').emptyCart();
                CheckoutModel.resetCheckoutData();
                if (multiOrder.itemsList().length > 0) {
                    if(deferred) {
                        deferred.done(function () {
                            var items = multiOrder.itemsList();
                            var firstItem = items[0];
                            multiOrder.currentId(firstItem['increment_id']);
                            multiOrder.currentOrderData(firstItem);
                            firstItem.is_after_place_order_success = true;
                            Checkout(firstItem);
                        });
                    } else {
                        var items = multiOrder.itemsList();
                        var firstItem = items[0];
                        multiOrder.currentId(firstItem['increment_id']);
                        multiOrder.currentOrderData(firstItem);
                        firstItem.is_after_place_order_success = true;
                        Checkout(firstItem);
                    }
                } else {
                    multiOrder.currentId(0);
                    multiOrder.currentOrderData({});
                }
                if (this.printWindow()) {
                    this.printWindow().close();
                }
            },
            sendEmail: function () {
                if (this.getCustomerEmail()) {
                    CheckoutModel.sendEmail(this.getCustomerEmail(), this.getOrderData('increment_id'));
                    AddNoti(Translate("An email has been sent for this order"), true, "success", Translate('Message'));
                } else {
                    Alert({
                        priority: "warning",
                        title: "Warning",
                        message: "Please enter the email address"
                    });
                }
            },
            saveEmail: function (data, event) {
                if (!this.orderData()) {
                    this.orderData({});
                }
                this.orderData().customer_email = event.target.value;
            }
        });
    }
);