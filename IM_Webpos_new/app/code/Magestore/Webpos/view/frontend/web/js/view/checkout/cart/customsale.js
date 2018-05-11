/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
    
define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/checkout/cart/customsale-factory',
    ],
    function ($, ko, Component, Helper, CustomsaleFactory) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/cart/customsale'
            },
            numberpadCache: ko.observableArray(),
            productPrice:ko.pureComputed(function(){
                return Helper.formatPrice(CustomsaleFactory.get().productPrice());
            }),
            productName: ko.pureComputed(function(){
                return CustomsaleFactory.get().productName();
            }),
            taxClassId: CustomsaleFactory.get().taxClassId,
            customSaleDescription: CustomsaleFactory.get().customSaleDescription,
            shipAble: ko.pureComputed(function(){
                return CustomsaleFactory.get().shipAble();
            }),
            taxClasses: ko.pureComputed(function(){
                return CustomsaleFactory.get().taxClasses();
            }),
            initialize: function () {
                this._super();
                this.stopCache = false;
                this.popupEl = '#popup-custom-sale';
                this.overlayEl = '.wrap-backover';
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
                    CustomsaleFactory.get().productPrice(price);
                }else{
                    CustomsaleFactory.get().productPrice("0.00");
                }
            },
            addToCart: function(){
                CustomsaleFactory.get().addToCart();
                this.numberpadCache([]);
                this.hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
                $('#c-button--push-left').removeClass('hide');
            },
            hide: function(){
                $(this.popupEl).hide();
                $(this.overlayEl).hide();
                $(this.popupEl).addClass("fade");
                $(this.popupEl).removeClass("show");
                $(this.popupEl).removeClass("fade-in");
            },
            show: function(){
                if($(this.overlayEl).length > 0){
                    $(this.overlayEl).show();
                }
                $(this.popupEl).show();
            },
            setName: function(data,event){
                CustomsaleFactory.get().productName(event.target.value);
            },
            setPrice: function(data,event){
                var price = Helper.toNumber(event.target.value);
                CustomsaleFactory.get().productPrice(price);
            },
            setTaxClass: function(data,event){
                //CustomsaleFactory.get().taxClassId(event.target.value);
            },
            setShipAble: function(data,event){
                var shipAble = (event.target.checked)?true:false;
                CustomsaleFactory.get().shipAble(shipAble);
            }
        });
    }
);