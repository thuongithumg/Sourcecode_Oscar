/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [ 'jquery',
        'ko',
        'Magestore_Webpos/js/view/base/grid/abstract',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/staff',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/shift/data'
    ],
    function ($, ko, listAbstract, priceHelper, staffHelper, Helper, ShiftData) {
        "use strict";

        return listAbstract.extend({
            realClosingBalance:ShiftData.realClosingBalance,
            differenceAmount: ShiftData.differenceAmount,
            theoretialClosingBalance: ShiftData.theoretialClosingBalance,
            profitLossReason: ShiftData.profitLossReason,
            isClosed: ShiftData.isClosed,
            shiftData: ko.observable({}),
            saleSummaryData: ko.observable({}),
            items: ko.observableArray([]),
            columns: ko.observableArray([]),
            total_sales: ko.observable(0),
            paymentMethodClass: ko.observable(null),
            priceFormatter: ko.observable(''),
            hasData: ko.observable(false),
            isNotSync: ko.observable(true),

            defaults: {
                template: 'Magestore_Webpos/shift/sales-summary/sales-summary',
            },

            initialize: function () {
                this._super();
                this._render();
            },

            setData: function(data){
                if(!data){
                    this.hasData(false);
                    return;
                }
                this.setItems(data);
                if (data.length == 0){
                   this.hasData(false);
                }
                else {
                    this.hasData(true);
                }
            },

            setShiftData: function(data){
                this.shiftData(data);
                this.total_sales(data.total_sales);
                this.checkSync();

            },
            generatePaymentCode: function (paymentMethod) {
                return "icon-iconPOS-payment-" + paymentMethod;
            },
            
            checkSync: function () {

                if(priceHelper.toPositiveNumber(this.shiftData().entity_id) > 0){
                    this.isNotSync(false);
                }
                else {
                    this.isNotSync(true);
                }
            },

            setSyncSuccessful: function () {
                this.isNotSync(false);
            },

            getAddTransactionTotal : function(){
                var self = this;
                var total = 0;
                var transactions = self.shiftData().cash_transaction;
                if (typeof self.shiftData().cash_transaction !== 'undefined') {
                    $.each(transactions, function(index, transaction){
                        if((transaction.value > 0) && (['remove','refund'].indexOf(transaction.type)==-1)){
                            total += parseFloat(transaction.value);
                        }
                    });
                }

                return total;
            },

            getRemoveTransactionTotal : function(){
                var self = this;
                var total = 0;
                var transactions = self.shiftData().cash_transaction;
                if (typeof self.shiftData().cash_transaction !== 'undefined') {
                    $.each(transactions, function(index, transaction){
                        if((transaction.value > 0) && (['remove','refund'].indexOf(transaction.type)!=-1)){
                            total += parseFloat(transaction.value);
                        }
                    });
                }
                return total;
            },
            setClosingBalance: function(){
                Helper.dispatchEvent('start_set_closing_balance', '');
            },
            putMoneyIn: function(){
                Helper.dispatchEvent('start_put_money_in', '');
            },
            takeMoneyOut: function(){
                Helper.dispatchEvent('start_take_money_out', '');
            },
            showAddTransactionsDetail: function(){
                Helper.dispatchEvent('start_show_add_transactions_detail', '');
            },
            showRemoveTransactionsDetail: function(){
                Helper.dispatchEvent('start_show_remove_transactions_detail', '');
            },

            canEndSession: ko.computed(function () {
                return staffHelper.isHavePermission('Magestore_Webpos::close_shift');
            })
        });
    }
);
