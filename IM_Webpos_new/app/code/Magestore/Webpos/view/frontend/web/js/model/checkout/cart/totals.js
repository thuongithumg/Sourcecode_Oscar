/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/checkout/cart/totals/total',
        'Magestore_Webpos/js/model/checkout/taxcalculator',
        'Magestore_Webpos/js/model/checkout/cart/discountpopup',
        'Magestore_Webpos/js/helper/staff',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/data/cart'
    ],
    function ($, ko, modelAbstract, Items, Total, TaxCalculator, DiscountModel, Staff, Helper, CartData) {
        "use strict";

        Helper.observerEvent('recollect_totals', function (event, data) {
            if (data.code) {
                ko.utils.arrayForEach(CartData.totals(), function (total) {
                    if (total.code() !== data.code) {
                        if (total.actions() && total.actions().collect) {
                            if (typeof total.actions().collect == 'function') {
                                total.actions().collect();
                            }
                        }
                    }
                });
            }
        });

        return modelAbstract.extend({
            totals: CartData.totals,
            extraTotals: CartData.extraTotals,
            quoteTotals: CartData.quoteTotals,
            CartModel: ko.observable(),
            buttons: ko.observableArray(),
            shippingData: ko.observable(),
            shippingFee: ko.observable(0),
            baseShippingTaxAmount: ko.observable(0),
            baseShippingTaxAmountBeforeDiscount: ko.observable(0),
            loadedTaxData: ko.observable(false),
            subtotal: ko.pureComputed(function () {
                var subtotal = 0;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    // var convertedAmount = Helper.convertPrice(item.row_total());
                    subtotal += item.row_total();
                });
                return subtotal;
            }),
            baseSubtotal: ko.pureComputed(function () {
                var subtotal = 0;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    // var convertedAmount = Helper.convertPrice(item.row_total());
                    subtotal += item.base_row_total();
                });
                return subtotal;
            }),
            subtotalIncludeTax: ko.pureComputed(function () {
                var subtotal = 0;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    subtotal += item.row_total_include_tax();
                });
                return subtotal;
            }),
            baseSubtotalIncludeTax: ko.pureComputed(function () {
                var subtotal = 0;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    subtotal += item.base_row_total_include_tax();
                });
                return subtotal;
            }),
            discountTaxCompensationAmount: ko.pureComputed(function () {
                var discountTaxCompensationAmount = 0;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    discountTaxCompensationAmount += Helper.correctPrice(item.tax_amount_before_discount() - item.tax_amount());
                });
                return discountTaxCompensationAmount;
            }),
            baseDiscountTaxCompensationAmount: ko.pureComputed(function () {
                var baseDiscountTaxCompensationAmount = 0;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    baseDiscountTaxCompensationAmount = Helper.correctPrice(item.base_tax_amount_before_discount() - item.base_tax_amount());
                });
                return baseDiscountTaxCompensationAmount;
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
                this._super();
                var self = this;

                self.initObservables();
                self.initButtons();
                self.initTotals();
                
                if(!CartData.TotalsModel){
                    CartData.TotalsModel = this;
                }

                if (self.loadedTaxData() == false) {
                    self.initTaxData();
                    self.loadedTaxData(true);
                }
                if (!this.reinitObserver) {
                    this.initObserver();
                }
                return this;
            },
            initObservables: function () {
                var self = this;
                self.grandTotalBeforeDiscount = ko.pureComputed(function () {
                    var grandTotal = self.getBasePositiveTotal();
                    var negativeAmount = self.getBaseNegativeTotal();
                    if (negativeAmount < 0) {
                        grandTotal += negativeAmount;
                    }
                    if (DiscountModel.appliedDiscount() == true || DiscountModel.appliedPromotion() == true || Helper.isUseOnline('checkout')) {
                        grandTotal += DiscountModel.calculateBaseAmount(grandTotal);
                    }
                    return grandTotal;
                });
                self.tax = function () {
                    var tax = 0;
                    if (Helper.isUseOnline('checkout') && Helper.getOnlineConfig('quote_id') && Helper.isOnCheckoutPage()) {
                        tax = self.getOnlineValue(self.TAX_TOTAL_CODE);
                    } else {
                        ko.utils.arrayForEach(CartData.items(), function (item) {
                            tax += item.getTaxAmount();
                        });
                        tax += Helper.convertPrice(self.baseShippingTaxAmount());
                    }
                    return tax;
                };
                self.baseTax = function () {
                    var tax = 0;
                    if (Helper.isUseOnline('checkout') && Helper.getOnlineConfig('quote_id') && Helper.isOnCheckoutPage()) {
                        tax = self.getOnlineValue(self.TAX_TOTAL_CODE);
                        tax = Helper.toBasePrice(tax);
                    } else {
                        ko.utils.arrayForEach(CartData.items(), function (item) {
                            tax += item.getBaseTaxAmount();
                        });
                        tax += self.baseShippingTaxAmount();
                    }
                    return tax;
                };
                self.tax_before_discount = ko.pureComputed(function () {
                    var tax = 0;
                    ko.utils.arrayForEach(CartData.items(), function (item) {
                        tax += item.tax_amount_before_discount();
                    });
                    tax += Helper.convertPrice(self.baseShippingTaxAmountBeforeDiscount());
                    return tax;
                });
                self.base_tax_before_discount = ko.pureComputed(function () {
                    var tax = 0;
                    ko.utils.arrayForEach(CartData.items(), function (item) {
                        tax += item.base_tax_amount_before_discount();
                    });
                    tax += self.baseShippingTaxAmountBeforeDiscount();
                    return tax;
                });
                self.discountAmount = ko.pureComputed(function () {
                    var discountAmount = 0;
                    var grandTotal = 0;
                    if (Helper.isUseOnline('checkout') && Helper.getOnlineConfig('quote_id')) {
                        discountAmount -= self.getOnlineValue(self.DISCOUNT_TOTAL_CODE);
                    } else {
                        if ((DiscountModel.appliedDiscount() == true || DiscountModel.appliedPromotion() == true) && DiscountModel.cartBaseDiscountAmount() > 0 && self.positiveTotal() > 0) {
                            ko.utils.arrayForEach(self.getTotals(), function (total) {
                                if (
                                    total.code() !== self.DISCOUNT_TOTAL_CODE
                                    && total.code() !== self.GRANDTOTAL_TOTAL_CODE
                                    && total.value()
                                    && total.value() > 0
                                ) {
                                    var apply_after_discount = window.webposConfig['tax/calculation/apply_after_discount'];
                                    var discount_tax = window.webposConfig['tax/calculation/discount_tax'];
                                    if (total.code() !== self.TAX_TOTAL_CODE) {
                                        grandTotal += parseFloat(total.value());
                                    } else {
                                        if ((apply_after_discount != 1) || (discount_tax == 1)) {
                                            grandTotal += ((discount_tax == 1) && Helper.isShowTaxFinal()) ? parseFloat(total.finalValue()) : parseFloat(total.value());
                                        }
                                    }
                                }
                            });
                            discountAmount = DiscountModel.calculateAmount(grandTotal);
                        }
                    }
                    return discountAmount;
                });
                self.baseDiscountAmount = ko.pureComputed(function () {
                    var discountAmount = 0;
                    var baseGrandTotal = 0;
                    if (Helper.isUseOnline('checkout') && Helper.getOnlineConfig('quote_id')) {
                        discountAmount -= Helper.toBasePrice(self.getOnlineValue(self.DISCOUNT_TOTAL_CODE));
                    } else {
                        if ((DiscountModel.appliedDiscount() == true || DiscountModel.appliedPromotion() == true) && DiscountModel.cartBaseDiscountAmount() > 0 && self.positiveTotal() > 0) {
                            ko.utils.arrayForEach(self.getTotals(), function (total) {
                                if (
                                    total.code() !== self.DISCOUNT_TOTAL_CODE
                                    && total.code() !== self.GRANDTOTAL_TOTAL_CODE
                                    && total.value()
                                    && total.value() > 0
                                ) {
                                    var apply_after_discount = window.webposConfig['tax/calculation/apply_after_discount'];
                                    var discount_tax = window.webposConfig['tax/calculation/discount_tax'];
                                    if (total.code() !== self.TAX_TOTAL_CODE) {
                                        baseGrandTotal += parseFloat(total.baseValue());
                                    } else {
                                        if ((apply_after_discount != 1) || (discount_tax == 1)) {
                                            baseGrandTotal += ((discount_tax == 1) && Helper.isShowTaxFinal()) ? parseFloat(total.baseFinalValue()) : parseFloat(total.baseValue());
                                        }
                                    }
                                }
                            });
                            discountAmount = DiscountModel.calculateBaseAmount(baseGrandTotal);
                        }
                    }
                    return discountAmount;
                });
                self.negativeTotal = ko.pureComputed(function () {
                    return self.getNegativeTotal();
                });
                self.positiveTotal = ko.pureComputed(function () {
                    return self.getPositiveTotal();
                });
                self.grandTotal = ko.pureComputed(function () {
                    return self.getGrandTotal();
                });
                self.baseNegativeTotal = ko.pureComputed(function () {
                    return self.getBaseNegativeTotal();
                });
                self.basePositiveTotal = ko.pureComputed(function () {
                    return self.getBasePositiveTotal();
                });
                self.baseGrandTotal = ko.pureComputed(function () {
                    return self.getBaseGrandTotal();
                });
            },
            initObserver: function () {
                var self = this;
                self.reinitObserver = false;
                Helper.observerEvent('cart_empty_after', function (event, data) {
                    self.extraTotals([]);
                    self.quoteTotals([]);
                });
                Helper.observerEvent('load_totals_online_after', function (event, data) {
                    if (data && data.items) {
                        self.updateTotalsFromQuote(data.items);
                    }
                });
                Helper.observerEvent('collect_totals', function (event, data) {
                    self.collectShippingTotal();
                    self.collectTaxTotal();
                });
                self.baseDiscountAmount.subscribe(function (amount) {
                    self.updateDiscountTotal(amount);
                    if (Helper.isUseOnline('checkout')) {
                        var onlineBaseAmount = Helper.toBasePrice(self.getOnlineValue(self.DISCOUNT_TOTAL_CODE));
                        DiscountModel.promotionDiscountAmount(-onlineBaseAmount);
                    }
                });
                self.quoteTotals.subscribe(function (totals) {
                    if (Helper.isUseOnline('checkout')) {
                        var shippingAmount = self.getOnlineValue(self.SHIPPING_TOTAL_CODE);
                        shippingAmount = Helper.toBasePrice(shippingAmount);
                        self.updateShippingAmount(shippingAmount);
                        if (Helper.isUseOnline('checkout')) {
                            var onlineBaseAmount = Helper.toBasePrice(self.getOnlineValue(self.DISCOUNT_TOTAL_CODE));
                            DiscountModel.promotionDiscountAmount(-onlineBaseAmount);
                            DiscountModel.appliedPromotion(true)
                        }
                        self.updateDiscountTotal();
                        self.getDisplayTotals();
                    }
                });
                self.extraTotals.subscribe(function (totals) {
                    self.getDisplayTotals();
                });
                self.totals.subscribe(function (totals) {
                    self.getDisplayTotals();
                    setTimeout(function () {
                        $('#order-items').height($(window).height() - $('#webpos_cart .o-header').outerHeight() - $('.grand-total-footer').outerHeight());
                    }, 300);
                });
                self.getDisplayTotals();
            },
            getGrandTotal: function () {
                if (Helper.isUseOnline('checkout') && Helper.getOnlineConfig('quote_id') && Helper.isOnCheckoutPage()) {
                    var grandTotal = this.getOnlineValue(this.GRANDTOTAL_TOTAL_CODE);
                } else {
                    var grandTotal = this.positiveTotal();
                    if (this.negativeTotal() < 0) {
                        grandTotal += this.negativeTotal();
                    }
                }
                return grandTotal;
            },
            getPositiveTotal: function () {
                var self = this;
                var grandTotal = 0;
                ko.utils.arrayForEach(self.getTotals(), function (total) {
                    if (total.code() !== self.GRANDTOTAL_TOTAL_CODE && total.value() && total.value() > 0) {
                        if (total.code() == self.TAX_TOTAL_CODE) {
                            // if(!Helper.isEnableCrossBorderTrade()){
                            grandTotal += (Helper.isShowTaxFinal()) ? parseFloat(total.finalValue()) : parseFloat(total.value());
                            // }
                        } else {
                            grandTotal += parseFloat(total.value());
                        }
                    }
                });
                if (Helper.isUseOnline('checkout')) {
                    ko.utils.arrayForEach(self.extraTotals(), function (total) {
                        if (total.isPrice() && total.value() && total.value() > 0) {
                            var value = parseFloat(total.value());
                            grandTotal += parseFloat(value);
                        }
                    });
                }
                grandTotal = Helper.correctPrice(grandTotal);
                return grandTotal;
            },
            getNegativeTotal: function () {
                var self = this;
                var grandTotal = 0;
                ko.utils.arrayForEach(self.getTotals(), function (total) {
                    if (total.code() !== self.GRANDTOTAL_TOTAL_CODE && total.value() && total.value() < 0) {
                        grandTotal += parseFloat(total.value());
                    }
                });
                if (Helper.isUseOnline('checkout')) {
                    ko.utils.arrayForEach(self.extraTotals(), function (total) {
                        if (total.isPrice() && total.value() && total.value() < 0) {
                            grandTotal += parseFloat(total.value());
                        }
                    });
                }
                grandTotal = Helper.correctPrice(grandTotal);
                return grandTotal;
            },
            getBaseGrandTotal: function () {
                if (Helper.isUseOnline('checkout') && Helper.getOnlineConfig('quote_id') && Helper.isOnCheckoutPage()) {
                    var grandTotal = this.getOnlineValue(this.GRANDTOTAL_TOTAL_CODE);
                    grandTotal = Helper.toBasePrice(grandTotal);
                } else {
                    var grandTotal = this.basePositiveTotal();
                    if (this.baseNegativeTotal() < 0) {
                        grandTotal += this.baseNegativeTotal();
                    }
                }
                return grandTotal;
            },
            getBasePositiveTotal: function () {
                var self = this;
                var grandTotal = 0;
                ko.utils.arrayForEach(self.getTotals(), function (total) {
                    if (total.code() !== self.GRANDTOTAL_TOTAL_CODE && total.baseValue() && total.baseValue() > 0) {
                        if (total.code() == self.TAX_TOTAL_CODE) {
                            // if(!Helper.isEnableCrossBorderTrade()){
                            grandTotal += (Helper.isShowTaxFinal()) ? parseFloat(total.baseFinalValue()) : parseFloat(total.baseValue());
                            // }
                        } else {
                            grandTotal += parseFloat(total.baseValue());
                        }
                    }
                });
                if (Helper.isUseOnline('checkout')) {
                    ko.utils.arrayForEach(self.extraTotals(), function (total) {
                        if (total.isPrice() && total.value() && total.value() > 0) {
                            var value = parseFloat(total.value());
                            var baseValue = Helper.toBasePrice(value);
                            grandTotal += parseFloat(baseValue);
                        }
                    });
                }
                grandTotal = Helper.correctPrice(grandTotal);
                return grandTotal;
            },
            getBaseNegativeTotal: function () {
                var self = this;
                var grandTotal = 0;
                ko.utils.arrayForEach(self.getTotals(), function (total) {
                    if (total.code() !== self.GRANDTOTAL_TOTAL_CODE && total.baseValue() && total.baseValue() < 0) {
                        grandTotal += parseFloat(total.baseValue());
                    }
                });
                if (Helper.isUseOnline('checkout')) {
                    ko.utils.arrayForEach(self.extraTotals(), function (total) {
                        if (total.isPrice() && total.value() && total.value() < 0) {
                            var value = parseFloat(total.value());
                            var baseValue = Helper.toBasePrice(value);
                            grandTotal += parseFloat(baseValue);
                        }
                    });
                }
                grandTotal = Helper.correctPrice(grandTotal);
                return grandTotal;
            },
            initTaxData: function () {
                TaxCalculator().initData();
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
                return CartData.totals();
            },
            addTotal: function (data) {
                if (this.isNewTotal(data.code)) {
                    var total = new Total();
                    total.init(data);
                    CartData.totals.push(total);
                } else {
                    this.setTotalData(data.code, "value", data.value);
                    this.setTotalData(data.code, "baseValue", data.baseValue);
                    this.setTotalData(data.code, "finalValue", data.finalValue);
                    this.setTotalData(data.code, "baseFinalValue", data.baseFinalValue);
                    if (data.includeTaxValue) {
                        this.setTotalData(data.code, "includeTaxValue", data.includeTaxValue);
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
                var total = ko.utils.arrayFirst(CartData.totals(), function (total) {
                    return total.code() == totalCode;
                });
                return (total) ? false : true;
            },
            getTotalValue: function (totalCode) {
                var self = this;
                var value = "";
                var total = this.getTotal(totalCode);
                if (total !== false) {
                    value = total.value();
                }
                return value;
            },
            getBaseTotalValue: function (totalCode) {
                var self = this;
                var value = "";
                var total = this.getTotal(totalCode);
                if (total !== false) {
                    value = total.baseValue();
                }
                return value;
            },
            getTotalFinalValue: function (totalCode) {
                var self = this;
                var value = "";
                var total = this.getTotal(totalCode);
                if (total !== false) {
                    value = total.finalValue();
                }
                return value;
            },
            getBaseTotalFinalValue: function (totalCode) {
                var self = this;
                var value = "";
                var total = this.getTotal(totalCode);
                if (total !== false) {
                    value = total.baseFinalValue();
                }
                return value;
            },
            getTotal: function (totalCode) {
                var totalFound = ko.utils.arrayFirst(CartData.totals(), function (total) {
                    return total.code() == totalCode;
                });
                return (totalFound) ? totalFound : false;
            },
            updateTotal: function (totalCode, data) {
                var totals = ko.utils.arrayMap(CartData.totals(), function (total) {
                    if (total.code() == totalCode) {
                        if (typeof data.isVisible != "undefined") {
                            total.isVisible(data.isVisible);
                        }
                        if (typeof data.value != "undefined") {
                            total.value(data.value);
                        }
                        if (typeof data.baseValue != "undefined") {
                            total.baseValue(data.baseValue);
                        }
                        if (typeof data.finalValue != "undefined") {
                            total.finalValue(data.finalValue);
                        }
                        if (typeof data.baseFinalValue != "undefined") {
                            total.baseFinalValue(data.baseFinalValue);
                        }
                        if (typeof data.title != "undefined") {
                            total.title(data.title);
                        }
                    }
                    return total;
                });
                CartData.totals(totals);
            },
            initTotals: function () {
                var self = this;
                this.addTotal({
                    code: self.SUBTOTAL_TOTAL_CODE,
                    cssClass: "subtotal",
                    title: Helper.__("Subtotal"),
                    value: this.subtotal(),
                    baseValue: this.baseSubtotal(),
                    includeTaxValue: this.subtotalIncludeTax(),
                    displayIncludeTax: Helper.isCartDisplayIncludeTax('subtotal'),
                    isVisible: true,
                    removeAble: false
                });
                var canUseDiscount = false;
                if (
                    Staff.isHavePermission("Magestore_Webpos::all_discount") ||
                    Staff.isHavePermission("Magestore_Webpos::apply_coupon") ||
                    Staff.isHavePermission("Magestore_Webpos::apply_discount_per_cart")
                ) {
                    canUseDiscount = true;
                }
                this.addTotal({
                    code: self.ADD_DISCOUNT_TOTAL_CODE,
                    cssClass: "add-discount",
                    title: Helper.__("Add Discount"),
                    value: "",
                    isVisible: ((this.baseDiscountAmount() > 0 && canUseDiscount) || !canUseDiscount) ? false : true,
                    removeAble: false
                });
                this.addTotal({
                    code: self.DISCOUNT_TOTAL_CODE,
                    cssClass: "discount",
                    title: Helper.__("Discount"),
                    value: -this.discountAmount(),
                    baseValue: -this.baseDiscountAmount(),
                    isVisible: (this.baseDiscountAmount() > 0 && canUseDiscount) ? true : false,
                    removeAble: ko.pureComputed(function () {
                        var isOnline = Helper.isUseOnline('checkout');
                        var baseAmount = -self.baseDiscountAmount();
                        var onlineBaseAmount = Helper.toBasePrice(self.getOnlineValue(self.DISCOUNT_TOTAL_CODE));
                        var removeAble = (!isOnline || (isOnline && (baseAmount != onlineBaseAmount)));
                        return removeAble ? true : false;
                    }),
                    actions: {
                        remove: 'removeDiscount',
                        collect: $.proxy(DiscountModel.collect, DiscountModel)
                    }
                });
                this.addTotal({
                    code: self.SHIPPING_TOTAL_CODE,
                    cssClass: "shipping",
                    title: Helper.__("Shipping"),
                    value: Helper.convertPrice(this.shippingFee()),
                    baseValue: this.shippingFee(),
                    isVisible: (this.shippingFee() > 0) ? true : false,
                    removeAble: false
                });
                this.addTotal({
                    code: self.TAX_TOTAL_CODE,
                    cssClass: "tax",
                    title: Helper.__("Tax"),
                    value: this.tax(),
                    baseValue: this.baseTax(),
                    finalValue: this.tax_before_discount(),
                    baseFinalValue: this.base_tax_before_discount(),
                    isVisible: true,
                    removeAble: false
                });
                this.addTotal({
                    code: self.GRANDTOTAL_TOTAL_CODE,
                    cssClass: "total",
                    title: Helper.__("Total"),
                    value: this.grandTotal(),
                    baseValue: this.baseGrandTotal(),
                    isVisible: true,
                    removeAble: false
                });
            },
            updateShippingAmount: function (shippingAmount) {
                var hasShipping = (shippingAmount > 0 || this.shippingData()) ? true : false;
                this.updateTotal(this.SHIPPING_TOTAL_CODE, {
                    isVisible: hasShipping,
                    value: Helper.convertPrice(shippingAmount),
                    baseValue: shippingAmount
                });
                this.shippingFee(shippingAmount);
                this.collectShippingTax();
            },
            updateDiscountTotal: function () {
                var canUseDiscount = false;
                if (
                    Staff.isHavePermission("Magestore_Webpos::all_discount") ||
                    Staff.isHavePermission("Magestore_Webpos::apply_coupon") ||
                    Staff.isHavePermission("Magestore_Webpos::apply_discount_per_cart")
                ) {
                    canUseDiscount = true;
                }
                var name = (DiscountModel.appliedPromotion() == true) ? DiscountModel.couponCode() : DiscountModel.cartDiscountName();
                var hasDiscount = (this.baseDiscountAmount() > 0 && canUseDiscount) ? true : false;
                var title = (name != "") ? Helper.__("Discount ") + "( " + name + " )" : Helper.__("Discount");
                this.updateTotal(this.DISCOUNT_TOTAL_CODE, {
                    title: title,
                    isVisible: hasDiscount,
                    value: -this.discountAmount(),
                    baseValue: -this.baseDiscountAmount()
                });
                this.updateTotal(this.ADD_DISCOUNT_TOTAL_CODE, {
                    isVisible: (!hasDiscount && canUseDiscount)
                });
            },
            collectShippingTotal: function () {
                var shippingFee = 0;
                var shippingBaseFee = 0;
                var shippingData = this.shippingData();
                if (shippingData && typeof shippingData.price != "undefined") {
                    shippingFee = parseFloat(shippingData.price);
                    if (typeof shippingData.price_type != "undefined") {
                        shippingFee = (shippingData.price_type == "I") ? (shippingFee * Items.totalShipableItems()) : shippingFee;
                    }
                }
                shippingFee = parseFloat(shippingFee);
                shippingBaseFee = shippingFee;
                var hasShipping = (shippingFee > 0 || this.shippingData()) ? true : false;

                if (Helper.isUseOnline('checkout')) {
                    shippingBaseFee = Helper.toBasePrice(shippingBaseFee);
                } else {
                    shippingFee = Helper.convertPrice(shippingFee);
                }
                this.updateTotal(this.SHIPPING_TOTAL_CODE, {
                    isVisible: hasShipping,
                    value: shippingFee,
                    baseValue: shippingBaseFee
                });
                this.shippingFee(shippingBaseFee);
                this.collectShippingTax();
            },
            getBaseDiscountAmount: function (){
                var discountAmount = this.baseDiscountAmount() ? this.baseDiscountAmount() : 0;
                var eventData = {base_discount_amount: discountAmount};
                Helper.dispatchEvent('webpos_total_get_base_discount_amount', eventData);
                return eventData.base_discount_amount;
            },
            getDiscountAmount: function (){
                var discountAmount = this.discountAmount() ? this.discountAmount() : 0;
                var eventData = {discount_amount: discountAmount};
                Helper.dispatchEvent('webpos_total_get_discount_amount', eventData);
                return eventData.discount_amount;
            },
            getBaseShippingDiscountAmount: function () {
                var baseShippingDiscountAmount = this.getBaseDiscountAmount() -
                    this.baseSubtotal() -
                    this.baseTax() +
                    this.baseShippingTaxAmount();
                var eventData = {base_discount_amount: baseShippingDiscountAmount};
                // Helper.dispatchEvent('webpos_total_get_base_shipping_discount_amount', eventData);
                return Math.max(eventData.base_discount_amount, 0);
            },
            collectShippingTax: function () {
                var shippingFee = this.shippingFee();
                var shippingFeeAfterDiscount = this.shippingFee();
                var shippingTaxClass = webposConfig['tax/classes/shipping_tax_class'];
                if (!shippingTaxClass || shippingTaxClass == 0) {
                    this.baseShippingTaxAmountBeforeDiscount(0);
                    this.baseShippingTaxAmount(0);
                } else if (shippingFee && shippingFee > 0) {
                    if (window.webposConfig['tax/calculation/apply_after_discount'] == 1) {
                        var shippingDiscountAmount = this.getBaseShippingDiscountAmount();
                        shippingFeeAfterDiscount = Math.max(shippingFee - shippingDiscountAmount, 0);
                    }
                    var data = {tax_class_id: shippingTaxClass};
                    data = this.CartModel().collectTaxRate(data);
                    var tax = 0,
                        taxAfterDiscount = 0;
                    var taxRates = data.tax_rates;
                    if (taxRates && taxRates.length > 0) {
                        $.each(taxRates, function (index, rate) {
                            tax += Helper.roundPrice(rate * shippingFee / 100);
                            taxAfterDiscount += Helper.roundPrice(rate * shippingFeeAfterDiscount / 100);
                        });
                    }
                    tax = Helper.correctPrice(tax);
                    taxAfterDiscount = Helper.correctPrice(taxAfterDiscount);
                    this.baseShippingTaxAmountBeforeDiscount(tax);
                    this.baseShippingTaxAmount(taxAfterDiscount);
                } else {
                    this.baseShippingTaxAmountBeforeDiscount(0);
                    this.baseShippingTaxAmount(0);
                }
            },
            collectTaxTotal: function () {
                var tax = 0;
                var baseTax = 0;
                var finalValue = 0;
                var baseFinalValue = 0;
                if (CartData.items().length > 0) {
                    $.each(CartData.items(), function () {
                        tax += this.getTaxAmount();
                        baseTax += this.getBaseTaxAmount();
                        finalValue += this.tax_amount_before_discount();
                        baseFinalValue += this.base_tax_amount_before_discount();
                    });
                    tax += Helper.convertPrice(this.baseShippingTaxAmount());
                    baseTax += this.baseShippingTaxAmount();
                    finalValue += Helper.convertPrice(this.baseShippingTaxAmountBeforeDiscount());
                    baseFinalValue += this.baseShippingTaxAmountBeforeDiscount();
                }
                this.updateTotal(this.TAX_TOTAL_CODE, {
                    isVisible: true,
                    value: tax,
                    baseValue: baseTax,
                    finalValue: finalValue,
                    baseFinalValue: baseFinalValue,
                });
            },
            getDisplayTotals: function () {
                var displayTotals = ko.observableArray();
                var self = this;
                self.initTotals();
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
                    if (this.code() == self.DISCOUNT_TOTAL_CODE) {
                        if (Helper.isUseOnline('checkout')) {
                            //var title = self.getOnlineValue(this.code(), 'title');
                            var name = DiscountModel.cartDiscountName();
                            var title = (name != "") ? Helper.__("Discount ") + "( " + name + " )" : Helper.__("Discount");
                            if (title.length > 50) {
                                title = title.substring(0, 50) + '...';
                            }
                            this.title(title);
                        } else {
                            this.title(Helper.__("Discount"));
                        }
                    }
                    if (
                        this.code() !== self.GRANDTOTAL_TOTAL_CODE
                        && this.code() !== self.TAX_TOTAL_CODE
                        && this.code() !== self.SUBTOTAL_TOTAL_CODE
                    ) {
                        displayTotals.push(this);
                    }
                });
                displayTotals.push(this.getTotal(self.TAX_TOTAL_CODE));
                displayTotals.push(this.getTotal(self.GRANDTOTAL_TOTAL_CODE));
                return displayTotals;
            },
            getGrandTotalWithoutCustomTotal: function (totalCode) {
                var grandTotal = this.getTotalValue('grand_total');
                $.each(this.getTotals(), function () {
                    if (this.code() == totalCode) {
                        grandTotal -= this.value();
                    }
                });
                return grandTotal;
            },
            getAdditionalInfo: function () {
                return this.additionalInfo();
            },
            addAdditionalInfo: function (data) {
                var infoFound = ko.utils.arrayFirst(this.additionalInfo(), function (info) {
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
                    this.additionalInfo().push(info);
                }
                this.additionalInfo(this.additionalInfo());
            },
            getMaxDiscountAbleAmount: function () {
                var self = this;
                return (CartData.apply_tax_after_discount == true) ? (self.grandTotalBeforeDiscount() - self.baseTax()) : self.grandTotalBeforeDiscount();
            },
            hasSpecialDiscount: function () {
                var self = this;
                var hasSpecialDiscount = false;
                $.each(this.getTotals(), function () {
                    if ($.inArray(this.code(), self.BASE_TOTALS) < 0 && this.value() < 0) {
                        hasSpecialDiscount = true;
                    }
                });
                if (Helper.isUseOnline('checkout')) {
                    ko.utils.arrayForEach(self.extraTotals(), function (total) {
                        if (total.isPrice() && total.value() && total.value() < 0) {
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
            updateTotalsFromQuote: function (totals) {
                if (totals && totals.length > 0) {
                    var self = this;
                    var extraTotals = [];
                    var quoteTotals = [];
                    $.each(totals, function (index, total) {
                        if ($.inArray(total.code, self.BASE_TOTALS) < 0) {
                            extraTotals.push(self.processExtraTotals(total));
                        } else {
                            total.value = parseFloat(total.value);
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
            getOnlineValue: function (code, key) {
                var self = this;
                var totalValue = (key) ? '' : 0;
                if (self.quoteTotals().length > 0 && code && Helper.isUseOnline('checkout')) {
                    var totalValid = ko.utils.arrayFirst(self.quoteTotals(), function (total) {
                        return total.code == code;
                    });
                    if (totalValid) {
                        totalValue = (key) ? totalValid[key] : totalValid.value;
                    }
                }
                if (self.extraTotals().length > 0 && code && Helper.isUseOnline('checkout')) {
                    var totalValid = ko.utils.arrayFirst(self.extraTotals(), function (total) {
                        return total.code() == code;
                    });
                    if (totalValid) {
                        totalValue = (key) ? totalValid[key]() : totalValid.value();
                    }
                }
                return totalValue;
            },
            /**
             * Init online total to offline model
             * @param data
             * @returns {*}
             */
            processExtraTotals: function (data) {
                var self = this;
                var total = new Total();
                var nonPriceTotals = ['rewardpointsearning'];
                var isPrice = ($.inArray(data.code, nonPriceTotals) < 0) ? true : false;
                total.init({
                    code: data.code,
                    title: data.title,
                    value: (Helper.isUseOnline('checkout')) ? parseFloat(data.value) : parseFloat(data.value),
                    baseValue: (Helper.isUseOnline('checkout')) ? Helper.toBasePrice(parseFloat(data.value)) : parseFloat(data.value),
                    isPrice: isPrice
                });
                if (data.code == 'rewardpointsearning') {
                    total.setData('formated', data.value + ' ' + Helper.__('Point(s)'));
                }

                var eventData = {data: data, total: total};
                Helper.dispatchEvent('webpos_cart_process_extra_total_from_quote_after', eventData);
                total = eventData.total;
                return total;
            }
        });
    }
);