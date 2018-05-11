/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/model/shift/cash-transaction',
        'Magestore_Webpos/js/model/shift/shift',
        'Magestore_Webpos/js/model/shift/current-shift',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/action/notification/add-notification',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/resource-model/magento-rest/shift/shift',
        'Magestore_Webpos/js/model/shift/cash-counting',
        'Magestore_Webpos/js/model/shift/data',
        'Magestore_Webpos/js/model/shift/pos',
        'Magento_Ui/js/modal/confirm',
        'mage/translate'
    ],
    function ($, ko, Component, cashTransactionModel, shift, currentShift, priceHelper, Helper, datetimeHelper, notification, Event, shiftOnlineResource, CashCounting, ShiftData, PosManagement, confirm, Translate) {
        "use strict";

        if (!window.hadObserver) {
            window.hadObserver = [];
        }

        return Component.extend({
            totalValues: CashCounting.totalValues,
            countingItems: CashCounting.countingItems,
            cashValues: CashCounting.cashValues,
            showHeaderBalance: ko.observable(false),
            headerBalanceTitle: ko.observable(''),
            shiftData: ko.observable({}),
            balance: ko.observable(''),
            closed_amount: ko.observable(''),
            closed_note: ko.observable(''),
            cash_left: ko.observable(''),
            staffId: ko.observable(window.webposConfig.staffId),
            staffName: ko.observable(window.webposConfig.staffName),

            defaults: {
                template: 'Magestore_Webpos/shift/shift/close-shift',
            },

            initialize: function () {
                this._super();

                this.totalValuesFormatted = ko.pureComputed(function () {
                    return priceHelper.formatPrice(this.totalValues());
                }, this);

                this.totalValues.subscribe(function(value){
                    this.closed_amount(value);
                }, this);

                var self = this;
                if ($.inArray('close-shift-ui-comment', window.hadObserver) < 0) {
                    Event.observer('start_set_closing_balance', function(){
                        self.openForm();
                    });
                    Event.observer('verify_close_session', function(){
                        self.closeShift();
                    });
                    window.hadObserver.push('close-shift-ui-comment');
                }
            },

            //set all cash transaction data of the selected shift to Items
            //each transaction is an Item
            setData: function (data) {
                this.setItems(data);
            },

            //set all information of the selected shift to ShiftData
            //call this funciton from shift-listing
            setShiftData: function (data) {
                this.shiftData(data);
                this.initData();
            },

            /* update value of the estimated cash in the cash drawer*/
            initData: function () {
                this.balance(this.shiftData().balance);
                this.balance(priceHelper.formatPrice(this.balance()));
            },

            //get data from the form and call to CashTransaction model then save to database
            closeShift: function () {
                this.saveOfflineShift();
            },

            /**
             * do some additional task when everything is completed.
             */
            closeCompleted: function () {
                //set the current open shift to null
                window.webposConfig.shiftId = '';
                //clear input value
                this.cash_left('');
                this.closed_amount('');
                this.closed_note('');
                ShiftData.resetData();
                CashCounting.resetData();
                PosManagement.close('');
                currentShift.isOpenShift(false);
            },

            /**
             * prepare data to update shift
             * @returns {{}}
             */
            getShiftDataOnline: function () {
                var postData = {};
                // Do not add real amount to cash remove
                // var removedValue = priceHelper.toPositiveNumber(this.closed_amount()) - priceHelper.toPositiveNumber(this.cash_left());
                postData.base_currency_code = window.webposConfig.baseCurrencyCode;
                postData.shift_currency_code = window.webposConfig.currentCurrencyCode;
                postData.shift_id = this.shiftData().shift_id;
                postData.entity_id = this.shiftData().entity_id;
                postData.pos_id = PosManagement.currentPosId();
                postData.staff_id = priceHelper.toPositiveNumber(this.shiftData().staff_id);
                postData.location_id = priceHelper.toPositiveNumber(this.shiftData().location_id);
                postData.float_amount = priceHelper.toPositiveNumber(this.shiftData().float_amount);
                postData.base_float_amount = priceHelper.currencyConvert(postData.float_amount, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode);
                postData.closed_amount = priceHelper.toPositiveNumber(this.closed_amount());
                postData.base_closed_amount = priceHelper.currencyConvert(postData.closed_amount, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode);
                postData.closed_at = datetimeHelper.getBaseSqlDatetime();
                postData.opened_at = this.shiftData().opened_at;
                postData.closed_note = this.closed_note();
                postData.cash_left = priceHelper.toPositiveNumber(this.cash_left());
                postData.base_cash_left = priceHelper.currencyConvert(postData.cash_left, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode);
                postData.status = 1;
                postData.total_sales = priceHelper.toPositiveNumber(this.shiftData().total_sales);
                postData.base_total_sales = priceHelper.currencyConvert(postData.total_sales, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode);
                postData.balance = priceHelper.toPositiveNumber(this.cash_left());
                postData.base_balance = priceHelper.currencyConvert(postData.balance, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode);
                postData.opened_note = this.shiftData().opened_note;
                postData.cash_added = this.shiftData().cash_added;
                postData.base_cash_added = priceHelper.currencyConvert(postData.cash_added, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode);
                postData.cash_removed = priceHelper.toPositiveNumber(this.shiftData().cash_removed);
                postData.base_cash_removed = priceHelper.currencyConvert(postData.cash_removed, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode);
                postData.cash_sale = priceHelper.toPositiveNumber(this.shiftData().cash_sale);
                postData.base_cash_sale = priceHelper.currencyConvert(postData.cash_sale, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode);
                postData.profit_loss_reason = this.shiftData().profit_loss_reason;
                return postData;
            },

            /**
             *
             * @returns {*|{}}
             */
            getShiftDataOffline: function () {
                var postData = this.getShiftDataOnline();
                // var lastTransactionData = this.getLastTransactionData();

                postData.sale_summary = this.shiftData().sale_summary;
                postData.cash_transaction = this.shiftData().cash_transaction;

                // if (lastTransactionData.value > 0) {
                //     postData.cash_transaction.push(lastTransactionData);
                // }
                postData.zreport_sales_summary = this.shiftData().zreport_sales_summary;
                if(!postData.staff_name && (postData.staff_id == window.webposConfig.staffId)){
                    postData.staff_name = window.webposConfig.staffName;
                }
                if(!postData.pos_name && (postData.pos_id == PosManagement.currentPosId())){
                    postData.pos_name = PosManagement.getCurrentPosName();
                }
                return postData;
            },

            /**
             *
             */
            saveOfflineShift: function () {
                var self = this;
                var shiftModel = shift();
                var postData = self.getShiftDataOffline();
                var deferred = shiftModel.setData(postData).setMode('offline').update();
                deferred.done(function (response) {
                    if (response) {
                        Event.dispatch('after_closed_shift', response);
                        self.syncShift();
                    }
                });
            },

            syncShift: function () {
                var self = this;
                var postData = this.getShiftDataOnline();
                var deferred = $.Deferred();
                shiftOnlineResource().setPush(true).createShift(postData, deferred, "sync_offline_shift_after");
                deferred.always(function (response) {
                    self.closeCompleted();
                });
            },

            /**
             * Do not add real amount to cash remove
             * prepare data for the last transaction: remove cash from cash drawer before closing a shift.
             * @returns {{shift_id: (*|exports.indexes.shift_id|{unique}|schema.shift.indexes.shift_id), location_id: (*|exports.indexes.location_id|{unique}), value: number, base_value: *, note: string, balance: *, base_balance: *, type: string, base_currency_code: *, transaction_currency_code: *, created_at: string}}
             */
            // getLastTransactionData: function () {
            //     var value = priceHelper.toPositiveNumber(this.closed_amount()) - priceHelper.toPositiveNumber(this.cash_left());
            //     var balance = priceHelper.toPositiveNumber(this.cash_left());
            //
            //     var data = {
            //         'shift_id': this.shiftData().shift_id,
            //         'location_id': priceHelper.toPositiveNumber(this.shiftData().location_id),
            //         'value': value,
            //         'base_value': priceHelper.currencyConvert(value, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode),
            //         'note': 'Remove cash when closed Session',
            //         'balance': balance,
            //         'base_balance': priceHelper.currencyConvert(balance, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode),
            //         'type': 'remove',
            //         'base_currency_code': window.webposConfig.baseCurrencyCode,
            //         'transaction_currency_code': window.webposConfig.currentCurrencyCode,
            //         'created_at': datetimeHelper.getBaseSqlDatetime()
            //     };
            //
            //     return data;
            // },
            openForm: function(){
                $("#popup-close-shift").addClass('fade-in');
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

            /**
             * Add new counting record
             */
            addNewCashRecord: function(){
                CashCounting.addNewRecord();
                if (CashCounting.cashValues().length === 0) {
                    Helper.alert({priority:"danger",title: Helper.__("Error"),message: Helper.__("Please add the cash denominations in backend!")});
                }
                var itemsBody = $("#popup-close-shift .cash-counting-items");
                var lastItemQtyInput = $("#popup-close-shift .cash-counting-items .cash-counting-qty:last");
                itemsBody.animate({ scrollTop: itemsBody.prop("scrollHeight")}, 300);
                lastItemQtyInput.focus();
            },
            /**
             * Remove cash record
             */
            removeCashRecord: function(item){
                CashCounting.removeRecord(item);
            },
            /**
             * Set closing balance
             */
            setClosingBalance: function(){
                var compareText;
                var self = this;
                this.closeForm();
                ShiftData.closingBalance(CashCounting.totalValues());
                ShiftData.cashCounted(true);
                if (ShiftData.differenceAmount() !== 0) {
                    if (ShiftData.differenceAmount() > 0) {
                        compareText = Translate('Overage: ');
                    } else {
                        compareText = Translate('Shortage: ');
                    }
                    confirm({
                        modalClass: 'confirm close-confirm',
                        title: Translate('Theory is not the same as the real balance. Do you want to continue?'),
                        content:
                            Translate("Real balance: ") + priceHelper.formatPrice(ShiftData.realClosingBalance()) + "<br />" +
                            Translate("Theory is: ") + priceHelper.formatPrice(ShiftData.theoretialClosingBalance()) + "<br />" +
                            compareText + priceHelper.formatPrice(Math.abs(ShiftData.differenceAmount())) + "<br />",
                        actions: {
                            confirm: function () {
                                self.openClosingBalance();
                            }
                        }
                    });
                } else {
                    ShiftData.verified(false);
                }


            },

            /**
             * Open closing balance
             */
            openClosingBalance: function(){
                $("#set-closing").addClass('fade-in');
                $(".wrap-backover").show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            }
        });
    }
);