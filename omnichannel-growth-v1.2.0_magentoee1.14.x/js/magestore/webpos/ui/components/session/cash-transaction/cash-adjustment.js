/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'posComponent',
        'model/session/cash-transaction',
        'model/session/session',
        'helper/price',
        'helper/datetime',
        'eventManager',
        'model/resource-model/magento-rest/session/session',
        'model/resource-model/magento-rest/session/cash-transaction',
        'helper/general'
    ],
    function ($, ko, Component, cashTransactionModel, shift, priceHelper, datetimeHelper, Event, shiftOnlineResource, transactionOnlineResource, Helper) {
        "use strict";

        return Component.extend({
            adjustmentTitle: ko.observable(),
            adjustmentNotice: ko.observable(),
            shiftData: ko.observable({}),
            value: ko.observable(''),
            note: ko.observable(''),
            add_cash_class: ko.observable('cash_adjustment_active'),
            remove_cash_class: ko.observable('cash_adjustment_inactive'),
            valueFormatted: ko.observable(''),
            balance: ko.observable(''),
            balanceFormatted: ko.observable(''),
            valueErrorMessage: ko.observable(''),
            type: ko.observable('add'),
            staffId: ko.observable(window.webposConfig.staffId),
            staffName: ko.observable(window.webposConfig.staffName),

            defaults: {
                template: 'ui/session/cash-transaction/cash-adjustment',
            },

            initialize: function () {
                this._super();

                this.valueFormatted = ko.pureComputed(function () {
                    return priceHelper.formatPrice(this.value());
                }, this);

                var self = this;
                Event.observer('start_put_money_in', function(){
                    self.type('add');
                    self.adjustmentTitle(Helper.__('Put Money In'));
                    self.adjustmentNotice(Helper.__('Fill in this form if you put money in the cash-drawer'));
                    self.openForm();
                });
                Event.observer('start_take_money_out', function(){
                    self.type('remove');
                    self.adjustmentTitle(Helper.__('Take Money Out'));
                    self.adjustmentNotice(Helper.__('Fill in this form if you take money from the cash-drawer'));
                    self.openForm();
                });
            },

            //set all cash transaction data of the selected shift to Items
            //each transaction is an Item
            setData: function (data) {
                this.setItems(data);
            },

            //set all information of the selected shift to ShiftData
            //call this function from shift-listing
            setShiftData: function (data) {
                this.shiftData(data);
                this.balance(data.balance);
                this.balanceFormatted(priceHelper.formatPrice(data.balance));
            },

            //change the value of type to "add"
            addCash: function () {
                this.type('add');
                this.add_cash_class('cash_adjustment_active');
                this.remove_cash_class('cash_adjustment_inactive');
            },

            //change the value of type to "remove"
            removeCash: function () {
                this.type('remove');
                this.add_cash_class('cash_adjustment_inactive');
                this.remove_cash_class('cash_adjustment_active');
            },

            //get data from the form and call to CashTransaction model then save to database
            createCashAdjustment: function () {
                if (!this.validateInputAmount()) {
                    return;
                }

                this.saveShiftOffline();
                this.closeForm();

            },

            getShiftDataOnline: function () {
                var postData = {};
                var finalValue = this.parseIntValue(this.value());
                if (this.type() == 'remove') {
                    finalValue = finalValue * (-1);
                }
                var balance = this.parseIntValue(this.shiftData().balance);

                postData.balance = balance + finalValue;
                if (this.type() == 'add') {
                    postData.cash_added = this.parseIntValue(this.shiftData().cash_added) + this.parseIntValue(this.value());
                    postData.cash_removed = this.parseIntValue(this.shiftData().cash_removed);
                }
                else {
                    postData.cash_added = this.parseIntValue(this.shiftData().cash_added);
                    postData.cash_removed = this.parseIntValue(this.shiftData().cash_removed) + this.parseIntValue(this.value());
                }
                postData.base_currency_code = this.shiftData().base_currency_code
                postData.shift_currency_code = this.shiftData().shift_currency_code;
                postData.shift_id = this.shiftData().shift_id;
                postData.entity_id = this.shiftData().entity_id;
                postData.staff_id = this.shiftData().staff_id;
                postData.location_id = this.shiftData().location_id;
                postData.float_amount = this.shiftData().float_amount;
                postData.base_float_amount = priceHelper.currencyConvert(postData.float_amount, postData.shift_currency_code, postData.base_currency_code);
                postData.closed_amount = this.shiftData().closed_amount;
                postData.base_closed_amount = priceHelper.currencyConvert(postData.closed_amount, postData.shift_currency_code, postData.base_currency_code);
                postData.closed_at = this.shiftData().closed_at;
                postData.closed_note = this.shiftData().closed_note;
                postData.cash_left = this.shiftData().cash_left;
                postData.base_cash_left = priceHelper.currencyConvert(postData.cash_left, postData.shift_currency_code, postData.base_currency_code);
                postData.status = this.shiftData().status;
                postData.total_sales = this.shiftData().total_sales;
                postData.base_total_sales = priceHelper.currencyConvert(postData.total_sales, postData.shift_currency_code, postData.base_currency_code);
                //postData.balance = balance;
                postData.base_balance = priceHelper.currencyConvert(postData.balance, postData.shift_currency_code, postData.base_currency_code);
                postData.opened_note = this.shiftData().opened_note;
                //postData.cash_added = this.shiftData().cash_added;
                postData.base_cash_added = priceHelper.currencyConvert(postData.cash_added, postData.shift_currency_code, postData.base_currency_code);
                //postData.cash_removed = this.shiftData().cash_removed;
                postData.base_cash_removed = priceHelper.currencyConvert(postData.cash_removed, postData.shift_currency_code, postData.base_currency_code);
                postData.cash_sale = this.shiftData().cash_sale;
                postData.base_cash_sale = priceHelper.currencyConvert(postData.cash_sale, postData.shift_currency_code, postData.base_currency_code);
                return postData;
            },

            getShiftDataOffline: function () {
                var postData = this.getShiftDataOnline();
                var lastTransaction = this.getCashTransactionDataOnline();

                postData.opened_at = this.shiftData().opened_at;
                postData.sale_summary = this.shiftData().sale_summary;
                postData.cash_transaction = this.shiftData().cash_transaction;
                postData.zreport_sales_summary = this.shiftData().zreport_sales_summary;
                postData.cash_transaction.push(lastTransaction);
                return postData;
            },

            getCashTransactionDataOnline: function () {
                var finalValue = this.value();
                if (this.type() == 'remove') {
                    finalValue = finalValue * (-1);
                }

                var balance = this.parseIntValue(this.shiftData().balance);
                balance = balance + finalValue;
                var data = {
                    'shift_id': this.shiftData().shift_id,
                    'location_id': this.shiftData().location_id,
                    'value': this.value(),
                    'base_value': priceHelper.currencyConvert(this.value(), window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode),
                    'note': this.note(),
                    'balance': balance,
                    'base_balance': priceHelper.currencyConvert(balance, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode),
                    'type': this.type(),
                    'base_currency_code': window.webposConfig.baseCurrencyCode,
                    'transaction_currency_code': window.webposConfig.currentCurrencyCode,
                    'created_at': datetimeHelper.getBaseSqlDatetime(),
                    'staff_id': window.webposConfig.staffId,
                    'staff_name': window.webposConfig.staffName
                };

                return data;
            },

            syncTransaction: function () {
                var self = this;
                var postData = this.getCashTransactionDataOnline();
                var deferred = $.Deferred();
                transactionOnlineResource().setPush(true).createTransaction(postData, deferred);
                deferred.always(function (response) {
                    self.clearInput();
                    // if(Helper.isUseShiftOnline()){
                        Event.dispatch('refresh_session_listing', '');
                    // }
                });
            },

            saveShiftOffline: function () {
                var self = this;
                var postData = this.getShiftDataOffline();
                var shiftModel = shift();
                var deferred = shiftModel.setData(postData).setMode('offline').update();

                deferred.done(function (response) {
                    if (response) {
                        if(!Helper.isUseShiftOnline()){
                            Event.dispatch('refresh_shift_listing', '');
                        }
                        self.syncTransaction();
                    }
                });
            },

            synShiftOnline: function () {
                var self = this;
                var postData = this.getShiftDataOnline();
                var deferred = $.Deferred();

                shiftOnlineResource().setPush(true).createShift(postData, deferred);
                deferred.done(function (response) {
                    //close the cash adjustment form
                    self.clearInput();
                });
            },
            openForm: function(){
                var ptop = 150;
                $("#popup-make-adjustment").addClass('fade-in');
                $("#popup-make-adjustment").css({top: ptop + 'px'}).fadeIn(350);
                $(".wrap-backover").show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            },
            closeForm: function () {
                $(".popup-for-right").hide();
                $(".popup-for-right").removeClass('fade-in');
                $(".wrap-backover").hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
            },

            clearInput: function () {
                //clear input value
                this.value('');
                this.note('');
            },

            valueChange: function (data, event) {
                this.value(priceHelper.toNumber(event.target.value));
                this.validateInputAmount();
            },

            /**
             * check if remove value is less than current balance or not
             * @returns {boolean}
             */
            validateInputAmount: function () {
                if ((this.value() > this.balance()) && (this.type() == 'remove')) {
                    this.valueErrorMessage(Helper.__("Remove amount must be less than the balance!"));
                    return false;
                }

                if (this.value() <= 0) {
                    this.valueErrorMessage(Helper.__("Amount must be greater than 0!"));
                    return false;
                }

                this.valueErrorMessage("");
                return true;
            },

            //parseInt a field from SQL.
            parseIntValue: function (value) {
                if (!value) {
                    return 0;
                }
                value = parseFloat(value);
                return value;
            }
        });
    }
);
