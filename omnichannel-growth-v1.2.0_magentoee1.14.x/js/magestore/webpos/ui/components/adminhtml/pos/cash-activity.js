/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [ 'jquery',
        'ko',
        'posComponent',
        'helper/datetime',
        'helper/price',
        'eventManager',
        'model/adminhtml/pos/management',
        'ui/lib/modal/modal',
        'mage/translate'
    ],
    function ($, ko, Component, datetimeHelper, PriceHelper, Event, PosManagement, Modal, __) {
        "use strict";

        return Component.extend({
            types: ko.observableArray([
                {
                    type: 'add',
                    css: 'processing',
                    selected: ko.observable(false),
                    label: __('Cash In'),
                },
                {
                    type: 'remove',
                    css: 'canceled',
                    selected: ko.observable(false),
                    label: __('Cash Out'),
                }
            ]),
            showingTypes: ko.observableArray([]),
            isShowingFull:  ko.observable(true),
            data:  PosManagement.data,
            items: ko.observableArray([]),

            defaults: {
                template: 'ui/adminhtml/pos/cash-activity',
            },

            initialize: function () {
                this._super();

                var self = this;
                Event.observer('start_show_add_transactions_detail', function(){
                    self.showingTypes(['add']);
                    self.processItemsVisibility();
                    self.openForm();
                });
                Event.observer('start_show_remove_transactions_detail', function(){
                    self.showingTypes(['remove','refund']);
                    self.processItemsVisibility();
                    self.openForm();
                });
            },
            
            getTransactionSymbol: function (type, value) {
                if(!value){
                    return "";
                }
                if(parseInt(value)==0){
                    return "";
                }


                var symbol = "+";
                if(type == "remove" || type == "refund"){
                    symbol = "-";
                }
                return symbol;
            },

            steveFormatPrice: function (amount) {
                amount = parseFloat(amount);
                return PriceHelper.formatPrice(amount);

            },
            /**
             * return a date time with format: Thursday 4 May, 2016 15:26PM
             * @param dateString
             * @returns {string}
             */
            getFullDatetime: function (dateString) {
                var currentTime = datetimeHelper.stringToCurrentTime(dateString);
                return datetimeHelper.getFullDatetime(currentTime);
            },

            filterTypes: function (data, event) {
                var self = this;
                var el = $(event.currentTarget);
                var types = self.types();
                if (data.selected()) {
                    self.showingTypes.splice(self.showingTypes.indexOf(el.attr('type')), 1);
                }
                else {
                    self.showingTypes.push(el.attr('type'));
                }
                self.processItemsVisibility();
            },

            processItemsVisibility: function(data){
                var self = this;
                var items = (data)?data:self.data().cash_transaction;
                var showingTypes = self.showingTypes();
                $.each(items, function(index, item){
                    var type = (item.type == 'remove')?'remove':'add';
                    var showing = (($.inArray(type, showingTypes) >= 0) || (showingTypes.length == 0))?true:false;
                    if(!item.showing){
                        item.showing = ko.observable(showing);
                    }else{
                        item.showing(showing);
                    }
                });
                $.each(self.types(), function(index, item){
                    var showing = ($.inArray(item.type, showingTypes) >= 0)?true:false;
                    item.selected(showing);
                });

                var showFull = ((showingTypes.length == 0) || (showingTypes.length == 2))?true:false;
                self.isShowingFull(showFull);
                if(data){
                    return items;
                }else{
                    self.items(items);
                }
            },

            formatPrice: function(price){
                return PriceHelper.formatPrice(price);
            },
            initModal: function(){
                var self = this;
                Modal({
                    title: __('Activities'),
                    clickableOverlay: true
                }, $('#cash-activities-popup'));
            },
            openForm: function(){
                $('#cash-activities-popup').modal('openModal');
            }
        });
    }
);
