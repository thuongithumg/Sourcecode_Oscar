/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'ko',
        'jquery',
        'model/adminhtml/pos/management',
        'model/adminhtml/request',
        'helper/datetime',
        'helper/price'
    ],
    function (ko, $, PosManagement, Request, HelperDatetime, PriceHelper) {
        'use strict';

        var CashAdjustment = {
            data: PosManagement.data,
            value: ko.observable(''),
            note: ko.observable(''),
            type: ko.observable('add'),
            /**
             * Initialize
             * @returns {PosManagement}
             */
            initialize: function(){
                var self = this;
                self.initObserver();
                self.resetData();
                return self;
            },
            /**
             * Init Observer
             * @returns {PosManagement}
             */
            initObserver: function(){
                var self = this;
                self.balance = ko.pureComputed(function(){
                    var data = self.data();
                    return data.balance;
                });
                return self;
            },
            /**
             * Reset data
             * @returns {PosManagement}
             */
            resetData: function(){
                var self = this;
                self.value('');
                self.note('');
                self.type('add');
                return self;
            },
            /**
             * Save data
             */
            save: function (deferred) {
                var self = this;
                var params = {
                    'pos_id': PosManagement.currentPosId(),
                    'transaction': self.getTransactionData()
                };
                Request.send(PosManagement.saveTransactionUrl(), 'post', params, deferred).done(function(response){
                    if (response) {
                        PosManagement.initData(response, true);
                        self.resetData();
                    }
                });
            },
            /**
             * Get transaction data
             * @returns {{shift_id: *, location_id: *, value: *, base_value: *, note: *, balance: *, base_balance: *, type: *, base_currency_code: *, transaction_currency_code: *, created_at: *}}
             */
            getTransactionData: function () {
                var self = this;
                var data = self.data();
                var finalValue = self.value();
                if (self.type() == 'remove') {
                    finalValue = finalValue * (-1);
                }

                var balance = parseFloat(data.balance);
                balance = balance + finalValue;
                var data = {
                    'shift_id': data.shift_id,
                    'location_id': data.location_id,
                    'value': self.value(),
                    'base_value': PriceHelper.currencyConvert(self.value(), window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode),
                    'note': self.note(),
                    'balance': balance,
                    'base_balance': PriceHelper.currencyConvert(balance, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode),
                    'type': self.type(),
                    'base_currency_code': window.webposConfig.baseCurrencyCode,
                    'transaction_currency_code': window.webposConfig.currentCurrencyCode,
                    'created_at': HelperDatetime.getBaseSqlDatetime()
                };

                return data;
            }

        };
        return CashAdjustment.initialize();
    }
);
