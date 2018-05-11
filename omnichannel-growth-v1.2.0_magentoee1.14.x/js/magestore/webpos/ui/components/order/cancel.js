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
        'model/sales/order-factory',
        'mage/translate',
        'ui/components/order/action',
        'action/notification/add-notification',
        'helper/general',
        'ui/lib/modal/confirm'
    ],
    function ($, ko, OrderFactory, $t, Component, Notification, Helper, Confirm) {
        "use strict";

        return Component.extend({
            inputId: 'input-add-cancel-comment-order',
            isVisible: ko.observable(false),
            classIn: ko.observable(''),
            stypeDisplay: ko.observable('none'),
            
            defaults: {
                template: 'ui/order/cancel',
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
                            Notification(Helper.__('The order has been canceled successfully.'), true, 'success', Helper.__('Success'));
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