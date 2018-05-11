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
        'dataManager',
        'eventManager',
        'model/appConfig'
    ],
    function ($, ko, DataManager, Event, AppConfig) {
        "use strict";
        var TaxCalculator = {
            rules: ko.observableArray([]),
            rates: ko.observableArray([]),
            groups: ko.observableArray([]),
            initialize: function () {
                var self = this;
                self.initData();
                Event.observer(AppConfig.EVENT.DATA_MANAGER_SET_DATA_AFTER, function(event, data){
                    self.initData();
                });
                return self;
            },
            initData: function () {
                var self = this;
                if (self.rules().length <= 0) {
                    self.getRules();
                }
                if (self.rates().length <= 0) {
                    self.getRates();
                }
                if (self.groups().length <= 0) {
                    self.getGroups();
                }
            },
            reInitData: function () {
                var self = this;
                self.getRules();
                self.getRates();
                self.getGroups();
            },
            getRules: function () {
                var self = this;
                self.rules([]);
                var taxRules = DataManager.getData('tax_rules');
                if (taxRules && taxRules.length > 0) {
                    self.rules(taxRules);
                }
            },
            getRates: function () {
                var self = this;
                self.rates([]);
                var taxRates = DataManager.getData('tax_rates');
                if (taxRates && taxRates.length > 0) {
                    self.rates(taxRates);
                }
            },
            getGroups: function () {
                var self = this;
                self.groups([]);
                var groups = DataManager.getData('customer_group');
                if (groups && groups.length > 0) {
                    self.groups(groups);
                }
            },
            getProductTaxRate: function (productTaxClassId, customerGroup, billingAddress) {
                var self = this;
                var customerTaxClassId = self.getCustomerTaxClassId(customerGroup);
                var rate = self.getRate(productTaxClassId, customerTaxClassId, billingAddress);
                return rate;
            },
            getCustomerTaxClassId: function (customerGroup) {
                var customerTaxClassId = "";
                if (customerGroup && this.groups() && this.groups().length > 0) {
                    $.each(this.groups(), function () {
                        if (this.id == customerGroup) {
                            customerTaxClassId = this.tax_class_id;
                        }
                    });
                }
                return customerTaxClassId;
            },
            getRate: function(productTaxClassId, customerTaxClassId, address){
                var self = this;
                var rateIds = "";
                var rates = [];
                var tempRates = [];
                if(customerTaxClassId != ""){
                    if(this.rules() && this.rules().length > 0){
                        $.each(this.rules(), function(){
                            if(
                                $.inArray(customerTaxClassId, this.customer_tc_ids) !== -1
                                && $.inArray(productTaxClassId, this.product_tc_ids) !== -1
                            ){
                                rateIds = this.rates_ids;
                                var tempRate = self.getRateValue(rateIds, address);
                                if(tempRate > 0){
                                    var priority = this.priority;
                                    var isNew = true;
                                    $.each(tempRates, function(index, rate){
                                        if(this.priority == priority){
                                            isNew = false;
                                            rate.rate += parseFloat(tempRate);
                                            tempRates[index] = rate;
                                        }
                                    });
                                    if(isNew == true){
                                        tempRates.push({priority:priority, rate: parseFloat(tempRate)});
                                    }
                                }
                            }
                        });
                    }
                }else{
                    if(this.rules() && this.rules().length > 0){
                        rateIds = [];
                        $.each(this.rules(), function(){
                            if(
                                $.inArray(productTaxClassId, this.product_tc_ids) !== -1
                            ){
                                rateIds = this.rates_ids;
                                var tempRate = self.getRateValue(rateIds, address);
                                if(tempRate > 0){
                                    var priority = this.priority;
                                    var isNew = true;
                                    $.each(tempRates, function(index, rate){
                                        if(this.priority == priority){
                                            isNew = false;
                                            rate.rate += parseFloat(tempRate);
                                            tempRates[index] = rate;
                                        }
                                    });
                                    if(isNew == true){
                                        tempRates.push({priority:priority, rate: parseFloat(tempRate)});
                                    }
                                }
                            }
                        });
                    }
                }
                tempRates.sort(self.sortRulesRate);
                $.each(tempRates, function(){
                    rates.push(this.rate);
                });
                return rates;
            },
            getRateValue: function(rateIds, address){
                var self = this;
                var rate = 0;
                if(rateIds != ""){
                    if(rateIds && rateIds.length > 0){
                        $.each(rateIds, function(){
                            var rateId = this;
                            if(self.rates() && self.rates().length > 0){
                                $.each(self.rates(), function(){
                                    if(this.id == rateId){
                                        if(
                                            (this.country == "*" || this.country == address.country_id)&&
                                            (this.region_id == "*" || this.region_id == "0" || this.region_id == 0 || this.region_id == address.region_id)&&
                                            (this.postcode == "*" || this.postcode == address.postcode)
                                        ){
                                            if(this.rate > rate){
                                                rate = this.rate;
                                            }
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
                return rate;
            },
            getOriginRate: function(productTaxClassId, customerGroup){
                var self = this;
                var customerTaxClassId = self.getCustomerTaxClassId(customerGroup);
                var address = {
                    country_id:window.webposConfig['shipping/origin/country_id'],
                    postcode:window.webposConfig['shipping/origin/postcode'],
                    region_id:(window.webposConfig['shipping/origin/region_id'])?window.webposConfig['shipping/origin/region_id']:0
                };
                var rateIds = "";
                var rates = [];
                var tempRates = [];
                if(customerTaxClassId != ""){
                    if(this.rules() && this.rules().length > 0){
                        $.each(this.rules(), function(){
                            if(
                                $.inArray(customerTaxClassId, this.customer_tc_ids) !== -1
                                && $.inArray(productTaxClassId, this.product_tc_ids) !== -1
                            ){
                                rateIds = this.rates_ids;
                                var tempRate = self.getRateValue(rateIds, address);
                                if(tempRate > 0){
                                    var priority = this.priority;
                                    var isNew = true;
                                    $.each(tempRates, function(index, rate){
                                        if(this.priority == priority){
                                            isNew = false;
                                            rate.rate += parseFloat(tempRate);
                                            tempRates[index] = rate;
                                        }
                                    });
                                    if(isNew == true){
                                        tempRates.push({priority:priority, rate: parseFloat(tempRate)});
                                    }
                                }
                            }
                        });
                    }
                }else{
                    if(this.rules() && this.rules().length > 0){
                        rateIds = [];
                        $.each(this.rules(), function(){
                            if(
                                $.inArray(productTaxClassId, this.product_tc_ids) !== -1
                            ){
                                rateIds = this.rates_ids;
                                var tempRate = self.getRateValue(rateIds, address);
                                if(tempRate > 0){
                                    var priority = this.priority;
                                    var isNew = true;
                                    $.each(tempRates, function(index, rate){
                                        if(this.priority == priority){
                                            isNew = false;
                                            rate.rate += parseFloat(tempRate);
                                            tempRates[index] = rate;
                                        }
                                    });
                                    if(isNew == true){
                                        tempRates.push({priority:priority, rate: parseFloat(tempRate)});
                                    }
                                }
                            }
                        });
                    }
                }
                tempRates.sort(self.sortRulesRate);
                $.each(tempRates, function(){
                    rates.push(this.rate);
                });
                return rates;
            },
            sortRulesRate: function(ruleA, ruleB){
                var aPriority = parseFloat(ruleA.priority);
                var bPriority = parseFloat(ruleB.priority);
                return ((aPriority < bPriority) ? -1 : ((aPriority > bPriority) ? 1 : 0));
            }
        };
        return TaxCalculator.initialize();
    }
);