/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/shift/shift',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/shift',
        'Magestore_Webpos/js/helper/datetime',
    ],
    function ($, ko, Event, shiftModel, priceHelper, shiftHelper, datetimeHelper) {
        "use strict";

        return {
            execute: function() {
                Event.observer('sales_order_take_payment_beforeSave',function(event,data){
                    if(data){
                        if(!window.webposConfig.shiftId){
                            return;
                        }

                        var methodData = [];
                        if(data.response.payment.method_data != undefined){
                            methodData = data.response.payment.method_data;

                            var cashValue = 0;
                            var grandTotal = 0;

                            var orderId = data.response.order_increment_id;
                            //var orderId = "";

                            ko.utils.arrayForEach(methodData, function(item) {
                                grandTotal = grandTotal + priceHelper.toPositiveNumber(item.real_amount);
                                if (item.code == 'cashforpos'){
                                    cashValue = priceHelper.toPositiveNumber(item.real_amount);
                                }
                            });

                            var currentShift = shiftModel();
                            var deferred = currentShift.load(window.webposConfig.shiftId);
                            deferred.done(function (currentShiftData) {
                                var balance = priceHelper.toPositiveNumber(currentShiftData.balance) + priceHelper.toPositiveNumber(cashValue);
                                var transactionData = {
                                    'shift_id':window.webposConfig.shiftId,
                                    'location_id': window.webposConfig.locationId,
                                    'staff_name': window.webposConfig.staffName,
                                    'staff_id': window.webposConfig.staffId,
                                    'value': cashValue,
                                    'base_value': priceHelper.currencyConvert(cashValue, window.webposConfig.currentCurrencyCode,window.webposConfig.baseCurrencyCode),
                                    'note': 'Add cash from order with id = ' + orderId,
                                    'balance': balance,
                                    'base_balance': priceHelper.currencyConvert(balance, window.webposConfig.currentCurrencyCode,window.webposConfig.baseCurrencyCode),
                                    'type': 'order',
                                    'base_currency_code': window.webposConfig.baseCurrencyCode,
                                    'transaction_currency_code': window.webposConfig.currentCurrencyCode,
                                    'created_at': datetimeHelper.getSqlDatetime()
                                };

                                //update data for shift.
                                currentShiftData.balance = balance;
                                currentShiftData.base_balance = transactionData.base_balance;
                                currentShiftData.cash_sale = priceHelper.toPositiveNumber(currentShiftData.cash_sale) + priceHelper.toPositiveNumber(cashValue);
                                currentShiftData.base_cash_sale = priceHelper.currencyConvert(currentShiftData.cash_sale, window.webposConfig.currentCurrencyCode,window.webposConfig.baseCurrencyCode);
                                currentShiftData.total_sales = priceHelper.toPositiveNumber(currentShiftData.total_sales) + priceHelper.toPositiveNumber(grandTotal);
                                currentShiftData.base_total_sales = priceHelper.currencyConvert(currentShiftData.total_sales, window.webposConfig.currentCurrencyCode,window.webposConfig.baseCurrencyCode);

                                //update cash transaction
                                if(cashValue > 0){
                                    var cashTransaction = [];
                                    if(currentShiftData.cash_transaction){
                                        cashTransaction = currentShiftData.cash_transaction;
                                    }
                                    cashTransaction.push(transactionData);
                                    currentShiftData.cash_transaction = cashTransaction;
                                }


                                //update sale summary
                                var newSaleSummaryData = {};
                                var methodCode = [];
                                var newItem = {};

                                ko.utils.arrayForEach(methodData, function(methodItem) {
                                    newItem = {};
                                    newItem['payment_method'] = methodItem.code;
                                    newItem['payment_amount'] = methodItem.real_amount;
                                    newItem['base_payment_amount'] = methodItem.base_real_amount;
                                    newItem['method_title'] = methodItem.title;
                                    newSaleSummaryData[methodItem.code] = newItem;
                                    methodCode.push(methodItem.code);
                                });

                                ko.utils.arrayForEach(currentShiftData.sale_summary, function (saleSummaryItem) {
                                    var method = saleSummaryItem['payment_method'];

                                    if(newSaleSummaryData[method]){
                                        var item1 = newSaleSummaryData[method];
                                        var item2 =  saleSummaryItem;
                                        newItem = {};
                                        newItem['payment_method'] = method;
                                        newItem['payment_amount'] = priceHelper.toPositiveNumber(item1.payment_amount) + priceHelper.toPositiveNumber(item2.payment_amount);
                                        newItem['base_payment_amount'] = priceHelper.toPositiveNumber(item1.base_payment_amount) + priceHelper.toPositiveNumber(item2.base_payment_amount);
                                        newItem['method_title'] = item1.method_title;
                                        newSaleSummaryData[method] = newItem;
                                    }
                                    else {
                                        newSaleSummaryData[method] = saleSummaryItem;
                                        methodCode.push(method);
                                    }

                                });

                                var sale_summary = [];
                                methodCode.forEach(
                                    function (method, index) {
                                        if(newSaleSummaryData[method]['payment_amount'] > 0){
                                            sale_summary.push(newSaleSummaryData[method]);
                                        }

                                    }
                                );
                                currentShiftData.sale_summary = sale_summary;

                                //update zreport data
                                var zReportData = currentShiftData.zreport_sales_summary;
                                zReportData.grand_total = currentShiftData.total_sales;
                                currentShiftData.zreport_sales_summary = zReportData;

                                var self = this;
                                var model = shiftModel();
                                var deferred = model.setData(currentShiftData).setMode('offline').save();

                                deferred.done(function (response) {
                                    if(response){
                                        Event.dispatch('refresh_shift_listing',response);
                                    }
                                });
                            });

                        }
                    }
                });
            }
        }
    }
);