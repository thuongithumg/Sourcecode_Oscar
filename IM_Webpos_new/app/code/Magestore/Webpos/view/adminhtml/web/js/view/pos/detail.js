/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/model/pos/management',
        'Magestore_Webpos/js/model/pos/cash-adjustment',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/event-manager',
        'mage/translate'
    ],
    function ($, ko, Component, PosManagement, CashAdjustment, PriceHelper, Event, __) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/pos/detail'
            },
            data: PosManagement.data,
            cashCounted: PosManagement.cashCounted,
            verified: PosManagement.verified,
            sessionId: PosManagement.sessionId,
            sessions: PosManagement.sessions,
            realClosingBalance:PosManagement.realClosingBalance,
            differenceAmount: PosManagement.differenceAmount,
            profitLossReason: PosManagement.profitLossReason,
            theoretialClosingBalance: PosManagement.theoretialClosingBalance,
            isClosed: PosManagement.isClosed,
            initialize: function () {
                this._super();
                this.hasSales = ko.pureComputed(function(){
                    return (PosManagement.data() && PosManagement.data().sale_summary && PosManagement.data().sale_summary.length > 0)?true:false;
                });
                this.hasData = ko.pureComputed(function(){
                    return (PosManagement.sessions() && PosManagement.sessions().length > 0)?true:false;
                });
            },

            formatPrice: function(price){
                return PriceHelper.formatPrice(price);
            },

            generatePaymentCode: function (paymentMethod) {
                return "icon-iconPOS-payment-" + paymentMethod;
            },

            getAddTransactionTotal : function(){
                var self = this;
                var total = 0;
                var transactions = self.data().cash_transaction;
                $.each(transactions, function(index, transaction){
                    if((transaction.value > 0) && (transaction.type != "remove")){
                        total += parseFloat(transaction.value);
                    }
                });
                return total;
            },

            getRemoveTransactionTotal : function(){
                var self = this;
                var total = 0;
                var transactions = self.data().cash_transaction;
                $.each(transactions, function(index, transaction){
                    if((transaction.value > 0) && (transaction.type == "remove")){
                        total += parseFloat(transaction.value);
                    }
                });
                return total;
            },
            refreshData: function(){
                PosManagement.refreshData();
            },
            setClosingBalance: function(){
                $('#popup-close-shift').modal('openModal');
            },
            putMoneyIn: function(){
                Event.dispatch('start_put_money_in', '');
            },
            takeMoneyOut: function(){
                Event.dispatch('start_take_money_out', '');
            },
            print: function(){
                var printUrl = PosManagement.getPrintUrl();
                $("#webpos_pos_session_print_iframe").attr("src", printUrl);
            },
            validateClosing: function(){
                if(PosManagement.cashCounted() && !PosManagement.verified()){
                    PosManagement.verified(true);
                    PosManagement.closeCurrentSession();
                }
            },
            showAddTransactionsDetail: function(){
                Event.dispatch('start_show_add_transactions_detail', '');
            },
            showRemoveTransactionsDetail: function(){
                Event.dispatch('start_show_remove_transactions_detail', '');
            }
        });
    }
);
