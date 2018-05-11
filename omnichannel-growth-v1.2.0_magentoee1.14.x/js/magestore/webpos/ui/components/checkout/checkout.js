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
        'model/checkout/checkout/payment',
        'model/checkout/cart',
        'model/checkout/cart/totals',
        'helper/general',
        'model/appConfig'
    ],
    function ($, ko, Component, CheckoutModel, PaymentModel, CartModel, Totals, Helper, AppConfig) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'ui/checkout/checkout'
            },
            initialize: function(){
                this._super();
                var self = this;
                self.cannotAddPayment = ko.pureComputed(function(){
                    if (PaymentModel.isStripeIntegration()) {
                        return false;
                    } else {
                        return (PaymentModel.showCcForm() || CheckoutModel.remainTotal() <= 0 || !PaymentModel.hasSelectedPayment())?true:false;
                    }
                });
                Helper.observerEvent('go_to_checkout_page', function(){
                    var autoCheckPromotion =  Helper.isAutoCheckPromotion();
                    if(autoCheckPromotion == true){
                        CheckoutModel.autoCheckPromotion();
                    }
                });
                Helper.observerEvent('hide_payment_popup', function(){
                    $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_ADD_PAYMENT_POPUP).posOverlay({
                        onClose: function(){
                            self.showPaymentPopup(false);
                        }
                    }).close();
                });
            },
            showPaymentPopup: ko.observable(false),
            loading: ko.pureComputed(function(){
                return (CheckoutModel.loading() == true || CheckoutModel.autoCheckingPromotion() == true)?true:false;
            }),
            cartTotal: ko.pureComputed(function(){
                return Helper.convertAndFormatPrice((Totals.grandTotal()) ? Totals.grandTotal():0);
            }),
            remainTotal: ko.pureComputed(function(){
                return Helper.convertAndFormatPrice((CheckoutModel.remainTotal()) ? Math.abs(CheckoutModel.remainTotal()) : 0);
            }),
            selectedShippingTitle: ko.pureComputed(function(){
                return (CheckoutModel.selectedShippingTitle())?CheckoutModel.selectedShippingTitle():"";
            }),
            selectedShippingCode: ko.pureComputed(function(){
                return (CheckoutModel.selectedShippingCode())?CheckoutModel.selectedShippingCode():"";
            }),
            selectedShippingPrice: ko.pureComputed(function(){
                return (CheckoutModel.selectedShippingPrice())?CheckoutModel.selectedShippingPrice():"";
            }),
            shippingHeader: ko.pureComputed(function() {
                return "Shipping: "+CheckoutModel.selectedShippingTitle();
            }),
            shipAble: ko.pureComputed(function(){
                return (CartModel.isVirtual())?false:true;
            }),
            autoCreateShipment: CheckoutModel.autoCreateShipment,
            checkoutButtonLabel: ko.pureComputed(function(){
                var label = Helper.__("Place Order");
                if(Helper.toNumber(CheckoutModel.remainTotal()) > 0 && CheckoutModel.createInvoice() != true
                    && CheckoutModel.selectedPayments().length > 0){
                    label = Helper.__("Mark as partial");
                }
                return label;
            }),
            remainTitle: ko.pureComputed(function () {
                if(CheckoutModel.remainTotal() > 0)
                    return Helper.__('Remain money');
                return Helper.__('Expected Change');
            }),
            canPaid: ko.pureComputed(function(){
                var canCreate = true;
                if(CheckoutModel.remainTotal() > 0 || !CheckoutModel.isAllowToCreateInvoice()) {
                    canCreate = false;
                }
                CheckoutModel.createInvoice(canCreate);
                var createInvoiceButton = $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_CREATE_INVOICE_BUTTON);
                if(createInvoiceButton.length > 0 && createInvoiceButton.find(AppConfig.ELEMENT_SELECTOR.UI_SELECT_INPUT) != undefined){
                    var bootstrapSlide = createInvoiceButton.find(AppConfig.ELEMENT_SELECTOR.UI_SELECT_INPUT);
                    if(canCreate == true){
                        bootstrapSlide.addClass(AppConfig.CLASS.CHECKED);
                    }else{
                        bootstrapSlide.removeClass(AppConfig.CLASS.CHECKED);
                    }
                }
                return canCreate;
            }),
            canShip: ko.pureComputed(function(){
                var canCreate = true;
                if(!CheckoutModel.createShipment() || (!CartModel.canShipWarehousesItems() && !CheckoutModel.autoCreateShipment())) {
                    canCreate = false;
                    CheckoutModel.createShipment(false);
                }
                var createShipButton = $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_CREATE_SHIPMENT_BUTTON);
                if(createShipButton.length > 0 && createShipButton.find(AppConfig.ELEMENT_SELECTOR.UI_SELECT_INPUT) != undefined){
                    var bootstrapSlide = createShipButton.find(AppConfig.ELEMENT_SELECTOR.UI_SELECT_INPUT);
                    if(canCreate == true){
                        bootstrapSlide.addClass(AppConfig.CLASS.CHECKED);
                    }else{
                        bootstrapSlide.removeClass(AppConfig.CLASS.CHECKED);
                    }
                }
                return canCreate;
            }),
            placeOrder: function(){
                if(CheckoutModel.placingOrder() == true){
                    Helper.alert({
                        priority: "warning",
                        title: Helper.__("Message"),
                        message: Helper.__("Placing order, please wait...")
                    });
                    return false;
                }
                if(((!CheckoutModel.selectedPayments() || CheckoutModel.selectedPayments().length <= 0 )
                    && Totals.grandTotal() > 0) || !CheckoutModel.paymentCode()){
                    Helper.alert({
                        priority: "danger",
                        title: Helper.__("Message"),
                        message: Helper.__("Please select the payment method")
                    });
                    return false;
                }


                if(!CheckoutModel.selectedShippingCode()){
                    CheckoutModel.useWebposShipping();
                }

                // if (!this.validateForm('#form-checkout-creditcard')) {
                //     return;
                // }
                if(Helper.isOnlineCheckout()){
                    if(!CartModel.hasOnlineQuote()){
                        Helper.alert({
                            priority: "danger",
                            title: Helper.__("Message"),
                            message: Helper.__("The quote does not exist for online checkout")
                        });
                        return false;
                    }
                    if(CheckoutModel.isAuthorizeNetDirectpost()){
                        CheckoutModel.startAuthorizeNetPayment();
                    }

                    if(CheckoutModel.useNlPayInStorePayment()){
                        CheckoutModel.startNlPayInStorePayment();
                    }

                    CheckoutModel.placeOrderOnline();
                    return true;
                }
                if(!CartModel.canCheckoutStorecredit()){
                    Helper.alert({
                        priority: "danger",
                        title: Helper.__("Message"),
                        message: Helper.__("Please select customer")
                    });
                    return false;
                }
                CheckoutModel.createOrder();
            },
            initCheckboxStyle: function(){
                $(".ios").iosCheckbox();
            },
            afterRenderCheckout: function(){
                CheckoutModel.initDefaultData();
            },
            createInvoice: function(data,event){
                var createInvoice = (event.target.checked) ? true : false;
                CheckoutModel.createInvoice(createInvoice);
            },
            createShipment: function(data,event){
                var createShipment = (event.target.checked)?true:false;
                if(createShipment && !CartModel.canShipWarehousesItems()){
                    Helper.alert({
                        priority: "danger",
                        title: Helper.__("Message"),
                        message: Helper.__("Cannot ship the items from another warehouses at this time")
                    });
                }
                CheckoutModel.createShipment(createShipment);
            },
            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            },
            addMorePayments: function () {
                var self = this;
                self.showPaymentPopup(true);
                $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_ADD_PAYMENT_POPUP).posOverlay({
                    onClose: function(){
                        self.showPaymentPopup(false);
                    }
                });
            },
            showPayments: ko.pureComputed(function(){
                if(Totals.grandTotal() > 0 || Totals.hasSpecialDiscount() == true)
                    return true;
                return false;
            }),
            payAble: function () {
                if(Totals.grandTotal() > 0)
                    return true;
                return false;
            }
        });
    }
);