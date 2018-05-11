/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'jquery',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/shift/pos'
    ],
    function (ko, $, Helper, PosManagement) {
        'use strict';

        var CountingItem = function(item){
            var self = this;
            var value = (item && item.value)?parseFloat(item.value):0;
            var qty = (item && item.qty)?parseFloat(item.qty):0;
            self.cashValue = ko.observable(value);
            self.cashQty = ko.observable(qty);
            self.subtotal = ko.pureComputed(function(){
                return self.cashValue() * self.cashQty();
            });
            self.subtotalFormated = ko.pureComputed(function(){
                return Helper.formatPrice(self.subtotal());
            });
            return self;
        };

        var CashCounting = {
            countingItems: ko.observableArray([]),
            cashValues: ko.observableArray([]),
            /**
             * Initialize
             * @returns {CashCounting}
             */
            initialize: function(){
                var self = this;
                self.initObserver();
                self.initData();
                self.resetData();
                return self;
            },
            /**
             * Init Observer
             * @returns {CashCounting}
             */
            initObserver: function(){
                var self = this;
                self.totalValues = ko.pureComputed(function(){
                    var totals = 0;
                    ko.utils.arrayForEach(self.countingItems(), function(item) {
                        var total = item.subtotal();
                        total = (total)?total:0;
                        totals += total;
                    });
                    return totals;
                });
                PosManagement.currentPosId.subscribe(function(){
                    self.cashValues(PosManagement.getCurrentPosDenominations());
                });
                Helper.observerEvent(PosManagement.EVENT_INIT_DATA_AFTER, function(){
                    self.cashValues(PosManagement.getCurrentPosDenominations());
                });
                return self;
            },
            /**
             * Reset data
             * @returns {CashCounting}
             */
            resetData: function(){
                var self = this;
                self.countingItems([]);
                self.addNewRecord();
                return self;
            },
            /**
             * Init Data
             * @param cashValues
             * @returns {CashCounting}
             */
            initData: function(cashValues){
                var self = this;
                var cashValues = (cashValues)?cashValues: Helper.getOnlineConfig('cash_values');
                if(cashValues && cashValues.length > 0){
                    self.cashValues(cashValues);
                }else{
                    self.cashValues(PosManagement.getCurrentPosDenominations());
                }
                return self;
            },
            /**
             * Add new record
             * @returns {CashCounting}
             */
            addNewRecord: function(){
                var self = this;
                var cashValues = self.cashValues();
                if(cashValues && cashValues.length > 0){
                    var firstCash = cashValues[0];
                    var firstValue = (firstCash && firstCash.denomination_value)?firstCash.denomination_value:0;
                    self.countingItems.push(new CountingItem({
                        value: firstValue,
                        qty:0
                    }));
                }
                return self;
            },
            /**
             * Remove record
             * @param item
             * @returns {CashCounting}
             */
            removeRecord: function(item){
                var self = this;
                self.countingItems.remove(item);
                return self;
            }
        };
        return CashCounting.initialize();
    }
);
