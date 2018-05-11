/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/resource-model/magento-rest/checkout/shopping-cart',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/items'
    ],
    function ($, ko, CartModel, CheckoutModel, Event, ShoppingCartResource, Helper, CartItems) {
        "use strict";

        var ShoppingCartItem = function(data){
            var self = this;
            var name = (data && data.name)?data.name:0;
            var qty = (data && data.qty)?parseFloat(data.qty):0;
            var image_url = (data && data.image_url)?data.image_url:'';
            var unit_price = (data && data.unit_price)?parseFloat(data.unit_price):0;
            var remove_this = (data && data.remove_this)?data.remove_this:false;
            var add_this = (data && data.add_this)?data.add_this:false;
            this.reoder_customer_id = null;
            self.data = ko.observable(data.data);
            self.remove_this = ko.observable(remove_this);
            self.add_this = ko.observable(add_this);
            self.name = ko.observable(name);
            self.image_url = ko.observable(image_url);
            self.qty = ko.observable(qty);
            self.unit_price = ko.observable(unit_price);
            self.row_total = ko.pureComputed(function(){
                return Helper.convertPrice(self.unit_price()) * self.qty();
            });
            self.unit_price_formated = ko.pureComputed(function(){
                return Helper.formatPrice(self.unit_price());
            });
            self.row_total_formated = ko.pureComputed(function(){
                return Helper.formatPrice(self.row_total());
            });
            return self;
        };

        var ShoppingCartModel = {
            items: ko.observableArray([]),
            loading: ko.observable(false),
            DATA:{
                STATUS: {
                    SUCCESS: '1',
                    ERROR: '0'
                }
            },
            initialize: function(){
                var self = this;
                self.initObserver();
                return self;
            },
            initObserver: function(){
                var self = this;
                Event.observer('checkout_select_customer_after', function(event, data){
                    if(Helper.isOnlineCheckout()) {
                        if(data && data.customer){
                            var isGuest = self.isUseGuestCustomer(data.customer);
                            if(!isGuest){
                                self.refresh();
                            }else{
                                self.items([]);
                            }
                        }
                    }
                });
                self.totalNumberItems = ko.pureComputed(function(){
                    var qty = 0;
                    $.each(self.items(), function(index, item){
                        qty += item.qty;
                    });
                    return qty;
                });
                self.removeItemIds = ko.pureComputed(function(){
                    var itemIds = [];
                    $.each(self.items(), function(index, item){
                        if(item.remove_this){
                            itemIds.push(item.data.item_id);
                        }
                    });
                    return itemIds;
                });
                self.needAddItemIds = ko.pureComputed(function(){
                    var itemIds = [];
                    $.each(self.items(), function(index, item){
                        if(item.add_this()){
                            itemIds.push(item.data.item_id);
                        }
                    });
                    return itemIds;
                });
            },
            getShopppingCartParams: function(){
                var self = this;
                var params = CartModel.getQuoteInitParams();
                params.customer_id = this.reoder_customer_id ? this.reoder_customer_id : CartModel.customerId();
                this.reoder_customer_id = null;
                params.section = [];
                return params;
            },
            addItemsToWebPosCart: function(){
                $.each(self.items(), function(index, item){
                    if(item.add_this()){
                        CartModel.addProduct(item.data());
                    }
                });
                return ;
            },
            refresh: function(){
                var self = this;
                if(self.loading()){
                    return false;
                }
                var params = self.getShopppingCartParams();
                self.loading(true);
                var apiRequest = $.Deferred();
                ShoppingCartResource().setPush(true).setLog(false).getShoppingCart(params, apiRequest);

                apiRequest.done(
                    function (response) {
                        self.processItemsResponse(response);
                    }
                ).always(function(){
                    self.loading(false);
                });
                return apiRequest;
            },
            submit: function(){
                var add = [];
                $.each(this.items(), function(index,item){
                    if(item.add_this()){
                        item.offline_item_id = parseInt(Math.random()*1000000000);
                        item.shopping_cart_online_item = true;
                        add.push(item);
                    }
                });
                CartItems.updateItemsFromQuote(add);
                return;

                var self = this;
                if(self.loading()){
                    return false;
                }
                var params = self.getShopppingCartParams();
                self.loading(true);
                var apiRequest = $.Deferred();
                ShoppingCartResource().setPush(true).setLog(false).updateShoppingCartItems(params, apiRequest);
                apiRequest.done(
                    function (response) {
                        CartItems.updateItemsFromQuote(add);
                    }
                ).always(function(){
                    self.loading(false);
                });
                return apiRequest;
            },
            processItemsResponse: function(response){
                if(response){
                    this.items([]);
                    $.each(response.items, function(index, itemData){
                        itemData.remove_this = ko.observable(false);
                        itemData.add_this = ko.observable(true);
                        itemData.offline_item_id = true;
                        itemData.unit_price_formated = ko.pureComputed(function(){
                            return Helper.formatPrice(itemData.price);
                        });
                        itemData.row_total_formated = ko.pureComputed(function(){
                            return Helper.formatPrice(itemData.row_total);
                        });
                        this.items.push(itemData);
                    }.bind(this));
                    CartModel.currentOnlineCartItem = this.items;
                }
            },
            isUseGuestCustomer: function(customer){
                return !CartModel.customerId();
            }
        };
        return ShoppingCartModel.initialize();
    }
);