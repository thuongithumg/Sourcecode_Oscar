/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'ko',
        'jquery',
        'helper/general'
    ],
    function (ko, $, Helper) {
        'use strict';

        var ShiftData = {
            verified: ko.observable(false),
            cashCounted: ko.observable(false),
            closingBalance: ko.observable(0),
            data: ko.observable({}),
            CLOSED_STATUS:1,
            /**
             * Initialize
             * @returns {PosManagement}
             */
            initialize: function(){
                var self = this;
                self.initObserver();
                self.resetData();
                self.initData();
                return self;
            },
            /**
             * Init Observer
             * @returns {PosManagement}
             */
            initObserver: function(){
                var self = this;
                self.isClosed = ko.pureComputed(function(){
                    var data = self.data();
                    return (data && (data.status != self.CLOSED_STATUS))?false:true;
                });
                self.realClosingBalance = ko.pureComputed(function(){
                    var data = self.data();
                    if(data && self.isClosed()){
                        return parseFloat(data.closed_amount);
                    }
                    return (ShiftData.cashCounted())?ShiftData.closingBalance():0;
                });
                self.differenceAmount = ko.pureComputed(function(){
                    var actualBalance = self.realClosingBalance();
                    var balance = self.theoretialClosingBalance();
                    actualBalance = (actualBalance)?actualBalance:0;
                    balance = (balance)?balance:0;
                    return (actualBalance - balance);
                });
                self.theoretialClosingBalance = ko.pureComputed(function(){
                    var data = self.data();
                    if(self.isClosed()){
                        var salesAmount = parseFloat(data.cash_sale);
                        var addedAmount = parseFloat(data.cash_added);
                        var removedAmount = parseFloat(data.cash_removed);
                        var openAmount = parseFloat(data.float_amount);
                        // var closedAmount = parseFloat(data.closed_amount);
                        return (openAmount + salesAmount + addedAmount - removedAmount);
                    }else{
                        return (data)?parseFloat(data.balance):0;
                    }
                });
                self.profitLossReason = ko.pureComputed(function () {
                    var data = self.data();
                    return data.profit_loss_reason;
                });
                return self;
            },
            /**
             * Reset data
             * @returns {PosManagement}
             */
            resetData: function(){
                var self = this;
                self.closingBalance(0);
                self.cashCounted(false);
                self.verified(false);
                return self;
            },
            /**
             * Init Data
             * @returns {ShiftData}
             */
            initData: function(){
                var self = this;
                return self;
            }
        };
        return ShiftData.initialize();
    }
);
