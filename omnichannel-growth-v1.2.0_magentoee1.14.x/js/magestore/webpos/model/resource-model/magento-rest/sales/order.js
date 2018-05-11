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
        'model/resource-model/magento-rest/abstract',
        'model/resource-model/indexed-db/sales/order',
        'eventManager',
    ],
    function ($, onlineAbstract, offlineResource, eventmanager) {
        "use strict";

        return onlineAbstract.extend({
            interfaceName: 'sales_order',

            initialize: function () {
                this._super();
                // this.setLoadApi('/webpos/orders/');
                this.setSearchApiUrl('/webpos/order/find');
                this.setCreateApiUrl('/webpos/order/create');
            },

            cancelOrder: function(orderData, comment, model, deferred){
                var self = this;
                if(comment){
                    if(!orderData.status_histories)
                        orderData.status_histories = [];
                    orderData.status_histories.push(comment);
                }
                var deferredCancel = $.Deferred();
                this.setPush(true);
                this.callRestApi('/webpos/order/cancel', 'post', {id: orderData.entity_id}, {comment: comment, id: orderData.entity_id}, deferredCancel, this.interfaceName + '_afterSave');

                eventmanager.dispatch('sales_order_cancel_after', {'response': orderData});
                eventmanager.dispatch(this.interfaceName + '_afterSave', {'response': orderData});
            },

            sendEmail: function(jsObject, email, model, deferred){
                var self = this;
                var id = model.getData().entity_id;
                var url = '/webpos/order/email';
                this.callRestApi(url, 'post', {id: id}, {email: email, id: id}, deferred);
            },

            addComment: function(orderData, comment, model, deferred){
                var self = this;
                if(comment && comment.statusHistory){
                    if(!orderData.status_histories)
                        orderData.status_histories = [];
                    orderData.status_histories.push(comment.statusHistory);
                }
                var id = model.getData().entity_id;
                var url = '/webpos/order/comments';
                this.setPush(true);
                this.callRestApi(url, 'post', {id: id}, {comment: comment, id: id}, deferred, this.interfaceName + '_afterSave');
                eventmanager.dispatch('sales_order_add_comment_after', {'response': orderData});
                eventmanager.dispatch(this.interfaceName + '_afterSave', {'response': orderData});
            },
        });
    }
);