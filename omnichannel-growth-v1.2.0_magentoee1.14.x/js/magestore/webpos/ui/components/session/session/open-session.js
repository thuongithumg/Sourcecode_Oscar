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
        'posComponent',
        'helper/staff',
        'model/session/session',
        'model/session/current-session',
        'helper/price',
        'helper/session',
        'action/notification/add-notification',
        'model/resource-model/magento-rest/session/session',
        'helper/datetime',
        'eventManager',
        'model/session/pos',
        'model/session/cash-counting',
        'mage/translate',
        'lib/cookie'
    ],
    function ($, ko, Component, staffHelper, shift, currentShift, priceHelper, shiftHelper, notification, onlineResource, datetimeHelper, Event, PosManagement, CashCounting, $t, Cookies) {
        "use strict";

        return Component.extend({
            totalValues: CashCounting.totalValues,
            countingItems: CashCounting.countingItems,
            cashValues: CashCounting.cashValues,
            currentPosId: PosManagement.currentPosId,
            availablePos: PosManagement.availablePos,
            showHeaderBalance: ko.observable(true),
            headerBalanceTitle: ko.observable($t('Opening Balance')),
            loading: ko.observable(false),
            showError: ko.observable(false),
            errorMessage: ko.observable(''),
            float_amount: ko.observable(''),
            opened_note: ko.observable(''),
            locationId: ko.observable(window.webposConfig.locationId),
            shiftId: ko.observable(window.webposConfig.shiftId),
            staffId: ko.observable(window.webposConfig.staffId),
            staffName: ko.observable(window.webposConfig.staffName),
            defaults: {
                template: 'ui/session/session/open-session',
            },

            initialize: function () {
                this._super();

                this.floatAmountFormatted = ko.pureComputed(function () {
                    return priceHelper.formatPrice(this.totalValues());
                }, this);

                this.totalValuesFormatted = ko.pureComputed(function () {
                    return priceHelper.formatPrice(this.totalValues());
                }, this);

                this.totalValues.subscribe(function(value){
                    this.float_amount(value);
                }, this);

                this.initEvents();

            },
            /**
             * Init events
             */
            initEvents: function () {
                var self = this;
                Event.observer('show_open_session_popup_after', function (event, eventData) {
                    var checkListPos = $.Deferred();
                    self.loading(true);
                    PosManagement.refreshData('', checkListPos);
                    checkListPos.always(function(response){
                        self.loading(false);
                    });
                });
                Event.observer('checkout_show_container_after', function (event, eventData) {
                    self.checkSessionOpen();
                });
            },
            /**
             * prepare data to update shift to online database using rest Api
             */

            getOnlineData: function () {
                var postData = {};

                var date  = new Date();
                //var dateGmt0 = new Date(date.valueOf() + date.getTimezoneOffset() * 60000);

                postData.float_amount = priceHelper.toPositiveNumber(this.float_amount());
                postData.base_float_amount = priceHelper.currencyConvert(postData.float_amount, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode);
                postData.balance = priceHelper.toPositiveNumber(this.float_amount());
                postData.base_balance = priceHelper.currencyConvert(postData.balance, window.webposConfig.currentCurrencyCode, window.webposConfig.baseCurrencyCode);
                postData.opened_note = this.opened_note();
                postData.base_currency_code = window.webposConfig.baseCurrencyCode;
                postData.shift_currency_code = window.webposConfig.currentCurrencyCode;
                postData.status = 0;
                postData.pos_id = priceHelper.toPositiveNumber(PosManagement.currentPosId());
                postData.staff_id = priceHelper.toPositiveNumber(this.staffId());
                postData.location_id = priceHelper.toPositiveNumber(this.locationId());
                //postData.opened_at = datetimeHelper.getBaseSqlDatetime(dateGmt0);
                postData.opened_at = datetimeHelper.getBaseSqlDatetime();
                postData.closed_amount = 0;
                postData.base_closed_amount = 0;
                postData.closed_at = '';
                postData.closed_note = "";
                postData.cash_left = 0;
                postData.base_cash_left = 0;
                postData.total_sales = 0;
                postData.base_total_sales = 0;
                postData.cash_added = postData.float_amount;
                postData.base_cash_added = postData.base_float_amount;
                postData.cash_removed = 0;
                postData.base_cash_removed = 0;
                postData.cash_sale = 0;
                postData.base_cash_sale = 0;
                postData.profit_loss_reason = '';
                return postData;
            },

            /**
             * get all data of a shift to be store in indexed-db.
             * The data format is like an item in listing page.
             */
            getOfflineData: function () {
                var postData = this.getOnlineData();

                postData['entity_id'] = 0;
                postData['sale_summary'] = [];
                postData['cash_transaction'] = [];
                postData['zreport_sales_summary'] = {grand_total: 0, discount_amount: 0, total_refunded: 0}
                if(!postData.staff_name && (postData.staff_id == window.webposConfig.staffId)){
                    postData.staff_name = window.webposConfig.staffName;
                }
                if(!postData.pos_name && (postData.pos_id == PosManagement.currentPosId())){
                    postData.pos_name = PosManagement.getCurrentPosName();
                }

                return postData;
            },

            OpenNewShift: function () {
                var self = this;
                if(self.loading()){
                    return false;
                }
                self.loading(true);
                var shiftModel = shift();
                var offlineData = self.getOfflineData();
                var deferred = shiftModel.setData(offlineData).setMode('offline').update();
                deferred.done(function (response) {
                    if(response){
                        self.syncShift(response.shift_id);
                    }
                });
                self.closeForm();

                //show notification
                //notification('Shift opened! Float Amount=' + priceHelper.formatPrice(postData.float_amount), true, 'success', 'Notice');
            },

            syncShift: function (shiftId) {
                var self = this;
                var params = this.getOnlineData();
                params.shift_id = shiftId;
                var deferred = $.Deferred();
                onlineResource().setPush(true).createShift(params,deferred);
                deferred.done(function (response) {
                    Event.dispatch('refresh_session_listing',response);
                    Event.dispatch('open_shift_after', response);
                    self.clearInput();
                });
            },

            closeForm: function () {
                $(".popup-for-right").hide();
                $(".popup-for-right").removeClass('fade-in');
                $(".wrap-backover").hide();
                $(".show-menu-button").show();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
            },

            floatAmountChange: function (data,event) {
                this.float_amount(priceHelper.toNumber(event.target.value));
            },

            //clear input value
            clearInput: function () {
                this.float_amount('');
                this.opened_note('');
                CashCounting.resetData();
            },

            /**
             * Validate session open
             */
            checkSessionOpen: function(){
                if(!PosManagement.validate() && staffHelper.isHavePermission('Magestore_Webpos::open_shift')){
                    Event.dispatch('show_open_session_popup','');
                }
            },
            /**
             * Add new counting record
             */
            addNewCashRecord: function(){
                CashCounting.addNewRecord();
                var itemsBody = $("#popup-open-session .cash-counting-items");
                var lastItemQtyInput = $("#popup-open-session .cash-counting-items .cash-counting-qty:last");
                itemsBody.animate({ scrollTop: itemsBody.prop("scrollHeight")}, 300);
                lastItemQtyInput.focus();
            },
            /**
             * Remove cash record
             */
            removeCashRecord: function(item){
                CashCounting.removeRecord(item);
            }
        });
    }
);