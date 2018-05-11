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
        'model/resource-model/magento-rest/integration/rewardpoints/reward-points',
        'model/checkout/integration/data/reward-points',
        'model/checkout/cart',
        'model/checkout/cart/totals',
        'model/checkout/cart/items',
        'helper/general',
        'model/checkout/integration/rewardpoints/rate',
        'model/checkout/checkout',
        'dataManager'
    ],
    function ($, ko, onlineResource, PointsModel, CartModel, Totals, Items, Helper, RateModel, CheckoutModel, DataManager) {
        "use strict";
        var RewardPointsModel = {
            TOTAL_CODE:'rewardpoints',
            MODULE_CODE:'os_reward_points',
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
            useMaxPoint: ko.observable(true),
            updatingBalance: ko.observable(false),
            remaining: CheckoutModel.remainTotal,
            rates: ko.observableArray([]),
            earningRateValue: ko.observable(0),
            spendingRateValue: ko.observable(0),
            earningRate: ko.observable(),
            spendingRate: ko.observable(),
            discountAmount: ko.observable(),
            initialize: function () {
                if(Helper.isRewardPointsEnable()) {
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
            loadRates: function() {
                var self = this;
                var deferred = RateModel().getCollection().setOrder('sort_order', 'DESC').load();
                deferred.done(function(data){
                    self.rates(data.items);
                });
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
                        if(item.item_base_point_discount){
                            totalBase += item.item_base_point_discount;
                        }
                        if(item.item_point_discount){
                            totalAmount += item.item_point_discount;
                        }
                    });
                    amount.base = Helper.toBasePrice(this.discountAmount()) - totalBase;
                    amount.amount = this.discountAmount() - totalAmount;
                }
                return amount;
            },
            initObserver: function(){
                var self = this;
                self.hadObserver = true;
                self.loadRates();
                self.visible = ko.pureComputed(function(){
                    return self.canUseExtension();
                });

                self.applied(false);

                self.balanceAfterApply = ko.pureComputed(function () {
                    return (self.applied()) ? (self.balance() - self.appliedAmount()) : self.balance();
                });

                self.earningPoint = ko.pureComputed(function () {
                    var point = 0;
                    if(self.earningRateValue() > 0 && self.canUseExtension()){
                        point = parseFloat(self.collectTotalToEarn()) * parseFloat(self.earningRateValue());
                        point = self.roundEarning(point);
                    }
                    return point;
                });

                self.earningPoint.subscribe(function (value) {
                    if(!Helper.isOnlineCheckout()){
                        var visible = (value > 0)?true:false;
                        self.TotalsModel.addAdditionalInfo({
                            code:'rewardpoints_earning_point',
                            title:Helper.__('Customer will earn ')+value+Helper.__(' point(s)'),
                            value:'',
                            visible:visible
                        });
                    }
                });

                self.useMaxPoint.subscribe(function (value) {
                    if (value == true) {
                        if(Helper.isOnlineCheckout()){
                            if(self.maxPoint()){
                                self.currentAmount(self.maxPoint());
                            }else{
                                self.validate(self.balance(),self.convertMoneyToPoint(self.TotalsModel.positiveTotal()));
                            }
                            if(self.canUseExtension()){
                                self.spendPointOnline();
                            }
                        }else{
                            self.validate(self.balance(),self.convertMoneyToPoint(self.TotalsModel.positiveTotal()));
                        }
                    }
                });
                self.balance.subscribe(function (value) {
                    self.updateBalance();
                    if (self.useMaxPoint() == true) {
                        self.currentAmount(self.balance());
                    }
                });
                self.currentAmount.subscribe(function (value) {
                    // self.validate();
                });

                self.TotalsModel.positiveTotal.subscribe(function (value) {
                    if(!Helper.isOnlineCheckout()) {
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
                            self.useMaxPoint(true);
                        }
                    }
                });
                self.TotalsModel.baseDiscountAmount.subscribe(function (value) {
                    if(!Helper.isOnlineCheckout()){
                        if (self.applied() && self.applied() == true) {
                            var maxAmount = self.TotalsModel.positiveTotal() - value;
                            var valid = self.validate(self.appliedAmount(), self.convertMoneyToPoint(maxAmount));
                            if (valid) {
                                self.apply();
                            }
                        }else {
                            var maxAmount = self.TotalsModel.positiveTotal() - value;
                            self.currentAmount(self.convertMoneyToPoint(maxAmount));
                            self.useMaxPoint(true);
                        }
                    }
                });

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
                    if(data && data.customer){
                        var isGuest = self.isUseGuestCustomer(data.customer);
                        if(!isGuest){
                            self.selectCustomerToCheckoutAfter(data);
                        }
                    }
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
                    if(!Helper.isOnlineCheckout()){
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
                Helper.observerEvent('rewardpoint_rates_finish_pull_after',function(event,data){
                    self.loadRates();
                });

                /**
                 * Collect rates
                 */
                Helper.observerEvent('go_to_checkout_page',function(event,data){
                    self.collectRates();
                    var useMaxPoint = self.getConfig('rewardpoints/spending/max_point_default');
                    if(self.appliedAmount() <= 0){
                        self.useMaxPoint(false);
                        if(useMaxPoint == self.STATUS_ACTIVE){
                            self.useMaxPoint(true);
                        }
                    }
                });

                /**
                 * Process data after call api
                 */
                Helper.observerEvent('checkout_call_api_after',function(event, data){
                    self.checkoutCallApiAfter(data);
                });

                /**
                 * Update customer point balance on local after checkout
                 */
                Helper.observerEvent('webpos_place_order_online_after', function (event, data) {
                    self.orderSaveAfter(data.data);
                });
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
             * Event after select customer - load balance and collect rates
             * @param data
             */
            selectCustomerToCheckoutAfter: function(data){
                var self = this;
                if (self.updatingBalance() == false && CartModel.customerId()) {
                    if (data.customer && data.customer.id) {
                        self.loadBalanceByCustomerId(data.customer.id);
                        var autoSyncBalance = Helper.isAutoSyncRewardPointsBalance();
                        if(autoSyncBalance == true){
                            self.updateBalance();
                        }
                        self.collectRates();
                    }
                }
            },
            /**
             *
             * @param customer
             * @returns {boolean}
             */
            isUseGuestCustomer: function(customer){
                var customerId = (customer)?customer.id:CartModel.customerId();
                var defaultCustomer = DataManager.getData('default_customer');
                return (customerId && defaultCustomer && (customerId == defaultCustomer.id))?true:false;
            },
            /**
             * Event before show receipt - Add data to show on receipt
             * @param data
             */
            prepareReceipt: function(data){
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
                    var showBalance = Helper.isShowPointsBalanceOnReceipt();
                    if (CartModel.customerId() && showBalance == true) {
                        var balance = self.balance() - self.appliedAmount();
                        var earnWhenInvoice = self.getConfig('rewardpoints/earning/order_invoice');
                        var holdDays = Helper.toNumber(self.getConfig('rewardpoints/earning/holding_days'));
                        if(holdDays <= 0) {
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
            orderSaveAfter: function(data){
                var self = this;
                var balance = self.balance();
                if (data && data.customer_id && data.rewardpoints_spent > 0) {
                    balance -= data.rewardpoints_spent;
                }
                var earnWhenInvoice = self.getConfig('rewardpoints/earning/order_invoice');
                var holdDays = Helper.toNumber(self.getConfig('rewardpoints/earning/holding_days'));
                if(holdDays <= 0) {
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
                if(balance != self.balance()){
                    self.saveBalance(data.customer_id, balance);
                }
            },
            /**
             * Event place order before - add data to order
             * @param data
             */
            placeOrderBefore: function(data){
                var self = this;
                if (data && data.increment_id && CartModel.customerId()) {
                    var order_data = [];
                    if(self.earningPoint() > 0){
                        data.rewardpoints_earn = self.earningPoint();
                        order_data.push({
                            key: "rewardpoints_earn",
                            value: self.earningPoint()
                        });
                    }
                    if(self.applied() && self.appliedAmount() > 0){
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
                        if(self.getAmountForShipping(data.items).amount > 0){
                            order_data.push({
                                key: "rewardpoints_amount",
                                value: self.getAmountForShipping(data.items).amount
                            });
                            order_data.push({
                                key: "rewardpoints_base_amount",
                                value: self.getAmountForShipping(data.items).base
                            });
                        }
                    }
                    if(order_data.length > 0){
                        data.sync_params.integration.push({
                            'module': 'os_reward_points',
                            'event_name': 'webpos_create_order_with_points_after',
                            'order_data': order_data,
                            'extension_data': []
                        });
                    }
                }
            },
            refundAfter: function(data){
                var self = this;
                if (data && data.response && data.response.customer_id) {
                    if(data.response.rewardpoints_earn > 0 || data.response.rewardpoints_spent > 0){
                        self.updateBalance(data.response.customer_id);
                    }
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
                this.earningRateValue(0);
                this.spendingRateValue(0);
                this.earningRate({});
                this.spendingRate({});
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
            updateBalance: function(customerId){
                customerId = (customerId)?customerId:CartModel.customerId();
                if(customerId){
                    var self = this;
                    var deferred = $.Deferred();
                    var params = {
                        customer_id: customerId
                    };
                    onlineResource().setPush(true).setLog(false).getBalance(params,deferred);
                    self.updatingBalance(true);
                    deferred.done(function (response) {
                        var data =  response.data;
                        if(data && typeof data.balance != undefined){
                            self.balance(data.balance);
                            self.saveBalance(customerId, data.balance);
                            self.balance(data.balance);
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
            apply: function(apply){
                var amount = (apply === false)?0:this.currentAmount();
                var visible = (amount > 0)?true:false;
                this.applied(visible);
                this.currentAmount(amount);
                if(visible){
                    this.appliedAmount(amount);
                }else{
                    this.appliedAmount(0);
                }
                this.collectDiscountAmount();
                this.TotalsModel.addTotal({
                    code: this.TOTAL_CODE,
                    cssClass: 'discount',
                    title: Helper.__('Points Discount'),
                    value: -this.discountAmount(),
                    isVisible: visible,
                    removeAble: true,
                    actions:{
                        remove: $.proxy(this.remove, this),
                        collect: $.proxy(this.collect, this)
                    }
                });
                this.TotalsModel.updateTotal('rewardpoints',{isVisible: visible});
                this.process(Helper.toBasePrice(this.discountAmount()));
                Helper.dispatchEvent('reset_payments_data', '');
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
                var self = this;
                var customerId = CartModel.customerId();
                var moduleEnable = Helper.isRewardPointsEnable();
                var canuse = (
                    moduleEnable
                    && customerId
                    && !self.isUseGuestCustomer()
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
                var self = this;
                if(!self.canUseExtension()){
                    if(self.visible() == true && self.applied() && self.applied() == true){
                        self.remove();
                    }
                    return false;
                }
                var amount = 0;
                if(!balance){
                    amount = self.currentAmount();
                }else{
                    amount = balance;
                }
                var validTotal = self.collectValidTotal();
                var max = (max)?max:self.convertMoneyToPoint(validTotal);
                if(max > self.balance()){
                    max = self.balance();
                }
                if(self.spendingRate() && self.spendingRate().rate){
                    var point = parseFloat(self.spendingRate().rate.points);
                    var floorAmount = Math.floor(max/point) * point;
                    var ceilAmount = Math.ceil(max/point) * point;
                    var validAmount = (ceilAmount <= self.balance())?ceilAmount:floorAmount;
                    max = parseFloat(validAmount);
                }
                amount = (amount > max || (self.useMaxPoint() == true))?max:amount;
                if(self.currentAmount() > amount || balance){
                    amount = (parseFloat(amount) > 0)?amount:0;
                    self.currentAmount(parseFloat(amount));
                }

                if(self.spendingRate() && self.spendingRate().rate) {
                    var point = parseFloat(self.spendingRate().rate.points);
                    var floorAmount = Math.floor(self.currentAmount() / point) * point;
                    var ceilAmount = Math.ceil(self.currentAmount() / point) * point;
                    var validAmount = (ceilAmount <= self.balance()) ? ceilAmount : floorAmount;
                    self.currentAmount(parseFloat(validAmount));
                }
                return true;
            },
            collectValidTotal: function(){
                var self = this;
                var grandTotal = (self.TotalsModel.grandTotal() > 0)?self.TotalsModel.grandTotal():0;
                var discountAmount = (self.discountAmount())?self.discountAmount():0;
                var applyAfterTax = (Helper.getBrowserConfig('tax/calculation/apply_after_discount'))?'0':'1';
                var validTotal = (applyAfterTax == '0')?(grandTotal + discountAmount - self.TotalsModel.tax()):(grandTotal + discountAmount);
                var useForShipping = self.getConfig('rewardpoints/spending/spend_for_shipping');
                if(useForShipping == '0'){
                    validTotal -= self.TotalsModel.shippingFee();
                }
                if(Helper.isOnlineCheckout()){
                    validTotal -= parseFloat(self.TotalsModel.getOnlineValue(self.TOTAL_CODE));
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
                var applyAfterTax = (Helper.getBrowserConfig('tax/calculation/apply_after_discount'))?'0':'1';
                if(applyAfterTax == '0'){
                    max -= Helper.toNumber(this.TotalsModel.tax());
                }
                var useForShipping = this.getConfig('rewardpoints/spending/spend_for_shipping');
                if(useForShipping == '0'){
                    max -= Helper.toNumber(this.TotalsModel.shippingFee());
                }
                return max;
            },
            collectDiscountAmount: function(){
                var discount = 0;
                if(this.spendingRateValue() > 0 && this.applied() && this.appliedAmount() > 0){
                    discount = this.convertPointToMoney(this.appliedAmount());
                    var max = this.collectMaxTotalToDiscount();
                    if(discount > max){
                        discount = max;
                    }
                }
                this.discountAmount(discount);
            },
            /**
             * Load balance by customer from local
             * @param customerId
             */
            loadBalanceByCustomerId: function(customerId){
                var self = this;
                if(customerId) {
                    var model = PointsModel();
                    model.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    model.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            var balance = parseFloat(response.items[0].point_balance);
                            self.balance(balance);
                            if(balance <= 0){
                                self.remove();
                            }else{
                                if(self.applied()){
                                    self.collect();
                                }
                            }
                        }else{
                            self.balance(0);
                            self.remove();
                        }
                    });
                }else{
                    if(self.visible() == true && self.applied() && self.applied() == true){
                        self.balance(0);
                        self.remove();
                    }
                }
            },
            /**
             * Update balance from local
             */
            updateStorageBalance: function(){
                var self = this;
                if(CartModel.customerId()) {
                    var model = PointsModel();
                    model.getCollection().addFieldToFilter('customer_id', CartModel.customerId(), 'eq');
                    model.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].point_balance));
                        }else{
                            self.balance(0);
                            self.remove();
                        }
                    });
                }else{
                    if(self.visible() == true && self.applied() && self.applied() == true){
                        self.balance(0);
                        self.remove();
                    }
                }
            },
            /**
             * Load point balance from local by customer id
             * @param customerId
             */
            loadStorageBalanceByCustomerId: function(customerId){
                var self = this;
                if(customerId){
                    var model = PointsModel();
                    model.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    model.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].point_balance));
                        }else{
                            self.balance(0);
                        }
                    });
                }else{
                    self.balance(0);
                }
            },
            /**
             * Save point balance to local
             * @param customerId
             * @param balance
             */
            saveBalance: function(customerId, balance){
                if(customerId) {
                    var self = this;
                    var model = PointsModel();
                    model.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    model.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            data.point_balance = balance;
                        }else{
                            data.customer_id = customerId;
                            data.point_balance = balance;
                        }
                        model.setData(data).save();
                    });
                }
            },
            /**
             * add point to balance on local
             * @param customerId
             * @param amount
             */
            addPoint: function(customerId, amount){
                if(customerId) {
                    var self = this;
                    var model = PointsModel();
                    model.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    model.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            data.point_balance += amount;
                            model.setData(data).save();
                        }
                    });
                }
            },
            /**
             * remove point from balance on local
             * @param customerId
             * @param amount
             */
            removePoint: function(customerId, amount){
                if(customerId) {
                    var self = this;
                    var model = PointsModel();
                    model.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    model.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            data.point_balance -= amount;
                            model.setData(data).save();
                        }
                    });
                }
            },
            /**
             * Reset discount per item
             */
            reset: function(){
                var self = this;
                ko.utils.arrayForEach(Items.items(), function (item) {
                    item.item_point_discount(0);
                    item.item_base_point_discount(0);
                });
            },
            /**
             * Process discount per item
             * @param cartBaseTotalAmount
             */
            process: function(cartBaseTotalAmount){
                var self = this;
                if(cartBaseTotalAmount > 0){
                    var maxAmount = Items.getMaxDiscountAmount();
                    var itemsAmountTotal = (cartBaseTotalAmount > maxAmount)?maxAmount:cartBaseTotalAmount;
                    var amountApplied = 0;
                    ko.utils.arrayForEach(Items.items(), function (item, index) {
                        var maxAmountItem = Items.getMaxItemDiscountAmount(item.item_id());
                        var discountPercent = maxAmountItem/maxAmount;
                        var item_base_amount = (index == Items.items().length - 1)?(itemsAmountTotal - amountApplied):itemsAmountTotal*discountPercent;
                        amountApplied += item_base_amount;
                        var item_amount = Helper.convertPrice(item_base_amount);
                        item.item_base_point_discount(item_base_amount);
                        item.item_point_discount(item_amount);
                        item.item_point_spent(self.convertMoneyToPoint(item_base_amount));
                        if(self.earningPoint() > 0){
                            var itemEarningPoint = parseFloat(item.row_total()) * parseFloat(self.earningRateValue());
                            item.item_point_earn(self.roundEarning(itemEarningPoint));
                        }
                    });
                }else{
                    self.reset();
                }
            },
            /**
             * Collect discount per item
             */
            collect: function(){
                var amount = (this.appliedAmount())?this.appliedAmount():0;
                this.currentAmount(amount);
                this.apply();
            },

            /**
             * Collect spending rate and earning rate
             */
            collectRates: function() {
                var self = this;
                self.earningRateValue(0);
                self.spendingRateValue(0);
                var earningRates = [];
                var spendingRates = [];
                ko.utils.arrayForEach(self.rates(), function (rate, index) {
                    if(self.canApplyRate(rate, CartModel.customerGroup())) {
                        if(rate.direction === self.TYPE_POINT_TO_MONEY) {
                            var value = parseFloat(rate.money) / parseFloat(rate.points);
                            spendingRates.push({
                                rate:rate,
                                value:value,
                                sort_order:parseFloat(rate.sort_order),
                            });
                        }
                        if(rate.direction === self.TYPE_MONEY_TO_POINT) {
                            var value = parseFloat(rate.points) / parseFloat(rate.money);
                            earningRates.push({
                                rate:rate,
                                value:value,
                                sort_order:parseFloat(rate.sort_order),
                            });
                        }
                    }
                });
                if(earningRates.length > 0){
                    earningRates.sort(self.sortBy("sort_order"));
                    self.earningRateValue(earningRates[0].value);
                    self.earningRate(earningRates[0]);
                }
                if(spendingRates.length > 0){
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
            canApplyRate: function(rate, customerGroupId) {
                if(rate && rate.customer_group_ids){
                    var groups = rate.customer_group_ids;
                    groups = groups.split(',');
                    if(typeof customerGroupId == "undefined" || !customerGroupId) {
                        return false;
                    }
                    if(groups.indexOf(this.CUSTOMER_GROUP_ALL) > -1) {
                        return true;
                    }
                    if(groups.indexOf(String(customerGroupId)) > -1) {
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
            roundEarning: function(point){
                var roundMethod = this.getConfig('rewardpoints/earning/rounding_method');
                switch(roundMethod){
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
            collectTotalToEarn: function(){
                var earnByShipping = this.getConfig('rewardpoints/earning/by_shipping');
                var earnByTax = this.getConfig('rewardpoints/earning/by_tax');
                var earnWhenSpend = this.getConfig('rewardpoints/earning/earn_when_spend');
                var cancelState = this.getConfig('rewardpoints/earning/order_cancel_state');
                var earnWhenInvoice = this.getConfig('rewardpoints/earning/order_invoice');
                var total = this.TotalsModel.grandTotal();
                if(earnByShipping == '0' && this.TotalsModel.shippingFee()){
                    total -= this.TotalsModel.shippingFee();
                }
                if(earnByTax == '0' && this.TotalsModel.tax()){
                    total -= this.TotalsModel.tax()
                }
                if(earnWhenSpend == '0' && this.applied() && this.appliedAmount() >0){
                    total = 0;
                }
                return total;
            },
            /**
             * Convert point to money
             * @param point
             * @returns {number}
             */
            convertPointToMoney: function(point){
                return parseFloat(point) * parseFloat(this.spendingRateValue())
            },
            /**
             * Convert money to point
             * @param discount
             * @returns {number}
             */
            convertMoneyToPoint: function(discount){
                return (this.spendingRateValue() > 0)?Math.ceil(parseFloat(discount) / parseFloat(this.spendingRateValue())):0;
            },
            /**
             * Function use to sort json array
             * @param prop
             * @returns {Function}
             */
            sortBy: function(prop){
                return function(a,b){
                    if( a[prop] > b[prop]){
                        return 1;
                    }else if( a[prop] < b[prop] ){
                        return -1;
                    }
                    return 0;
                }
            },
            /**
             * Spend reward points
             * @returns {*}
             */
            spendPointOnline: function(){
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                params.data = {use_point: self.currentAmount(), rule_id: 'rate'};
                CheckoutModel.loading(true);
                onlineResource().setPush(true).setLog(false).spendPoint(params,deferred);
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
                    if(data && data.rewardpoints && data.rewardpoints.used_point > 0){
                        self.currentAmount(data.rewardpoints.used_point);
                        self.appliedAmount(data.rewardpoints.used_point);
                    }
                    if(data && data.rewardpoints && typeof data.rewardpoints.max_point != 'undefined'){
                        self.maxPoint(data.rewardpoints.max_point);
                    }
                }
            }
        };
        return RewardPointsModel.initialize();
    }
);