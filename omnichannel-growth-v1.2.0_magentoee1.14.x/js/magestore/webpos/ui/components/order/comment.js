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
        'action/notification/add-notification'
    ],
    function ($, ko, OrderFactory, $t, Component, Notification) {
        "use strict";

        return Component.extend({
            inputId: 'input-add-comment-order',
            isVisible: ko.observable(false),
            classIn: ko.observable(''),
            stypeDisplay: ko.observable('none'),

            defaults: {
                template: 'ui/order/comment',
            },

            initialize: function () {
                this._super();
            },

            getCommentObject: function(comment){
                var today = new Date();
                var createdAt = today.toISOString().substring(0, 10) + today.toISOString().substring(11, 19);
                return {
                    "statusHistory": {
                        "comment": comment,
                        "createdAt": createdAt,
                        "entityId": 0,
                        "entityName": "string",
                        "isCustomerNotified": 1,
                        "isVisibleOnFront": 1,
                        "parentId": this.orderData().entity_id,
                        "status": this.orderData().status,
                        "extensionAttributes": {}
                    }
                }
            },

            addComment: function(){
                var self = this;
                var comment = $('#'+this.inputId).val();
                if(comment){
                    var comment = this.getCommentObject(comment);
                    OrderFactory.get().setData(this.orderData()).setMode('online').addComment(this.orderData(), comment, $.Deferred());
                    Notification($t('Add order comment successfully!'), true, 'success', $t('Success'));
                    this.display(false);
                }
            }
        });
    }
);