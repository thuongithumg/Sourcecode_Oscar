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
define([
    'ko',
    'jquery',
    'posComponent',
    'model/checkout/cart',
    'model/checkout/shopping-cart',
    'model/checkout/checkout',
    'model/checkout/cart/items',
    'model/customer/current-customer',
    'model/appConfig',
    'eventManager',
    'helper/general',
    'lib/jquery/jquery.fullscreen'
], function (ko, $, Component, CartModel, ShoppingCartModel, CheckoutModel, Items, currentCustomer, AppConfig, Event, Helper) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ui/checkout/cart'
        },
        numberItemsInShoppingCart: ShoppingCartModel.totalNumberItems,
        /**
         * Check if is using online
         */
        isOnlineCheckout: Helper.isOnlineCheckout,
        /**
         * Flag to show additional actions
         */
        showAdditional: ko.observable(false),
        /**
         * Flag to check the items component has been rendered
         */
        renderedItems: ko.observable(false),
        /**
         * Flag to check the totals component has been rendered
         */
        renderedTotals: ko.observable(false),
        /**
         * Current customer name
         */
        currentCustomerName: ko.pureComputed(function() {
            return currentCustomer.fullName();
        }),
        /**
         * Current customer ID
         */
        currentCustomerId: ko.pureComputed(function() {
            return CartModel.customerId();
        }),
        /**
         * Flag to check customer ID existing
         */
        isShowCustomerId: ko.pureComputed(function() {
            return currentCustomer.customerId()!= 0;
        }),

        /* Can edit customer or not*/
        isCanEditCustomer: ko.pureComputed(function () {
            return currentCustomer.customerId() != window.webposConfig.guestCustomerId;
        }),
        /**
         * Current page (cart/checkout)
         */
        currentPage: CartModel.currentPage,
        /**
         * Check if is on checkout page
         */
        isOnCheckoutPage: CartModel.isOnCheckoutPage,
        /**
         * Cart title (number of items in cart)
         */
        cartTitle: ko.pureComputed(function() {
            return " ("+ CartModel.totalItems() + ")";
        }),
        /**
         * Constructor
         */
        initialize: function () {
            this._super();
            var self = this;
            this.renderedPage = ko.pureComputed(function(){
                return (self.renderedItems() && self.renderedTotals() && CartModel.loading() != true);
            });
            if(!this.currentPage()){
                this.currentPage(CartModel.PAGE.CART);
            }
            this.createdOrder = ko.pureComputed(function(){
                return CheckoutModel.isCreatedOrder();
            });
            Event.observer('go_to_checkout_page', function(){
                self.switchToCheckout();
            });
            Event.observer('go_to_cart_page', function(){
                self.switchToCart();
                Event.dispatch('focus_search_input', '');
            });
            Event.observer('start_new_order', function(){
                self.switchToCart();
                self.emptyCart();
                Event.dispatch('focus_search_input', '');
            });
            Event.observer('save_cart_online_after', function(event, data){
                if(data && data.response && data.response.status){
                    Event.dispatch('go_to_checkout_page', '', true);
                }
            });
        },
        /**
         * Animate container to checkout page
         */
        goToCheckoutPage: function(){
            var checkoutSection = $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_SECTION);
            if(checkoutSection.length > 0){
                checkoutSection.addClass(AppConfig.CLASS.ACTIVE);
                var mainContainer = $(AppConfig.MAIN_CONTAINER);
                if(mainContainer.length > 0) {
                    var categoryWith = mainContainer.find(AppConfig.ELEMENT_SELECTOR.COL_LEFT).width();
                    mainContainer.css({
                        left: "-" + categoryWith + "px"
                    });
                }
                $('#popup-change-customer').addClass(AppConfig.CLASS.ACTIVE_ON_CHECKOUT);
            }
        },
        /**
         * Animate container to cart page
         */
        goToCartPage: function(){
            var checkoutSection = $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_SECTION);
            if(checkoutSection.length > 0){
                checkoutSection.removeClass(AppConfig.CLASS.ACTIVE);
                var mainContainer = $(AppConfig.MAIN_CONTAINER);
                if(mainContainer.length > 0) {
                    mainContainer.css({
                        left: "0px"
                    });
                }
                $('#popup-change-customer').removeClass(AppConfig.CLASS.ACTIVE_ON_CHECKOUT);
            }
        },
        /**
         * Hide menu button
         */
        hideMenuButton: function(){
            var showMenuButton = $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON);
            if(showMenuButton.length > 0){
                showMenuButton.hide();
                showMenuButton.addClass(AppConfig.CLASS.HIDE);
            }
        },
        /**
         * Show menu button
         */
        showMenuButton: function(){
            var showMenuButton = $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON);
            if(showMenuButton.length > 0){
                showMenuButton.show();
                showMenuButton.removeClass(AppConfig.CLASS.HIDE);
            }
        },
        /**
         * Animate UI
         */
        transformInterface: function(){
            var self = this;
            switch(self.currentPage()){
                case CartModel.PAGE.CART:
                    self.goToCartPage();
                    self.showMenuButton();
                    break;
                case CartModel.PAGE.CHECKOUT:
                    self.goToCheckoutPage();
                    self.hideMenuButton();
                    break;
            }
        },
        /**
         * Start switch to cart page
         */
        switchToCart: function(){
            Event.dispatch('before_switch_to_cart', '');
            this.currentPage(CartModel.PAGE.CART);
            this.transformInterface();
            var mainContainer = $(AppConfig.MAIN_CONTAINER);
            if(mainContainer.length > 0){
                mainContainer.addClass(AppConfig.CLASS.SHOW_MENU);
            }
            Event.dispatch('after_switch_to_cart', '');
        },
        /**
         * Start switch to checkout page
         */
        switchToCheckout: function(){
            if(Items.isEmpty()){
                return;
            }else{
                Event.dispatch('before_switch_to_checkout', '');
                this.currentPage(CartModel.PAGE.CHECKOUT);
                this.transformInterface();
                var mainContainer = $(AppConfig.MAIN_CONTAINER);
                if(mainContainer.length > 0){
                    mainContainer.removeClass(AppConfig.CLASS.SHOW_MENU);
                }
            }
            Event.dispatch('after_switch_to_checkout', '');
        },
        /**
         * Hide list actions
         */
        hideAddtitionalActions: function(){
            this.showAdditional(false);
        },
        /**
         * Show list actions
         */
        showAddtitionalActions: function(){
            this.showAdditional(true);
        },
        /**
         * After render
         */
        afterRenderCart: function(){

        },
        /**
         * Show form change customer
         */
        changeCustomer: function () {
            var commentPopup =  $('#popup-change-customer');
            if(commentPopup.length > 0){
                commentPopup.addClass('fade-in');
                commentPopup.posOverlay({
                    onClose: function(){
                        commentPopup.removeClass('fade-in');
                        $('.notification-bell').show();
                    }
                });

                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            }



            Helper.dispatchEvent('checkout_customer_list_show_after',{});
        },
        /**
         * Show form edit customer
         */
        editCustomer: function () {
            var self = this;
            if(!self.isOnCheckoutPage()){
                $('#form-edit-customer').removeClass('fade');
                $('#form-edit-customer').addClass('fade-in');
                $('#form-edit-customer').addClass('show');
                $('#form-edit-customer').posOverlay({
                    onClose: function(){
                        $('#form-edit-customer').addClass('fade');
                        $('#form-edit-customer').removeClass('fade-in');
                        $('#form-edit-customer').removeClass('show');
                    }
                });

                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            }
        },
        /**
         * Empty cart
         */
        emptyCart: function(){
            if(Helper.isOnlineCheckout()){
                CartModel.removeCartOnline();
            }else{
                CartModel.emptyCart();
            }
            Event.dispatch('focus_search_input', '');
        },
        /**
         * Show comment popup
         */
        showAddCommentPopup: function(){
            Event.dispatch('show_comment_popup', '');
            this.hideAddtitionalActions();
        },
        /**
         * Enter/exit full screen mode
         */
        toggleFullscreen: function(){
            $(document).toggleFullScreen();
        },
        /**
         * Show customer pending orders
         */
        showCustomerPendingOrders: function(){
            var customerEmail = currentCustomer.customerEmail();
            Event.dispatch('show_customer_pending_orders', customerEmail);
        },
        /**
         * Show shopping cart popup
         */
        showShoppingCart: function(){
            Event.dispatch('show_online_shopping_cart_popup', '');
        }
    });
});