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
    'jquery',
    'ko',
    'model/checkout/cart/items',
    'model/checkout/cart/totals/total',
    'model/checkout/cart/discountpopup',
    'helper/general',
    'helper/staff',
    'dataManager',
    'model/checkout/tax/calculator',
    'model/checkout/cart'
],function($, ko, Items, Total, DiscountModel, Helper, Staff, DataManager, TaxCalculator, Cart){
    var Totals = {
        totals: ko.observableArray(),
        displayTotals: ko.observableArray(),
        extraTotals: ko.observableArray(),
        quoteTotals: ko.observableArray(),
        buttons: ko.observableArray(),
        shippingData: ko.observable(),
        shippingFee: ko.observable(0),
        subtotal: ko.pureComputed(function () {
            var subtotal = 0;
            ko.utils.arrayForEach(Items.items(), function (item){
                var convertedAmount = Helper.convertPrice(item.row_total());
                subtotal += Helper.correctPrice(convertedAmount);
            });
            return Helper.toBasePrice(subtotal);
        }),
        subtotalIncludeTax: ko.pureComputed(function () {
            var subtotal = 0;
            ko.utils.arrayForEach(Items.items(), function (item) {
                var convertedAmount = Helper.convertPrice(item.row_total_include_tax());
                subtotal += Helper.correctPrice(convertedAmount);
            });
            return Helper.toBasePrice(subtotal);
        }),
        additionalInfo: ko.observableArray(),
        SUBTOTAL_TOTAL_CODE: "subtotal",
        TAX_TOTAL_CODE: "tax",
        ADD_DISCOUNT_TOTAL_CODE: "add-discount",
        DISCOUNT_TOTAL_CODE: "discount",
        SHIPPING_TOTAL_CODE: "shipping",
        GRANDTOTAL_TOTAL_CODE: "grand_total",
        BASE_TOTALS: [
            "subtotal",
            'tax',
            "add-discount",
            "discount",
            "shipping",
            "grand_total"
        ],
        HOLD_BUTTON_CODE: "hold",
        CHECKOUT_BUTTON_CODE: "checkout",
        HOLD_BUTTON_TITLE: Helper.__("Hold"),
        CHECKOUT_BUTTON_TITLE: Helper.__("Checkout"),
        initialize: function () {
            var self = this;
            self.grandTotalBeforeDiscount = ko.pureComputed(function(){
                var grandTotal = self.getPositiveTotal();
                var negativeAmount = self.getNegativeTotal();
                if (negativeAmount < 0) {
                    grandTotal += negativeAmount;
                }
                if(DiscountModel.appliedDiscount() == true || DiscountModel.appliedPromotion() == true || Helper.isOnlineCheckout()) {
                    grandTotal -= self.baseDiscountAmount();
                }
                return grandTotal;
            });
            self.tax = ko.pureComputed(function () {
                var tax = 0;
                if(Helper.isOnlineCheckout() && DataManager.getData('quote_id')  && Helper.isOnCheckoutPage()){
                    tax = self.getOnlineValue(self.TAX_TOTAL_CODE);
                    tax = Helper.toBasePrice(tax);
                }else{
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        tax += item.tax_amount();
                    });
                    tax += self.getShippingIncTax(self.shippingFee());
                }
                return tax;
            });
            if(!self.tax_before_discount) {
                self.tax_before_discount = ko.pureComputed(function () {
                    var tax = 0;
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        tax += item.tax_amount_before_discount();
                    });
                    tax += self.getShippingIncTax(self.shippingFee());
                    return tax;
                });
            }
            self.discountAmount = ko.pureComputed(function () {
                var discountAmount = 0;
                var grandTotal = 0;
                if(Helper.isOnlineCheckout() && DataManager.getData('quote_id')){
                    discountAmount -= self.getOnlineValue(self.DISCOUNT_TOTAL_CODE);
                }else{
                    if((DiscountModel.appliedDiscount() == true || DiscountModel.appliedPromotion() == true) && DiscountModel.cartBaseDiscountAmount() > 0 && self.positiveTotal() > 0) {
                        ko.utils.arrayForEach(self.getTotals(), function (total) {
                            if (
                                total.code() !== self.DISCOUNT_TOTAL_CODE
                                && total.code() !== self.GRANDTOTAL_TOTAL_CODE
                                && total.value()
                                && total.value() > 0
                                && (
                                    (total.code() !== self.TAX_TOTAL_CODE && window.webposConfig['tax/calculation/apply_after_discount'] == '1') ||
                                    (window.webposConfig['tax/calculation/apply_after_discount'] !== '1')
                                )
                            ) {
                                grandTotal += parseFloat(total.value());
                            }
                        });
                        grandTotal = Helper.convertPrice(grandTotal)
                        discountAmount = DiscountModel.calculateAmount(grandTotal);
                    }
                }
                return -discountAmount;
            });

            self.baseDiscountAmount = ko.pureComputed(function(){
                var discountAmount = 0;
                var baseGrandTotal = 0;
                if(Helper.isOnlineCheckout() && DataManager.getData('quote_id') ){
                    discountAmount -= Helper.toBasePrice(self.getOnlineValue(self.DISCOUNT_TOTAL_CODE));
                }else{
                    if((DiscountModel.appliedDiscount() == true || DiscountModel.appliedPromotion() == true) && DiscountModel.cartBaseDiscountAmount() > 0 && self.positiveTotal() > 0){
                        ko.utils.arrayForEach(self.getTotals(), function (total) {
                            if(total.code() == 'tax'){
                                //TODO
                            }
                            if(
                                total.code() !== self.DISCOUNT_TOTAL_CODE
                                && total.code() !== self.GRANDTOTAL_TOTAL_CODE
                                && total.value()
                                && total.value() > 0
                                && (
                                    (total.code() !== self.TAX_TOTAL_CODE && window.webposConfig['tax/calculation/apply_after_discount'] !== '1') ||
                                    (window.webposConfig['tax/calculation/apply_after_discount'] == '1')
                                )
                            ) {
                                baseGrandTotal += parseFloat(total.value());
                            }

                            if(total.code() == self.TAX_TOTAL_CODE && window.webposConfig['tax/calculation/apply_after_discount'] == '0'){
                                baseGrandTotal += parseFloat(total.value());
                            }

                        });
                        discountAmount = DiscountModel.calculateBaseAmount(baseGrandTotal);
                    }
                }
                return -discountAmount;
            });

            self.negativeTotal = ko.pureComputed(function () {
                var totalAmount = 0;
                ko.utils.arrayForEach(self.getTotals(), function (total) {
                    if(total.code() !== self.GRANDTOTAL_TOTAL_CODE && total.value() && total.value() < 0) {
                        totalAmount += parseFloat(total.value());
                    }
                });
                if(Helper.isOnlineCheckout()){
                    ko.utils.arrayForEach(self.extraTotals(), function (total) {
                        if(total.isPrice() && total.value() && total.value() < 0) {
                            totalAmount += parseFloat(total.value());
                        }
                    });
                }
                return totalAmount;
            });

            self.positiveTotal = ko.pureComputed(function () {
                var totalAmount = 0;
                ko.utils.arrayForEach(self.getTotals(), function (total) {
                    if(total.code() !== self.GRANDTOTAL_TOTAL_CODE && total.value() && (total.value() > 0 || total.finalValue())) {
                        if(total.code() == self.TAX_TOTAL_CODE){
                            // if(!Helper.isProductPriceIncludesTax() && Helper.isEnableCrossBorderTrade()){
                            if(!Helper.isProductPriceIncludesTax()){
                                totalAmount += (Helper.isShowTaxFinal())?parseFloat(total.finalValue()):parseFloat(total.value());
                            }
                        }else{
                            if(!Helper.isProductPriceIncludesTax()||!total.includeTaxValue()) {
                                totalAmount += parseFloat(total.value());
                            }else{
                                totalAmount += parseFloat(total.includeTaxValue());
                            }
                        }
                    }
                });
                if(Helper.isOnlineCheckout()){
                    ko.utils.arrayForEach(self.extraTotals(), function (total) {
                        if(total.isPrice() && total.value() && total.value() > 0) {
                            totalAmount += parseFloat(total.value());
                        }
                    });
                }
                return totalAmount;
            });

            self.grandTotal = ko.pureComputed(function () {
                return self.getGrandTotal();
            });
            self.initButtons();
            self.initTotals();

            self.initObserver();
            return this;
        },
        initObserver: function(){
            var self = this;
            Helper.observerEvent('cart_empty_after', function(event, data){
                self.extraTotals([]);
                self.quoteTotals([]);
            });
            Helper.observerEvent('load_totals_online_after', function(event, data){
                if(data && data.items){
                    self.updateTotalsFromQuote(data.items);
                }
            });
            Helper.observerEvent('collect_totals', function(event, data){
                self.collectShippingTotal();
                self.collectTaxTotal();
            });
            self.baseDiscountAmount.subscribe(function(amount){
                self.updateDiscountTotal(amount);
                if(Helper.isOnlineCheckout()){
                    var onlineBaseAmount = Helper.toBasePrice(self.getOnlineValue(self.DISCOUNT_TOTAL_CODE));
                    DiscountModel.promotionDiscountAmount(-onlineBaseAmount);
                }
            });
            self.quoteTotals.subscribe(function(totals){
                if(Helper.isOnlineCheckout()){
                    var shippingAmount = self.getOnlineValue(self.SHIPPING_TOTAL_CODE);
                    shippingAmount = Helper.toBasePrice(shippingAmount);
                    self.updateShippingAmount(shippingAmount);
                    self.getDisplayTotals();
                }
            });
            self.extraTotals.subscribe(function(totals){
                self.getDisplayTotals();
            });
            self.totals.subscribe(function(totals){
                self.getDisplayTotals();
            });
            self.getDisplayTotals();

        },
        getGrandTotal: function(){
            var grandTotal = this.positiveTotal();
            if (this.negativeTotal() < 0) {
                grandTotal += this.negativeTotal();
            }
            return grandTotal;
        },
        // getGrandTotal: function(){
        //     var self = this;
        //     if(Helper.isOnlineCheckout() && DataManager.getData('quote_id') && Helper.isOnCheckoutPage()){
        //         var grandTotal = self.getOnlineValue(self.GRANDTOTAL_TOTAL_CODE);
        //         grandTotal = Helper.toBasePrice(grandTotal);
        //     }else{
        //         var grandTotal = this.positiveTotal();
        //         if (this.negativeTotal() < 0) {
        //             grandTotal += this.negativeTotal();
        //         }
        //     }
        //     return grandTotal;
        // },
        getPositiveTotal: function(){
            var self = this;
            var grandTotal = 0;
            ko.utils.arrayForEach(self.getTotals(), function (total) {
                if(total.code() !== self.GRANDTOTAL_TOTAL_CODE && total.value() && total.value() > 0) {
                    if(total.code() == self.TAX_TOTAL_CODE){
                        //if(!(Helper.isProductPriceIncludesTax() && Helper.isEnableCrossBorderTrade())){
                            grandTotal += (Helper.isShowTaxFinal())?parseFloat(total.finalValue()):parseFloat(total.value());
                        //}
                    }else{
                        grandTotal += parseFloat(total.value());
                    }
                }

            });
            return Helper.correctPrice(grandTotal);
        },
        getNegativeTotal: function(){
            var self = this;
            var grandTotal = 0;
            ko.utils.arrayForEach(self.getTotals(), function (total) {
                if(total.code() !== self.GRANDTOTAL_TOTAL_CODE && total.value() && total.value() < 0) {
                    grandTotal += parseFloat(total.value());
                }
            });
            return Helper.correctPrice(grandTotal);
        },
        getButtons: function () {
            return this.buttons();
        },
        initButtons: function () {
            var self = this;
            if (self.isNewButton(self.HOLD_BUTTON_CODE)) {
                var hold = {
                    code: self.HOLD_BUTTON_CODE,
                    cssClass: "hold btn-cl-cfg-other",
                    title: self.HOLD_BUTTON_TITLE
                };
                self.buttons.push(hold);
            }
            if (self.isNewButton(self.CHECKOUT_BUTTON_CODE)) {
                var checkout = {
                    code: self.CHECKOUT_BUTTON_CODE,
                    cssClass: "checkout btn-cl-cfg-active",
                    title: self.CHECKOUT_BUTTON_TITLE
                };
                self.buttons.push(checkout);
            }
        },
        isNewButton: function (buttonCode) {
            var button = ko.utils.arrayFirst(this.buttons(), function (button) {
                return button.code == buttonCode;
            });
            return (button) ? false : true;
        },
        getTotals: function () {
            return this.totals();
        },
        addTotal: function (data) {
            var self = this;
            if (this.isNewTotal(data.code)) {
                var total = new Total();
                total.init(data);
                self.totals.push(total);
            } else {
                self.setTotalData(data.code, "value", data.value);
                self.setTotalData(data.code, "title", data.title);
                if(data.includeTaxValue){
                    self.setTotalData(data.code, "includeTaxValue", data.includeTaxValue);
                }
            }
        },
        setTotalData: function (totalCode, key, value) {
            var total = this.getTotal(totalCode);
            if (total != false) {
                total.setData(key, value);
            }
        },
        isNewTotal: function (totalCode) {
            var total = ko.utils.arrayFirst(this.totals(), function (total) {
                return total.code() == totalCode;
            });
            return (total) ? false : true;
        },
        getTotalValue: function (totalCode) {
            var value = "";
            var total = this.getTotal(totalCode);
            if (total !== false) {
                value = total.value();
            }
            return value;
        },
        getTotal: function (totalCode) {
            var totalFound = ko.utils.arrayFirst(this.totals(), function (total) {
                return total.code() == totalCode;
            });
            return (totalFound) ? totalFound : false;
        },
        updateTotal: function (totalCode, data) {
            var totals = ko.utils.arrayMap(this.totals(), function (total) {
                if (total.code() == totalCode) {
                    if (typeof data.isVisible != "undefined") {
                        total.isVisible(data.isVisible);
                    }
                    if (typeof data.value != "undefined") {
                        total.value(data.value);
                    }
                    if (typeof data.title != "undefined") {
                        total.title(data.title);
                    }
                }
                return total;
            });
            this.totals(totals);
        },
        initTotals: function () {
            var self = this;
            self.addTotal({
                code: self.SUBTOTAL_TOTAL_CODE,
                cssClass: "subtotal",
                title: Helper.__("Subtotal"),
                value: self.subtotal,
                includeTaxValue: self.subtotalIncludeTax,
                displayIncludeTax: Helper.isCartDisplayIncludeTax('subtotal'),
                isVisible: true,
                autoValue: true,
                isRequired: true,
                removeAble: false
            });
            var canUseDiscount = true;
            var canUseDiscount = false;
            if(
                Staff.isHavePermission("Magestore_Webpos::all_discount") ||
                Staff.isHavePermission("Magestore_Webpos::apply_coupon") ||
                Staff.isHavePermission("Magestore_Webpos::apply_discount_per_cart")
            ){
                canUseDiscount = true;
            }
            self.addTotal({
                code: self.ADD_DISCOUNT_TOTAL_CODE,
                cssClass: "add-discount",
                title: Helper.__("Add Discount"),
                value: "",
                isVisible: ((self.baseDiscountAmount() > 0 && canUseDiscount) || !canUseDiscount) ? false : true,
                removeAble: false
            });
            self.addTotal({
                code: self.DISCOUNT_TOTAL_CODE,
                cssClass: "discount",
                title: Helper.__("Discount"),
                value: self.baseDiscountAmount,
                isVisible: (self.baseDiscountAmount() > 0 && canUseDiscount) ? true : false,
                autoValue: true,
                removeAble: ko.pureComputed(function(){
                    var isOnline = Helper.isOnlineCheckout();
                    var baseAmount = self.baseDiscountAmount();
                    var onlineBaseAmount = Helper.toBasePrice(self.getOnlineValue(self.DISCOUNT_TOTAL_CODE));
                    var removeAble = (!isOnline || (isOnline && (baseAmount != onlineBaseAmount)));
                    return removeAble?true:false;
                }),
                actions: {
                    remove: 'removeDiscount',
                    // collect: $.proxy(DiscountModel.collect, DiscountModel)
                }
            });
            self.addTotal({
                code: self.SHIPPING_TOTAL_CODE,
                cssClass: "shipping",
                title: Helper.__("Shipping"),
                value: self.shippingFee,
                autoValue: true,
                isVisible: (self.shippingFee() > 0) ? true : false,
                removeAble: false
            });
            self.addTotal({
                code: self.TAX_TOTAL_CODE,
                cssClass: "tax",
                title: Helper.__("Tax"),
                value: self.tax,
                finalValue: self.tax_before_discount,
                autoValue: true,
                isVisible: true,
                removeAble: false
            });
            self.addTotal({
                code: self.GRANDTOTAL_TOTAL_CODE,
                cssClass: "total",
                title: Helper.__("Total"),
                value: self.grandTotal,
                autoValue: true,
                isRequired: true,
                isVisible: true,
                removeAble: false
            });
        },
        updateShippingAmount: function (shippingAmount) {
            this.shippingFee(shippingAmount);
            Helper.dispatchEvent('update_shipping_price', {price:shippingAmount});
        },
        updateDiscountTotal: function (amount) {
            var self = this;
            var canUseDiscount = false;
            if(
                Staff.isHavePermission("Magestore_Webpos::all_discount") ||
                Staff.isHavePermission("Magestore_Webpos::apply_coupon") ||
                Staff.isHavePermission("Magestore_Webpos::apply_discount_per_cart")
            ){
                canUseDiscount = true;
            }
            amount = (amount)?amount:self.baseDiscountAmount();
            var hasDiscount = (amount < 0 && canUseDiscount) ? true : false;
            var data = {
                isVisible: (!hasDiscount && canUseDiscount)
            };
            self.updateTotal(self.ADD_DISCOUNT_TOTAL_CODE, data);
        },
        collectShippingTotal: function () {
            var shippingFee = 0;
            var shippingData = this.shippingData();
            if (shippingData && typeof shippingData.price != "undefined") {
                shippingFee = shippingData.price;
                if (typeof shippingData.price_type != "undefined") {
                    shippingFee = (shippingData.price_type == "I") ? (shippingFee * Items.totalShipableItems()) : shippingFee;
                }
            }
            // shippingFee = this.getShippingIncTax(shippingFee);
            shippingFee = Helper.toNumber(shippingFee);
            var hasShipping = (shippingFee > 0 || this.shippingData()) ? true : false;
            // this.updateTotal(this.SHIPPING_TOTAL_CODE, {
            //     isVisible: hasShipping,
            //     value: shippingFee
            // });
            this.shippingFee(shippingFee);
        },

        getShippingIncTax: function (shippingPrice) {
            if(window.webposConfig['tax/calculation/shipping_includes_tax']=='1'){
                return 0;
            } else {
                var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                var taxClass = window.webposConfig['tax/classes/shipping_tax_class'];
                var taxRates = [];
                var address = {
                    country_id: window.webposConfig["shipping/origin/country_id"],
                    postcode: window.webposConfig["shipping/origin/postcode"],
                    region_id: window.webposConfig["shipping/origin/region_id"]
                };
                if (window.webposConfig["defaultCustomerGroup"]) {
                    taxRates = TaxCalculator.getProductTaxRate(taxClass, window.webposConfig["defaultCustomerGroup"], address);
                } else {
                    switch (calculateTaxBaseOn) {
                        case 'shipping':
                            address = Cart.CheckoutModel().shippingAddress();
                            break;
                        case 'billing':
                            address = Cart.CheckoutModel().billingAddress();
                            break;
                    }
                    taxRates = TaxCalculator.getProductTaxRate(taxClass, Cart.customerGroup(), address);
                }
                var shippingPriceIncTax = 0;
                $.each(taxRates, function (index, _taxRate) {
                    shippingPriceIncTax += (_taxRate * shippingPrice / 100);
                });
                return shippingPriceIncTax;
            }
        },

        collectTaxTotal: function () {
            // var tax = 0;
            // if (Items.items().length > 0) {
            //     $.each(Items.items(), function () {
            //         tax += this.tax_amount();
            //     });
            // }
            // this.updateTotal(this.TAX_TOTAL_CODE, {
            //     isVisible: true,
            //     value: tax
            // });
        },
        /**
         * Sort totals possition
         */
        getDisplayTotals: function() {
            var displayTotals = [];
            var self = this;
            /**
             * Add subtotal first
             */
            displayTotals.push(self.getTotal(self.SUBTOTAL_TOTAL_CODE));

            /**
             * Add extra totals from online quote
             */
            $.each(self.extraTotals(), function () {
                displayTotals.push(this);
            });

            /**
             * Add another totals
             */
            $.each(this.getTotals(), function () {
                if(this.code() == self.DISCOUNT_TOTAL_CODE){
                    if(Helper.isOnlineCheckout() ){
                        var title = self.getOnlineValue(this.code(), 'title');
                        if(title.length > 50){
                            title = title.substring(0, 50)+'...';
                        }
                        this.title(title);
                    }else{
                        this.title(Helper.__("Discount"));
                    }
                }
                if(
                    this.code() !== self.GRANDTOTAL_TOTAL_CODE
                    && this.code() !== self.TAX_TOTAL_CODE
                    && this.code() !== self.SUBTOTAL_TOTAL_CODE
                ) {
                    displayTotals.push(this);
                }
            });

            /**
             * Add tax and grandtotal last
             */
            displayTotals.push(self.getTotal(self.TAX_TOTAL_CODE));
            displayTotals.push(self.getTotal(self.GRANDTOTAL_TOTAL_CODE));
            self.displayTotals(displayTotals);
        },
        getGrandTotalWithoutCustomTotal: function(totalCode) {
            var grandTotal = this.grandTotal();
            $.each(this.getTotals(), function () {
                if(this.code() == totalCode) {
                    grandTotal -= this.value();
                }
            });
            return grandTotal;
        },
        addAdditionalInfo: function (data) {
            var self = this;
            var infoFound = ko.utils.arrayFirst(self.additionalInfo(), function (info) {
                return info.code() == data.code;
            });

            if (infoFound) {
                infoFound.title(data.title);
                infoFound.value(data.value);
                infoFound.visible(data.visible);
            } else {
                var info = {};
                info.code = ko.observable(data.code);
                info.title = ko.observable(data.title);
                info.value = ko.observable(data.value);
                info.visible = ko.observable(data.visible);
                self.additionalInfo().push(info);
            }
            self.additionalInfo.valueHasMutated();
        },
        getMaxDiscountAbleAmount: function(){
            var self = this;
            return self.grandTotalBeforeDiscount();
            // return (Items.apply_tax_after_discount == true)?(self.grandTotalBeforeDiscount() - self.tax()):self.grandTotalBeforeDiscount();
        },
        hasSpecialDiscount: function(){
            var self = this;
            var hasSpecialDiscount = false;
            $.each(this.getTotals(), function () {
                if($.inArray(this.code(),self.BASE_TOTALS) < 0 && this.value() < 0) {
                    hasSpecialDiscount = true;
                }
            });
            if(Helper.isOnlineCheckout()){
                ko.utils.arrayForEach(self.extraTotals(), function (total) {
                    if(total.isPrice() && total.value() && total.value() < 0) {
                        hasSpecialDiscount = true;
                    }
                });
            }
            return hasSpecialDiscount;
        },
        /**
         * Use totals from online quote
         * @param totals
         */
        updateTotalsFromQuote: function(totals){
            if(totals && totals.length > 0){
                var self = this;
                var extraTotals = [];
                var quoteTotals = [];
                $.each(totals, function(index, total){
                    if($.inArray(total.code, self.BASE_TOTALS) < 0){
                        extraTotals.push(self.processExtraTotals(total));
                    }else{
                        quoteTotals.push(total);
                    }
                });
                self.quoteTotals(quoteTotals);
                self.extraTotals(extraTotals);
            }
        },
        /**
         * Get online total value
         * @param code
         * @param key
         * @returns {number}
         */
        getOnlineValue: function(code, key){
            var self = this;
            var totalValue = (key)?'':0;
            if(self.quoteTotals().length > 0 && code && Helper.isOnlineCheckout()){
                var totalValid = ko.utils.arrayFirst(self.quoteTotals(), function(total){
                    return total.code == code;
                });
                if(totalValid){
                    totalValue = (key)?totalValid[key]:totalValid.value;
                }
            }
            if(self.extraTotals().length > 0 && code && Helper.isOnlineCheckout()){
                var totalValid = ko.utils.arrayFirst(self.extraTotals(), function(total){
                    return total.code() == code;
                });
                if(totalValid){
                    totalValue = (key)?totalValid[key]():totalValid.value();
                }
            }
            return totalValue;
        },
        /**
         * Init online total to offline model
         * @param data
         * @returns {*}
         */
        processExtraTotals: function(data){
            var self = this;
            var total = new Total();
            var nonPriceTotals = ['rewardpoints_label'];
            var isPrice = ($.inArray(data.code, nonPriceTotals) < 0)?true:false;
            total.init({
                code: data.code,
                title: data.title,
                value: (Helper.isOnlineCheckout())?Helper.toBasePrice(data.value):data.value,
                isPrice: isPrice
            });
            if(data.code == 'rewardpoints_label'){
                total.setData('formated', data.value + ' ' + Helper.__('Point(s)'));
            }
            var eventData = {data:data, total:total};
            Helper.dispatchEvent('webpos_cart_process_extra_total_from_quote_after', eventData);
            total = eventData.total;
            return total;
        }
    }
    return Totals.initialize();
});