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
        'Magestore_Webpos/js/model/pos/cash-adjustment',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/model/event-manager',
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ],
    function ($, ko, Component, PosManagement, CashAdjustment, PriceHelper, DatetimeHelper, Event, Modal, __) {
        "use strict";

        return Component.extend({
            value: CashAdjustment.value,
            note: CashAdjustment.note,
            adjustmentTitle: ko.observable(),
            adjustmentNotice: ko.observable(),
            add_cash_class: ko.observable('cash_adjustment_active'),
            remove_cash_class: ko.observable('cash_adjustment_inactive'),
            valueErrorMessage: ko.observable(''),

            defaults: {
                template: 'Magestore_Webpos/pos/cash-adjustment',
            },

            initialize: function () {
                this._super();

                this.valueFormatted = ko.pureComputed(function () {
                    return PriceHelper.formatPrice(CashAdjustment.value());
                }, this);

                var self = this;
                Event.observer('start_put_money_in', function(){
                    CashAdjustment.type('add');
                    self.adjustmentTitle('Put Money In');
                    self.adjustmentNotice('Fill in this form if you put money in the cash-drawer');
                    self.openForm();
                });
                Event.observer('start_take_money_out', function(){
                    CashAdjustment.type('remove');
                    self.adjustmentTitle('Take Money Out');
                    self.adjustmentNotice('Fill in this form if you take money from the cash-drawer');
                    self.openForm();
                });
            },

            //get data from the form and call to CashTransaction model then save to database
            createCashAdjustment: function () {
                if (!this.validateInputAmount()) {
                    return;
                }
                this.syncTransaction();
            },

            syncTransaction: function () {
                var self = this;
                var deferred = $.Deferred();
                CashAdjustment.save(deferred);
                deferred.always(function (response) {
                    self.clearInput();
                    self.closeForm();
                });
            },
            initModal: function(){
                var self = this;
                Modal({
                    title: __('Cash Adjustment'),
                    clickableOverlay: true,
                    buttons: [{
                    text: __('Save'),
                    class: '',
                    click: function() {
                        self.createCashAdjustment();
                    }
                }]
                }, $('#popup-make-adjustment'));
            },
            openForm: function(){
                this.clearInput();
                $('#popup-make-adjustment').modal('openModal');
            },
            closeForm: function () {
                $('#popup-make-adjustment').modal('closeModal');
            },

            clearInput: function () {
                this.valueErrorMessage('');
                CashAdjustment.value('');
                CashAdjustment.note('');
            },

            valueChange: function (data, event) {
                CashAdjustment.value(PriceHelper.toNumber(event.target.value));
                this.validateInputAmount();
            },

            /**
             * check if remove value is less than current balance or not
             * @returns {boolean}
             */
            validateInputAmount: function () {
                if ((CashAdjustment.value() > CashAdjustment.balance()) && (CashAdjustment.type() == 'remove')) {
                    this.valueErrorMessage("Remove amount must be less than the balance!");
                    return false;
                }

                if (CashAdjustment.value() <= 0) {
                    this.valueErrorMessage("Amount must be greater than 0!");
                    return false;
                }

                this.valueErrorMessage("");
                return true;
            }
        });
    }
);
