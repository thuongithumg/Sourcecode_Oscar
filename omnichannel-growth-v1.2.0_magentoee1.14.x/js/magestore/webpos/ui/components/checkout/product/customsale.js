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
        'posComponent',
        'model/appConfig',
        'helper/price',
        'model/checkout/cart/customsale',
        'lib/bootstrap/bootstrap-switch'
    ],
    function ($, ko, Component, AppConfig, Helper, CustomSaleModel) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'ui/checkout/cart/customsale'
            },
            taxClasses: CustomSaleModel.taxClasses,
            numberpadCache: ko.observableArray(),
            productPrice:ko.pureComputed(function(){
                return Helper.formatPrice(CustomSaleModel.productPrice());
            }),
            productQuantity: ko.pureComputed(function(){
                return CustomSaleModel.productQuantity();
            }),
            productName: ko.pureComputed(function(){
                return CustomSaleModel.productName();
            }),
            taxClassId: ko.pureComputed(function(){
                return CustomSaleModel.taxClassId();
            }),
            shipAble: ko.pureComputed(function(){
                return CustomSaleModel.shipAble();
            }),

            initialize: function () {
                this._super();
                this.stopCache = false;
            },
            numberpadClick: function(data,event){
                var value = event.target.value;
                if(this.numberpadCache().length <= 0 && (value =="00" || value == "0")){
                    return;
                }
                if(value != "backspace" && typeof value != "undefined"){
                    if(value == "00"){
                        this.numberpadCache().push(0);
                        this.numberpadCache().push(0);
                    }else{
                        this.numberpadCache().push(value);
                    }
                }else{
                    this.numberpadCache().pop();
                }
                this.showPriceAmount();
            },
            showPriceAmount: function(){
                if(this.numberpadCache().length > 0){
                    var price = "";
                    $.each(this.numberpadCache(), function(){
                        price += ""+this;
                    });
                    price = parseFloat(price)/100;
                    CustomSaleModel.productPrice(price);
                }else{
                    CustomSaleModel.productPrice("0.00");
                }
            },
            addToCart: function(){
                CustomSaleModel.addToCart();
                this.numberpadCache([]);
                this.closeCustomSale();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
                $('#c-button--push-left').removeClass('hide');
            },
            setName: function(data,event){
                CustomSaleModel.productName(event.target.value);
            },
            setPrice: function(data,event){
                var price = Helper.toNumber(event.target.value);
                CustomSaleModel.productPrice(price);
            },
            setQuantity: function(data,event){
                var qty = Helper.toNumber(event.target.value);
                CustomSaleModel.productQuantity(qty);
            },
            setTaxClass: function(data,event){
                CustomSaleModel.taxClassId(event.target.value);
            },
            setShipAble: function(data,event){
                var shipAble = (event.target.checked)?true:false;
                CustomSaleModel.shipAble(shipAble);
            },

            afterRender: function () {
                $('#shippable_button').iosCheckbox();
            },

            closeCustomSale: function () {
                var customSalePopup =  $('#popup-custom-sale');
                var overlay = customSalePopup.parent().find(AppConfig.ELEMENT_SELECTOR.DYNAMIC_OVERLAY);
                customSalePopup.removeClass('fade-in');
                customSalePopup.removeClass('show');
                overlay.removeClass(AppConfig.CLASS.ACTIVE);
            }

        });
    }
);