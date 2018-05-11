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
        'model/resource-model/magento-rest/integration/giftcard/gift-card',
        'model/checkout/cart',
        'model/checkout/cart/totals',
        'model/checkout/cart/items',
        'helper/general',
        'model/checkout/checkout',
        'model/checkout/cart/discountpopup'
    ],
    function ($, ko, onlineResource, CartModel, Totals, Items, Helper, CheckoutModel, DiscountModel) {
        "use strict";

        var GiftCardModel = {
            sync_id:'customer_giftcard',
            TOTAL_CODE:'giftcard',
            MODULE_CODE:'os_gift_card',
            CAN_USE_WITH_COUPON:'1',
            balance: ko.observable(0),
            currentAmount: ko.observable(0),
            appliedAmount: ko.observable(0),
            visible: ko.observable(false),
            applied: ko.observable(false),
            useMaxPoint: ko.observable(true),
            updatingBalance: ko.observable(false),
            remaining: CheckoutModel.remainTotal,
            giftcardCode: ko.observable(),
            appliedCards:ko.observableArray(),
            customerCodes:ko.observableArray(),
            initialize: function () {
                if(Helper.isGiftCardEnable()) {
                    this.TotalsModel = Totals;
                    this.initObserver();
                }
                return this;
            },
            getConfig: function(path){
                var allConfig = Helper.getBrowserConfig('plugins_config');
                if(allConfig[this.MODULE_CODE]){
                    var configs = allConfig[this.MODULE_CODE];
                    if(configs[path]){
                        return configs[path];
                    }
                }
                return false;
            },
            getAmountForShipping: function(items){
                var amount = {
                    base:0,
                    amount:0
                };
                if(items && items.length > 0){
                    var totalBase = 0;
                    var totalAmount = 0;
                    ko.utils.arrayForEach(items, function(item) {
                        if(item.item_base_giftcard_discount){
                            totalBase += item.item_base_giftcard_discount;
                        }
                        if(item.item_giftcard_discount){
                            totalAmount += item.item_giftcard_discount;
                        }
                    });
                    amount.base = this.totalAppliedAmount() - totalBase;
                    amount.amount = Helper.convertPrice(this.totalAppliedAmount()) - totalAmount;
                }
                return amount;
            },
            initObserver: function(){
                var self = this;
                self.visible = ko.pureComputed(function(){
                    return self.canUseExtension();
                });

                self.applied(false);

                self.balanceAfterApply = ko.pureComputed(function () {
                    return (self.applied()) ? (self.balance() - self.appliedAmount()) : self.balance();
                });

                self.totalAppliedAmount = ko.pureComputed(function () {
                    return self.getTotalAppliedAmount();
                });

                self.useMaxPoint.subscribe(function (value) {
                    if (value == true) {
                        var maxApplyAble = self.TotalsModel.positiveTotal() - self.totalAppliedAmount() + self.appliedAmount();
                        self.validate(self.balance(),maxApplyAble);
                    }
                });
                self.balance.subscribe(function (value) {
                    if (self.useMaxPoint() == true) {
                        self.currentAmount(self.balance());
                    }
                });
                self.currentAmount.subscribe(function (value) {
                    self.validate();
                });

                self.TotalsModel.positiveTotal.subscribe(function (value) {
                    if(!Helper.isOnlineCheckout()) {
                        if (value == 0) {
                            self.remove();
                        } else {
                            if (self.applied() && self.applied() == true) {
                                var valid = self.validate(self.totalAppliedAmount(), value);
                                if (valid) {
                                    self.apply();
                                }
                            } else {
                                self.currentAmount(value);
                                self.useMaxPoint(true);
                            }
                        }
                    }
                });
                self.TotalsModel.baseDiscountAmount.subscribe(function (value) {
                    if(!Helper.isOnlineCheckout()) {
                        if (self.TotalsModel.positiveTotal() == 0) {
                            self.remove();
                        } else {
                            if (self.applied() && self.applied() == true) {
                                var maxAmount = self.TotalsModel.positiveTotal() - value;
                                var valid = self.validate(self.totalAppliedAmount(), maxAmount);
                                if (valid) {
                                    self.apply();
                                }
                            } else {
                                var maxAmount = self.TotalsModel.positiveTotal() - value;
                                self.currentAmount(maxAmount);
                                self.useMaxPoint(true);
                            }
                        }
                    }
                });

                Helper.observerEvent('cart_empty_after', function (event, data) {
                    self.cartEmptyAfter(data);
                });

                Helper.observerEvent('prepare_receipt_totals', function (event, data) {
                    self.prepareReceipt(data);
                });

                Helper.observerEvent('webpos_order_save_after', function (event, data) {
                    if(!Helper.isOnlineCheckout()) {
                        self.orderSaveAfter(data);
                    }
                });

                Helper.observerEvent('webpos_place_order_before', function (event, data) {
                    if(!Helper.isOnlineCheckout()) {
                        self.placeOrderBefore(data);
                    }
                });

                /**
                 * Call api to update gift card balance
                 */
                Helper.observerEvent('order_refund_after', function (event, data) {
                    self.refundAfter(data);
                });

                /**
                 * Collect rates
                 */
                Helper.observerEvent('go_to_checkout_page',function(event,data){
                    if(!Helper.isOnlineCheckout()) {
                        self.customerCodes([]);
                    }
                });

                /**
                 * Process data after call api
                 */
                Helper.observerEvent('checkout_call_api_after',function(event, data){
                    self.checkoutCallApiAfter(data);
                });

                Helper.observerEvent('reset_payments_data', function(){
                    if(Helper.isOnlineCheckout()) {
                        self.resetAllData();
                    }
                });
            },
            refundAfter: function(data){
                var self = this;
                if (data && data.response && data.response.giftcard_amount_to_refund > 0) {
                    self.refundToBalance(data.response.increment_id, data.response.giftcard_amount_to_refund);
                }
            },
            refundToBalance: function(order_increment_id, amount){
                if(order_increment_id && amount) {
                    var deferred = $.Deferred();
                    var params = {
                        increment_id: order_increment_id,
                        amount: amount,
                        base_amount: Helper.toBasePrice(amount)
                    };
                    onlineResource().setPush(true).setLog(false).refundToBalance(params, deferred);
                }
            },
            /**
             * Not allow to use coupon code when using gift card code
             * @param data
             */
            modifyPermissionUseCoupon: function(){
                var canUseWithCoupon =  Helper.getPluginConfig('os_gift_card', 'giftvoucher/general/use_with_coupon');
                if(canUseWithCoupon != this.CAN_USE_WITH_COUPON && this.applied() && this.totalAppliedAmount() > 0){
                    DiscountModel.modifierCanUseCoupon(false);
                }else{
                    DiscountModel.modifierCanUseCoupon(true);
                }
            },
            /**
             * Event cart empty after - remove applied data
             * @param data
             */
            cartEmptyAfter: function(data){
                this.remove();
                this.resetAllData();
            },
            /**
             * Event before show receipt - Add data to show on receipt
             * @param data
             */
            prepareReceipt: function(data){
                data.totals.push({
                    code: 'gift_voucher_discount',
                    title: 'Gift Card',
                    required: false,
                    sortOrder: 38
                });
            },
            /**
             * Event order save after - update balance on local
             * @param data
             */
            orderSaveAfter: function(data){

            },
            /**
             * Event place order before - add data to order
             * @param data
             */
            placeOrderBefore: function(data){
                var self = this;
                if (data && data.increment_id && self.applied() && self.totalAppliedAmount() > 0) {
                    data.gift_voucher_discount = -Helper.convertPrice(self.totalAppliedAmount());
                    data.base_gift_voucher_discount = -self.totalAppliedAmount();

                    var order_data = [
                        {
                            key: "gift_voucher_discount",
                            value: Helper.convertPrice(self.totalAppliedAmount())
                        },
                        {
                            key: "base_gift_voucher_discount",
                            value: self.totalAppliedAmount()
                        }
                    ];

                    var useForShipping = self.getConfig('giftvoucher/general/use_for_ship');
                    if (useForShipping == '1') {
                        order_data.push({
                            key: "giftvoucher_discount_for_shipping",
                            value: self.getAmountForShipping(data.items).amount
                        });
                        order_data.push({
                            key: "base_giftvoucher_discount_for_shipping",
                            value: self.getAmountForShipping(data.items).base
                        });
                    }

                    var extension_data = self.getCardDataForOrder();

                    data.sync_params.integration.push({
                        'module': 'os_gift_card',
                        'event_name': 'webpos_create_order_with_giftcard_after',
                        'order_data': order_data,
                        'extension_data': extension_data
                    });
                }
            },
            /**
             * Reset all model data
             */
            resetAllData: function(){
                this.balance(0);
                this.currentAmount(0);
                this.applied(false);
                this.useMaxPoint(false);
                this.giftcardCode('');
                this.appliedCards([]);
            },
            /**
             * Reset model data
             */
            resetData: function(){
                this.currentAmount(0);
                this.applied(false);
                this.useMaxPoint(false);
            },
            /**
             * Update balance from server
             * @param customerId
             */
            updateBalance: function(giftcardCode){
                giftcardCode = (giftcardCode)?giftcardCode:this.giftcardCode();
                if(giftcardCode){
                    var self = this;
                    var deferred = $.Deferred();
                    var params = CheckoutModel.getCheckPromotionParams();
                    params.coupon_code = giftcardCode;
                    self.giftcardCode(giftcardCode);

                    onlineResource().setPush(true).setLog(false).getBalance(params,deferred);
                    self.updatingBalance(true);
                    deferred.done(function (response) {
                        var data =  response.data;
                        if(data && typeof data.base_balance != 'undefined' && typeof data.base_balance != undefined && data.base_balance){
                            self.giftcardCode(data.code);
                            self.balance(data.base_balance);
                            if(self.remaining() > 0){
                                var maxAmount = self.remaining();
                                var addedCard = self.getCardDataByCode(giftcardCode);
                                if(addedCard){
                                    maxAmount += addedCard.value();
                                }
                                self.validate(data.base_balance, maxAmount);
                                self.apply();
                            }else{
                                self.appliedAmount(0);
                                self.currentAmount(0);
                            }
                        }else{
                            self.balance(0);
                            self.currentAmount(0);
                            self.appliedAmount(0);
                        }
                    }).always(function (response) {
                        self.updatingBalance(false);
                    });
                }
            },
            /**
             * get totals gift card amount
             * @returns {number}
             */
            getTotalAppliedAmount: function(){
                var amount = 0;
                if(this.appliedCards().length > 0) {
                    ko.utils.arrayForEach(this.appliedCards(), function (giftcard) {
                        amount += giftcard.value();
                    });
                }
                return amount
            },
            /**
             * get totals gift card amount exlude one
             * @returns {number}
             */
            getTotalAmountExcludeOne: function(code){
                var amount = 0;
                if(this.appliedCards().length > 0 && code) {
                    ko.utils.arrayForEach(this.appliedCards(), function (giftcard) {
                        if(giftcard.code != code){
                            amount += giftcard.value();
                        }
                    });
                }
                return amount
            },
            /**
             * get applied giftcard data by code
             * @param giftcardCode
             * @returns {number}
             */
            getCardDataByCode: function(giftcardCode){
                var data = 0;
                if(this.appliedCards().length > 0 && giftcardCode) {
                    data = ko.utils.arrayFirst(this.appliedCards(), function (giftcard) {
                        return giftcard.code == giftcardCode;
                    });
                }
                return data
            },
            /**
             * get applied giftcard information to sync
             * @returns {Array}
             */
            getCardDataForOrder: function(){
                var codes = [];
                if(this.appliedCards().length > 0) {
                    ko.utils.arrayForEach(this.appliedCards(), function (giftcard) {
                        var data = {
                            key: giftcard.code,
                            value: giftcard.value()
                        };
                        codes.push(data);
                    });
                }
                return codes;
            },
            /**
             * get applied giftcard information to sync
             * @returns {Array}
             */
            getAppliedGiftCodes: function(){
                var codes = [];
                if(this.appliedCards().length > 0) {
                    ko.utils.arrayForEach(this.appliedCards(), function (giftcard) {
                        codes.push(giftcard.code);
                    });
                }
                return codes.toString();
            },
            /**
             * Apply discount to cart
             * @param apply
             */
            apply: function(apply){
                var amount = (apply === false)?0:this.currentAmount();
                this.currentAmount(amount);
                if(amount > 0){
                    this.appliedAmount(amount);
                }else{
                    this.appliedAmount(0);
                }

                if(apply === false){
                    this.appliedCards([]);
                }else{
                    this.applied(true);
                    var giftcardCode = this.giftcardCode();
                    this.applyGiftCode(giftcardCode, amount);
                }

                if(this.appliedCards().length > 0){
                    amount = this.getTotalAppliedAmount();
                }
                amount = Helper.correctPrice(amount);
                var visible = (amount > 0)?true:false;
                this.applied(visible);

                this.TotalsModel.addTotal({
                    code: this.TOTAL_CODE,
                    cssClass: 'discount',
                    title: Helper.__('Gift card'),
                    value: -amount,
                    isVisible: visible,
                    removeAble: true,
                    actions:{
                        remove: $.proxy(this.remove, this),
                        collect: $.proxy(this.collect, this)
                    }
                });
                this.TotalsModel.updateTotal(this.TOTAL_CODE,{isVisible: visible});
                this.process(amount);
                this.modifyPermissionUseCoupon();
                Helper.dispatchEvent('reset_payments_data', '');

            },
            applyGiftCode: function(code, value){
                var self = this;
                var appliedCard = ko.utils.arrayFirst(self.appliedCards(), function(giftcard) {
                    return (giftcard.code == code);
                });
                if(appliedCard){
                    if(value >= 0) {
                        appliedCard.value(value);
                        appliedCard.balance(self.balance());
                        appliedCard.remain(self.balanceAfterApply());
                        appliedCard.usemax(self.useMaxPoint());
                    }else{
                        if(Helper.isOnlineCheckout()){
                            self.removeGiftcardOnline(appliedCard.code).done(function(){
                                self.appliedCards.remove(appliedCard);
                            });
                        }else{
                            self.appliedCards.remove(appliedCard);
                        }
                        if(appliedCard.code == self.giftcardCode()){
                            self.resetEditingCard();
                            if(self.appliedCards().length > 0){
                                self.editCard(self.appliedCards()[self.appliedCards().length - 1]);
                            }
                        }
                    }
                }else{
                    if(code && value >= 0){

                        self.appliedCards.push({
                            code:code,
                            value:ko.observable(value),
                            balance:ko.observable(self.balance()),
                            remain:ko.observable(self.balanceAfterApply()),
                            usemax:ko.observable(self.useMaxPoint())
                        });
                    }
                }
                if(self.appliedCards().length == 0){
                    self.remove();
                }else{
                    if(!Helper.isOnlineCheckout()){
                        self.collect();
                    }
                }
            },
            resetEditingCard: function(){
                this.balance(0);
                this.useMaxPoint(true);
                this.giftcardCode('');
                this.appliedAmount(0);
                this.currentAmount(0);
            },
            editCard: function(card){
                this.balance(card.balance());
                this.appliedAmount(card.value());
                this.giftcardCode(card.code);
                this.useMaxPoint(card.usemax());
                this.currentAmount(-1);
                this.currentAmount(card.value());
            },
            /**
             * Remove data
             */
            remove: function(){
                this.resetData();
                this.apply(false);
            },
            /**
             * Validate can use module
             * @returns {boolean}
             */
            canUseExtension: function(){
                var moduleEnable = Helper.isGiftCardEnable();
                var moduleActive = this.getConfig('giftvoucher/general/active');
                var canUseWithCoupon = this.getConfig('giftvoucher/general/use_with_coupon');
                var useWithCoupon = true;
                if(canUseWithCoupon != this.CAN_USE_WITH_COUPON && DiscountModel.appliedPromotion() && DiscountModel.couponCode() && DiscountModel.cartBaseDiscountAmount() > 0){
                    useWithCoupon = false;
                }

                var canuse = (
                    moduleEnable
                    && moduleActive
                    && useWithCoupon
                    && !Items.hasGiftvoucherItem()
                )?true:false;
                return canuse;
            },
            /**
             * Validate amount before apply
             * @param balance
             * @param max
             * @returns {boolean}
             */
            validate: function(balance, max){
                if(!this.canUseExtension()){
                    if(this.visible() == true && this.applied() && this.applied() == true){
                        this.remove();
                    }
                    return false;
                }
                var amount = 0;
                if(!balance){
                    amount = this.currentAmount();
                }else{
                    amount = balance;
                }
                var validTotal = this.collectValidTotal();
                var max = (max)?max:validTotal;
                if(max > this.balance()){
                    max = this.balance();
                }
                amount = (amount > max || (this.useMaxPoint() == true))?max:amount;
                if(this.currentAmount() > amount || balance){
                    amount = (parseFloat(amount) > 0)?amount:0;
                    this.currentAmount(-1);
                    this.currentAmount(parseFloat(amount));
                }
                return true;
            },
            collectValidTotal: function(){
                var validTotal = 0;
                var totalAppliedAmount = (this.totalAppliedAmount()) ? this.totalAppliedAmount() : 0;
                var grandTotal = this.TotalsModel.grandTotal() + totalAppliedAmount;
                if(grandTotal > 0) {
                    var applyAfterTax = (this.getConfig('giftvoucher/general/apply_after_tax')) ? '1' : '0';
                    validTotal = (applyAfterTax == '0') ? (this.TotalsModel.grandTotal() + totalAppliedAmount - this.TotalsModel.tax()) : (this.TotalsModel.grandTotal() + totalAppliedAmount);
                    var useForShipping = this.getConfig('giftvoucher/general/use_for_ship');
                    if (useForShipping == '0') {
                        validTotal -= this.TotalsModel.shippingFee();
                    }
                    validTotal -= this.getTotalAmountExcludeOne(this.giftcardCode());
                }
                return validTotal;
            },
            collectMaxTotalToDiscount: function(){
                var max = 0;
                if(Totals.totals().length > 0){
                    var self = this;
                    ko.utils.arrayForEach(Totals.totals(), function (total) {
                        if(total.code() != self.TOTAL_CODE && total.code() != self.TotalsModel.GRANDTOTAL_TOTAL_CODE && total.value()){
                            max += Helper.toNumber(total.value());
                        }
                    });
                }
                // var applyAfterTax = (Helper.getBrowserConfig('tax/calculation/apply_after_discount'))?'0':'1';
                var applyAfterTax = (this.getConfig('giftvoucher/general/apply_after_tax'))?'1':'0';
                if(applyAfterTax == '0'){
                    max -= Helper.toNumber(this.TotalsModel.tax());
                }
                var useForShipping = this.getConfig('giftvoucher/general/use_for_ship');
                if(useForShipping == '0'){
                    max -= Helper.toNumber(this.TotalsModel.shippingFee());
                }
                return max;
            },
            /**
             * Reset discount per item
             */
            reset: function(){
                var self = this;
                ko.utils.arrayForEach(Items.items(), function (item) {
                    item.item_giftcard_discount(0);
                    item.item_base_giftcard_discount(0);
                });
            },
            /**
             * Process discount per item
             * @param cartBaseTotalAmount
             */
            process: function(cartBaseTotalAmount){
                var self = this;
                if(cartBaseTotalAmount > 0){
                    var taxAfterDiscount = (self.getConfig('giftvoucher/general/apply_after_tax'))?false:true;
                    var maxAmount = Items.getMaxDiscountAmount(taxAfterDiscount);
                    var itemsAmountTotal = (cartBaseTotalAmount > maxAmount)?maxAmount:cartBaseTotalAmount;
                    var amountApplied = 0;
                    ko.utils.arrayForEach(Items.items(), function (item, index) {
                        var maxAmountItem = Items.getMaxItemDiscountAmount(item.item_id(), taxAfterDiscount);
                        var discountPercent = maxAmountItem/maxAmount;
                        var item_base_amount = (index == Items.items().length - 1)?(itemsAmountTotal - amountApplied):itemsAmountTotal*discountPercent;
                        amountApplied += item_base_amount;
                        var item_amount = Helper.convertPrice(item_base_amount);
                        item.item_base_giftcard_discount(item_base_amount);
                        item.item_giftcard_discount(item_amount);
                    });
                }else{
                    self.reset();
                }
            },
            /**
             * Collect discount per item
             */
            collect: function(){
                var amount = 0;
                if(this.appliedCards().length > 0){
                    amount = this.getTotalAppliedAmount();
                }
                var visible = (amount > 0)?true:false;
                this.applied(visible);

                this.TotalsModel.addTotal({
                    code: this.TOTAL_CODE,
                    cssClass: 'discount',
                    title: Helper.__('Gift card'),
                    value: -amount,
                    isVisible: visible,
                    removeAble: true,
                    actions:{
                        remove: $.proxy(this.remove, this),
                        collect: $.proxy(this.collect, this)
                    }
                });
                this.TotalsModel.updateTotal(this.TOTAL_CODE,{isVisible: visible});
                this.process(amount);
                Helper.dispatchEvent('reset_payments_data', '');
            },
            /**
             * Use gift card online
             * @returns {*}
             */
            applyGiftcardOnline: function(code){
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                params.coupon_code = code;
                CheckoutModel.loading(true);
                onlineResource().setPush(true).setLog(false).applyGiftcard(params,deferred);
                deferred.always(function(){
                    CheckoutModel.loading(false);
                });
                return deferred;
            },
            /**
             * Remove gift card online
             * @returns {*}
             */
            removeGiftcardOnline: function(code){
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                params.coupon_code = code;
                CheckoutModel.loading(true);
                onlineResource().setPush(true).setLog(false).removeGiftcard(params,deferred);
                deferred.always(function(){
                    CheckoutModel.loading(false);
                });
                return deferred;
            },
            /**
             * Process response data
             * @param responseData
             */
            checkoutCallApiAfter: function(responseData){
                var self = this;
                if(Helper.isOnlineCheckout() && responseData){
                    var data = responseData.data;
                    if(data && data.giftcard){
                        if(data && data.giftcard.used_codes) {
                            var codes = data.giftcard.used_codes;
                            $.each(codes, function (code, baseAmount) {
                                self.applyGiftCode(code, baseAmount);
                            });
                        }
                        if(data && data.giftcard.existed_codes) {
                            var customerCodes = data.giftcard.existed_codes;
                            self.customerCodes(customerCodes);
                        }else{
                            self.customerCodes([]);
                        }
                    }
                }
            }
        };
        return GiftCardModel.initialize();
    }
);