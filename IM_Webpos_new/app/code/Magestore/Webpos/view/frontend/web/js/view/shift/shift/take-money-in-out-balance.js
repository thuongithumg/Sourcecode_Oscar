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
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/shift/cash-counting'
    ],
    function ($, ko, ViewManager, colGrid, ShiftData, priceHelper, CashCounting) {
        "use strict";

        return colGrid.extend({
            reason: ko.observable(''),

            moneyAmount: ko.computed(function () {
               return priceHelper.formatPrice(Math.abs(ShiftData.differenceAmount()));
            }),

            defaults: {
                template: 'Magestore_Webpos/shift/shift/take-money-in-out-balance',
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
                ShiftData.profitLossReason(this.reason());
                ShiftData.closingBalance(CashCounting.totalValues());
                ShiftData.cashCounted(true);
                ShiftData.verified(false);
                this.closeForm();
            },

            apply: function () {
                this.closeForm();
            }
        });
    }
);