/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'require',
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/customer/current-customer',
        'Magestore_Webpos/js/model/resource-model/magento-rest/checkout/checkout'
    ],
    function (require, $, ko, Component, Items, CartModel, currentCustomer, CheckoutResource) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/online-cart'
            },
            getItems: Items.items,
            initialize: function () {
                this._super();
                this.OUT_OF_STOCK_MESSAGE = "out of stock";
            },
            remove: function(item,event){

            },
            afterRenderCart: function(){

            },
            merge: function(){

            },
            loadCartDataOnline: function(section){

            },
            hide: function(){
                $('#customer-online-cart').addClass('fade');
                $('#customer-online-cart').removeClass('fade-in');
                $('#customer-online-cart').removeClass('show');
                $('.wrap-backover').hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
            }
        });
    }
);