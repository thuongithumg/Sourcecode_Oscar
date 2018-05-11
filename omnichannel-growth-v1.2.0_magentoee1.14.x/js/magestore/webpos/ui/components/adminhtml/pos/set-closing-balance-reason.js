/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'posComponent',
        'ko',
        'ui/lib/modal/modal',
        'mage/translate',
        'model/adminhtml/pos/management',
        'model/adminhtml/pos/cash-counting'
    ],
    function ($, Component, ko, Modal, __, PosManagement, CashCounting) {
        "use strict";

        return Component.extend({
            reason: ko.observable(''),
            defaults: {
                template: 'ui/adminhtml/pos/set-closing-balance-reason'
            },
            initialize: function () {
                this._super();
            },
            closeForm: function(){
                $('#set-closing-balance-reason').modal('closeModal');
            },
            initModal: function(){
                var self = this;
                Modal({
                    title: __('Set Reason'),
                    clickableOverlay: true,
                    buttons: [{
                        text: __('Confirm'),
                        class: '',
                        click: function() {
                            var oldShiftData = PosManagement.data();
                            oldShiftData.profit_loss_reason =  self.reason();
                            PosManagement.data(oldShiftData);
                            self.reason('');
                            PosManagement.closingBalance(CashCounting.totalValues());
                            PosManagement.cashCounted(true);
                            PosManagement.verified(false);
                            self.closeForm();
                        }
                    }]
                }, $('#set-closing-balance-reason'));
            }

        });
    }
);