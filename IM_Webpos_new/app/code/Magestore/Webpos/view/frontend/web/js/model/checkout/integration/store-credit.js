/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/integration/storecredit/store-credit',
        'Magestore_Webpos/js/model/resource-model/indexed-db/integration/storecredit/store-credit',
        'Magestore_Webpos/js/model/collection/integration/store-credit',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/view/checkout/cart',
        'Magestore_Webpos/js/model/checkout/cart/data/cart',
        'Magestore_Webpos/js/view/settings/general/storecredit/show-storecredit-balance',
        'Magestore_Webpos/js/view/settings/general/storecredit/auto-sync-balance',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/view/checkout/checkout/payment',
        'Magestore_Webpos/js/view/checkout/checkout/payment_selected',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/checkout/cart/totals'
    ],
    function ($, ko, modelAbstract, onlineResource, offlineResource, collection, CartModel, CartView, CartData, ShowCreditBalance, AutoSyncBalance, Helper, Payment, SelectedPayments, CheckoutModel, TotalsModel) {
        "use strict";
        if (!window.hadObserver) {
            window.hadObserver = [];
        }
        return modelAbstract.extend({
            sync_id: 'customer_credit',
            TOTAL_CODE: 'storecredit',
            MODULE_CODE: 'os_store_credit',
            balance: ko.observable(0),
            currentAmount: ko.observable(0),
            appliedAmount: ko.observable(0),
            visible: ko.observable(false),
            applied: ko.observable(false),
            useMaxPoint: ko.observable(false),
            updatingBalance: ko.observable(false),
            remaining: CheckoutModel.remainTotal,
            baseDiscountForShipping: ko.observable(0),
            initialize: function () {
                if (Helper.isStoreCreditEnable()) {
                    this._super();
                    this.setResource(onlineResource(), offlineResource());
                    this.setResourceCollection(collection());
                    this.TotalsModel = TotalsModel();
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
            getAmountForShipping: function (items) {
                var amount = {
                    base: 0,
                    amount: 0
                };
                if (items && items.length > 0) {
                    var totalBase = 0;
                    var totalAmount = 0;
                    ko.utils.arrayForEach(items, function (item) {
                        if (item.base_customercredit_discount) {
                            totalBase += item.base_customercredit_discount;
                        }
                        if (item.customercredit_discount) {
                            totalAmount += item.customercredit_discount;
                        }
                    });
                    amount.base = Helper.toBasePrice(this.appliedAmount()) - totalBase;
                    amount.amount = this.appliedAmount() - totalAmount;
                }
                return amount;
            },
            initObserver: function () {
                var self = this;
                /**
                 *  Reset credit data after list payment reset
                 */
                // Helper.observerEvent('payments_reset_after', function (event, data) {
                //     self.paymentResetAfter(data);
                // });

                /**
                 * Reset credit data after cart empty
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
                 * Show customer credit balance on receipt
                 */
                Helper.observerEvent('prepare_receipt_totals', function (event, data) {
                    self.prepareReceipt(data);
                });

                /**
                 * Update customer credit balance on local after checkout
                 */
                Helper.observerEvent('webpos_order_save_after', function (event, data) {
                    self.orderSaveAfter(data);
                });

                /**
                 * Add params to sync credit data when syncing order
                 */
                Helper.observerEvent('webpos_place_order_before', function (event, data) {
                    self.placeOrderBefore(data);
                });

                /**
                 * Call api to update customer credit balance after refund by credit
                 */
                Helper.observerEvent('order_refund_after', function (event, data) {
                    self.refundAfter(data);
                });

                /**
                 * Add params to sync credit data when place order online
                 */
                Helper.observerEvent('webpos_place_order_online_before', function (event, data) {
                    self.placeOrderOnlineBefore(data);
                });

                /**
                 * Update customer credit balance on local after checkout
                 */
                Helper.observerEvent('webpos_place_order_online_after', function (event, data) {
                    self.orderSaveAfter(data);
                });

                /**
                //  * After get base total discount amount
                //  */
                // Helper.observerEvent('webpos_total_get_base_discount_amount', function (event, data) {
                //     data.base_discount_amount += self.appliedAmount();
                // });
                //
                //
                // /**
                //  * fter get total discount amount
                //  */
                // Helper.observerEvent('webpos_total_get_discount_amount', function (event, data) {
                //     data.discount_amount += Helper.convertPrice(self.appliedAmount());
                // });
                //
                // /**
                //  * Update customer point balance on local after checkout
                //  */
                // Helper.observerEvent('webpos_total_get_base_shipping_discount_amount', function (event, data) {
                //     data.base_discount_amount += self.baseDiscountForShipping();
                // });
                //
                // /**
                //  * Update base discount item
                //  */
                // Helper.observerEvent('webpos_cart_item_get_base_discount_after', function (event, data) {
                //     data.base_amount += (data.item.item_base_credit_amount() ? data.item.item_base_credit_amount() : 0);
                // });
                //
                // /**
                //  * Update discount item
                //  */
                // Helper.observerEvent('webpos_cart_item_get_discount_after', function (event, data) {
                //     data.amount += (data.item.item_credit_amount() ? data.item.item_credit_amount() : 0);
                // });
            },
            initVariable: function () {
                var self = this;
                self.visible = ko.pureComputed(function () {
                    return self.canUseExtension() &&
                        (self.remaining() > 0 || self.appliedAmount()) &&
                        (!self.balance() || (self.balance() && self.balanceAfterApply() > 0));
                });
                self.applied(false);
                self.balanceAfterApply = ko.pureComputed(function () {
                    return (self.applied()) ? (self.balance() - self.appliedAmount()) : self.balance();
                });
                self.useMaxPoint.subscribe(function (value) {
                    if (value == true) {
                        self.validate(self.balance());
                    }
                });
                self.balance.subscribe(function (value) {
                    if (self.useMaxPoint() == true) {
                        self.currentAmount(self.balance());
                    }
                });
                self.currentAmount.subscribe(function (value) {
                    self.validate(value);
                });

                // self.remaining.subscribe(function (remain) {
                //     self.validate();
                // });
            },
            cartEmptyAfter: function (data) {
                this.remove();
            },
            paymentResetAfter: function (data) {
                this.remove();
            },
            selectCustomerToCheckoutAfter: function (data) {
                var self = this;
                if (self.updatingBalance() == false && CartModel.customerId()) {
                    if (data.customer && data.customer.id) {
                        self.loadBalanceByCustomerId(data.customer.id);
                        var autoSyncBalance = Helper.getLocalConfig(AutoSyncBalance().configPath);
                        if (autoSyncBalance == true) {
                            self.updateBalance();
                        }
                    }
                }
            },
            prepareReceipt: function (data) {
                if (data.customer_id) {
                    var self = this;
                    var showBalance = Helper.getLocalConfig(ShowCreditBalance().configPath);
                    if (CartModel.customerId() && showBalance == true) {
                        var balance = self.balance() - self.appliedAmount();
                        data.accountInfo.push({
                            label: Helper.__('Customer credit balance'),
                            value: Helper.convertAndFormatPrice(balance)
                        });
                    }
                }
            },
            orderSaveAfter: function (data) {
                var self = this;
                if (data.customer_id && self.applied() && self.appliedAmount() > 0) {
                    var balance = self.balance() - self.appliedAmount();
                    self.saveBalance(data.customer_id, balance);
                }
            },
            placeOrderBefore: function (data) {
                var self = this;
                if (data && data.increment_id && CartModel.customerId() && self.applied() && self.appliedAmount() > 0) {
                    data.base_customercredit_discount = -Helper.toBasePrice(self.appliedAmount());
                    data.customercredit_discount = -self.appliedAmount();
                    var order_data = [
                        {
                            key: "base_customercredit_discount",
                            value: -self.appliedAmount()
                        },
                        {
                            key: "customercredit_discount",
                            value: -Helper.convertPrice(self.appliedAmount())
                        }
                    ];
                    var extension_data = [{
                        key: 'base_customercredit_discount',
                        value: self.appliedAmount()
                    }];
                    data.sync_params.integration.push({
                        'module': 'customer_credit',
                        'event_name': 'webpos_use_customer_credit_after',
                        'order_data': order_data,
                        'extension_data': extension_data
                    });
                }
            },
            placeOrderOnlineBefore: function (params) {
                var self = this;
                if (CartModel.customerId() && self.applied() && self.appliedAmount() > 0) {
                    var order_data = [
                        {
                            key: "base_customercredit_discount",
                            value: -self.appliedAmount()
                        },
                        {
                            key: "customercredit_discount",
                            value: -Helper.convertPrice(self.appliedAmount())
                        }
                    ];
                    var extension_data = [{
                        key: 'base_customercredit_discount',
                        value: self.appliedAmount()
                    }];
                    params.integration.push({
                        'module': 'customer_credit',
                        'event_name': 'webpos_use_customer_credit_after',
                        'order_data': order_data,
                        'extension_data': extension_data
                    });
                }
            },
            refundAfter: function (data) {
                var self = this;
                if (data && data.response && data.response.customer_id && data.response.credit_amount_to_refund) {
                    var deferred = $.Deferred();
                    var params = {
                        order_id: data.response.entity_id,
                        order_increment_id: data.response.increment_id,
                        customer_id: data.response.customer_id,
                        amount: data.response.credit_amount_to_refund
                    };
                    self.updateCustomerCreditBalance(data.response.customer_id, data.response.credit_amount_to_refund);

                    onlineResource().setPush(true).setLog(false).refund(params, deferred);
                    deferred.done(function (response) {

                    }).fail(function (response) {
                        if (response.responseText) {
                            var error = JSON.parse(response.responseText);
                            if (error.message != undefined) {
                                Helper.addNotification(error.message, true, 'danger', 'Error');
                            }
                        } else {
                            Helper.addNotification("Please check your network connection", true, 'danger', 'Error');
                        }
                    }).always(function (response) {
                        var data = JSON.parse(response);
                        if (data.message != undefined) {
                            if (data.success) {
                                Helper.addNotification(data.message, true, 'success', 'Message');
                            }
                            if (data.error) {
                                Helper.addNotification(data.message, true, 'danger', 'Error');
                            }
                        }
                    });
                }
            },
            /**
             * Reset data
             */
            resetData: function () {
                this.balance(0);
                this.currentAmount(0);
                this.applied(false);
                this.useMaxPoint(false);
            },
            /**
             * Get credit balance from server and save to local
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
                            //run change event - fix issue does not update balance on UI view
                            self.balance(0);
                            self.balance(data.balance);
                            self.saveBalance(customerId, data.balance);
                        }
                    }).always(function (response) {
                        self.updatingBalance(false);
                    });
                }
            },
            /**
             * Apply credit data to payment
             * @param apply
             */
            apply: function (apply) {
                this.baseDiscountForShipping(0);
                var amount = (apply === false) ? 0 : this.currentAmount();
                var visible = (amount > 0) ? true : false;
                // this.applied(visible);
                this.currentAmount(amount);
                if (visible) {
                    this.appliedAmount(amount);
                } else {
                    this.appliedAmount(0);
                }
                if (amount > 0 && this.remaining() > 0) {
                    Payment().addExtensionMethod({
                        code: this.TOTAL_CODE,
                        is_extension_method: true,
                        is_default: 0,
                        is_pay_later: 0,
                        is_reference_number: 0,
                        title: "Customer Credit",
                        type: 0,
                        price: Helper.convertPrice(amount),
                        base_price: amount,
                        paid_amount: Helper.convertPrice(amount),
                        base_paid_amount: amount,
                        cart_total: Helper.convertPrice(amount),
                        base_cart_total: amount,
                        actions: {
                            remove: $.proxy(this.remove, this)
                        }
                    });
                }

                this.applied((apply === false || amount == 0) ? false : true);
                // else {
                // this.TotalsModel.addTotal({
                //     code: this.TOTAL_CODE,
                //     cssClass: 'discount',
                //     title: Helper.__('Customer Credit'),
                //     value: -this.currentAmount(),
                //     baseValue: -Helper.toBasePrice(this.currentAmount()),
                //     isVisible: visible,
                //     removeAble: true,
                //     actions: {
                //         remove: $.proxy(this.remove, this),
                //         collect: $.proxy(this.collect, this)
                //     }
                // });
                // this.TotalsModel.updateTotal(this.TOTAL_CODE, {isVisible: visible});
                // }
                // this.process(amount);
            },
            /**
             * Remove applied credit data
             */
            remove: function () {
                this.useMaxPoint(false);
                this.apply(false);
            },
            /**
             * Check module enable and some condition to use credit on checkout
             * @returns {boolean}
             */
            canUseExtension: function () {
                var self = this;
                var customerId = CartModel.customerId();
                var customerGroup = CartModel.customerGroup();
                var moduleEnable = parseInt(this.getConfig('customercredit/general/enable'));
                var customerGroups = this.getConfig('customercredit/general/assign_credit');
                var hasCreditProduct = !CartModel.hasStorecredit();
                var canuse = (
                    moduleEnable
                    && customerId
                    && customerGroup
                    && hasCreditProduct
                    && $.inArray(customerGroup.toString(), customerGroups.split(',')) >= 0
                ) ? true : false;
                return canuse;
            },
            /**
             * Validate input data before apply
             * @param balance
             * @returns {boolean}
             */
            validate: function (balance) {
                if (!this.canUseExtension()) {
                    if (this.visible() == true && this.applied() && this.applied() == true) {
                        this.remove();
                    }
                    return false;
                }
                var amount = 0;
                if (!balance) {
                    amount = this.currentAmount();
                } else {
                    amount = balance;
                }
                var max = this.collectValidTotal();
                // var appliedAmount = (this.applied()) ? this.appliedAmount() : 0;
                // var max = this.remaining() + appliedAmount;
                if (max > this.balance()) {
                    max = this.balance();
                }
                amount = (amount > max || (this.useMaxPoint() == true)) ? max : amount;
                if (this.currentAmount() > amount || balance || !this.applied()) {
                    amount = (parseFloat(amount) > 0) ? amount : 0;
                    this.currentAmount(parseFloat(amount));
                }
                return true;
            },
            /**
             * Prepare valid total to discount
             * 
             * @returns {number}
             */
            collectValidTotal: function () {
                var validTotal = 0;
                if (Helper.isUseOnline('checkout')) {
                    validTotal -= parseFloat(this.TotalsModel.getOnlineValue(this.TOTAL_CODE));
                } else {
                    // var grandTotal = (self.TotalsModel.grandTotal() > 0) ? self.TotalsModel.grandTotal() : 0;
                    var grandTotal = this.TotalsModel.grandTotal();
                    var discountAmount = (this.appliedAmount()) ? this.appliedAmount() : 0;
                    var applyAfterDiscount = (Helper.getBrowserConfig('tax/calculation/apply_after_discount')) ? '0' : '1';
                    validTotal = (applyAfterDiscount == '0') ? (grandTotal + discountAmount - this.TotalsModel.tax()) : (grandTotal + discountAmount);
                    var useForShipping = this.getConfig('customercredit/spend/shipping');
                    if (useForShipping == '0') {
                        validTotal -= this.TotalsModel.shippingFee();
                    }
                    validTotal = validTotal.toFixed(12);
                }

                return validTotal;
            },
            /**
             * Load customer credit balance from local by customer id
             * @param customerId
             */
            loadBalanceByCustomerId: function (customerId) {
                var self = this;
                if (customerId) {
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].credit_balance));
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
             * Load customer in cart credit balance from local
             */
            updateStorageBalance: function () {
                var self = this;
                if (CartModel.customerId()) {
                    self.getCollection().addFieldToFilter('customer_id', CartModel.customerId(), 'eq');
                    self.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].credit_balance));
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
             * load balance save on local by customer id
             * @param customerId
             */
            loadStorageBalanceByCustomerId: function (customerId) {
                var self = this;
                if (customerId) {
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].credit_balance));
                        } else {
                            self.balance(0);
                        }
                    });
                } else {
                    self.balance(0);
                }
            },
            /**
             * Update blance on local for customer by id
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
                            data.credit_balance = balance;
                        } else {
                            data.customer_id = customerId;
                            data.credit_balance = balance;
                        }
                        self.setData(data).save();
                    });
                }
            },
            /**
             * Update balance on local by amount
             * @param customerId
             * @param amount
             */
            updateCustomerCreditBalance: function (customerId, amount) {
                if (customerId) {
                    var self = this;
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            var currentBalance = parseFloat(data.credit_balance);
                            currentBalance += parseFloat(amount);
                            data.credit_balance = currentBalance;
                            self.setData(data).save();
                        }
                    });
                }
            },
            /* S: Calculate amount for each cart items - use this functionality if credit works the same as discount (not payment mode) */
            reset: function () {
                var self = this;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    item.item_credit_amount(0);
                    item.item_base_credit_amount(0);
                });
                this.baseDiscountForShipping(0);
            },
            process: function (cartBaseTotalAmount) {
                var self = this;
                if (cartBaseTotalAmount > 0) {
                    var maxAmount = CartData.getMaxDiscountAmount();
                    ko.utils.arrayForEach(CartData.items(), function (item, index) {
                        maxAmount += (item.item_base_credit_amount() ? item.item_base_credit_amount() : 0);
                    })
                    var itemsAmountTotal = (cartBaseTotalAmount > maxAmount) ? maxAmount : cartBaseTotalAmount;
                    var amountApplied = 0;
                    ko.utils.arrayForEach(CartData.items(), function (item, index) {
                        var maxAmountItem = CartData.getMaxItemDiscountAmount(item.item_id());
                        maxAmountItem += (item.item_base_credit_amount() ? item.item_base_credit_amount() : 0);
                        var discountPercent = maxAmountItem / maxAmount;
                        var item_base_amount = (index == CartData.items().length - 1) ? (itemsAmountTotal - amountApplied) : itemsAmountTotal * discountPercent;
                        amountApplied += item_base_amount;
                        var item_amount = Helper.convertPrice(item_base_amount);
                        item.item_base_credit_amount(item_base_amount);
                        item.item_credit_amount(item_amount);
                    });
                    if (cartBaseTotalAmount > amountApplied) {
                        var useForShipping = self.getConfig('customercredit/spend/shipping');
                        if (useForShipping) {
                            var baseDiscountShipping = cartBaseTotalAmount - amountApplied;
                            this.baseDiscountForShipping(baseDiscountShipping);
                        }
                    }
                } else {
                    self.reset();
                }
                this.TotalsModel.collectShippingTax();
                this.TotalsModel.collectTaxTotal();
            },
            collect: function () {
                var amount = (this.appliedAmount()) ? this.appliedAmount() : 0;
                this.currentAmount(amount);
                this.apply();
            },
            /* E: Calculate amount for each cart items */
        });
    }
);