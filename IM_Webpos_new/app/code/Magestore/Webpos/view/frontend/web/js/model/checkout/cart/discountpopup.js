/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/data/cart'
    ],
    function ($,ko, Helper, CartData) {
        "use strict";

        return {
            loading: ko.observable(),
            cartDiscountName: ko.observable(),
            cartDiscountAmount: ko.observable(0),
            cartBaseDiscountAmount: ko.observable(0),
            cartDiscountPercent: ko.observable(),
            cartDiscountType: ko.observable(),
            promotionDiscountAmount: ko.observable(),
            couponCode: ko.observable(),
            appliedRuleIds: ko.observable(),
            maximumPercent: ko.observable(window.webposConfig.maximum_discount_percent),
            appliedDiscount: ko.observable(false),
            appliedPromotion: ko.observable(false),
            applyPromotion: ko.observable('0'),
            modifierCanUseCoupon: ko.observable(true),
            TOTAL_CODE: "discount",
            STAGES: {
                DISCOUNT:"discount",
                PROMOTION:"promotion"
            },
            DISCOUNT_TYPES: {
                FIXED:"fixed",
                PERCENT:"percent"
            },
            calculateAmount: function(grandTotal){
                var self = this;
                var discountAmount = 0;
                grandTotal = (grandTotal)?grandTotal:0;
                if (self.appliedDiscount() == true || self.appliedPromotion() == true) {
                    discountAmount = self.cartDiscountAmount();
                    var maximumPercent = parseFloat(self.maximumPercent());
                    if (self.cartDiscountType() == self.DISCOUNT_TYPES.FIXED) {
                        var percent = discountAmount / grandTotal * 100;
                        if (percent > maximumPercent) {
                            discountAmount = grandTotal * maximumPercent / 100;
                        } else {
                            discountAmount = discountAmount;
                        }
                    } else {
                        if (discountAmount > maximumPercent) {
                            discountAmount = parseFloat(maximumPercent);
                        }
                        discountAmount = discountAmount * grandTotal / 100;
                    }
                }
                return Helper.correctPrice(discountAmount);
            },
            calculateBaseAmount: function(baseGrandTotal){
                var self = this;
                var discountAmount = 0;
                baseGrandTotal = (baseGrandTotal)?baseGrandTotal:0;
                if (self.appliedDiscount() == true || self.appliedPromotion() == true) {
                    discountAmount = self.cartBaseDiscountAmount();
                    var maximumPercent = parseFloat(self.maximumPercent());
                    if (self.cartDiscountType() == self.DISCOUNT_TYPES.FIXED) {
                        var percent = discountAmount / baseGrandTotal * 100;
                        if (percent > maximumPercent) {
                            discountAmount = baseGrandTotal * maximumPercent / 100;
                        } else {
                            discountAmount = discountAmount;
                        }
                    } else {
                        if (discountAmount > maximumPercent) {
                            discountAmount = parseFloat(maximumPercent);
                        }
                        discountAmount = discountAmount * baseGrandTotal / 100;
                    }
                }
                return Helper.correctPrice(discountAmount);
            },

            /* Start: Cart discount per item */
            reset: function(){
                var self = this;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    item.item_discount_amount(0);
                    item.item_base_discount_amount(0);
                });
                self.cartDiscountType(self.DISCOUNT_TYPES.FIXED);
                self.cartDiscountAmount(0);
                self.cartBaseDiscountAmount(0);
                self.cartDiscountName("");
                self.cartDiscountPercent(0);
                self.promotionDiscountAmount(0);
                self.couponCode("");
                self.appliedRuleIds("");
                self.appliedDiscount(false);
                self.appliedPromotion(false);
            },
            process: function(cartBaseTotalAmount){
                var self = this;
                var maxAmount = CartData.getMaxDiscountAmount();
                ko.utils.arrayForEach(CartData.items(), function (item, index) {
                    maxAmount += (item.item_discount_amount() ? item.item_discount_amount() : 0);
                })
                var itemsAmountTotal = (cartBaseTotalAmount > maxAmount)?maxAmount:cartBaseTotalAmount;
                var amountApplied = 0;
                ko.utils.arrayForEach(CartData.items(), function (item, index) {
                    var maxAmountItem = CartData.getMaxItemDiscountAmount(item.item_id());
                    maxAmountItem += (item.item_discount_amount() ? item.item_discount_amount() : 0);
                    var discountPercent = maxAmountItem/maxAmount;
                    var item_base_amount = (index == CartData.items().length - 1)?(itemsAmountTotal - amountApplied):itemsAmountTotal*discountPercent;
                    amountApplied += item_base_amount;
                    var item_amount = Helper.convertPrice(item_base_amount);
                    item.item_base_discount_amount(item_base_amount);
                    item.item_discount_amount(item_amount);
                });
                //Helper.dispatchEvent('recollect_totals', {code:this.TOTAL_CODE});
            },
            collect: function(){

            },
            /* End: Cart discount per item */

            /**
             * Get params to apply discount online api
             * @returns {{cart_discount_name: *, cart_discount_type: *, cart_discount_value: *}}
             */
            getApplyOnlineParams: function(){
                var self = this;
                var params = {
                    webpos_cart_discount_name: self.cartDiscountName(),
                    webpos_cart_discount_type: (self.cartDiscountType() == self.DISCOUNT_TYPES.PERCENT)?'%':'$',
                    webpos_cart_discount_value: (self.cartDiscountType() == self.DISCOUNT_TYPES.PERCENT)?self.cartDiscountPercent():self.cartDiscountAmount()
                };
                return params;
            }
        };
    }
);