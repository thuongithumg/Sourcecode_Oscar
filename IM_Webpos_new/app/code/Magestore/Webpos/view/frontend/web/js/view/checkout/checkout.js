/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/layout',
        'mageUtils',
        'uiComponent',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/view/settings/general/checkout/auto-check-promotion-rules',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
        'Magestore_Webpos/js/model/checkout/multiorder',
        'Magestore_Webpos/js/action/checkout/cancel-tab',
        'mage/validation'
    ],
    function ($, ko, ViewManager, utils, Component, CheckoutModel, CartModel, Helper, AutoCheckPromotion, TotalsFactory, multiOrder, CancelOnhold) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout'
            },
            payAble: ko.observable(true),
            askSendSaleEmail: window.webposConfig['webpos/general/ask_for_sale_order_email']=='1',
            sendSaleEmail: ko.observable(true),
            initialize: function(){
                this._super();
                Helper.observerEvent('go_to_checkout_page', function(){
                    var autoCheckPromotion =  Helper.getLocalConfig(AutoCheckPromotion().configPath);
                    if(!Helper.isUseOnline('checkout') && autoCheckPromotion == true){
                        CheckoutModel.autoCheckPromotion();
                    }
                });
                Helper.observerEvent('checkout_select_customer_after', function(event, data){
                    var autoCheckPromotion =  Helper.getLocalConfig(AutoCheckPromotion().configPath);
                    if(autoCheckPromotion == true){
                        if(!Helper.isUseOnline('checkout') && Helper.isOnCheckoutPage()) {
                            CheckoutModel.autoCheckPromotion();
                        }
                    }
                });
                CheckoutModel.autoCheckingPromotion.subscribe(function(value){
                    if(value == true){
                        $('#checkout-loader').show();
                    }else{
                        $('#checkout-loader').hide();
                    }
                });
                var self = this;
                TotalsFactory.get().grandTotal.subscribe(function(value){
                    self.payAble((value > 0)?true:false);
                });

                Helper.observerEvent('start_place_order', function(){
                    self.placeOrder();
                });
            },
            loading: ko.pureComputed(function(){
                return (CheckoutModel.loading() == true)?true:false;
            }),
            cartTotal: ko.pureComputed(function(){
                return Helper.formatPrice((TotalsFactory.get().getTotalValue('grand_total')) ? TotalsFactory.get().getTotalValue('grand_total'):0);
            }),
            remainTotal: ko.pureComputed(function(){
                return Helper.formatPrice((CheckoutModel.remainTotal()) ? Math.abs(CheckoutModel.remainTotal()) : 0);
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
            fulFill: ko.pureComputed(function(){
                var canFulfill = parseInt(window.webposConfig.online_data.fulfill_online);
                return (canFulfill)?true:false;
            }),
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
                    return 'Remain money';
                return 'Expected Change';
            }),
            canPaid: ko.pureComputed(function(){
                var canCreate = true;
                if(CheckoutModel.remainTotal() > 0 || !CheckoutModel.isAllowToCreateInvoice()) {
                    canCreate = false;
                }
                CheckoutModel.createInvoice(canCreate);
                var select = $('#can_paid');
                if($('#can_paid').find('.ios-ui-select') != undefined){
                    var bootstrapSlide = $('#can_paid').find('.ios-ui-select');
                    if(canCreate == true){
                        bootstrapSlide.addClass('checked');
                    }else{
                        bootstrapSlide.removeClass('checked');
                    }
                }
                return canCreate;
            }),
            canSendEmail: ko.pureComputed(function(){
                var sendSaleEmail = true;
                if(!CheckoutModel.sendSaleEmail()) {
                    sendSaleEmail = false;
                }
                if($('#can_send_email').find('.ios-ui-select') != undefined){
                    var bootstrapSlide = $('#can_send_email').find('.ios-ui-select');
                    if(sendSaleEmail){
                        bootstrapSlide.addClass('checked');
                    }else{
                        bootstrapSlide.removeClass('checked');
                    }
                }
                return sendSaleEmail;
            }),
            canShip: ko.pureComputed(function(){
                var canCreate = true;
                if(!CheckoutModel.createShipment()) {
                    canCreate = false;
                }
                var createShipButton = $('#can_ship');
                if(createShipButton.length > 0 && createShipButton.find('.ios-ui-select') != undefined){
                    var bootstrapSlide = createShipButton.find('.ios-ui-select');
                    if(canCreate == true){
                        bootstrapSlide.addClass('checked');
                    }else{
                        bootstrapSlide.removeClass('checked');
                    }
                }
                return canCreate;
            }),
            placeOrder: function(){
                if(CheckoutModel.placingOrder() == true){
                    Helper.alert({
                        priority: "warning",
                        title: "Message",
                        message: "Placing order, please wait..."
                    });
                    return;
                }
                if((!CheckoutModel.selectedPayments() || CheckoutModel.selectedPayments().length <= 0)
                   && TotalsFactory.get().getTotalValue('grand_total') > 0){
                    Helper.alert({
                        priority: "danger",
                        title: "Message",
                        message: "Please select the payment method"
                    });
                    return;
                }
                if (!this.validateForm('#form-checkout-creditcard')) {
                    return;
                }
                if(!CheckoutModel.selectedShippingCode()){
                    CheckoutModel.useWebposShipping();
                }

                if (multiOrder.currentId()){

                    CancelOnhold(multiOrder.currentOrderData());
                    multiOrder.currentId(0);
                    multiOrder.currentOrderData({});
                }
                if(Helper.isUseOnline('checkout')){
                    if(!CartModel.hasOnlineQuote()){
                        Helper.alert({
                            priority: "danger",
                            title: "Message",
                            message: "The quote does not exist for online checkout"
                        });
                        return false;
                    }
                    if(this.isHasCreditCardPayment(CheckoutModel.selectedPayments())) {
                        CheckoutModel.startAuthorizePopup();
                    }
                    if(!CheckoutModel.isPrepaid()){
                        CheckoutModel.placeOrderOnline();
                    }
                    return true;
                }else{
                    if(this.isHasCreditCardPayment(CheckoutModel.selectedPayments())) {
                        CheckoutModel.submitOrderOnline();
                        CheckoutModel.startAuthorizePopup();
                        return;
                    }
                }
                if(!CartModel.canCheckoutStorecredit()){
                    Helper.alert({
                        priority: "danger",
                        title: "Message",
                        message: "Please select customer"
                    });
                    return;
                }
                CheckoutModel.createOrder();
            },
            isHasCreditCardPayment: function(selectedPayments){
                var hasCreditCartPayment = false;
                $.each(selectedPayments, function (key, item) {
                    if(item.type == 1){
                        hasCreditCartPayment = true;
                    }
                });
                return hasCreditCartPayment;
            },
            initCheckboxStyle: function(){
                $(".ios").iosCheckbox();
            },
            afterRenderButton: function () {
                $('.checkout-footer .add-payment').click(function (event) {
                    var ptop = event.pageY - 130;
                    $("#add-more-payment").addClass('fade-in');

                    $(".wrap-backover").show();
                });
                $('.wrap-backover').click(function () {
                    $(".popup-for-right").hide();
                    $(".popup-for-right").removeClass('fade-in');
                    $(".wrap-backover").hide();
                    $('.notification-bell').show();
                    if($('#checkout_container').hasClass('showMenu')){
                        $('#c-button--push-left').show();
                        $('#c-button--push-left').removeClass('hide');
                    }else{
                        $('#c-button--push-left').hide();
                        $('#c-button--push-left').addClass('hide');
                    }
                });
            },
            afterRenderCheckout: function(){
                CheckoutModel.initDefaultData();
            },
            createInvoice: function(data,event){
                var createInvoice = (event.target.checked) ? true : false;
                CheckoutModel.createInvoice(createInvoice);
            },
            changeCanSendEmail: function(data,event){
                var sendSaleEmail = (event.target.checked) ? true : false;
                CheckoutModel.sendSaleEmail(sendSaleEmail);
            },
            createShipment: function(data,event){
                var createShipment = (event.target.checked)?true:false;
                CheckoutModel.createShipment(createShipment);
            },
            createFulfillOnline: function(data,event){
                var fulFill = (event.target.checked)?true:false;
                CheckoutModel.createFulFill(fulFill);
            },
            /* Validation Form*/
            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            },
            addMorePayments: function () {
                ViewManager.getSingleton('view/checkout/checkout/payment_popup')._prepareItems();
                $("#add-more-payment").addClass('fade-in');
                $(".wrap-backover").show();
                $(".notification-bell").hide();
            },
            showPayments: ko.pureComputed(function(){
                if(TotalsFactory.get().getTotalValue('grand_total') > 0 || TotalsFactory.get().hasSpecialDiscount() == true)
                    return true;
                return false;
            })
        });
    }
);