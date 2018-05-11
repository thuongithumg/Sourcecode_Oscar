/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'Magestore_Webpos/js/model/checkout/cart/totals',
    ]);
    
define(
    [
        'jquery',
        'ko',    
        'uiComponent',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/checkout/cart/discountpopup',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function ($, ko, Component, CheckoutModel, DiscountModel, TotalsFactory, Helper, Event) {
        "use strict";
        DiscountModel.reset();
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/cart/discountpopup'
            },
            isOnCheckout: ko.observable(),
            isOnlineCheckout: ko.pureComputed(function(){
                return (Helper.isUseOnline('checkout'))?true:false;
            }),
            stage: ko.observable(),
            discountTitle: ko.observable(),
            cancelPromotionButtonTitle: ko.observable(),
            applyButtonTitle: ko.observable(),
            numberpadCache: ko.observableArray([]),
            stopCache: ko.observable(),
            discountType: ko.pureComputed(function(){
                if(!DiscountModel.cartDiscountType()){
                    DiscountModel.cartDiscountType(DiscountModel.DISCOUNT_TYPES.FIXED);
                }
                return DiscountModel.cartDiscountType();
            }),
            calculatedDiscountAmount: ko.pureComputed(function(){
                return TotalsFactory.get().discountAmount();
            }),
            discountName: ko.pureComputed(function(){
                if(!DiscountModel.cartDiscountName()){
                    DiscountModel.cartDiscountName("");
                }
                return DiscountModel.cartDiscountName();
            }),
            couponCode: ko.pureComputed(function(){
                if(!DiscountModel.couponCode()){
                    DiscountModel.couponCode("");
                }
                return DiscountModel.couponCode();
            }),
            promotionDiscount: ko.pureComputed(function(){
                if(!DiscountModel.promotionDiscountAmount()){
                    DiscountModel.promotionDiscountAmount("");
                }
                return Helper.convertAndFormatPrice(DiscountModel.promotionDiscountAmount());
            }),
            hasPromotionDiscount: ko.pureComputed(function(){
                return (DiscountModel.promotionDiscountAmount())?true:false;
            }),
            loading: ko.pureComputed(function(){
                return (DiscountModel.loading())?true:false;
            }),
            initialize: function () {
                this._super();
                this.initDefaultData();

                var self = this;
                this.isDiscountStage = ko.pureComputed(function(){
                    return (self.stage() == DiscountModel.STAGES.DISCOUNT)?true:false;
                });
                this.isPromotionStage = ko.pureComputed(function(){
                    return (self.stage() == DiscountModel.STAGES.PROMOTION)?true:false;
                });
                this.discountAmount = ko.pureComputed(function(){
                    if(!DiscountModel.cartDiscountAmount()){
                        DiscountModel.cartDiscountAmount("");
                    }
                    var amount = (DiscountModel.cartDiscountAmount())?DiscountModel.cartDiscountAmount():0;
                    if(self.discountType() == DiscountModel.DISCOUNT_TYPES.PERCENT){
                        amount = DiscountModel.cartDiscountPercent();
                    }else{
                        var priceFormat = window.webposConfig.priceFormat;
                        var precision = (priceFormat && priceFormat.precision)?priceFormat.precision:2;
                        amount = parseFloat(amount).toFixed(precision);
                    }
                    return amount;
                });
                this.isDiscountFixed = ko.pureComputed(function(){
                    return (self.discountType() == DiscountModel.DISCOUNT_TYPES.FIXED)?true:false;
                });
                this.isDiscountPercent = ko.pureComputed(function(){
                    return (self.discountType() == DiscountModel.DISCOUNT_TYPES.PERCENT)?true:false;
                });
                this.stopCache(false);
                this.disableDiscountAmountInput = true;
                this.disablePromotionAmountInput = true;
                this.popupEl = '#webpos_cart_discountpopup';
                this.overlayEl = '.wrap-backover';
                Event.observer('show_cart_discount_popup', function(){
                    self.show();
                });
                Event.observer('cart_empty_after', function(){
                    self.resetData();
                });
                Event.observer('remove_cart_discount', function(){
                    if(Helper.isUseOnline('checkout')){

                    }else{
                        self.resetData();
                        self.initDefaultData();
                        TotalsFactory.get().updateDiscountTotal();
                    }
                });
            },
            initDefaultData: function(){
                var self = this;
                var title = "Discount - Maximum "+DiscountModel.maximumPercent()+"%";
                self.stage(DiscountModel.STAGES.DISCOUNT);
                self.discountTitle(Helper.__(title));
                self.cancelPromotionButtonTitle(Helper.__("Remove"));
                self.applyButtonTitle(Helper.__("Apply"));
            },
            resetData: function(){
                DiscountModel.reset();
                this.stopCache(false);
                this.numberpadCache([]);
            },
            showDiscount: function(){
                this.stage(DiscountModel.STAGES.DISCOUNT);
            },
            showPromotion: function(){
                this.stage(DiscountModel.STAGES.PROMOTION);
            },
            discountFixed: function(){
                this.numberpadCache([]);
                this.stopCache(false);
                DiscountModel.cartDiscountType(DiscountModel.DISCOUNT_TYPES.FIXED);
                this.showDiscountAmount();
            },
            discountPercent: function(){
                this.numberpadCache([]);
                this.stopCache(false);
                DiscountModel.cartDiscountType(DiscountModel.DISCOUNT_TYPES.PERCENT);
                DiscountModel.cartDiscountPercent(0);
                this.showDiscountAmount();
            },
            numberpadClick: function(data,event){
                var value = event.target.getAttribute("value");
                if(this.numberpadCache().length <= 0 && (value =="00" || value == "0")){
                    return;
                }
                if(value != "backspace" && typeof value != "undefined" && this.stopCache() == false){
                    if(value == "00"){
                        this.numberpadCache().push(0);
                        this.numberpadCache().push(0);
                    }else{
                        this.numberpadCache().push(value);
                    }
                }else{
                    if(value == "backspace" && this.numberpadCache().length > 0){
                        this.numberpadCache().pop();
                    }
                }
                this.showDiscountAmount();
            },
            showDiscountAmount: function(){
                if(this.numberpadCache().length > 0){
                    var discountAmount = "";
                    $.each(this.numberpadCache(), function(){
                        discountAmount += ""+this;
                    });
                    discountAmount = parseFloat(discountAmount)/100;
                    var grandTotal = TotalsFactory.get().getMaxDiscountAbleAmount();
                    var maximumPercent = DiscountModel.maximumPercent();
                    if(this.isDiscountFixed() == true){
                        var percent = Helper.toBasePrice(discountAmount)/grandTotal*100;
                        if(percent > maximumPercent){
                            discountAmount = maximumPercent * grandTotal / 100;
                            this.stopCache(true);
                            DiscountModel.cartDiscountAmount(Helper.convertPrice(discountAmount));
                            DiscountModel.cartBaseDiscountAmount(discountAmount);
                        }else{
                            this.stopCache(false);
                            DiscountModel.cartDiscountAmount(discountAmount);
                            DiscountModel.cartBaseDiscountAmount(Helper.toBasePrice(discountAmount));
                        }
                    }else{
                        if(discountAmount > maximumPercent){
                            discountAmount = parseFloat(maximumPercent);
                            this.stopCache(true);
                        }else{
                            this.stopCache(false);
                        }
                        DiscountModel.cartDiscountPercent(discountAmount.toFixed(2));
                        DiscountModel.cartDiscountAmount(discountAmount);
                        DiscountModel.cartBaseDiscountAmount(discountAmount);
                    }
                }else{
                    DiscountModel.cartDiscountAmount("0.00");
                    DiscountModel.cartBaseDiscountAmount(0);
                    DiscountModel.cartDiscountPercent(0);
                }
                if(DiscountModel.appliedPromotion() || DiscountModel.appliedDiscount()){
                    DiscountModel.process(TotalsFactory.get().baseDiscountAmount());
                }
            },
            apply: function(){
                if(this.isPromotionStage() == true){
                    if(Helper.isUseOnline('checkout')){
                        CheckoutModel.applyCouponOnline();
                        DiscountModel.appliedPromotion(true);
                        DiscountModel.appliedDiscount(false);
                    }else {
                        window.webposConfig.discountApply = 'coupon';
                        DiscountModel.appliedPromotion(true);
                        DiscountModel.appliedDiscount(false);
                        DiscountModel.cartDiscountType(DiscountModel.DISCOUNT_TYPES.FIXED);
                        DiscountModel.cartBaseDiscountAmount(DiscountModel.promotionDiscountAmount());
                        DiscountModel.cartDiscountAmount(Helper.convertPrice(DiscountModel.promotionDiscountAmount()));
                    }
                }else{
                    if(Helper.isUseOnline('checkout')){
                        CheckoutModel.applyCartDiscountOnline();
                        DiscountModel.appliedPromotion(false);
                        DiscountModel.appliedDiscount(true);
                    }else {
                        window.webposConfig.discountApply = 'discount';
                        DiscountModel.appliedRuleIds('');
                        DiscountModel.promotionDiscountAmount(0);
                        DiscountModel.appliedPromotion(false);
                        DiscountModel.appliedDiscount(true);
                    }
                }
                if(!Helper.isOnlineCheckout()) {
                    if (DiscountModel.cartBaseDiscountAmount() <= 0) {
                        DiscountModel.reset();
                    }
                    TotalsFactory.get().updateDiscountTotal();
                    DiscountModel.process(TotalsFactory.get().baseDiscountAmount());
                    TotalsFactory.get().collectShippingTax();
                }
                this.hide();
                Helper.dispatchEvent('reset_payments_data', '');
            },
            hide: function(){
                $(this.popupEl).hide();
                $(this.overlayEl).hide();
                $('.notification-bell').show();
                if($('#checkout_container').hasClass('showMenu')){
                    $('#c-button--push-left').show();
                    $('#c-button--push-left').removeClass('hide');
                }else{
                    $('#c-button--push-left').hide();
                    $('#c-button--push-left').addClass('hide');
                }
                Helper.dispatchEvent('focus_search_input', '');
            },
            show: function(){
                if(DiscountModel.appliedPromotion() == true){
                    this.showPromotion();
                }else{
                    this.showDiscount();
                    if(DiscountModel.appliedDiscount() == true){
                        DiscountModel.appliedDiscount(false);
                        TotalsFactory.get().updateDiscountTotal();
                        DiscountModel.process(0);
                        TotalsFactory.get().collectShippingTax();
                    }
                }

                if($(this.overlayEl).length > 0){
                    $(this.overlayEl).show();
                }

                var totalTop = -100+"vh";
                if($('.'+TotalsFactory.get().ADD_DISCOUNT_TOTAL_CODE).length > 0){
                    var discountTotal = TotalsFactory.get().getTotal(TotalsFactory.get().ADD_DISCOUNT_TOTAL_CODE);
                    if(discountTotal !== false && discountTotal.isVisible() == true){
                        totalTop = $('.'+TotalsFactory.get().ADD_DISCOUNT_TOTAL_CODE).offset().top;
                    }
                }
                if($('.'+TotalsFactory.get().DISCOUNT_TOTAL_CODE).length > 0){
                    var discountTotal = TotalsFactory.get().getTotal(TotalsFactory.get().DISCOUNT_TOTAL_CODE);
                    if(discountTotal !== false && discountTotal.isVisible() == true){
                        totalTop = $('.'+TotalsFactory.get().DISCOUNT_TOTAL_CODE).offset().top;
                    }
                }
                var windowHeight = $(window).height();
                var bottom = windowHeight - totalTop - 30;
                bottom += "px";
                if(this.isOnCheckout() == true){
                    $(this.popupEl).addClass("active-on-checkout");
                }else{
                    $(this.popupEl).removeClass("active-on-checkout");
                }
                $(this.popupEl+" .arrow").css({bottom:bottom});
                $(this.popupEl).show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
                if(
                    Helper.isHavePermission("Magestore_Webpos::apply_discount_per_cart") &&
                    !Helper.isHavePermission("Magestore_Webpos::apply_coupon")
                ){
                    this.showDiscount();
                }
                if(
                    !Helper.isHavePermission("Magestore_Webpos::apply_discount_per_cart") &&
                    Helper.isHavePermission("Magestore_Webpos::apply_coupon")
                ){
                    this.showPromotion();
                }
            },
            cancelPromotion: function(){
                if(Helper.isUseOnline('checkout')){
                    if(DiscountModel.appliedPromotion() == true || (DiscountModel.promotionDiscountAmount() != 0)){
                        CheckoutModel.cancelCouponOnline();
                    }
                }else {
                    DiscountModel.reset();
                    DiscountModel.couponCode("");
                    if (DiscountModel.appliedPromotion() == true) {
                        DiscountModel.appliedPromotion(false);
                        DiscountModel.promotionDiscountAmount(0);
                        DiscountModel.cartDiscountAmount(0);
                        DiscountModel.cartBaseDiscountAmount(0);
                        DiscountModel.appliedDiscount(false);
                        DiscountModel.appliedPromotion(false);
                    }
                    TotalsFactory.get().updateDiscountTotal();
                }
            },
            setCoupon: function(data,event){
                DiscountModel.couponCode(event.target.value);
            },
            setDiscountName: function(data,event){
                DiscountModel.cartDiscountName(event.target.value)
            },
            checkPromotion: function(data,event){
                if(this.isPromotionStage() == true && this.loading() == false){
                    CheckoutModel.checkPromotion();
                }
            },
            getCurrencySymbol: function(){
                return (window.webposConfig.currentCurrencySymbol)?window.webposConfig.currentCurrencySymbol:window.webposConfig.currentCurrencyCode;
            },
            canUseDiscount: function(){
                return (Helper.isHavePermission("Magestore_Webpos::all_discount") || Helper.isHavePermission("Magestore_Webpos::apply_discount_per_cart"));
            },
            canUseCoupon: function(){
                var canUseCoupon = (Helper.isHavePermission("Magestore_Webpos::all_discount") || Helper.isHavePermission("Magestore_Webpos::apply_coupon"));
                return canUseCoupon;
            },
            canUseCouponCode: ko.pureComputed(function(){
                var canUseCoupon = (Helper.isHavePermission("Magestore_Webpos::all_discount") || Helper.isHavePermission("Magestore_Webpos::apply_coupon"));
                return canUseCoupon && DiscountModel.modifierCanUseCoupon();
            }),
            enterCoupon: function(data,event){
                if(event.keyCode == 13){
                    if(Helper.isUseOnline('checkout')){
                        CheckoutModel.applyCouponOnline();
                    }else{
                        this.checkPromotion();
                    }
                    event.target.blur();
                }
            }
        });
    }
);