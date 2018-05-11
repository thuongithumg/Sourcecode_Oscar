/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/layout',
        'uiComponent',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/helper/alert',
        'Magestore_Webpos/js/action/cart/hold',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/shift/pos',
        'Magestore_Webpos/js/model/checkout/checkout'
    ],
    function ($, ko, ViewManager, Component, Items, Alert, Hold, Event, TotalsFactory, CartModel, Helper, PosManagement, CheckoutModel) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/cart/totals'
            },
            isZeroTotal: ko.pureComputed(function(){
                return (TotalsFactory.get().getTotalValue('grand_total'))?false:true;
            }),
            isOnCartPage: ko.pureComputed(function(){
                return (ViewManager.getSingleton('view/checkout/cart').currentPage() == ViewManager.getSingleton('view/checkout/cart').PAGE.CART)?true:false;
            }),
            isOnCheckoutPage: ko.pureComputed(function(){
                return (ViewManager.getSingleton('view/checkout/cart').currentPage() == ViewManager.getSingleton('view/checkout/cart').PAGE.CHECKOUT)?true:false;
            }),
            createdOrder: ko.pureComputed(function(){
                return (ViewManager.getSingleton('view/checkout/cart').createdOrder() == true)?true:false;
            }),
            initialize: function () {
                this._super();
            },
            getTotals: function(){
                return TotalsFactory.get().getDisplayTotals();
            },
            getButtons: function(){
                return TotalsFactory.get().getButtons();
            },
            totalItemClick: function(totalItem,event){
                if(this.createdOrder() == true){
                    return false;
                }
                var classes = event.target.getAttribute("class");
                if(totalItem.code() == TotalsFactory.get().ADD_DISCOUNT_TOTAL_CODE || totalItem.code() == TotalsFactory.get().DISCOUNT_TOTAL_CODE ){
                    if(Items.isEmpty()){
                        Alert({
                            priority: "warning",
                            title: "Warning",
                            message: "Please add item(s) to cart!"
                        });
                        Event.dispatch('focus_search_input', '');
                    }else{
                        if(!classes || (classes && classes.indexOf("icon-iconPOS-remove") < 0)){
                            ViewManager.getSingleton('view/checkout/cart').showCartDiscountPopup(event);
                        }
                    }
                }else{
                    if(this.isOnCartPage()){
                        Event.dispatch('focus_search_input', '');
                    }
                }
            },
            buttonClick: function(button){
                if(!PosManagement.validate()){
                    Event.dispatch('show_open_session_popup','');
                    return;
                }
                // ViewManager.getSingleton('view/checkout/checkout/integration/storecredit-ee').getBalance();
                if(button.code == TotalsFactory.get().HOLD_BUTTON_CODE){
                    Event.dispatch('focus_search_input', '');
                    if(Items.isEmpty()){
                        Alert({
                            priority: "warning",
                            title: "Warning",
                            message: "Please add item(s) to cart!"
                        });
                        return;
                    }else{
                        Hold.execute();
                        return;
                    }
                }
                if(button.code == TotalsFactory.get().CHECKOUT_BUTTON_CODE){
                    if(Items.isEmpty()){
                        Alert({
                            priority: "warning",
                            title: "Warning",
                            message: "Please add item(s) to cart!"
                        });
                        return;
                    }else{
                        if(Helper.isUseOnline('checkout')){
                            CartModel.saveCartBeforeCheckoutOnline();
                        }else {
                            ViewManager.getSingleton('view/checkout/cart').switchToCheckout();
                            ViewManager.getSingleton('view/checkout/checkout/shipping').saveDefaultShippingMethod();
                            ViewManager.getSingleton('view/checkout/checkout/payment').collection.reset();
                            Event.dispatch('go_to_checkout_page', '', true);
                        }
                        ViewManager.getSingleton('view/checkout/checkout/payment').saveDefaultPaymentMethod();
                        return;
                    }
                }
                if(button.code == TotalsFactory.get().BACK_CART_BUTTON_CODE){
                    ViewManager.getSingleton('view/checkout/cart').switchToCart();
                }
            },
            removeDiscount: function(){
                Event.dispatch('remove_cart_discount', '');
                if(this.isOnCartPage()){
                    Event.dispatch('focus_search_input', '');
                }
            },
            getAdditionalInfo: function() {
                return TotalsFactory.get().getAdditionalInfo();
            },
            removeTotal: function(el){
                if(el.actions() && el.actions().remove){
                    if(typeof el.actions().remove == 'string'){
                        if(typeof this[el.actions().remove] == 'function'){
                            this[el.actions().remove]();
                        }
                    }
                    if(typeof el.actions().remove == 'function'){
                        el.actions().remove();
                    }
                }
            },
            removeGiftCard: function(el){
                ViewManager.getSingleton('view/checkout/checkout/integration/gift-card-account').removeGiftCard(el.giftcardCode());
            },

            afterRender: function () {
                setTimeout(function(){
                    $('#order-items').height($(window).height() - $('#webpos_cart .o-header').outerHeight() - $('.grand-total-footer').outerHeight());
                }, 300);
            }
        });
    }
);