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
        'model/resource-model/magento-rest/integration/storecredit/store-credit',
        'model/checkout/integration/data/store-credit',
        'model/checkout/cart',
        'model/checkout/cart/items',
        'model/checkout/cart/totals',
        'model/checkout/checkout/payment',
        'helper/general',
        'model/checkout/checkout',
        'dataManager'
    ],
    function ($, ko, onlineResource, CreditModel, CartModel, Items, Totals, PaymentModel, Helper, CheckoutModel, DataManager) {
        "use strict";
        var CustomerCreditModel = {
            TOTAL_CODE:'storecredit',
            MODULE_CODE:'os_store_credit',
            balance: ko.observable(0),
            currentAmount: ko.observable(0),
            appliedAmount: ko.observable(0),
            visible: ko.observable(false),
            applied: ko.observable(false),
            useMaxPoint: ko.observable(true),
            updatingBalance: ko.observable(false),
            remaining: CheckoutModel.remainTotal,
            initialize: function () {
                if(Helper.isStoreCreditEnable()){
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
                        if(item.base_customercredit_discount){
                            totalBase += item.base_customercredit_discount;
                        }
                        if(item.customercredit_discount){
                            totalAmount += item.customercredit_discount;
                        }
                    });
                    amount.base = Helper.toBasePrice(this.appliedAmount()) - totalBase;
                    amount.amount = this.appliedAmount() - totalAmount;
                }
                return amount;
            },
            initObserver: function(){
                var self = this;
                self.applied(false);
                self.balanceAfterApply = ko.pureComputed(function () {
                    return (self.applied()) ? (self.balance() - self.appliedAmount()) : self.balance();
                });
                self.visible = ko.pureComputed(function(){
                    self.balanceAfterApply();
                    return self.canUseExtension() && self.remaining() > 0 && (!self.balance() || (self.balance() && self.balanceAfterApply() > 0));
                });
                self.visible.subscribe(function(visible){
                    // re render UI view - fix issue does not update balance
                    var balance = self.balance();
                    self.balance(0);
                    self.balance(balance);
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

                self.remaining.subscribe(function (remain) {
                    self.validate();
                });

                Totals.grandTotal.subscribe(function(value){
                    if(self.applied()){
                        self.remove();
                    }
                });

                /**
                 *  Reset credit data after list payment reset
                 */
                Helper.observerEvent('payments_reset_after', function (event, data) {
                    self.paymentResetAfter(data);
                });

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
                    if(data && data.customer){
                        var isGuest = self.isUseGuestCustomer(data.customer);
                        if(!isGuest){
                            self.selectCustomerToCheckoutAfter(data);
                        }
                    }
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
                    self.orderSaveAfter(data.data);
                });
            },
            cartEmptyAfter: function(data){
                this.remove();
            },
            paymentResetAfter: function(data){
                this.remove();
            },
            selectCustomerToCheckoutAfter: function(data){
                var self = this;
                if (self.updatingBalance() == false && CartModel.customerId()) {
                    if (data.customer && data.customer.id) {
                        self.loadBalanceByCustomerId(data.customer.id);
                        var autoSyncBalance = Helper.isAutoSyncCreditBalance();
                        if(autoSyncBalance == true){
                            self.updateBalance();
                        }
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
            prepareReceipt: function(data){
                if (data.customer_id) {
                    var self = this;
                    var showBalance = Helper.isShowCreditBalanceOnReceipt();
                    if (CartModel.customerId() && showBalance == true) {
                        var balance = self.balance() - self.appliedAmount();
                        data.accountInfo.push({
                            label: Helper.__('Customer credit balance'),
                            value: Helper.convertAndFormatPrice(balance)
                        });
                    }
                }
            },
            orderSaveAfter: function(data){
                var self = this;
                if (data.customer_id && self.applied() && self.appliedAmount() > 0) {
                    var balance = self.balance() - self.appliedAmount();
                    self.saveBalance(data.customer_id, balance);
                }
            },
            placeOrderBefore: function(data){
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
                        key:'base_customercredit_discount',
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
            placeOrderOnlineBefore: function(params){
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
                        key:'base_customercredit_discount',
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
            refundAfter: function(data){
                var self = this;
                if (data && data.response && data.response.customer_id && data.response.credit_amount_to_refund) {
                    var deferred = $.Deferred();
                    var params = {
                        order_id: data.response.entity_id,
                        increment_id: data.response.increment_id,
                        customer_id: data.response.customer_id,
                        amount: data.response.credit_amount_to_refund
                    };
                    // self.updateCustomerCreditBalance(data.response.customer_id , data.response.credit_amount_to_refund);
                    // onlineResource().setPush(true).setLog(false).refund(params,deferred);
                }
            },
            /**
             * Reset data
             */
            resetData: function(){
                this.balance(0);
                this.currentAmount(0);
                this.applied(false);
                this.useMaxPoint(true);
            },
            /**
             * Get credit balance from server and save to local
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
            apply: function(apply){
                var amount = (apply === false)?0:this.currentAmount();
                var visible = (amount > 0)?true:false;
                this.currentAmount(amount);
                if(visible){
                    this.appliedAmount(amount);
                }else{
                    this.appliedAmount(0);
                }
                this.process(amount);
                if(amount > 0 && this.remaining() > 0){
                    PaymentModel.addExtensionPayment({
                        code:this.TOTAL_CODE,
                        is_extension_method:true ,
                        is_default:"0" ,
                        is_pay_later:0 ,
                        is_reference_number:"0",
                        title:"Customer Credit",
                        type:"0",
                        price:amount,
                        paid_amount:amount,
                        cart_total:amount,
                        actions:{
                            remove: $.proxy(this.remove, this)
                        }
                    });
                }
                this.applied((apply === false || amount == 0)?false:true);
            },
            /**
             * Remove applied credit data
             */
            remove: function(){
                var self = this;
                self.appliedAmount(0);
                self.useMaxPoint(true);
                self.currentAmount(this.remaining());
                if(self.applied() == true){
                    self.applied(false);
                }
            },
            /**
             * Check module enable and some condition to use credit on checkout
             * @returns {boolean}
             */
            canUseExtension: function(){
                var self = this;
                var customerId = CartModel.customerId();
                var customerGroup = CartModel.customerGroup();
                var moduleEnable = this.getConfig('customercredit/general/enable');
                var customerGroups = this.getConfig('customercredit/general/assign_credit');
                var hasCreditProduct = !CartModel.hasStorecredit();
                var canuse = (
                    moduleEnable
                    && customerId
                    && customerGroup
                    && hasCreditProduct
                    && $.inArray(customerGroup.toString(), customerGroups.split(',')) >= 0
                    && !self.isUseGuestCustomer()
                )?true:false;
                return canuse;
            },
            /**
             * Validate input data before apply
             * @param balance
             * @returns {boolean}
             */
            validate: function(balance){
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
                var appliedAmount = (this.applied())?this.appliedAmount():0;
                var max = this.remaining() + appliedAmount;
                if(max > this.balance()){
                    max = this.balance();
                }
                amount = (amount > max || (this.useMaxPoint() == true))?max:amount;
                if(this.currentAmount() > amount || balance || !this.applied()){
                    amount = (parseFloat(amount) > 0)?amount:0;
                    this.currentAmount(parseFloat(amount));
                }
                return true;
            },
            /**
             * Load customer credit balance from local by customer id
             * @param customerId
             */
            loadBalanceByCustomerId: function(customerId){
                var self = this;
                if(customerId) {
                    var model = CreditModel();
                    model.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    model.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].credit_balance));
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
             * Load customer in cart credit balance from local
             */
            updateStorageBalance: function(){
                var self = this;
                if(CartModel.customerId()) {
                    var model = CreditModel();
                    model.getCollection().addFieldToFilter('customer_id', CartModel.customerId(), 'eq');
                    model.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].credit_balance));
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
             * load balance save on local by customer id
             * @param customerId
             */
            loadStorageBalanceByCustomerId: function(customerId){
                var self = this;
                if(customerId){
                    var model = CreditModel();
                    model.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    model.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].credit_balance));
                        }else{
                            self.balance(0);
                        }
                    });
                }else{
                    self.balance(0);
                }
            },
            /**
             * Update blance on local for customer by id
             * @param customerId
             * @param balance
             */
            saveBalance: function(customerId, balance){
                if(customerId) {
                    var self = this;
                    var model = CreditModel();
                    model.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    model.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            data.credit_balance = balance;
                        }else{
                            data.customer_id = customerId;
                            data.credit_balance = balance;
                        }
                        model.setData(data).save();
                    });
                }
            },
            /**
             * Update balance on local by amount
             * @param customerId
             * @param amount
             */
            updateCustomerCreditBalance: function(customerId, amount){
                if(customerId) {
                    var self = this;
                    var model = CreditModel();
                    model.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    model.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            var currentBalance = parseFloat(data.credit_balance);
                            currentBalance += parseFloat(amount);
                            data.credit_balance = currentBalance;
                            model.setData(data).save();
                        }
                    });
                }
            },
            /* S: Calculate amount for each cart items - use this functionality if credit works the same as discount (not payment mode) */
            reset: function(){
                var self = this;
                ko.utils.arrayForEach(Items.items(), function (item) {
                    item.item_credit_amount(0);
                    item.item_base_credit_amount(0);
                });
            },
            process: function(cartBaseTotalAmount){
                var self = this;
                if(cartBaseTotalAmount > 0){
                    console.log('process');
                    var maxAmount = Items.getMaxDiscountAmount();
                    var itemsAmountTotal = (cartBaseTotalAmount > maxAmount)?maxAmount:cartBaseTotalAmount;
                    var amountApplied = 0;
                    ko.utils.arrayForEach(Items.items(), function (item, index) {
                        var maxAmountItem = Items.getMaxItemDiscountAmount(item.item_id());
                        var discountPercent = maxAmountItem/maxAmount;
                        var item_base_amount = (index == Items.items().length - 1)?(itemsAmountTotal - amountApplied):itemsAmountTotal*discountPercent;
                        amountApplied += item_base_amount;
                        var item_amount = Helper.convertPrice(item_base_amount);
                        item.item_base_credit_amount(item_base_amount);
                        item.item_credit_amount(item_amount);
                    });
                }else{
                    self.reset();
                }
            },
            collect: function(){
                var amount = (this.appliedAmount())?this.appliedAmount():0;
                this.currentAmount(amount);
                this.apply();
            },
            /* E: Calculate amount for each cart items */
        };
        return CustomerCreditModel.initialize();
    }
);