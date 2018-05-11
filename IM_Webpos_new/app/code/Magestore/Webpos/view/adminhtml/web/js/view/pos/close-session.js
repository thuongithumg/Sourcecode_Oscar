/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/model/pos/management',
        'Magestore_Webpos/js/model/pos/cash-counting',
        'Magestore_Webpos/js/helper/price',
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ],
    function ($, ko, Component, PosManagement, CashCounting, HelperPrice, Modal, __) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/pos/close-session'
            },
            totalValues: CashCounting.totalValues,
            countingItems: CashCounting.countingItems,
            cashValues: CashCounting.cashValues,
            showHeaderBalance: ko.observable(false),
            headerBalanceTitle: ko.observable(''),
            initialize: function () {
                this._super();
                this.totalValuesFormatted = ko.pureComputed(function () {
                    return HelperPrice.formatPrice(this.totalValues());
                }, this);
            },
            /**
             * Add new counting record
             */
            addNewCashRecord: function(){
                CashCounting.addNewRecord();
                // var itemsBody = $("#popup-close-shift .cash-counting-items");
                // var lastItemQtyInput = $("#popup-close-shift .cash-counting-items .cash-counting-qty:last");
                // itemsBody.animate({ scrollTop: itemsBody.prop("scrollHeight")}, 300);
                // lastItemQtyInput.focus();
            },
            /**
             * Remove cash record
             */
            removeCashRecord: function(item){
                CashCounting.removeRecord(item);
            },
            /**
             * Set closing blance
             */
            setClosingBalance: function(){
                PosManagement.closingBalance(CashCounting.totalValues());
                PosManagement.cashCounted(true);
                PosManagement.verified(false);
                this.closeForm();
            },
            closeForm: function(){
                $('#popup-close-shift').modal('closeModal');
            },
            initModal: function(){
                var self = this;
                Modal({
                    title: __('Set Closing Balance'),
                    clickableOverlay: true,
                    buttons: [{
                        text: __('Confirm'),
                        class: '',
                        click: function() {
                            PosManagement.closingBalance(CashCounting.totalValues());
                            PosManagement.cashCounted(true);
                            if (PosManagement.differenceAmount === 0) {
                                self.setClosingBalance();
                            } else {
                                $('#set-closing-balance-reason').modal('openModal');
                                self.closeForm();
                            }
                        }
                    }]
                }, $('#popup-close-shift'));
            }
        });
    }
);
