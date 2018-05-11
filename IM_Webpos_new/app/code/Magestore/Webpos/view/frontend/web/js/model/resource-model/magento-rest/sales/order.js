/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract',
        'Magestore_Webpos/js/model/resource-model/indexed-db/sales/order',
        'Magestore_Webpos/js/model/event-manager',
    ],
    function ($, onlineAbstract, offlineResource, eventmanager) {
        "use strict";

        return onlineAbstract.extend({
            interfaceName: 'sales_order',
            
            initialize: function () {
                this._super();
                this.setLoadApi('/webpos/orders/');
                this.setSearchApiUrl('/webpos/orders')
            },

            cancelOrder: function(orderData, comment, model, deferred){
                var self = this;
                if(comment){
                    if(!orderData.status_histories)
                        orderData.status_histories = [];
                    orderData.status_histories.push(comment);
                }
                var deferredCancel = $.Deferred();
                this.callRestApi('/webpos/orders/:id/cancel', 'post', {id: orderData.entity_id}, {comment: comment}, deferredCancel, this.interfaceName + '_afterSave');
                
                eventmanager.dispatch('sales_order_cancel_after', {'response': orderData});
                eventmanager.dispatch(this.interfaceName + '_afterSave', {'response': orderData});
            },

            sendEmail: function(jsObject, email, model, deferred){
                var self = this;
                var id = model.getData().entity_id;
                var url = '/webpos/orders/:id/emails';
                this.callRestApi(url, 'post', {id: id}, {email: email}, deferred);
            },
            
            addComment: function(orderData, comment, model, deferred){
                var self = this;
                if(comment && comment.statusHistory){
                    if(!orderData.status_histories)
                        orderData.status_histories = [];
                    orderData.status_histories.push(comment.statusHistory);
                }
                var id = model.getData().entity_id;
                var url = '/webpos/orders/:id/comments';
                this.callRestApi(url, 'post', {id: id}, comment, deferred, this.interfaceName + '_afterSave');
                eventmanager.dispatch('sales_order_add_comment_after', {'response': orderData});
                eventmanager.dispatch(this.interfaceName + '_afterSave', {'response': orderData});
            },
        });
    }
);