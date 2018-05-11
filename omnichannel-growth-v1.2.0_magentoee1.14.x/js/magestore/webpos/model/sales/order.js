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
        'model/abstract',
        'model/resource-model/magento-rest/sales/order',
        'model/resource-model/indexed-db/sales/order',
        'model/collection/sales/order',
        'helper/staff'
    ],
    function ($, modelAbstract, orderRest, orderIndexedDb, orderCollection, staffHelper) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'order',
            initialize: function () {
                this._super();
                this.setResource(orderRest(), orderIndexedDb());
                this.setResourceCollection(orderCollection());
            },

            canSync: function(){
                return this.data.state === 'notsync' && this.data.error;
            },

            isOnHold: function(){
                return this.data.state === 'onhold';
            },

            isCanceled: function(){
                return this.data.state === 'canceled';
            },

            isNotSync: function(){
                return this.data.state === 'notsync';
            },

            isPaymentReview: function () {
                return this.data.state === 'payment_review';
            },

            getIsVirtual: function () {
                return this.data.is_virtual;
            },

            canCancel: function(){
                if (this.canUnhold()) {
                    return false;
                }
                if(this.isCanceled() || this.data.state === 'complete' || this.data.state === 'closed')
                    return false;
                var allInvoiced = true;
                $.each(this.data.items, function(index, value){
                    if(parseFloat(value.qty_ordered) - parseFloat(value.qty_invoiced) - parseFloat(value.qty_canceled) > 0) {
                        allInvoiced = false;
                    }
                });
                if (allInvoiced) {
                    return false;
                }
                return true;
            },

            canInvoice: function(){
                if(this.canUnhold() || this.isPaymentReview())
                    return false;
                if(this.isCanceled() || this.data.state === 'complete' || this.data.state === 'closed')
                    return false;
                var allInvoiced = true;
                $.each(this.data.items, function(index, value){
                    if(parseFloat(value.qty_ordered) - parseFloat(value.qty_invoiced) - parseFloat(value.qty_canceled) > 0)
                        allInvoiced = false;
                });
                if (!allInvoiced)
                    return true;
                return false;
            },

            canShip: function () {
                if(this.canUnhold() || this.isPaymentReview())
                    return false;
                if (this.getIsVirtual() == 1 || this.isCanceled() == 1) {
                    return false;
                }
                var allShip = true;
                $.each(this.data.items, function(index, value){
                    if (value.product_type == 'customsale' && value.is_virtual == 1) {
                        return true;
                    }
                    if(parseFloat(value.qty_ordered) - parseFloat(value.qty_shipped) - parseFloat(value.qty_refunded) - parseFloat(value.qty_canceled)>0)
                        allShip = false;
                });
                if (!allShip)
                    return true;
                return false;
            },

            canCreditmemo: function(){
                if (!staffHelper.isHavePermission('Magestore_Webpos::can_use_refund') && (!staffHelper.isHavePermission('Magestore_Webpos::all')
                    || !staffHelper.isHavePermission('Magestore_Webpos::manage_order_me')
                    || !staffHelper.isHavePermission('Magestore_Webpos::manage_order_other_staff')
                    || !staffHelper.isHavePermission('Magestore_Webpos::manage_all_order')))
                    return false
                if (this.data.base_total_refunded == this.data.base_grand_total) {
                    return false;
                }
                if (this.data.total_paid == 0)
                    return false;
                if (this.canUnhold() || this.isPaymentReview())
                    return false;
                if (this.isCanceled() || this.data.state === 'closed')
                    return false;
                if(typeof this.data.total_refunded != 'undefined'){
                    if(parseFloat(this.data.total_paid) - parseFloat(this.data.total_refunded) < 0.0001)
                        return false;
                }
                return true;
            },

            canUnhold: function () {
                if(this.isPaymentReview())
                    return false;
                return this.data.state === 'holded';
            },

            canTakePayment: function(){
                if(this.isCanceled() || this.isNotSync() || this.canUnhold())
                    return false;
                var allInvoicedAndCanceled = true;
                if(this.data.items.length>0){
                    $.each(this.data.items, function(index, value){
                        if(value.qty_ordered > value.qty_invoiced + value.qty_canceled)
                            allInvoicedAndCanceled = false;
                    });
                }
                if(allInvoicedAndCanceled)
                    return false;
                if(this.data.base_total_due && this.data.base_total_due > 0)
                    return true;
                if(this.data.base_total_paid)
                    return (this.data.base_grand_total - this.data.base_total_paid) > 0
                return false;
            },

            cancel: function(orderData, comment, deferred) {
                this.getResource().cancelOrder(orderData, comment, this, deferred);
            },

            sendEmail: function(jsObject, email, deferred){
                this.getResource().sendEmail(jsObject, email, this, deferred);
            },

            addComment: function(jsObject, comment, deferred){
                this.getResource().addComment(jsObject, comment, this, deferred);
            }
        });
    }
);