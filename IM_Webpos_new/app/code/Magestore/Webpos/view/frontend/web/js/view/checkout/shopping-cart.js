/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'ko',
    'jquery',
    'uiComponent',
    'Magestore_Webpos/js/model/event-manager',
    'Magestore_Webpos/js/helper/general',
    'Magestore_Webpos/js/model/checkout/shopping-cart'
], function (ko, $, Component, Event, Helper, ShoppingCartModel) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magestore_Webpos/checkout/shopping-cart'
        },
        items: ShoppingCartModel.items,
        loading: ShoppingCartModel.loading,
        popupId: "webpos_shopping_cart",
        popup: ko.observable(),
        /**
         * Constructor
         */
        initialize: function () {
            this._super();
            var self = this;
            Event.observer('show_online_shopping_cart_popup', function(){
                self.open();
            });
            Event.observer('close_online_shopping_cart_popup', function(){
                self.close();
            });
        },
        open: function(){
            var shoppingCart = $('#webpos_shopping_cart');
            shoppingCart.addClass('fade-in');
            shoppingCart.addClass('show');
            shoppingCart.removeClass('fade');
            $('.wrap-backover').show();
            $('.notification-bell').hide();
            $('#c-button--push-left').hide();
        },
        close: function(){
            var shoppingCart = $('#webpos_shopping_cart');
            shoppingCart.removeClass('fade-in');
            shoppingCart.removeClass('show');
            shoppingCart.addClass('fade');
            $('.wrap-backover').hide();
            $('.notification-bell').show();
            $('#c-button--push-left').show();
        },
        update: function(){
            ShoppingCartModel.submit();
            this.close();
        },
        initData: function(element){

        },
        refreshData: function(){
            ShoppingCartModel.refresh();
        }
    });
});