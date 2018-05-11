/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'Magestore_Webpos/js/model/checkout/cart/editpopup',
    ]);

define(
    [
        'jquery',
        'ko',       
        'uiComponent',
        'Magestore_Webpos/js/model/checkout/cart/items/item-factory',
        'Magestore_Webpos/js/model/checkout/cart/editpopup-factory',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/staff',

    ],
    function ($, ko, Component, ItemFactory, EditPopupFactory, HelperPrice, Staff) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/cart/editpopup'
            },
            
            focusQtyInput: true,
            initialize: function () {
                this._super();
                var self = this;
                this.hasCustomAmount = ko.pureComputed(function(){
                    return (EditPopupFactory.get().getData("has_custom_price") == true)?true:false;
                });
                this.isCustomPrice = ko.pureComputed(function(){
                    return (EditPopupFactory.get().getData("custom_type") == ItemFactory.get().CUSTOM_PRICE_CODE && self.hasCustomAmount() == true)?true:false;
                });
                this.isCustomDiscount = ko.pureComputed(function(){
                    return (EditPopupFactory.get().getData("custom_type") == ItemFactory.get().CUSTOM_DISCOUNT_CODE && self.hasCustomAmount() == true)?true:false;
                });
                this.isTypeFixed = ko.pureComputed(function(){
                    return (EditPopupFactory.get().getData("custom_price_type") == ItemFactory.get().FIXED_AMOUNT_CODE)?true:false;
                });
                this.isTypePercent = ko.pureComputed(function(){
                    return (EditPopupFactory.get().getData("custom_price_type") == ItemFactory.get().PERCENTAGE_CODE)?true:false;
                });
                this.timeout = "";
            },
            incQty: function(){
                var increment = HelperPrice.toNumber((EditPopupFactory.get().getData('qty_increment'))?EditPopupFactory.get().getData('qty_increment'):1);
                var isQtyDecimal = EditPopupFactory.get().getData('is_qty_decimal');
                increment = (increment > 0)?increment:1;
                var qty = HelperPrice.toNumber(this.getQty());
                qty = qty + increment;
                if(qty%increment > 0){
                    qty -= (isQtyDecimal)?parseFloat(qty%increment):parseInt(qty%increment);
                    qty = (qty > 0)?qty:increment;
                }
                EditPopupFactory.get().setData('qty',qty);
            },
            descQty: function(){
                var increment = HelperPrice.toNumber((EditPopupFactory.get().getData('qty_increment'))?EditPopupFactory.get().getData('qty_increment'):1);
                var isQtyDecimal = EditPopupFactory.get().getData('is_qty_decimal');
                increment = (increment > 0)?increment:1;
                var qty = HelperPrice.toNumber(this.getQty());
                qty = qty - increment;
                if(qty%increment > 0 || qty == 0){
                    qty -= (isQtyDecimal)?parseFloat(qty%increment):parseInt(qty%increment);
                    qty = (qty > 0)?qty:increment;
                }
                EditPopupFactory.get().setData('qty',qty);
            },
            modifyQty: function(data,event){
                var increment = HelperPrice.toNumber(EditPopupFactory.get().getData('qty_increment'));
                var isQtyDecimal = EditPopupFactory.get().getData('is_qty_decimal');
                var qty = event.target.value;
                qty = (isQtyDecimal)?parseFloat(qty):parseInt(qty);
                if((increment > 0) && qty%increment > 0){
                    qty -= parseFloat(qty%increment);
                    qty = (qty > 0)?qty:increment;
                }
                var maximum_qty = EditPopupFactory.get().getData('maximum_qty');
                if(maximum_qty && qty > maximum_qty){
                    qty = maximum_qty;
                }
                var minimum_qty = EditPopupFactory.get().getData('minimum_qty');
                if(minimum_qty && qty < minimum_qty){
                    qty = minimum_qty;
                }else if(!minimum_qty && qty <= 0){
                    qty = 1;
                }
                event.target.value = qty;
                EditPopupFactory.get().setData('qty',HelperPrice.toNumber(qty));
            },
            getProductName: function(){
                return EditPopupFactory.get().getData('product_name');
            },
            getProductImageUrl: function(){
                return EditPopupFactory.get().getData('image_url');
            },
            getQty: function(){
                return EditPopupFactory.get().getData('qty');
            },
            getCustomPriceAmount: function(){
                return HelperPrice.correctPrice(EditPopupFactory.get().getData('custom_price_amount'));
            },
            getCurrencySymbol: function(){
                return (window.webposConfig.currentCurrencySymbol)?window.webposConfig.currentCurrencySymbol:window.webposConfig.currentCurrencyCode;
            },
            customPrice: function(){
                if(EditPopupFactory.get().getData('has_custom_price') == true && EditPopupFactory.get().getData('custom_type') == ItemFactory.get().CUSTOM_PRICE_CODE){
                    EditPopupFactory.get().setData('has_custom_price',false);
                }else{
                    EditPopupFactory.get().setData('has_custom_price',true);
                    EditPopupFactory.get().setData('custom_type',ItemFactory.get().CUSTOM_PRICE_CODE);
                    if(!EditPopupFactory.get().getData('custom_price_type')){
                        EditPopupFactory.get().setData('custom_price_type',ItemFactory.get().FIXED_AMOUNT_CODE);
                    }
                }
            },
            customDiscount: function(){
                if(EditPopupFactory.get().getData('has_custom_price') == true && EditPopupFactory.get().getData('custom_type') == ItemFactory.get().CUSTOM_DISCOUNT_CODE){
                    EditPopupFactory.get().setData('has_custom_price',false);
                }else{
                    EditPopupFactory.get().setData('has_custom_price',true);
                    EditPopupFactory.get().setData('custom_type',ItemFactory.get().CUSTOM_DISCOUNT_CODE);
                    if(!EditPopupFactory.get().getData('custom_price_type')){
                        EditPopupFactory.get().setData('custom_price_type',ItemFactory.get().FIXED_AMOUNT_CODE);
                    }
                }
            },
            setTypeFixed: function(){
                EditPopupFactory.get().setData('custom_price_type',ItemFactory.get().FIXED_AMOUNT_CODE);
            },
            setTypePercent: function(){
                EditPopupFactory.get().setData('custom_price_type',ItemFactory.get().PERCENTAGE_CODE);
            },
            modifyPrice: function(data,event){
                clearTimeout(this.timeout);
                this.timeout = setTimeout(function () {
                    EditPopupFactory.get().setData('custom_price_amount',HelperPrice.toNumber(event.target.value));
                }, 500);
            },
            canAddDiscount: function(){
                return (Staff.isHavePermission("Magestore_Webpos::all_discount") || Staff.isHavePermission("Magestore_Webpos::apply_discount_per_item"));
            },
            canAddCustomPrice: function(){
                return (Staff.isHavePermission("Magestore_Webpos::all_discount") || Staff.isHavePermission("Magestore_Webpos::apply_custom_price"));
            }
            // isDisplayFullScreen: function () {
            //     if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            //         return false;
            //     } else {
            //         return true;
            //     }
            // }
        });
    }
);