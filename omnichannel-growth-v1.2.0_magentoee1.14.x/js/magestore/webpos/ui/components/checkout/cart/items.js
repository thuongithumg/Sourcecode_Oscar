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
    'model/checkout/cart/items',
    'model/checkout/cart/editpopup',
    'action/checkout/cart/show-edit-popup',
    'helper/general'
], function (ko, $, Component, CartModel, Items, EditPopupModel, ShowEditCartItemPopup, Helper) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ui/checkout/cart/items'
        },
        initialize: function () {
            this._super();
            this.OUT_OF_STOCK_MESSAGE = "out of stock";
            this.isOnCartPage = ko.pureComputed(function(){
                return (CartModel.currentPage() == CartModel.PAGE.CART)?true:false;
            });
            this.isOnCheckoutPage = ko.pureComputed(function(){
                return (CartModel.currentPage() == CartModel.PAGE.CHECKOUT)?true:false;
            });
        },
        getItems: function(){
            return Items.items();
        },
        prepareEditData: function(item,event){
            if(this.isOnCartPage() == true){
                EditPopupModel.setItem(item);
                ShowEditCartItemPopup(event);
            }
        },
        remove: function(item,event){
            if(Helper.isOnlineCheckout() && item.saved_online_item()){
                CartModel.removeItemOnline(item.item_id());
            }else{
                CartModel.removeItem(item.item_id());
            }
        }
    });
});