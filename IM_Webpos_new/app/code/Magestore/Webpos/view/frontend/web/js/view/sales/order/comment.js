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
        
    ],
    function ($, ko, OrderFactory, $t, Component) {
        "use strict";

        return Component.extend({
            inputId: 'input-add-comment-order',
            isVisible: ko.observable(false),
            classIn: ko.observable(''),
            stypeDisplay: ko.observable('none'),
            
            defaults: {
                template: 'Magestore_Webpos/sales/order/comment',
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
                    this.addNotification($t('Add order comment successfully!'), true, 'success', 'Success');
                    this.display(false);
                }
            }
        });
    }
);