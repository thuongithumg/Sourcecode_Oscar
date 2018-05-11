/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [ 'jquery',
        'ko',
        'Magestore_Webpos/js/view/base/grid/abstract',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function ($, ko, listAbstract, datetimeHelper, priceHelper, Event) {
        "use strict";

        return listAbstract.extend({
            types: ko.observableArray([
                {
                    type: 'add',
                    css: 'processing',
                    selected: ko.observable(false),
                    label: 'Cash In',
                },
                {
                    type: 'remove',
                    css: 'canceled',
                    selected: ko.observable(false),
                    label: 'Cash Out',
                }
            ]),
            showingTypes: ko.observableArray([]),
            showing:  ko.observable(false),
            isShowingFull:  ko.observable(true),
            shiftData:  ko.observable({}),
            saleSummaryData: ko.observable({}),
            items: ko.observableArray([]),
            columns: ko.observableArray([]),
            staffName: ko.observable(window.webposConfig.staffName),

            defaults: {
                template: 'Magestore_Webpos/shift/cash-transaction/activity',
            },

            initialize: function () {
                this._super();
                this._render();

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


            setData: function(data){
                data = this.processItemsVisibility(data);
                this.setItems(data);
            },

            setShiftData: function(data){

                this.shiftData(data);
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
                return priceHelper.formatPrice(amount);

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

            openForm: function(){
                var self = this;
                var popup = $('#cash-activities-popup');
                self.showing(true);
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
                popup.posOverlay({
                    onClose: function(){
                        self.closeForm();
                    }
                });
            },

            closeForm: function () {
                this.showing(false);
                $('.notification-bell').show();
                $('#c-button--push-left').show();
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
                var items = (data)?data:self.items();
                var showingTypes = self.showingTypes();
                $.each(items, function(index, item){
                    var type = (['remove','refund'].indexOf(item.type) != -1)?'remove':'add';
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
        });
    }
);
