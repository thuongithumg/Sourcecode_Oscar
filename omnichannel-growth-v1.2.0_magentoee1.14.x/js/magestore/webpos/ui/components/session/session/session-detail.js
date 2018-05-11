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
        'helper/staff',
        'helper/general',
        'model/session/data'
    ],
    function ($, ko, Component, datetimeHelper, staffHelper, Helper, ShiftData) {
        "use strict";

        return Component.extend({
            shiftData: ko.observable({}),
            shiftOpenedAt: ko.observable(''),
            isClosedShift: ko.observable(false),
            noSalesSummary: ko.observable('main-content'),

            defaults: {
                template: 'ui/session/session/session-detail',
            },
            canMakeAdjustment: ko.pureComputed(function(){
                return (staffHelper.isHavePermission('Magestore_Webpos::manage_shift_adjustment'))?true:false;
            }),
            initialize: function () {
                this._super();

                this.closeButtonTitle = ko.pureComputed(function(){
                    return (!ShiftData.verified() && ShiftData.cashCounted())?Helper.__('Validate Closing'):Helper.__('End of Session');
                });
            },

            setShiftData: function(data){
                this.shiftData(data);
                this.shiftOpenedAt(datetimeHelper.getFullDate(data.opened_at));
                if (data.status == 1){
                    this.isClosedShift(true);
                }
                else {
                    this.isClosedShift(false);
                }
                if(data.sale_summary.length == 0){
                    this.noSalesSummary("no-sales-summary main-content");
                }
                else {
                    this.noSalesSummary("main-content");
                }
            },
            
            afterClosedShift: function () {
                this.isClosedShift(true);
            },

            afterRenderCashAdjustmentButton: function () {
                $('.wrap-backover').click(function () {
                    $(".popup-for-right").hide();
                    $(".popup-for-right").removeClass('fade-in');
                    $(".wrap-backover").hide();
                    $('.notification-bell').show();
                    $('#c-button--push-left').show();
                });
            },

            afterRenderCloseButton: function () {
                $('.wrap-backover').click(function () {
                    $(".popup-for-right").hide();
                    $(".popup-for-right").removeClass('fade-in');
                    $(".wrap-backover").hide();
                    $('.notification-bell').show();
                    $('#c-button--push-left').show();
                });
            },

            afterRenderZReportButton: function () {
                $('.footer-shift .btn-print').click(function () {
                    $("#print-shift-popup").addClass('fade-in');
                    $(".wrap-backover").show();
                    $('.notification-bell').hide();
                    $('#c-button--push-left').hide();
                });

                $('.wrap-backover').click(function () {
                    $(".popup-for-right").hide();
                    $(".popup-for-right").removeClass('fade-in');
                    $(".wrap-backover").hide();
                    $('.notification-bell').show();
                    $('#c-button--push-left').show();   
                });
            },

            startCloseSession: function(){
                if(!ShiftData.verified() && ShiftData.cashCounted()){
                    Helper.dispatchEvent('verify_close_session', '');
                }else{
                    Helper.dispatchEvent('start_set_closing_balance', '');
                }
            },


            canEndSession: ko.computed(function () {
                return staffHelper.isHavePermission('Magestore_Webpos::close_shift');
            })

        });
    }
);
