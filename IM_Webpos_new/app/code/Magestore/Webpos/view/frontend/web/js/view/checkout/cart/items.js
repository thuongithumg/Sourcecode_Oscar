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
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/checkout/cart/editpopup-factory',
        'Magestore_Webpos/js/helper/general'
    ],
    function ($, ko, ViewManager, Component, CartModel, Items, EditpopupFactory, Helper) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/cart/items'
            },
            initialize: function () {
                this._super();
                this.OUT_OF_STOCK_MESSAGE = "out of stock";
                this.isOnCartPage = ko.pureComputed(function(){
                    return (ViewManager.getSingleton('view/checkout/cart').currentPage() == ViewManager.getSingleton('view/checkout/cart').PAGE.CART)?true:false;
                });
                this.isOnCheckoutPage = ko.pureComputed(function(){
                    return (ViewManager.getSingleton('view/checkout/cart').currentPage() == ViewManager.getSingleton('view/checkout/cart').PAGE.CHECKOUT)?true:false;
                });
            },
            getItems: Items.items,
            prepareEditData: function(item,event){
                if(this.isOnCartPage() == true){
                    EditpopupFactory.get().setItem(item);
                    EditpopupFactory.get().showPopup(event);
                }
            },
            remove: function(item,event){
                if(Helper.isUseOnline('checkout') && item.saved_online_item()){
                    CartModel.removeItemOnline(item.item_id());
                }else {
                    CartModel.removeItem(item.item_id());
                }
            }
        });
    }
);