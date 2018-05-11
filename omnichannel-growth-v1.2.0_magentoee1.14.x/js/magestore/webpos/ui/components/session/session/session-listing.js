/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'ui/components/layout',
        'ui/components/base/list/collection-list',
        'helper/staff',
        'model/session/session',
        'helper/price',
        'helper/session',
        'helper/datetime',
        'model/staff/current-staff',
        'eventManager',
        'helper/general',
        'model/session/data'
    ],
    function ($, ko, ViewManager, colGrid, staffHelper, shift, priceHelper, shiftHelper, datetimeHelper, CurrentStaff, Event, Helper, ShiftData) {
        "use strict";

        return colGrid.extend({
            items: ko.observableArray([]),
            columns: ko.observableArray([]),
            selectedId: ko.observable(null),
            canOpenShift: ko.observable(true),
            needOpenShilf: ko.observable(false),
            shiftListingData: ko.observable({}),
            staffId: ko.observable(window.webposConfig.staffId),
            hasNoShift: ko.observable(false),

            defaults: {
                template: 'ui/session/session/session-listing',
            },
            initialize: function () {
                this.listenMenuClickedEvent();
                this.isShowHeader = true;
                this._super();

            },
            _prepareCollection: function () {
                var mode = Helper.isUseShiftOnline();
                this.collection = shift().getCollection();
                this.collection.setMode(mode);
                this.collection.addFieldToFilter('pos_id', window.webposConfig.posId, 'eq');
            },
            loadItem: function (data, event) {
                this.initData(data);
                this.selectedId(data.shift_id);
            },

            getSelectedId: function () {
                return this.selectedId();
            },

            _prepareItems: function () {
                var deferred = $.Deferred();
                var self = this;
                this.getCollection().setOrder('opened_at', 'DESC').load(deferred);
                this.startLoading();
                deferred.done(function (data) {
                    self.finishLoading();
                    self.setItems(data.items);
                    if (data.total_count > 0) {
                        self.hasNoShift(false);
                        self.shiftListingData(data);
                        var checkOpen = shiftHelper.checkHasOpenShift(data.items);
                        if (checkOpen.hasOpen) {
                            self.canOpenShift(false);
                            window.webposConfig.shiftId = checkOpen.shiftId;
                        }
                        else {
                            window.webposConfig.shiftId = '';
                            if (staffHelper.isHavePermission('Magestore_Webpos::open_shift')) {
                                self.canOpenShift(true);
                            } else {
                                self.canOpenShift(false);
                            }
                        }
                        self.initData(data.items[0]);
                        self.selectedId(data.items[0].shift_id);
                    }
                    else {
                        if (staffHelper.isHavePermission('Magestore_Webpos::open_shift')) {
                            self.canOpenShift(true);
                        } else {
                            self.canOpenShift(false);
                        }
                    }
                });
            },

            getDateOnly: function (dateString) {
                var currentTime = datetimeHelper.stringToCurrentTime(dateString);
                var datetime = this.reFormatDateString(currentTime);
                return datetimeHelper.getWeekDay(currentTime) + " " + datetime.getDate() + " " + datetimeHelper.getMonthShortText(currentTime);
            },

            getTimeOnly: function (dateString) {
                var currentTime = datetimeHelper.stringToCurrentTime(dateString);
                var datetime = this.reFormatDateString(currentTime);
                return datetimeHelper.getTimeOfDay(datetime);
            },


            reFormatDateString: function (dateString) {
                var date = '';
                if (typeof dateString === 'string') {
                    date = new Date(dateString.split(' ').join('T'))
                } else {
                    date = new Date(dateString);
                }
                return date;
            },


            initData: function (data) {
                ShiftData.data(data);
                var viewManager = require('ui/components/layout');
                viewManager.getSingleton('ui/components/session/sales-summary/sales-summary').setData(data.sale_summary);
                viewManager.getSingleton('ui/components/session/sales-summary/sales-summary').setShiftData(data);
                viewManager.getSingleton('ui/components/session/cash-transaction/activity').setData(data.cash_transaction);
                viewManager.getSingleton('ui/components/session/cash-transaction/activity').setShiftData(data);
                viewManager.getSingleton('ui/components/session/session/session-detail').setShiftData(data);
                viewManager.getSingleton('ui/components/session/cash-transaction/cash-adjustment').setShiftData(data);
                viewManager.getSingleton('ui/components/session/session/close-session').setShiftData(data);
                viewManager.getSingleton('ui/components/session/sales-summary/zreport').setShiftData(data);
            },

            afterRenderOpenButton: function () {
                var self = this;
                $('#shift_container .o-header .o-header-nav .icon-add .icon-iconPOS-add').click(function () {
                   self.showOpenSessionPopup();
                });
                $('.shift-wrap-backover').click(function () {
                    $(".shift-wrap-backover").hide();
                    $('.notification-bell').show();
                    $('#c-button--push-left').show();
                });
            },

            refreshData: function () {
                var mode = Helper.isUseShiftOnline();
                this.collection = shift().getCollection();
                this.collection.setMode(mode);
                this.collection.addFieldToFilter('pos_id', window.webposConfig.posId, 'eq');
                this._prepareItems();
            },

            listenMenuClickedEvent: function () {
                var self = this;
                Event.observer('register_shift_show_container_after', function (event, eventData) {
                    self.refreshData();
                });
                Event.observer('show_open_session_popup', function (event, eventData) {
                    if($('#register_shift').length == 0){
                        self.needOpenShilf(true);
                    }else{
                        $('#register_shift').click();
                    }
                    self.showOpenSessionPopup();
                });
                Event.observer('register_shift_menu_item_render_after', function (event, eventData) {
                    if(self.needOpenShilf()){
                        self.needOpenShilf(false);
                        $('#session').click();
                    }
                });

            },

            toNumber: function (amount) {
                return priceHelper.toNumber(amount);
            },

            showOpenSessionPopup: function(){
                $("#popup-open-session").addClass('fade-in');
                $(".shift-wrap-backover").show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
                Event.dispatch('show_open_session_popup_after','');
            }
        });
    }
);