/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/view/base/list/collection-list',
        'Magestore_Webpos/js/model/shift/data',
        'Magestore_Webpos/js/model/shift/cash-counting'
    ],
    function ($, ko, ViewManager, colGrid, ShiftData, CashCounting) {
        "use strict";

        return colGrid.extend({
            reason: ko.observable(''),

            defaults: {
                template: 'Magestore_Webpos/shift/shift/set-closing-balance-reason',
            },

            initialize: function () {
                this._super();
            },

            closeForm: function () {
                $(".popup-for-right").hide();
                $(".popup-for-right").removeClass('fade-in');
                $(".wrap-backover").hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
            },

            handleConfirm: function () {
                var oldShiftData = ShiftData.data();
                oldShiftData.profit_loss_reason = this.reason();
                ShiftData.data(oldShiftData);
                ShiftData.closingBalance(CashCounting.totalValues());
                ShiftData.cashCounted(true);
                ShiftData.verified(false);
                this.reason('');
                this.closeForm();
            },

            handleTakeInOut: function () {
                this.closeForm();
                $("#take-money-in-out-balance").addClass('fade-in');
                $(".wrap-backover").show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            },
        });
    }
);