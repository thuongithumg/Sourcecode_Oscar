/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'Magento_Ui/js/modal/modal',
        'mage/translate',
        'Magestore_Webpos/js/model/pos/management',
        'Magestore_Webpos/js/model/pos/cash-counting'
    ],
    function ($, Component, ko, Modal, __, PosManagement, CashCounting) {
        "use strict";

        return Component.extend({
            reason: ko.observable(''),
            defaults: {
                template: 'Magestore_Webpos/pos/set-closing-balance-reason'
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