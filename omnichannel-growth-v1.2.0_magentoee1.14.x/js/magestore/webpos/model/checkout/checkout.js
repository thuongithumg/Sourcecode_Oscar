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
        'mage/translate',
        'model/checkout/cart',
        'model/checkout/cart/discountpopup',
        'helper/price',
        'helper/order',
        'helper/staff',
        'helper/datetime',
        'eventManager',
        'model/checkout/cart/totals',
        'helper/general',
        'helper/alert',
        'model/sales/order-factory',
        'model/resource-model/magento-rest/checkout/checkout',
        'model/checkout/checkout/payment/creditcard',
        'dataManager',
        'action/notification/add-notification',
        'model/checkout/checkout/payment/authorize-directpost',
        'model/checkout/checkout/payment/nlpay-instore',
        'lib/moment',
        'lib/cookie',
        'action/customer/change/select-customer-checkout',
        'model/checkout/shopping-cart',
    ],
    function ($, ko, __, CartModel, DiscountModel, HelperPrice, OrderHelper, Staff, DateTime, Event, Totals, Helper,
              Alert, OrderFactory, CheckoutResource, CreditCard, DataManager, AddNoti, DirectPost, PaynlInstore, moment, Cookies, selectCustomerCheckout,
            ShoppingCartModel
    ) {
        "use strict";
        var storeAddress = DataManager.getData('webpos_store_address');
        var CheckoutModel = {
            ccMethods: ko.observableArray(),
            selectedPayments: ko.observableArray(),
            selectedShippingTitle: ko.observable(),
            selectedShippingCode: ko.observable(),
            selectedShippingPrice: ko.observable(0),
            selectedShippingPriceType: ko.observable("O"),
            paymentCode: ko.observable(),
            orderComment: ko.observable(),
            createShipment: ko.observable(),
            createInvoice: ko.observable(),
            billingAddress: ko.observable(),
            shippingAddress: ko.observable(),
            //storeAddress: ko.observable(storeAddress),
            createOrderResult: ko.observable({}),
            loading: ko.observable(),
            remainTotal: ko.observable(),
            placingOrder: ko.observable(false),
            autoCheckingPromotion: ko.observable(false),
            selectingCustomer: ko.observable(false),
            autoCreateShipment: ko.observable(true),
            ADDRESS_TYPE: {
                BILLING: "billing",
                SHIPPING: "shipping"
            },
            couponCode: ko.pureComputed(function () {
                return DiscountModel.couponCode();
            }),

            cartDiscountAmount: ko.pureComputed(function () {
                return Totals.discountAmount();
            }),

            cartBaseDiscountAmount: ko.pureComputed(function () {
                return Totals.baseDiscountAmount();
            }),

            cartDiscountName: ko.pureComputed(function () {
                if (DiscountModel.cartDiscountAmount() > 0 && !DiscountModel.cartDiscountName()) {
                    return 'Custom Discount';
                }
                return DiscountModel.cartDiscountName();
            }),

            isCreatedOrder: function () {
                return (this.createOrderResult() && this.createOrderResult().increment_id) ? true : false;
            },

            initDefaultData: function () {
                if(ShoppingCartModel){
                    ShoppingCartModel.checkoutModel = this;
                }

                var self = this;
                self.isCreditCardPayment = ko.pureComputed(function () {
                    return (self.ccMethods().length > 0 && self.selectedPayments().length > 0) ? true : false;
                });
                self.selectedShippingTitle(DataManager.getData('default_shipping_title'));
                self.selectedShippingCode(DataManager.getData('default_shipping_method'));
                self.selectedShippingPrice(0);
                self.orderComment("");
                self.paymentCode(DataManager.getData('default_payment_method'));
                self.createOrderResult({});
                self.placingOrder(false);
                self.initObservable();
                self.useDefaultAddress(self.ADDRESS_TYPE.BILLING);
                self.useDefaultAddress(self.ADDRESS_TYPE.SHIPPING);
                self.createShipment(self.autoCreateShipment());
                CreditCard.resetData();

                CartModel.CheckoutModel(CheckoutModel);
            },
            initObservable: function () {
                var self = this;
                Event.observer('cart_empty_after', function () {
                    self.resetCheckoutData();
                });
                Event.observer('update_shipping_price', function (event, data) {
                    var price = (data && data.price) ? data.price : 0;
                    self.saveShipping({
                        price: price
                    });
                });
                Event.observer('load_shipping_online', function () {
                    if (Helper.isOnlineCheckout()) {
                        self.loadShippingOnline();
                    }
                });
                Event.observer('load_payment_online', function () {
                    if (Helper.isOnlineCheckout()) {
                        self.loadPaymentOnline();
                    }
                });
                Event.observer('load_cart_data_online', function (event, data) {
                    if (Helper.isOnlineCheckout()) {
                        self.loadCartDataOnline(data.section ? data.section : false);
                    }
                });
                Event.observer('checkout_select_customer_after', function (event, data) {
                    if (Helper.isOnlineCheckout() && CartModel.isOnCheckoutPage()) {
                        self.selectCustomerOnline();
                    }
                });
                Event.observer('sync_offline_order_after_error', function (event, data) {
                    if (data.action) {
                        var orderParams = data.action.payload;
                        var extension_data = orderParams.extension_data;
                        var orderId = "";
                        $.each(extension_data, function (index, field) {
                            if (field && field.key == "webpos_order_id") {
                                orderId = field.value;
                            }
                        });
                        if (data.action && data.action.error) {
                            if (orderId != "") {
                                var message = __("Cannot sync order");
                                message += ":#" + orderId;
                                AddNoti(message, true, 'danger', 'Error');
                                AddNoti(data.action.error, false, 'danger', 'Error');
                                OrderFactory.get().setMode('offline').load(orderId).done(function (order) {
                                    order.error = 1;
                                    OrderFactory.create().setData(order).setMode('offline').save().done(function (response) {
                                        if (response) {
                                            Event.dispatch('order_pull_after', response);
                                        }
                                    });
                                });
                            }
                        }
                    }
                });
                Event.observer(DirectPost.SUCCESS_EVENT_NAME, function (event, data) {
                    self.loading(false);
                });
                Event.observer(DirectPost.ERROR_EVENT_NAME, function (event, data) {
                    self.loading(false);
                });
                Event.observer(PaynlInstore.SUCCESS_EVENT_NAME, function(event, data){
                    self.loading(false);
                });
                Event.observer(PaynlInstore.ERROR_EVENT_NAME, function(event, data){
                    self.loading(false);
                });
                self.billingAddress.subscribe(function (address) {
                    CartModel.billingAddress(address);
                });
                self.shippingAddress.subscribe(function (address) {
                    CartModel.shippingAddress(address);
                });
            },
            resetCheckoutData: function () {
                var self = this;
                self.createOrderResult({});
                self.selectedShippingTitle(DataManager.getData('default_shipping_title'));
                self.selectedShippingCode(DataManager.getData('default_shipping_method'));
                self.selectedShippingPrice(0);
                self.paymentCode(DataManager.getData('default_payment_method'));
                DiscountModel.cartDiscountAmount(0);
                DiscountModel.cartBaseDiscountAmount(0);
                DiscountModel.cartDiscountName("");
                self.orderComment("");
                DiscountModel.couponCode();
                CartModel.removeCustomer();
                self.useDefaultAddress(self.ADDRESS_TYPE.BILLING);
                self.useDefaultAddress(self.ADDRESS_TYPE.SHIPPING);
                Totals.updateDiscountTotal();
                self.resetDeliveryTime();
                self.placingOrder(false);
                CreditCard.resetData();

                self.createShipment(self.autoCreateShipment());
            },
            useWebposShipping: function () {
                var self = this;
                self.selectedShippingCode("webpos_shipping_storepickup");
                self.selectedShippingPrice(0);
                self.selectedShippingTitle(Helper.__('POS Shipping - Store Pickup'));
            },
            createOrder: function () {
                var self = this;
                self.placingOrder(true);
                var data = self.getOfflineOrderData();
                Event.dispatch('webpos_place_order_before', data);
                // if(data.validate && data.validate == true){
                //     delete data.validate;

                // change total paid if have webpos_change
                if(data.webpos_change !== 'undefined' && data.webpos_change > 0) {
                    data.total_paid -= data.webpos_change;
                    data.base_total_paid -= data.webpos_change;
                }
                OrderFactory.get().setData(data).setMode('offline').save().done(function (response) {
                    if (response) {
                        if (response.increment_id) {
                            var message = Helper.__('Order has been created successfully ') + "#" + response.increment_id;
                            AddNoti(message, true, "success", Helper.__('Message'));
                        }
                        self.placeOrder(response);
                        self.syncOrder(response, "checkout");
                    }
                });
                // }
            },
            placeOrder: function (response) {
                var self = this;
                Event.dispatch('webpos_place_order_after', response);
                self.createOrderResult(response);
                Event.dispatch('webpos_order_save_after', response, true);
            },
            checkPromotion: function () {
                var deferred = $.Deferred();
                var params = this.getCheckPromotionParams();
                CheckoutResource().setPush(true).setLog(false).checkPromotion(params, deferred);

                DiscountModel.loading(true);
                deferred.done(function (response) {
                    if (response.status && response.data && response.data.discount_amount) {
                        DiscountModel.promotionDiscountAmount(HelperPrice.toBasePrice(response.data.discount_amount));
                    } else {
                        DiscountModel.promotionDiscountAmount(0);
                    }
                    DiscountModel.loading(false);
                }).fail(function (response) {
                    DiscountModel.loading(false);
                }).always(function () {
                    DiscountModel.loading(false);
                });
            },
            sendEmail: function (email, increment_id) {
                var self = this;
                var deferred = $.Deferred();
                var params = {increment_id: increment_id, email: email};
                CheckoutResource().setPush(true).sendEmail(params, deferred);
            },
            syncOrder: function (orderData, page) {
                if (orderData && orderData.sync_params) {
                    var params = orderData.sync_params;
                    params.extension_data.push({
                        key: "webpos_order_id",
                        value: orderData.entity_id
                    });
                    if (page == "orderlist") {

                    }
                    var request = $.Deferred();
                    CheckoutResource().setActionId(orderData.entity_id).setPush(true).createOrder(params, request);
                    request.done(function (response) {
                        if (response && response.data) {
                            var data = response.data;
                            if (data.increment_id) {
                                var deleteRequest = OrderFactory.get().delete(data.increment_id);
                                deleteRequest.always(function () {
                                    OrderFactory.create().setMode("offline").setData(data).save();
                                    var viewManager = require('ui/components/layout');
                                    viewManager.getSingleton('ui/components/order/list')._prepareItems();
                                });
                            }
                        }
                    });
                }
            },
            saveShipping: function (data) {
                var self = this;
                if (typeof data.code != "undefined") {
                    self.selectedShippingCode(data.code);
                }
                if (typeof data.title != "undefined") {
                    self.selectedShippingTitle(data.title);
                }
                if (typeof data.price != "undefined") {
                    data.price = parseFloat(data.price);
                    self.selectedShippingPrice(data.price);
                    var shippingData = {
                        price: data.price
                    }
                    if (typeof data.price_type != "undefined") {
                        self.selectedShippingPriceType(data.price_type);
                        shippingData.price_type = data.price_type;
                    }
                    Totals.shippingData(shippingData);
                    Totals.collectShippingTotal();
                } else {
                    self.selectedShippingPrice(0);
                    Totals.updateShippingAmount(0);
                }
                if (Helper.isOnlineCheckout() && data.code) {
                    self.saveShippingMethodOnline(data.code);
                }
            },
            saveBillingAddress: function (data) {
                if (data.id == 0) {
                    if (data.firstname && data.lastname) {
                        this.useDefaultAddress(this.ADDRESS_TYPE.BILLING, data.firstname, data.lastname);
                    } else {
                        this.useDefaultAddress(this.ADDRESS_TYPE.BILLING);
                    }
                    var newAddress = this.billingAddress();
                    newAddress.firstname = data.firstname;
                    newAddress.lastname = data.lastname;
                    this.billingAddress(newAddress);
                } else {
                    this.billingAddress(data);
                }
                CartModel.billingAddress(this.billingAddress());
                CartModel.reCollectTaxRate();
            },
            updateBillingAddress: function (data) {
                var address = this.billingAddress();
                if (typeof address == "undefined") {
                    address = {};
                }
                if (data) {
                    $.each(data, function (key, value) {
                        address[key] = value;
                    });
                }
                this.billingAddress(address);
                CartModel.billingAddress(address);
                var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                if (calculateTaxBaseOn == 'billing') {
                    CartModel.reCollectTaxRate();
                }
            },
            updateShippingAddress: function (data) {
                var address = this.shippingAddress();
                if (typeof address == "undefined") {
                    address = {};
                }
                if (data) {
                    $.each(data, function (key, value) {
                        address[key] = value;
                    });
                }
                this.shippingAddress(address);
                var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                if (calculateTaxBaseOn == 'shipping') {
                    CartModel.reCollectTaxRate();
                }
            },
            saveShippingAddress: function (data) {
                if (data.id == 0) {
                    if (data.firstname && data.lastname) {
                        this.useDefaultAddress(this.ADDRESS_TYPE.SHIPPING, data.firstname, data.lastname);
                    } else {
                        this.useDefaultAddress(this.ADDRESS_TYPE.SHIPPING);
                    }
                    var newAddress = this.shippingAddress();
                    newAddress.firstname = data.firstname;
                    newAddress.lastname = data.lastname;
                    this.shippingAddress(newAddress);

                } else {
                    this.shippingAddress(data);
                }
                CartModel.reCollectTaxRate();
            },
            getHoldOrderParams: function () {
                var self = this;
                var orderParams = {
                    quote_init: CartModel.getQuoteInitParams(),
                    customer_id: CartModel.customerId(),
                    customer_group: CartModel.customerGroup(),
                    customerData: CartModel.customerData(),
                    items: CartModel.getItemsInitData(),
                    config: {
                        apply_promotion: DiscountModel.applyPromotion(),
                        cart_base_discount_amount: DiscountModel.cartBaseDiscountAmount(),
                        cart_discount_amount: DiscountModel.cartDiscountAmount(),
                        cart_discount_name: DiscountModel.cartDiscountName(),
                        cart_applied_discount: DiscountModel.appliedDiscount(),
                        cart_applied_promotion: DiscountModel.appliedPromotion(),
                        cart_discount_type: DiscountModel.cartDiscountType(),
                        cart_discount_percent: DiscountModel.cartDiscountPercent(),
                        currency_code: window.webposConfig.currentCurrencyCode
                    },
                    coupon_code: self.couponCode(),
                    billing_address: self.billingAddress(),
                    shipping_address: self.shippingAddress()
                };
                return orderParams;
            },
            getOrderParams: function () {
                var self = this;
                var orderParams = {
                    store: DataManager.getData('default_store_code'),
                    customer_id: String(CartModel.customerId()),
                    items: CartModel.getItemsInfo(),
                    payment: {
                        method: self.paymentCode(),
                        method_data: self.getPaymentsData(),
                        address: self.billingAddress()
                    },
                    shipping: {
                        method: self.selectedShippingCode(),
                        tracks: [],
                        address: self.shippingAddress(),
                        datetime: self.getDeliverytime()
                    },
                    config: {
                        apply_promotion: DiscountModel.applyPromotion(),
                        note: self.orderComment(),
                        create_invoice: self.createInvoice(),
                        create_shipment: self.createShipment(),
                        cart_base_discount_amount: self.cartBaseDiscountAmount(),
                        cart_discount_amount: self.cartDiscountAmount(),
                        cart_discount_name: self.cartDiscountName(),
                        currency_code: window.webposConfig.currentCurrencyCode
                    },
                    coupon_code: self.couponCode(),
                    extension_data: [
                        {
                            key: "grand_total",
                            value: HelperPrice.currencyConvert(Totals.grandTotal())
                        },
                        {
                            key: "base_grand_total",
                            value: Totals.grandTotal()
                        },
                        {
                            key: "tax_amount",
                            value: HelperPrice.currencyConvert(Totals.tax())
                        },
                        {
                            key: "base_tax_amount",
                            value: Totals.tax()
                        },
                        {
                            key: "subtotal",
                            value: HelperPrice.currencyConvert(Totals.subtotal())
                        },
                        {
                            key: "base_subtotal",
                            value: Totals.subtotal()
                        },
                        {
                            key: "location_id",
                            value: Helper.getLocalConfig('current_location_id'),
                        },
                        {
                            key: "shift_id",
                            value: window.webposConfig.shiftId,
                        },
                        // {
                        //     key:"created_at",
                        //     value: DateTime.getBaseSqlDatetime(),
                        // }
                    ],
                    session_data: [],
                    integration: []
                };

                orderParams.extension_data.push({
                    key: "webpos_staff_id",
                    value: Staff.getStaffId()
                });
                orderParams.extension_data.push({
                    key: "webpos_staff_name",
                    value: Staff.getStaffName()
                });
                if (self.couponCode()) {
                    orderParams.extension_data.push({
                        key: "discount_description",
                        value: self.couponCode()
                    });
                }
                if (self.remainTotal() && self.remainTotal() < 0) {
                    orderParams.extension_data.push({
                        key: "webpos_change",
                        value: -HelperPrice.currencyConvert(self.remainTotal())
                    });
                    orderParams.extension_data.push({
                        key: "webpos_base_change",
                        value: -self.remainTotal()
                    });
                }
                return orderParams;
            },
            getAllDiscountName: function () {
                var coupon = '';
                var self = this;
                if (self.couponCode())
                    coupon = self.couponCode();
                if (self.cartDiscountName())
                    coupon = self.cartDiscountName();
                if (self.couponCode() && self.cartDiscountName())
                    coupon = self.couponCode() + ',' + self.cartDiscountName();
                return coupon;
            },
            getOfflineOrderData: function () {
                var self = this;
                var createdTime = DateTime.getSqlDatetime();
                var baseCreatedTime = DateTime.getBaseSqlDatetime();
                var customerData = CartModel.customerData();
                var webpos_order_id = OrderHelper.generateId();
                var coupon = self.getAllDiscountName();
                var data = {
                    entity_id: webpos_order_id,
                    increment_id: webpos_order_id,
                    status: "notsync",
                    state: "notsync",
                    is_virtual: CartModel.isVirtual() ? 1 : 0,
                    created_at: baseCreatedTime,
                    updated_at: baseCreatedTime,
                    order_currency_code: window.webposConfig.currentCurrencyCode,
                    location_id: Helper.getLocalConfig('current_location_id'),
                    webpos_staff_id: Staff.getStaffId(),
                    webpos_staff_name: Staff.getStaffName(),
                    customer_email: customerData.email,
                    customer_firstname: customerData.firstname,
                    customer_id: CartModel.customerId(),
                    customer_lastname: customerData.lastname,
                    customer_telephone: customerData.telephone,
                    customer_note: self.orderComment(),
                    total_item_count: CartModel.totalItems(),
                    items: CartModel.getItemsDataForOrder(),
                    payment: {
                        method: self.paymentCode(),
                        additional_information: []
                    },
                    extension_attributes: {
                        shipping_assignments: [
                            {
                                items: "",
                                shipping: {
                                    address: self.getShippingAddressForOrder(),
                                    method: self.selectedShippingCode(),
                                }
                            }
                        ]
                    },
                    shipping_description: self.selectedShippingTitle(),
                    billing_address: self.getBillingAddressForOrder(),
                    status_histories: [
                        {
                            comment: self.orderComment(),
                            created_at: createdTime,
                            status: "notsync",
                        }
                    ],
                    webpos_base_change: (self.remainTotal() && self.remainTotal() < 0) ? -self.remainTotal() : 0,
                    webpos_change: (self.remainTotal() && self.remainTotal() < 0) ? -HelperPrice.currencyConvert(self.remainTotal()) : 0,
                    base_subtotal: Totals.getTotalValue(Totals.SUBTOTAL_TOTAL_CODE),
                    subtotal: HelperPrice.currencyConvert(Totals.getTotalValue(Totals.SUBTOTAL_TOTAL_CODE)),
                    base_tax_amount: Totals.getTotalValue(Totals.TAX_TOTAL_CODE),
                    tax_amount: HelperPrice.currencyConvert(Totals.getTotalValue(Totals.TAX_TOTAL_CODE)),
                    base_total_due: self.getTotalDue(),
                    total_due: HelperPrice.currencyConvert(self.getTotalDue()),
                    base_total_paid: self.getTotalPaid(),
                    total_paid: HelperPrice.currencyConvert(self.getTotalPaid()),
                    base_shipping_amount: Totals.getTotalValue(Totals.SHIPPING_TOTAL_CODE),
                    shipping_amount: HelperPrice.currencyConvert(Totals.getTotalValue(Totals.SHIPPING_TOTAL_CODE)),
                    base_discount_amount: Totals.getTotalValue(Totals.DISCOUNT_TOTAL_CODE),
                    discount_amount: HelperPrice.currencyConvert(Totals.getTotalValue(Totals.DISCOUNT_TOTAL_CODE)),
                    discount_description: coupon,
                    base_grand_total: Totals.getTotalValue(Totals.GRANDTOTAL_TOTAL_CODE),
                    grand_total: HelperPrice.currencyConvert(Totals.getTotalValue(Totals.GRANDTOTAL_TOTAL_CODE)),
                    sync_params: self.getOrderParams(),
                    base_currency_code: window.webposConfig.baseCurrencyCode,
                    initData: self.getHoldOrderParams(),
                    webpos_order_payments: self.getPaymentsDataForOrder(),
                    webpos_delivery_date: self.getDeliverytime()
                };
                return data;
            },
            getHoldOrderData: function () {
                var currentTime = $.now();
                var self = this;
                var data = self.getOfflineOrderData();
                delete data.sync_params;
                data.entity_id = currentTime.toString();
                data.increment_id = currentTime.toString();
                data.status = "onhold";
                data.state = "onhold";
                data.initData = self.getHoldOrderParams();
                return data;
            },
            getCheckPromotionParams: function () {
                var params = this.getOrderParams();
                return params;
            },
            getCheckShippingParams: function () {
                var params = this.getOrderParams();
                delete params.config;
                delete params.coupon_code;
                delete params.payment;
                delete params.shipping_method;
                params.zipcode = (this.shippingAddress() && this.shippingAddress().postcode) ? this.shippingAddress().postcode : "";
                params.country = (this.shippingAddress() && this.shippingAddress().country_id) ? this.shippingAddress().country_id : "";
                return params;
            },
            getDeliverytime: function () {
                if ($('#delivery_date') && $('#delivery_date').val() != '') {
                    var date = '';
                    date = $('#delivery_date').val();
                    //07/12/2017 16:33 PM
                    return moment(date, 'MM/DD/YYYY hh:mm a').format('YYYY-MM-DD HH:mm:ss');
                }
                return '';
            },
            resetDeliveryTime: function () {
                $('#delivery_date').val('');
            },
            getPaymentsData: function () {
                var self = this;
                var paymentsData = [];
                if (self.selectedPayments().length > 0) {
                    $.each(self.selectedPayments(), function () {
                        var data = {};
                        data.code = this.code;
                        data.title = this.title;
                        data.base_amount = this.cart_total;
                        data.amount = HelperPrice.currencyConvert(this.cart_total);
                        data.base_real_amount = this.paid_amount;
                        data.real_amount = HelperPrice.currencyConvert(this.paid_amount);
                        data.reference_number = this.reference_number;
                        data.is_pay_later = this.is_pay_later;
                        data.shift_id = window.webposConfig.shiftId;
                        data.additional_data = CreditCard.info;
                        paymentsData.push(data);
                    });
                }
                return paymentsData;
            },
            getPaymentsDataForOrder: function () {
                var self = this;
                var payments = [];
                ko.utils.arrayForEach(self.getPaymentsData(), function (payment) {
                    var data = {
                        base_payment_amount: (payment.is_pay_later) ? 0 : payment.base_amount,
                        payment_amount: (payment.is_pay_later) ? 0 : payment.amount,
                        base_display_amount: (payment.is_pay_later) ? 0 : payment.base_real_amount,
                        display_amount: (payment.is_pay_later) ? 0 : payment.real_amount,
                        method: payment.code,
                        method_title: payment.title,
                    }
                    payments.push(data);
                });
                return payments;
            },
            getTotalPaid: function () {
                var self = this;
                var totalPaid = 0;
                if (self.getPaymentsData().length > 0) {
                    $.each(self.getPaymentsData(), function () {
                        if (!this.is_pay_later) {
                            totalPaid += HelperPrice.toNumber(this.base_amount);
                        }
                    });
                }
                return totalPaid;
            },
            getTotalDue: function () {
                var self = this;
                var totalPaid = 0;
                var baseGrandTotal = HelperPrice.toNumber(Totals.getTotalValue(Totals.GRANDTOTAL_TOTAL_CODE));
                if (self.getPaymentsData().length > 0) {
                    $.each(self.getPaymentsData(), function () {
                        if (!this.is_pay_later) {
                            totalPaid += HelperPrice.toNumber(this.base_amount);
                        }
                    });
                }
                var totalDue = HelperPrice.toNumber(baseGrandTotal - totalPaid);
                return (totalDue > 0) ? totalDue : 0;
            },
            useDefaultAddress: function (type, firstname, lastname) {
                var self = this;
                var address = DataManager.getData('webpos_store_address');
                storeAddress = DataManager.getData('webpos_store_address');
                if (address) {
                    if (type == self.ADDRESS_TYPE.SHIPPING) {
                        self.shippingAddress(address);
                        if (firstname || lastname) {
                            self.updateShippingAddress({
                                'firstname': storeAddress.firstname,
                                'lastname': storeAddress.lastname
                            });
                        }
                    } else {
                        self.billingAddress(address);
                        if (firstname || lastname) {
                            self.updateBillingAddress({
                                'firstname': storeAddress.firstname,
                                'lastname': storeAddress.lastname
                            });
                        }
                    }
                }
            },
            getBillingAddressForOrder: function () {
                var billing = this.billingAddress() ? this.billingAddress() : {};
                var data = {};
                data.address_type = this.ADDRESS_TYPE.BILLING;
                $.each(billing, function (key, value) {
                    if (key == "region" && value.region) {
                        var regions = false;
                        // var regions = JSON.parse(window.webposConfig.regionJson);
                        if (regions && billing.country_id && regions[billing.country_id]) {
                            if (regions[billing.country_id][value.region_id]) {
                                value.region = regions[billing.country_id][value.region_id].name;
                                value.region_code = regions[billing.country_id][value.region_id].code;
                            }
                        }
                        value = value.region;
                    }
                    data[key] = value;
                });
                return data;
            },
            getShippingAddressForOrder: function () {
                var shipping = this.shippingAddress() ? this.shippingAddress() : {};
                var data = {};
                data.address_type = this.ADDRESS_TYPE.SHIPPING;
                $.each(shipping, function (key, value) {
                    if (key == "region" && value.region) {
                        var regions = false;
                        // var regions = JSON.parse(window.webposConfig.regionJson);
                        if (regions && shipping.country_id && regions[shipping.country_id]) {
                            if (regions[shipping.country_id][value.region_id]) {
                                value.region = regions[shipping.country_id][value.region_id].name;
                                value.region_code = regions[shipping.country_id][value.region_id].code;
                            }
                        }
                        value = value.region;
                    }
                    data[key] = value;
                });
                return data;
            },
            autoCheckPromotion: function () {
                var deferred = $.Deferred();
                var self = this;
                if (self.autoCheckingPromotion() == false) {
                    var params = this.getCheckPromotionParams();
                    CheckoutResource().setPush(true).setLog(false).checkPromotion(params, deferred);

                    self.autoCheckingPromotion(true);
                    deferred.done(function (response) {
                        if (response.status && response.data && response.data.discount_amount) {
                            self.applyPromotionDiscount(HelperPrice.toBasePrice(response.data.discount_amount));
                        } else {
                            self.applyPromotionDiscount(0);
                        }
                    }).fail(function (response) {
                        self.autoCheckingPromotion(false);
                    }).always(function () {
                        self.autoCheckingPromotion(false);
                    });
                }
                return deferred;
            },
            applyPromotionDiscount: function (amount) {
                amount = (amount) ? Helper.correctPrice(amount) : 0;
                if (amount > 0) {
                    if (DiscountModel.cartBaseDiscountAmount() != amount) {
                        DiscountModel.appliedPromotion(true);
                        DiscountModel.appliedDiscount(false);
                        DiscountModel.cartDiscountType(DiscountModel.DISCOUNT_TYPES.FIXED);
                        DiscountModel.cartBaseDiscountAmount(amount);
                        DiscountModel.cartDiscountAmount(Helper.convertPrice(amount));
                        Totals.updateDiscountTotal();
                        DiscountModel.process(Totals.baseDiscountAmount());
                        Helper.dispatchEvent('reset_payments_data', '');
                    }
                } else {
                    if (DiscountModel.cartBaseDiscountAmount() != amount && DiscountModel.appliedPromotion()) {
                        DiscountModel.reset();
                        Totals.updateDiscountTotal();
                        DiscountModel.process(Totals.baseDiscountAmount());
                        Helper.dispatchEvent('reset_payments_data', '');
                    }
                }
            },
            loadShippingOnline: function () {
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                self.loading(true);
                CheckoutResource().setPush(true).setLog(false).getShipping(params, deferred);
                deferred.always(function () {
                    self.loading(false);
                });
                return deferred;
            },
            loadPaymentOnline: function () {
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                self.loading(true);
                CheckoutResource().setPush(true).setLog(false).getPayment(params, deferred);
                deferred.always(function () {
                    self.loading(false);
                });
                return deferred;
            },
            loadCartDataOnline: function (section) {
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                params.section = (section) ? section : [
                    'items',
                    'totals',
                    'shipping',
                    'payment'
                ];
                self.loading(true);
                selectCustomerCheckout({'id':params.customer_id});
                CheckoutResource().setPush(true).setLog(false).getCartData(params, deferred);
                deferred.done(function (response) {
                    if (response.status == CartModel.DATA.STATUS.ERROR && response.messages) {
                        CartModel.hasErrors(true);
                        CartModel.errorMessages(response.messages);
                    } else {
                        CartModel.hasErrors(false);
                        CartModel.errorMessages('');
                    }
                }).always(function () {
                    self.loading(false);
                });
                return deferred;
            },
            saveShippingMethodOnline: function (code) {
                var deferred = $.Deferred();
                if (code) {
                    var self = this;
                    var params = CartModel.getQuoteInitParams();
                    params.shipping_method = code;
                    self.loading(true);
                    CheckoutResource().setPush(true).setLog(false).saveShippingMethod(params, deferred);
                    deferred.always(function () {
                        self.loading(false);
                    });

                }
                return deferred;
            },
            savePaymentMethodOnline: function (code) {
                var self = this;
                var deferred = $.Deferred();
                if (code && (self.loadingPayment != code)) {
                    var params = CartModel.getQuoteInitParams();
                    params.payment_method = code;
                    self.loading(true);
                    self.loadingPayment = code;
                    CheckoutResource().setPush(true).setLog(false).savePaymentMethod(params, deferred);
                    deferred.done(function () {
                        self.paymentCode(code);
                    }).always(function () {
                        self.loadingPayment = '';
                        self.loading(false);
                    });
                }
                return deferred;
            },
            placeOrderOnline: function () {
                var self = this;
                var deferred = $.Deferred();
                if (CartModel.hasErrors() && CartModel.errorMessages()) {
                    CheckoutResource().processResponseMessages(CartModel.errorMessages(), 0);
                } else {
                    var params = CartModel.getQuoteInitParams();
                    params.payment = self.getPaymentDataToPlaceOrder();
                    params.quote_data = self.getQuoteDataToPlaceOrder();
                    params.actions = {
                        create_invoice: self.createInvoice(),
                        create_shipment: self.createShipment(),
                        delivery_time: self.getDeliverytime()
                    };
                    params.integration = [];
                    Event.dispatch('webpos_place_order_online_before', params);
                    self.loading(true);
                    CheckoutResource().setPush(true).setLog(false).placeOrder(params, deferred);
                    deferred.done(function (response) {
                        if (response.status && response.data) {
                            if(response.data.paynl_status_url){
                                PaynlInstore.saveOrderSuccess(response.data, $.proxy(self.placeOrderSuccess, self));
                                return;
                            }
                            if (response.data.directpost) {
                                DirectPost.saveOrderSuccess(response.data, $.proxy(self.placeOrderSuccess, self));
                            } else {
                                self.placeOrderSuccess(response.data);
                            }
                        }
                    }).always(function () {
                        if (!self.isAuthorizeNetDirectpost()) {
                            self.loading(false);
                        }
                    });
                }
                return deferred;
            },
            isAuthorizeNet: function () {
                var self = this;
                return (self.paymentCode() == 'authorizenet') ? true : false;
            },
            useNlPayInStorePayment: function(){
                var self = this;

                if (self.paymentCode() === PaynlInstore.MULTIPLE_PAYMENT_CODE) {
                    return !!self.getPaymentsData().find((payment) => {
                        return payment.code === PaynlInstore.CODE;
                    });
                }

                return self.paymentCode() === PaynlInstore.CODE;
            },
            isAuthorizeNetDirectpost: function () {
                var self = this;
                return (self.paymentCode() == 'authorizenet_directpost') ? true : false;
            },
            isPaypalDirect: function () {
                var self = this;
                return (self.paymentCode() == 'paypal_direct') ? true : false;
            },
            isAllowToCreateInvoice: function () {
                var self = this;
                return (
                    !self.isAuthorizeNet() &&
                    !self.isAuthorizeNetDirectpost() &&
                    !self.useNlPayInStorePayment() &&
                    !self.isPaypalDirect()
                );
            },
            startAuthorizeNetPayment: function () {
                DirectPost.openAuthorizePopup();
            },
            startNlPayInStorePayment: function(){
                PaynlInstore.openPopup();
            },

            placeOrderSuccess: function (orderData) {
                if (orderData) {
                    var self = this;
                    if (orderData.increment_id) {
                        var message = Helper.__('Order has been created successfully ') + "#" + orderData.increment_id;
                        AddNoti(message, true, "success", Helper.__('Message'));
                    }
                    self.createOrderResult(orderData);
                    Event.dispatch('webpos_place_order_online_after', {data: orderData});
                }

            },
            applyCouponOnline: function () {
                var deferred = $.Deferred();
                var self = this;
                if (DiscountModel.couponCode()) {
                    deferred = self._afterSaveCart(self._applyCouponOnline);
                } else {
                    deferred = self.cancelCouponOnline();
                }
                return deferred;
            },
            _applyCouponOnline: function () {
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                params.coupon_code = DiscountModel.couponCode();
                CartModel.loading(true);
                CheckoutResource().setPush(true).setLog(false).applyCoupon(params, deferred);
                deferred.done(function (response) {
                    if (response.status) {
                        DiscountModel.appliedPromotion(true);
                    }
                }).always(function () {
                    CartModel.loading(false);
                });
                return deferred;
            },
            cancelCouponOnline: function () {
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                self.loading(true);
                CheckoutResource().setPush(true).setLog(false).cancelCoupon(params, deferred);
                deferred.done(function (response) {
                    if (response.status) {
                        DiscountModel.couponCode('');
                        DiscountModel.appliedPromotion(false);
                    }
                }).always(function () {
                    self.loading(false);
                });
                return deferred;
            },
            selectCustomerOnline: function () {
                var self = this;
                if (self.selectingCustomer()) {
                    return false;
                }
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                params.customer = CartModel.getQuoteCustomerParams();
                self.loading(true);
                self.selectingCustomer(true);
                CheckoutResource().setPush(true).setLog(false).selectCustomer(params, deferred);
                deferred.always(function () {
                    self.loading(false);
                    self.selectingCustomer(false);
                });
                return deferred;
            },
            /**
             * Check apply discount online
             * @returns {*}
             */
            applyCartDiscountOnline: function () {
                var self = this;
                var deferred = self._afterSaveCart(self._applyCartDiscountOnline);
                return deferred;
            },
            /**
             * Process apply discount online
             * @returns {*}
             */
            _applyCartDiscountOnline: function () {
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                params.quote_data = DiscountModel.getApplyOnlineParams();
                CartModel.loading(true);
                CheckoutResource().setPush(true).setLog(false).saveQuoteData(params, deferred);
                deferred.always(function () {
                    CartModel.loading(false);
                });
                return deferred;
            },
            /**
             * Call function after save cart
             * @param callback
             * @returns {*}
             * @private
             */
            _afterSaveCart: function (callback, args, saveBeforeRemove) {
                var deferred = $.Deferred();
                var self = this;
                if (CartModel.currentPage() == CartModel.PAGE.CHECKOUT) {
                    deferred = callback(args);
                } else {
                    CartModel.saveCartOnline(saveBeforeRemove).done(function () {
                        deferred = callback(args);
                    });
                }
                return deferred;
            },
            /**
             * Save cart and save note to quote
             * @returns {*}
             */
            saveCustomerNote: function () {
                var self = this;
                var deferred = self._afterSaveCart(self._saveCustomerNote, self.orderComment());
                return deferred;
            },
            /**
             * Call api to save
             * @returns {*}
             * @private
             */
            _saveCustomerNote: function (comment) {
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                params.quote_data = {customer_note: comment};
                CartModel.loading(true);
                CheckoutResource().setPush(true).setLog(false).saveQuoteData(params, deferred);
                deferred.always(function () {
                    CartModel.loading(false);
                });
                return deferred;
            },
            loadCurrentQuote: function () {
                var self = this;
                var params = CartModel.getQuoteInitParams();
                // var customerData = CartModel.getCustomerInitParams();
                // if(customerData){
                //     if(customerData.billing_address){
                //         self.saveBillingAddress(customerData.billing_address);
                //     }
                //     if(customerData.shipping_address){
                //         self.saveShippingAddress(customerData.shipping_address);
                //     }
                // }
                if (params.quote_id) {
                    return self.loadCartDataOnline();
                }

                return $.Deferred().resolve({});
            },
            getPaymentDataToPlaceOrder: function () {
                var self = this;

                var payment = {
                    method: self.paymentCode(),
                    method_data: self.getPaymentsData()
                }
                if (self.isCreditCardPayment()) {
                    Event.dispatch('resave_credit_card_data', '');
                    payment = CreditCard.getData();
                    payment.method_data = self.getPaymentsData();
                    payment.method = self.paymentCode();
                }
                if (payment.method === 'stripe_integration') {
                    payment.method = 'multipaymentforpos';
                }
                return payment;
            },
            getQuoteDataToPlaceOrder: function () {
                var self = this;
                var quote_data = {
                    webpos_delivery_date: self.getDeliverytime()
                };
                Event.dispatch('prepare_quote_data_to_place_order', quote_data);
                return quote_data;
            }
        };

        if (window.webposConfig && window.webposConfig['staffId']) {
            CheckoutModel.initDefaultData();
            /* todo: if reloading quote then force go checkout */
            CheckoutModel.loadCurrentQuote().then(function () {
            if(Helper.isOnlineCheckout() && Cookies.get('check_login')){
                    CartModel.saveCartBeforeCheckoutOnline();
                } else {
                    Event.dispatch('go_to_checkout_page', '', true);
                }
            });
        }
        window.WebposCheckoutModel = CheckoutModel;
        return CheckoutModel;
    }
);