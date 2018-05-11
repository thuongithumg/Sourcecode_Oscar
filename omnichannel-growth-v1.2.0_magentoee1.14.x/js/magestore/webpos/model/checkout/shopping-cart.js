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
        'model/checkout/cart',
        'model/checkout/checkout',
        'eventManager',
        'dataManager',
        'model/resource-model/magento-rest/checkout/shopping-cart',
        'helper/general'
    ],
    function ($, ko, CartModel, CheckoutModel, Event, DataManager, ShoppingCartResource, Helper) {
        "use strict";

        var ShoppingCartItem = function(data){
            var self = this;
            var name = (data && data.name)?data.name:0;
            var qty = (data && data.qty)?parseFloat(data.qty):0;
            var unit_price = (data && data.unit_price)?parseFloat(data.unit_price):0;
            var remove_this = (data && data.remove_this)?data.remove_this:false;
            var add_this = (data && data.add_this)?data.add_this:false;
            self.data = ko.observable(data.data);
            self.remove_this = ko.observable(remove_this);
            self.add_this = ko.observable(add_this);
            self.name = ko.observable(name);
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
            checkoutModel: null,
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
                CartModel.customerId.subscribe(function(customerId){
                    if(customerId && !self.isUseGuestCustomer({id:customerId}) && Helper.isOnlineCheckout()){
                        self.refresh();
                    }
                });
                self.totalNumberItems = ko.pureComputed(function(){
                    var qty = 0;
                    $.each(self.items(), function(index, item){
                        qty += item.qty();
                    });
                    return qty;
                });
                self.removeItemIds = ko.pureComputed(function(){
                    var itemIds = [];
                    $.each(self.items(), function(index, item){
                        if(item.remove_this()){
                            itemIds.push(item.data().item_id);
                        }
                    });
                    return itemIds;
                });
                self.needAddItemIds = ko.pureComputed(function(){
                    var itemIds = [];
                    $.each(self.items(), function(index, item){
                        if(item.add_this()){
                            itemIds.push(item.data().item_id);
                        }
                    });
                    return itemIds;
                });
            },
            getShopppingCartParams: function(){
                var self = this;
                var params = CartModel.getQuoteInitParams();
                params.customer_id = CartModel.customerId();
                params.remove_ids = self.removeItemIds();
                params.move_ids = self.needAddItemIds();
                return params;
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
                var self = this;
                self.loading(true);
                var apiRequest = $.Deferred();
                if(!CheckoutModel){
                    CheckoutModel = this.checkoutModel;
                }
                CheckoutModel._afterSaveCart(function(){
                    var params = self.getShopppingCartParams();
                    ShoppingCartResource().setPush(true).setLog(false).updateShoppingCartItems(params, apiRequest);
                    apiRequest.done(
                        function (response) {
                            self.processItemsResponse(response);
                            if(response && (response.status == self.DATA.STATUS.SUCCESS)){
                                Event.dispatch('close_online_shopping_cart_popup', '');
                            }
                        }
                    ).always(function(){
                        self.loading(false);
                    });
                });
                return apiRequest;
            },
            processItemsResponse: function(response){
                var self = this;
                if(response && response.data){
                    if(response.data.cart_items){
                        self.items([]);
                        $.each(response.data.cart_items, function(itemId, itemData){
                            var unitPrice = (itemData.base_original_price)?itemData.base_original_price:((Helper.isProductPriceIncludesTax())?itemData.base_price_incl_tax:(itemData.base_price_incl_tax - itemData.base_tax_amount));
                            self.items.push(new ShoppingCartItem({
                                name: itemData.name,
                                qty: itemData.qty,
                                unit_price: unitPrice,
                                data: itemData
                            }));
                        });
                    }
                }
            },
            isUseGuestCustomer: function(customer){
                var customerId = (customer)?customer.id:CartModel.customerId();
                var defaultCustomer = DataManager.getData('default_customer');
                return (customerId && defaultCustomer && (parseInt(customerId) == parseInt(defaultCustomer.id)))?true:false;
            }
        };
        return ShoppingCartModel.initialize();
    }
);