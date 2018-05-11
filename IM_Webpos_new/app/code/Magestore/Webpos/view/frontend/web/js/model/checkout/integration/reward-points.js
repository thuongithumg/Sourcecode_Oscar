/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/integration/rewardpoints/reward-points',
        'Magestore_Webpos/js/model/resource-model/indexed-db/integration/rewardpoints/reward-points',
        'Magestore_Webpos/js/model/collection/integration/reward-points',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/view/checkout/cart',
        'Magestore_Webpos/js/model/checkout/cart/totals',
        'Magestore_Webpos/js/model/checkout/cart/data/cart',
        'Magestore_Webpos/js/view/settings/general/rewardpoints/show-rewardpoints-balance',
        'Magestore_Webpos/js/view/settings/general/rewardpoints/auto-sync-balance',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/integration/rewardpoints/rate',
        'Magestore_Webpos/js/model/checkout/checkout'
    ],
    function ($, ko, modelAbstract, onlineResource, offlineResource, collection, CartModel, CartView, Totals, CartData, ShowPointBalance, AutoSyncBalance, Helper, RateModel, CheckoutModel) {
        "use strict";
        if (!window.hadObserver) {
            window.hadObserver = [];
        }
        return modelAbstract.extend({
            sync_id: 'customer_points',
            TOTAL_CODE: 'rewardpoints',
            MODULE_CODE: 'os_reward_points',
            STATUS_INACTIVE: 0,
            STATUS_ACTIVE: 1,
            TYPE_POINT_TO_MONEY: '1',
            TYPE_MONEY_TO_POINT: '2',
            CUSTOMER_GROUP_ALL: '0',
            EARN_WHEN_INVOICE: '1',
            balance: ko.observable(0),
            currentAmount: ko.observable(0),
            appliedAmount: ko.observable(0),
            maxPoint: ko.observable(0),
            visible: ko.observable(false),
            applied: ko.observable(false),
            useMaxPoint: ko.observable(false),
            updatingBalance: ko.observable(false),
            loading: ko.observable(false),
            remaining: CheckoutModel.remainTotal,
            rates: ko.observableArray([]),
            earningRateValue: ko.observable(0),
            spendingRateValue: ko.observable(0),
            earningRate: ko.observable(),
            spendingRate: ko.observable(),
            discountAmount: ko.observable(0),
            baseDiscountForShipping: ko.observable(0),
            discountForShipping: ko.observable(0),
            initialize: function () {
                if (Helper.isRewardPointsEnable()) {
                    this._super();
                    this.setResource(onlineResource(), offlineResource());
                    this.setResourceCollection(collection());
                    this.TotalsModel = Totals();
                    this.initVariable();
                    if ($.inArray(this.MODULE_CODE, window.hadObserver) < 0) {
                        this.initObserver();
                        window.hadObserver.push(this.MODULE_CODE);
                    }
                }
            },
            getConfig: function (path) {
                var allConfig = Helper.getBrowserConfig('plugins_config');
                if (allConfig[this.MODULE_CODE]) {
                    var configs = allConfig[this.MODULE_CODE];
                    if (configs[path]) {
                        return configs[path];
                    }
                }
                return false;
            },
            loadRates: function () {
                var self = this;
                var deferred = RateModel().getCollection().setOrder('sort_order', 'DESC').load();
                deferred.done(function (data) {
                    self.rates(data.items);
                });
            },
            // getAmountForShipping: function (items) {
            //     var amount = {
            //         base: 0,
            //         amount: 0
            //     };
            //     if (items && items.length > 0) {
            //         var totalBase = 0;
            //         var totalAmount = 0;
            //         ko.utils.arrayForEach(items, function (item) {
            //             if (item.item_base_point_discount) {
            //                 totalBase += item.item_base_point_discount;
            //             }
            //             if (item.item_point_discount) {
            //                 totalAmount += item.item_point_discount;
            //             }
            //         });
            //         amount.base = Helper.toBasePrice(this.discountAmount()) - totalBase;
            //         amount.amount = this.discountAmount() - totalAmount;
            //     }
            //     return amount;
            // },
            initVariable: function () {
                var self = this;
                self.loadRates();
                self.visible = ko.pureComputed(function () {
                    return self.canUseExtension();
                });

                self.applied(false);

                self.balanceAfterApply = ko.pureComputed(function () {
                    return (self.applied()) ? (self.balance() - self.appliedAmount()) : self.balance();
                });

                self.earningPoint = ko.pureComputed(function () {
                    var point = 0;
                    if (self.earningRateValue() > 0 && self.canUseExtension()) {
                        point = parseFloat(self.collectTotalToEarn()) * parseFloat(self.earningRateValue());
                        point = self.roundEarning(point);
                    }
                    return point;
                });

                self.earningPoint.subscribe(function (value) {
                    if (!Helper.isUseOnline('checkout')) {
                        var visible = (value > 0) ? true : false;
                        self.TotalsModel.addAdditionalInfo({
                            code: 'rewardpoints_earning_point',
                            title: Helper.__('Customer will earn ') + value + Helper.__(' point(s)'),
                            value: '',
                            visible: visible
                        });
                    }
                });

                self.useMaxPoint.subscribe(function (value) {
                    if (value == true) {
                        if (Helper.isUseOnline('checkout')) {
                            if (self.maxPoint()) {
                                self.currentAmount(self.maxPoint());
                            } else {
                                self.validate(self.balance(), self.convertMoneyToPoint(self.TotalsModel.positiveTotal()));
                            }
                            if (self.canUseExtension()) {
                                self.spendPointOnline();
                            }
                        } else {
                            self.validate(self.balance(), self.convertMoneyToPoint(self.TotalsModel.positiveTotal()));
                        }
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
                    if (!Helper.isUseOnline('checkout')) {
                        if (self.applied() && self.applied() == true) {
                            if (value > 0) {
                                var valid = self.validate(self.appliedAmount(), self.convertMoneyToPoint(value));
                                if (valid) {
                                    self.apply();
                                }
                            } else {
                                self.remove();
                            }
                        } else {
                            self.currentAmount(self.convertMoneyToPoint(value));
                            // self.useMaxPoint(true);
                        }
                    }
                });
                self.TotalsModel.baseDiscountAmount.subscribe(function (value) {
                    if (!Helper.isUseOnline('checkout')) {
                        if (self.applied() && self.applied() == true) {
                            var maxAmount = self.TotalsModel.positiveTotal() - value;
                            var valid = self.validate(self.appliedAmount(), self.convertMoneyToPoint(maxAmount));
                            if (valid) {
                                self.apply();
                            }
                        } else {
                            var maxAmount = self.TotalsModel.positiveTotal() - value;
                            self.currentAmount(self.convertMoneyToPoint(maxAmount));
                            self.useMaxPoint(true);
                        }
                    }
                });
            },
            initObserver: function () {
                var self = this;
                /**
                 * Reset data after cart empty
                 */
                Helper.observerEvent('cart_empty_after', function (event, data) {
                    self.cartEmptyAfter(data);
                });

                /**
                 * Load customer balance when select customer to checkout
                 */
                Helper.observerEvent('checkout_select_customer_after', function (event, data) {
                    self.selectCustomerToCheckoutAfter(data);
                });

                /**
                 * Show customer point balance on receipt
                 */
                Helper.observerEvent('prepare_receipt_totals', function (event, data) {
                    self.prepareReceipt(data);
                });

                /**
                 * Update customer point balance on local after checkout
                 */
                Helper.observerEvent('webpos_order_save_after', function (event, data) {
                    self.orderSaveAfter(data);
                });

                /**
                 * Add params to sync data when syncing order
                 */
                Helper.observerEvent('webpos_place_order_before', function (event, data) {
                    if (!Helper.isUseOnline('checkout')) {
                        self.placeOrderBefore(data);
                    }
                });

                /**
                 * Call api to update customer credit balance after refund by credit
                 */
                Helper.observerEvent('sales_order_creditmemo_afterSave', function (event, data) {
                    self.refundAfter(data);
                });

                /**
                 * Reload rate data after sync
                 */
                Helper.observerEvent('rewardpoint_rates_finish_pull_after', function (event, data) {
                    self.loadRates();
                });

                /**
                 * Collect rates
                 */
                Helper.observerEvent('go_to_checkout_page', function (event, data) {
                    self.collectRates();
                    var useMaxPoint = self.getConfig('rewardpoints/spending/max_point_default');
                    if (self.appliedAmount() <= 0) {
                        self.useMaxPoint(false);
                        if (useMaxPoint == self.STATUS_ACTIVE) {
                            self.useMaxPoint(true);
                        }
                    }
                });

                /**
                 * Process data after call api
                 */
                Helper.observerEvent('checkout_call_api_after', function (event, data) {
                    self.checkoutCallApiAfter(data);
                });

                /**
                 * Update customer point balance on local after checkout
                 */
                Helper.observerEvent('webpos_place_order_online_after', function (event, data) {
                    self.orderSaveAfter(data);
                });

                /**
                 * After get base total discount amount
                 */
                Helper.observerEvent('webpos_total_get_base_discount_amount', function (event, data) {
                    data.base_discount_amount += Helper.toBasePrice(self.discountAmount());
                });

                /**
                 * fter get total discount amount
                 */
                Helper.observerEvent('webpos_total_get_discount_amount', function (event, data) {
                    data.discount_amount += self.discountAmount();
                });

                /**
                 * Update shipping discount amount for cart
                 */
                Helper.observerEvent('webpos_total_get_base_shipping_discount_amount', function (event, data) {
                    data.base_discount_amount += self.baseDiscountForShipping();
                });

                /**
                 * Update base discount item
                 */
                Helper.observerEvent('webpos_cart_item_get_base_discount_after', function (event, data) {
                    data.base_amount += (data.item.item_base_point_discount() ? data.item.item_base_point_discount() : 0);
                });

                /**
                 * Update discount item
                 */
                Helper.observerEvent('webpos_cart_item_get_discount_after', function (event, data) {
                    data.amount += (data.item.item_point_discount() ? data.item.item_point_discount() : 0);
                });

                /**
                 * Prepare item info buy request before place order offline
                 */
                Helper.observerEvent('webpos_cart_item_prepare_info_buy_request_after', function (event, data) {
                    self.cartItemPrepareInforBuyRequestAfter(data);
                });

                /**
                 * Prepare item cart data before place order offline
                 */
                Helper.observerEvent('webpos_cart_item_prepare_data_for_offline_order_after', function (event, data) {
                    self.cartItemPrepareDataForOfflineOrderAfter(data);
                });

                /**
                 * Prepare discount amount when view order
                 */
                Helper.observerEvent('sales_order_list_load_order', function (event, data) {
                    self.orderListLoadOrderAfter(data);
                });
            },
            /**
             * Event cart empty after - remove applied data
             * @param data
             */
            cartEmptyAfter: function (data) {
                this.remove();
                this.resetAllData();
            },
            /**
             * Event after select customer - load balance and collect rates
             * @param data
             */
            selectCustomerToCheckoutAfter: function (data) {
                var self = this;
                if (self.updatingBalance() == false && CartModel.customerId()) {
                    if (data.customer && data.customer.id) {
                        self.loadBalanceByCustomerId(data.customer.id);
                        var autoSyncBalance = Helper.getLocalConfig(AutoSyncBalance().configPath);
                        if (autoSyncBalance == true) {
                            self.updateBalance();
                        }
                        self.collectRates();
                    }
                }
            },
            /**
             * Event before show receipt - Add data to show on receipt
             * @param data
             */
            prepareReceipt: function (data) {
                data.totals.push({
                    code: 'rewardpoints_discount',
                    title: 'Points Discount',
                    required: false,
                    sortOrder: 37,
                    isPrice: true
                });
                data.totals.push({
                    code: 'rewardpoints_spent',
                    title: 'Spent',
                    required: false,
                    sortOrder: 6,
                    isPrice: false,
                    valueLabel: Helper.__('Points')
                });
                data.totals.push({
                    code: 'rewardpoints_earn',
                    title: 'Earn',
                    required: false,
                    sortOrder: 5,
                    isPrice: false,
                    valueLabel: Helper.__('Points')
                });
                if (data.customer_id) {
                    var self = this;
                    var showBalance = Helper.getLocalConfig(ShowPointBalance().configPath);
                    if (CartModel.customerId() && showBalance == true) {
                        var balance = self.balance() - self.appliedAmount();
                        var earnWhenInvoice = self.getConfig('rewardpoints/earning/order_invoice');
                        var holdDays = Helper.toNumber(self.getConfig('rewardpoints/earning/holding_days'));
                        if (holdDays <= 0) {
                            if (earnWhenInvoice == self.EARN_WHEN_INVOICE) {
                                if (CheckoutModel.createInvoice()) {
                                    balance += self.earningPoint();
                                }
                            } else {
                                if (CheckoutModel.createInvoice() && CheckoutModel.createShipment()) {
                                    balance += self.earningPoint();
                                }
                            }
                        }
                        data.accountInfo.push({
                            label: Helper.__('Customer points balance'),
                            value: balance
                        });
                    }
                }
            },
            /**
             * Event order save after - update balance on local
             * @param data
             */
            orderSaveAfter: function (data) {
                var self = this;
                var balance = self.balance();
                if (data && data.customer_id && data.rewardpoints_spent > 0) {
                    balance -= data.rewardpoints_spent;
                }
                var earnWhenInvoice = self.getConfig('rewardpoints/earning/order_invoice');
                var holdDays = Helper.toNumber(self.getConfig('rewardpoints/earning/holding_days'));
                if (holdDays <= 0) {
                    if (earnWhenInvoice == self.EARN_WHEN_INVOICE) {
                        if (CheckoutModel.createInvoice()) {
                            if (data && data.customer_id && data.rewardpoints_earn > 0) {
                                balance += data.rewardpoints_earn;
                            }
                        }
                    } else {
                        if (CheckoutModel.createInvoice() && CheckoutModel.createShipment()) {
                            if (data && data.customer_id && data.rewardpoints_earn > 0) {
                                balance += data.rewardpoints_earn;
                            }
                        }
                    }
                }
                if (balance != self.balance()) {
                    self.saveBalance(data.customer_id, balance);
                }
            },
            /**
             * Event place order before - add data to order
             * @param data
             */
            placeOrderBefore: function (data) {
                var self = this;
                if (data && data.increment_id && CartModel.customerId()) {
                    var order_data = [];
                    var earningPoints = self.earningPoint();
                    // if (earningPoints > 0) {
                    data.rewardpoints_earn = earningPoints;
                    order_data.push({
                        key: "rewardpoints_earn",
                        value: earningPoints
                    });
                    // }
                    if (self.applied() && self.appliedAmount() > 0) {
                        data.rewardpoints_spent = self.appliedAmount();
                        data.rewardpoints_discount = -self.discountAmount();
                        data.rewardpoints_base_discount = -Helper.toBasePrice(self.discountAmount());

                        order_data.push({
                            key: "rewardpoints_spent",
                            value: self.appliedAmount()
                        });
                        order_data.push({
                            key: "rewardpoints_discount",
                            value: self.discountAmount()
                        });
                        order_data.push({
                            key: "rewardpoints_base_discount",
                            value: Helper.toBasePrice(self.discountAmount())
                        });
                    }

                    var useForShipping = self.getConfig('rewardpoints/spending/spend_for_shipping');
                    if (useForShipping == '1') {
                        order_data.push({
                            key: "rewardpoints_discount_for_shipping",
                            value: self.discountForShipping()
                        });
                        order_data.push({
                            key: "rewardpoints_base_discount_for_shipping",
                            value: self.baseDiscountForShipping()
                        });
                    }

                    data.sync_params.integration.push({
                        'module': 'os_reward_points',
                        'event_name': 'webpos_create_order_with_points_after',
                        'order_data': order_data,
                        'extension_data': []
                    });
                }
            },
            refundAfter: function (data) {
                var self = this;
                if (data && data.response && data.response.customer_id) {
                    if (data.response.rewardpoints_earn > 0 || data.response.rewardpoints_spent > 0) {
                        self.updateBalance(data.response.customer_id);
                    }
                }
            },
            /**
             * Reset all model data
             */
            resetAllData: function () {
                this.balance(0);
                this.currentAmount(0);
                this.applied(false);
                this.useMaxPoint(false);
                this.earningRateValue(0);
                this.spendingRateValue(0);
                this.earningRate({});
                this.spendingRate({});
            },
            /**
             * Reset model data
             */
            resetData: function () {
                this.currentAmount(0);
                this.applied(false);
                this.useMaxPoint(false);
            },
            /**
             * Update balance from server
             * @param customerId
             */
            updateBalance: function (customerId) {
                customerId = (customerId) ? customerId : CartModel.customerId();
                if (customerId) {
                    var self = this;
                    var deferred = $.Deferred();
                    var params = {
                        customer_id: customerId
                    };
                    onlineResource().setPush(true).setLog(false).getBalance(params, deferred);
                    self.updatingBalance(true);
                    deferred.done(function (response) {
                        var data = (typeof response == 'string') ? JSON.parse(response) : response;
                        if (data && typeof data.balance != undefined) {
                            self.balance(data.balance);
                            self.saveBalance(customerId, data.balance);
                        }
                    }).always(function (response) {
                        self.updatingBalance(false);
                    });
                }
            },
            /**
             * Apply discount to cart
             * @param apply
             */
            apply: function (apply) {
                this.baseDiscountForShipping(0);
                this.discountForShipping(0);
                var amount = (apply === false) ? 0 : this.currentAmount();
                var visible = (amount > 0) ? true : false;
                this.applied(visible);
                this.currentAmount(amount);
                if (visible) {
                    this.appliedAmount(amount);
                } else {
                    this.appliedAmount(0);
                }
                this.collectDiscountAmount();
                this.TotalsModel.addTotal({
                    code: this.TOTAL_CODE,
                    cssClass: 'discount',
                    title: Helper.__('Points Discount'),
                    value: -this.discountAmount(),
                    baseValue: -Helper.toBasePrice(this.discountAmount()),
                    isVisible: visible,
                    removeAble: true,
                    actions: {
                        remove: $.proxy(this.remove, this),
                        collect: $.proxy(this.collect, this)
                    }
                });
                this.TotalsModel.updateTotal('rewardpoints', {isVisible: visible});
                this.process(Helper.toBasePrice(this.discountAmount()));
                Helper.dispatchEvent('reset_payments_data', '');
            },
            /**
             * Remove data
             */
            remove: function () {
                this.resetData();
                this.apply(false);
            },
            /**
             * Validate can use module
             * @returns {boolean}
             */
            canUseExtension: function () {
                var self = this;
                var customerId = CartModel.customerId();
                var moduleEnable = Helper.isRewardPointsEnable();
                var canuse = (
                    moduleEnable
                    && customerId
                ) ? true : false;
                return canuse;
            },
            /**
             * Validate amount before apply
             * @param balance
             * @param max
             * @returns {boolean}
             */
            validate: function (balance, max) {
                var self = this;
                if (!self.canUseExtension()) {
                    if (self.visible() == true && self.applied() && self.applied() == true) {
                        self.remove();
                    }
                    return false;
                }
                var amount = 0;
                if (!balance) {
                    amount = self.currentAmount();
                } else {
                    amount = balance;
                }
                var validTotal = self.collectValidTotal();
                var max = (max) ? max : self.convertMoneyToPoint(validTotal);
                if (max > self.balance()) {
                    max = self.balance();
                }
                if (self.spendingRate() && self.spendingRate().rate) {
                    var point = parseFloat(self.spendingRate().rate.points);
                    var floorAmount = Math.floor(max / point) * point;
                    var ceilAmount = Math.ceil(max / point) * point;
                    var validAmount = (ceilAmount <= self.balance()) ? ceilAmount : floorAmount;
                    max = parseFloat(validAmount);
                }
                amount = (amount > max || (self.useMaxPoint() == true)) ? max : amount;
                if (self.currentAmount() > amount || balance) {
                    amount = (parseFloat(amount) > 0) ? amount : 0;
                    self.currentAmount(parseFloat(amount));
                }

                if (self.spendingRate() && self.spendingRate().rate) {
                    var point = parseFloat(self.spendingRate().rate.points);
                    var floorAmount = Math.floor(self.currentAmount() / point) * point;
                    var ceilAmount = Math.ceil(self.currentAmount() / point) * point;
                    var validAmount = (ceilAmount <= self.balance()) ? ceilAmount : floorAmount;
                    self.currentAmount(parseFloat(validAmount));
                }
                return true;
            },
            /**
             *  Collect Item totals to discount
             */
            getItemsTotal: function () {
                var validTotal = 0;
                var applyAfterDiscount = (Helper.getBrowserConfig('tax/calculation/apply_after_discount')) ? '0' : '1';
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    validTotal += item.item_price() * item.qty() -
                        (item.total_discount_item() ? item.total_discount_item() : 0) +
                        (item.item_point_discount() ? item.item_point_discount() : 0);
                    if (applyAfterDiscount == '1') {
                        validTotal += (item.tax_amount() ? item.tax_amount() : 0);
                    }
                });
                return validTotal;
            },
            /**
             *  Collect base Item totals to discount
             */
            getBaseItemsTotal: function () {
                var validTotal = 0;
                var applyAfterDiscount = (Helper.getBrowserConfig('tax/calculation/apply_after_discount')) ? '0' : '1';
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    validTotal += item.base_item_price() * item.qty() -
                        (item.base_total_discount_item() ? item.base_total_discount_item() : 0) +
                        (item.item_base_point_discount() ? item.item_base_point_discount() : 0);
                    if (applyAfterDiscount == '1') {
                        validTotal += (item.base_tax_amount() ? item.base_tax_amount() : 0);
                    }
                });
                return validTotal;
            },
            collectValidTotal: function () {
                // var self = this;
                // var validTotal = this.getItemsTotal();
                // var applyAfterDiscount = (Helper.getBrowserConfig('tax/calculation/apply_after_discount')) ? '0' : '1';
                // var useForShipping = self.getConfig('rewardpoints/spending/spend_for_shipping');
                // if (useForShipping) {
                //     validTotal += Helper.convertPrice(self.TotalsModel.shippingFee());
                //     validTotal -= Helper.convertPrice(self.TotalsModel.getBaseShippingDiscountAmount());
                //     validTotal += Helper.convertPrice(this.discountForShipping());
                //     if (applyAfterDiscount == 1) {
                //         validTotal += self.TotalsModel.baseShippingTaxAmount();
                //     }
                // }
                // if (this.spendingRate() && this.spendingRate().rate) {
                //     if (this.spendingRate().rate.max_price_spended_value && this.spendingRate().rate.max_price_spended_value > 0) {
                //         if (this.spendingRate().rate.max_price_spended_type == 'by_price') {
                //             validTotal = Math.min(validTotal, this.spendingRate().rate.max_price_spended_value);
                //         } else if (this.spendingRate().rate.max_price_spended_type == 'by_percent') {
                //             validTotal = validTotal * this.spendingRate().rate.max_price_spended_value / 100;
                //         }
                //     }
                // }
                // return validTotal;

                var self = this;
                // var grandTotal = (self.TotalsModel.grandTotal() > 0) ? self.TotalsModel.grandTotal() : 0;
                var grandTotal = self.TotalsModel.grandTotal();
                var discountAmount = (self.discountAmount()) ? self.discountAmount() : 0;
                var applyAfterDiscount = (Helper.getBrowserConfig('tax/calculation/apply_after_discount')) ? '0' : '1';
                var validTotal = (applyAfterDiscount == '0') ? (grandTotal + discountAmount - self.TotalsModel.tax()) : (grandTotal + discountAmount);
                var useForShipping = self.getConfig('rewardpoints/spending/spend_for_shipping');
                if (useForShipping == '0') {
                    validTotal -= self.TotalsModel.shippingFee();
                }
                validTotal = validTotal.toFixed(12);
                if (this.spendingRate() && this.spendingRate().rate) {
                    if (this.spendingRate().rate.max_price_spended_value && this.spendingRate().rate.max_price_spended_value > 0) {
                        if (this.spendingRate().rate.max_price_spended_type == 'by_price') {
                            validTotal = Math.min(validTotal, this.spendingRate().rate.max_price_spended_value);
                        } else if (this.spendingRate().rate.max_price_spended_type == 'by_percent') {
                            validTotal = validTotal * this.spendingRate().rate.max_price_spended_value / 100;
                        }
                    }
                }
                if (Helper.isUseOnline('checkout')) {
                    validTotal -= parseFloat(self.TotalsModel.getOnlineValue(self.TOTAL_CODE));
                }

                return validTotal;
            },
            collectMaxTotalToDiscount: function () {
                var max = 0;
                if (CartData.totals().length > 0) {
                    var self = this;
                    ko.utils.arrayForEach(CartData.totals(), function (total) {
                        if (total.code() != self.TOTAL_CODE && total.code() != self.TotalsModel.GRANDTOTAL_TOTAL_CODE && total.value()) {
                            max += Helper.toNumber(total.value());
                        }
                    });
                }
                var applyAfterDiscount = (Helper.getBrowserConfig('tax/calculation/apply_after_discount')) ? '0' : '1';
                if (applyAfterDiscount == '1') {
                    max -= Helper.toNumber(this.TotalsModel.tax());
                }
                var useForShipping = this.getConfig('rewardpoints/spending/spend_for_shipping');
                if (useForShipping == '0') {
                    max -= Helper.toNumber(this.TotalsModel.shippingFee());
                }
                return max;
            },
            collectDiscountAmount: function () {
                var discount = 0;
                if (this.spendingRateValue() > 0 && this.applied() && this.appliedAmount() > 0) {
                    discount = this.convertPointToMoney(this.appliedAmount());
                    var max = this.collectValidTotal();
                    if (discount > max) {
                        discount = max;
                        var points = this.convertMoneyToPoint(discount);
                        this.currentAmount(points);
                        this.appliedAmount(points);
                    }
                }
                this.discountAmount(discount);
            },
            /**
             * Load balance by customer from local
             * @param customerId
             */
            loadBalanceByCustomerId: function (customerId) {
                var self = this;
                if (customerId) {
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            var balance = parseFloat(response.items[0].point_balance);
                            self.balance(balance);
                            if (balance <= 0) {
                                self.remove();
                            } else {
                                if (self.applied()) {
                                    self.collect();
                                }
                            }
                        } else {
                            self.balance(0);
                            self.remove();
                        }
                    });
                } else {
                    if (self.visible() == true && self.applied() && self.applied() == true) {
                        self.balance(0);
                        self.remove();
                    }
                }
            },
            /**
             * Update balance from local
             */
            updateStorageBalance: function () {
                var self = this;
                if (CartModel.customerId()) {
                    self.getCollection().addFieldToFilter('customer_id', CartModel.customerId(), 'eq');
                    self.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].point_balance));
                        } else {
                            self.balance(0);
                            self.remove();
                        }
                    });
                } else {
                    if (self.visible() == true && self.applied() && self.applied() == true) {
                        self.balance(0);
                        self.remove();
                    }
                }
            },
            /**
             * Load point balance from local by customer id
             * @param customerId
             */
            loadStorageBalanceByCustomerId: function (customerId) {
                var self = this;
                if (customerId) {
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].point_balance));
                        } else {
                            self.balance(0);
                        }
                    });
                } else {
                    self.balance(0);
                }
            },
            /**
             * Save point balance to local
             * @param customerId
             * @param balance
             */
            saveBalance: function (customerId, balance) {
                if (customerId) {
                    var self = this;
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            data.point_balance = balance;
                        } else {
                            data.customer_id = customerId;
                            data.point_balance = balance;
                        }
                        self.setData(data).save();
                    });
                }
            },
            /**
             * add point to balance on local
             * @param customerId
             * @param amount
             */
            addPoint: function (customerId, amount) {
                if (customerId) {
                    var self = this;
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            data.point_balance += amount;
                            self.setData(data).save();
                        }
                    });
                }
            },
            /**
             * remove point from balance on local
             * @param customerId
             * @param amount
             */
            removePoint: function (customerId, amount) {
                if (customerId) {
                    var self = this;
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            data.point_balance -= amount;
                            self.setData(data).save();
                        }
                    });
                }
            },
            /**
             * Reset discount per item
             */
            reset: function () {
                var self = this;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    item.item_point_discount(0);
                    item.item_base_point_discount(0);
                    item.item_point_spent(0);
                });
                this.discountAmount(0);
                this.discountForShipping(0);
                this.baseDiscountForShipping(0);
            },
            /**
             * Process discount per item
             * @param cartBaseTotalAmount
             */
            process: function (cartBaseTotalAmount) {
                var self = this;
                if (cartBaseTotalAmount > 0) {
                    var maxAmount = CartData.getMaxDiscountAmount();
                    ko.utils.arrayForEach(CartData.items(), function (item, index) {
                        maxAmount += (item.item_base_point_discount() ? item.item_base_point_discount() : 0);
                    })
                    var itemsAmountTotal = (cartBaseTotalAmount > maxAmount) ? maxAmount : cartBaseTotalAmount;
                    var amountApplied = 0;
                    var earningPoints = self.earningPoint();
                    ko.utils.arrayForEach(CartData.items(), function (item, index) {
                        var maxAmountItem = CartData.getMaxItemDiscountAmount(item.item_id());
                        maxAmountItem += (item.item_base_point_discount() ? item.item_base_point_discount() : 0);
                        var discountPercent = maxAmountItem / maxAmount;
                        var item_base_amount = (index == CartData.items().length - 1) ? (itemsAmountTotal - amountApplied) : itemsAmountTotal * discountPercent;
                        amountApplied += item_base_amount;
                        var item_amount = Helper.convertPrice(item_base_amount);
                        item.item_base_point_discount(item_base_amount);
                        item.item_point_discount(item_amount);
                        item.item_point_spent(self.convertMoneyToPoint(item_base_amount));
                        if (earningPoints > 0) {
                            var itemEarningPoint = parseFloat(item.row_total()) * parseFloat(self.earningRateValue());
                            item.item_point_earn(self.roundEarning(itemEarningPoint));
                        } else {
                            item.item_point_earn(0);
                        }
                    });
                    if (cartBaseTotalAmount > amountApplied) {
                        var useForShipping = self.getConfig('rewardpoints/spending/spend_for_shipping');
                        if (useForShipping) {
                            // var baseShippingAmount = self.TotalsModel.shippingFee();
                            // baseShippingAmount -= self.TotalsModel.getBaseShippingDiscountAmount();
                            // baseShippingAmount += this.baseDiscountForShipping();
                            // baseShippingAmount = cartBaseTotalAmount - amountApplied;

                            var baseDiscountShipping = cartBaseTotalAmount - amountApplied;
                            // baseDiscountShipping = Math.min(baseDiscountShipping, baseShippingAmount);

                            this.baseDiscountForShipping(baseDiscountShipping);
                            this.discountForShipping(Helper.convertPrice(baseDiscountShipping));
                        }
                    }
                } else {
                    self.reset();
                }
                this.TotalsModel.collectShippingTax();
                this.TotalsModel.collectTaxTotal();
            },
            getMaxItemDiscountAmount: function (item_id, taxAfterDiscount) {
                var max = 0;
                var item = CartData.getItem(item_id);
                if (item !== false) {
                    taxAfterDiscount = (typeof taxAfterDiscount != undefined) ? taxAfterDiscount : CartData.apply_tax_after_discount;
                    max = (taxAfterDiscount == false) ? (item.base_row_total() + item.base_tax_amount()) : item.base_row_total();
                }
                return max;
            },
            /**
             * Collect discount per item
             */
            collect: function () {
                var amount = (this.appliedAmount()) ? this.appliedAmount() : 0;
                this.currentAmount(amount);
                this.apply();
            },

            /**
             * Collect spending rate and earning rate
             */
            collectRates: function () {
                var self = this;
                self.earningRateValue(0);
                self.spendingRateValue(0);
                var earningRates = [];
                var spendingRates = [];
                ko.utils.arrayForEach(self.rates(), function (rate, index) {
                    if (self.canApplyRate(rate, CartModel.customerGroup())) {
                        if (rate.direction === self.TYPE_POINT_TO_MONEY) {
                            var value = parseFloat(rate.money) / parseFloat(rate.points);
                            spendingRates.push({
                                rate: rate,
                                value: value,
                                sort_order: parseFloat(rate.sort_order),
                            });
                        }
                        if (rate.direction === self.TYPE_MONEY_TO_POINT) {
                            var value = parseFloat(rate.points) / parseFloat(rate.money);
                            earningRates.push({
                                rate: rate,
                                value: value,
                                sort_order: parseFloat(rate.sort_order),
                            });
                        }
                    }
                });
                if (earningRates.length > 0) {
                    earningRates.sort(self.sortBy("sort_order"));
                    self.earningRateValue(earningRates[0].value);
                    self.earningRate(earningRates[0]);
                }
                if (spendingRates.length > 0) {
                    spendingRates.sort(self.sortBy("sort_order"));
                    self.spendingRateValue(spendingRates[0].value);
                    self.spendingRate(spendingRates[0]);
                }
            },
            /**
             * Validate customer group allowed to use
             * @param rate
             * @param customerGroupId
             * @returns {boolean}
             */
            canApplyRate: function (rate, customerGroupId) {
                if (rate && rate.customer_group_ids) {
                    var groups = rate.customer_group_ids;
                    groups = groups.split(',');
                    if (typeof customerGroupId == "undefined" || !customerGroupId) {
                        return false;
                    }
                    if (groups.indexOf(this.CUSTOMER_GROUP_ALL) > -1) {
                        return true;
                    }
                    if (groups.indexOf(String(customerGroupId)) > -1) {
                        return true;
                    }
                }
                return false;
            },
            /**
             * Round earning point
             * @param point
             * @returns {*}
             */
            roundEarning: function (point) {
                var roundMethod = this.getConfig('rewardpoints/earning/rounding_method');
                switch (roundMethod) {
                    case 'round':
                        point = Math.round(point);
                        break;
                    case 'ceil':
                        point = Math.ceil(point);
                        break;
                    case 'floor':
                        point = Math.floor(point);
                        break;
                }
                return point;
            },
            /**
             * Collect total can earn point
             * @param point
             * @returns {*}
             */
            collectTotalToEarn: function () {
                var earnByShipping = this.getConfig('rewardpoints/earning/by_shipping');
                var earnByTax = this.getConfig('rewardpoints/earning/by_tax');
                var earnWhenSpend = this.getConfig('rewardpoints/earning/earn_when_spend');
                var cancelState = this.getConfig('rewardpoints/earning/order_cancel_state');
                var earnWhenInvoice = this.getConfig('rewardpoints/earning/order_invoice');
                var total = this.TotalsModel.getTotalValue('grand_total');
                if (earnByShipping == '0' && this.TotalsModel.shippingFee()) {
                    total -= this.TotalsModel.shippingFee();
                }
                if (earnByTax == '0' && this.TotalsModel.tax()) {
                    total -= this.TotalsModel.tax()
                }
                if (earnWhenSpend == '0' && this.applied() && this.appliedAmount() > 0) {
                    total = 0;
                }
                return total;
            },
            /**
             * Convert point to money
             * @param point
             * @returns {number}
             */
            convertPointToMoney: function (point) {
                return parseFloat(point) * parseFloat(this.spendingRateValue())
            },
            /**
             * Convert money to point
             * @param discount
             * @returns {number}
             */
            convertMoneyToPoint: function (discount) {
                return (this.spendingRateValue() > 0) ? Math.ceil(parseFloat(discount) / parseFloat(this.spendingRateValue())) : 0;
            },
            /**
             * Function use to sort json array
             * @param prop
             * @returns {Function}
             */
            sortBy: function (prop) {
                return function (a, b) {
                    if (a[prop] > b[prop]) {
                        return 1;
                    } else if (a[prop] < b[prop]) {
                        return -1;
                    }
                    return 0;
                }
            },
            /**
             * Spend reward points
             * @returns {*}
             */
            spendPointOnline: function () {
                var self = this;
                if (self.loading()) {
                    return false;
                }
                self.loading(true);
                var deferred = $.Deferred();
                var initParams = CartModel.getQuoteInitParams();
                var params = {quote_id: initParams.quote_id};
                params.used_point = self.currentAmount();
                params.rule_id = 'rate';
                CheckoutModel.loading(true);
                onlineResource().setPush(true).setLog(false).spendPoint(params, deferred);
                deferred.always(function () {
                    CheckoutModel.loading(false);
                    self.loading(false);
                });
                return deferred;
            },
            /**
             * Process response data
             * @param responseData
             */
            checkoutCallApiAfter: function (responseData) {
                var self = this;
                if (Helper.isUseOnline('checkout') && responseData) {
                    var data = responseData.data;
                    if (data && data.rewardpoints && data.rewardpoints.used_point > 0) {
                        self.currentAmount(parseFloat(data.rewardpoints.used_point));
                        self.appliedAmount(parseFloat(data.rewardpoints.used_point));
                    }
                    if (data && data.rewardpoints && typeof data.rewardpoints.max_points != 'undefined') {
                        self.maxPoint(parseFloat(data.rewardpoints.max_point));
                    }
                }
            },
            /**
             * Add rewardpoints item info buy request before place order offline
             *
             * @param eventData
             */
            cartItemPrepareInforBuyRequestAfter: function (eventData) {
                if (Helper.isRewardPointsEnable()) {
                    eventData.buy_request.extension_data.push({
                        key: "rewardpoints_earn",
                        value: Helper.correctPrice(eventData.item.item_point_earn())
                    });
                    eventData.buy_request.extension_data.push({
                        key: "rewardpoints_spent",
                        value: Helper.correctPrice(eventData.item.item_point_spent())
                    });
                    eventData.buy_request.extension_data.push({
                        key: "rewardpoints_discount",
                        value: Helper.correctPrice(eventData.item.item_point_discount())
                    });
                    eventData.buy_request.extension_data.push({
                        key: "rewardpoints_base_discount",
                        value: Helper.correctPrice(eventData.item.item_base_point_discount())
                    });
                }
            },
            /**
             * Add rewardpoints item data for offline order before place order offline
             *
             * @param eventData
             */
            cartItemPrepareDataForOfflineOrderAfter: function (eventData) {
                if (Helper.isRewardPointsEnable()) {
                    eventData.rewardpoints_earn = Helper.correctPrice(eventData.item.item_point_earn());
                    eventData.rewardpoints_spent = Helper.correctPrice(eventData.item.item_point_spent());
                    eventData.rewardpoints_discount = Helper.correctPrice(eventData.item.item_point_discount());
                    eventData.rewardpoints_base_discount = Helper.correctPrice(eventData.item.item_base_point_discount());
                }
            },
            orderListLoadOrderAfter: function (eventData) {
                if (eventData.order) {
                    if (!eventData.order.isShownRewardPoints) {
                        if (eventData.order.rewardpoints_base_discount) {
                            eventData.order.base_discount_amount = (eventData.order.base_discount_amount ? eventData.order.base_discount_amount : 0) -
                                eventData.order.rewardpoints_base_discount;
                            eventData.order.discount_amount = (eventData.order.discount_amount ? eventData.order.discount_amount : 0) -
                                eventData.order.rewardpoints_discount;
                            if (eventData.order.items && Array.isArray(eventData.order.items)) {
                                eventData.order.items.forEach(function (item) {
                                    item.base_discount_amount = (item.base_discount_amount ? item.base_discount_amount : 0) -
                                        (item.rewardpoints_base_discount ? item.rewardpoints_base_discount : 0);
                                    item.discount_amount = (item.discount_amount ? item.discount_amount : 0) -
                                        (item.rewardpoints_discount ? item.rewardpoints_discount : 0);

                                });
                            }
                        }
                        eventData.order.isShownRewardPoints = true;
                    }
                }
            }
        });
    }
);