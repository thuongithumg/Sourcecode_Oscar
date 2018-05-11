/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/sales/order-factory',
        'mage/translate',
        'Magestore_Webpos/js/view/sales/order/action',
        'Magento_Ui/js/modal/confirm',
        
    ],
    function ($, ko, OrderFactory, $t, Component, Confirm) {
        "use strict";

        return Component.extend({
            inputId: 'input-add-cancel-comment-order',
            isVisible: ko.observable(false),
            classIn: ko.observable(''),
            stypeDisplay: ko.observable('none'),
            
            defaults: {
                template: 'Magestore_Webpos/sales/order/cancel',
            },

            initialize: function () {
                this._super();

            },
            
            getCommentObject: function(comment){
                var today = new Date();
                var createdAt = today.toISOString().substring(0, 10) + today.toISOString().substring(11, 19);
                return {
                    "comment": comment,
                    "createdAt": createdAt,
                    "entityId": 0,
                    "entityName": "string",
                    "isCustomerNotified": 1,
                    "isVisibleOnFront": 1,
                    "parentId": this.orderData().entity_id,
                    "status": 'canceled',
                    "extensionAttributes": {}
                }
            },

            cancel: function() {
                var self = this;
                Confirm({
                    content: $t('Are you sure you want to cancel this order?'),
                    actions: {
                        confirm: function () {
                            var comment = $('#' + self.inputId).val();
                            if (comment)
                                var comment = self.getCommentObject(comment);
                            else
                                var comment = null;
                            OrderFactory.get().setData(self.orderData()).setMode('online').cancel(self.orderData(), comment, $.Deferred());
                            self.addNotification($t('The order has been canceled successfully.'), true, 'success', 'Success');
                            self.display(false);
                        },
                        always: function (event) {
                            event.stopImmediatePropagation();
                        }
                    }
                });
            }
        });
    }
);