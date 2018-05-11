/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/sales/order',
        'Magestore_Webpos/js/model/resource-model/indexed-db/sales/order',
        'Magestore_Webpos/js/model/collection/sales/order'
    ],
    function ($, modelAbstract, orderRest, orderIndexedDb, orderCollection) {
        "use strict";
        return modelAbstract.extend({
            sync_id: 'order',
            initialize: function () {
                this._super();
                this.setResource(orderRest(), orderIndexedDb());
                this.setResourceCollection(orderCollection());
            },

            canSync: function () {
                return this.data.state === 'notsync' && this.data.error;
            },

            isOnHold: function () {
                return ((this.data.state === 'onhold') || (this.data.state === 'holded'));
            },

            isCanceled: function () {
                return this.data.state === 'canceled';
            },

            isNotSync: function () {
                return this.data.state === 'notsync';
            },

            isNew: function () {
                return this.data.state === 'new';
            },

            isPaymentReview: function () {
                return this.data.state === 'payment_review';
            },

            getIsVirtual: function () {
                return this.data.is_virtual;
            },

            canCancel: function () {
                if (this.canUnhold()) {
                    return false;
                }
                if (this.isCanceled() || this.data.state === 'complete' || this.data.state === 'closed')
                    return false;
                var allInvoiced = true;
                $.each(this.data.items, function (index, value) {
                    if (parseFloat(value.qty_ordered) - parseFloat(value.qty_invoiced) - parseFloat(value.qty_canceled) > 0) {
                        allInvoiced = false;
                    }
                });
                if (allInvoiced) {
                    return false;
                }
                return true;
            },

            canInvoice: function () {
                if (this.canUnhold() || this.isPaymentReview())
                    return false;
                if (this.isCanceled() || this.data.state === 'complete' || this.data.state === 'closed')
                    return false;
                var allInvoiced = true;
                $.each(this.data.items, function (index, value) {
                    if (parseFloat(value.qty_ordered) - parseFloat(value.qty_invoiced) - parseFloat(value.qty_canceled) > 0)
                        allInvoiced = false;
                });
                if (!allInvoiced)
                    return true;
                return false;
            },

            canShip: function () {
                if (this.canUnhold() || this.isPaymentReview())
                    return false;
                if (this.getIsVirtual() == 1 || this.isCanceled() == 1) {
                    return false;
                }
                var allShip = true;
                $.each(this.data.items, function (index, value) {
                    if (value.product_type == 'customsale' && value.is_virtual == 1) {
                        return true;
                    }
                    if (value.product_type == 'simple' && value.parent_item_id) {
                        return true;
                    }
                    if (parseFloat(value.qty_ordered) - parseFloat(value.qty_shipped) - parseFloat(value.qty_refunded) - parseFloat(value.qty_canceled) > 0)
                        allShip = false;
                });
                if (!allShip)
                    return true;
                return false;
            },

            canCreditmemo: function () {
                if (this.data.forced_can_creditmemo === true)
                    return true;
                if (this.data.state != 'closed' && this.data.grand_total == 0 && this.data.customer_balance_amount > 0)
                    return true;
                if (this.canUnhold() || this.isPaymentReview())
                    return false;
                if (this.isNew() || this.isCanceled() || this.data.state === 'closed' || this.data.grand_total == 0)
                    return false;
                if (typeof this.data.total_refunded != 'undefined') {
                    if (parseFloat(this.data.total_paid) - parseFloat(this.data.total_refunded) < 0.0001)
                        return false;
                }
                return true;
            },

            canUnhold: function () {
                if (this.isPaymentReview())
                    return false;
                return this.data.state === 'holded';
            },

            canTakePayment: function () {
                if (this.isCanceled() || this.isNotSync() || this.canUnhold() || this.data.webpos_paypal_invoice_id)
                    return false;
                var allInvoicedAndCanceled = true;
                if (this.data.items.length > 0) {
                    $.each(this.data.items, function (index, value) {
                        if (value.qty_ordered > value.qty_canceled)
                            allInvoicedAndCanceled = false;
                    });
                }
                if (allInvoicedAndCanceled)
                    return false;

                if (this.data.base_total_due && this.data.base_total_due > 0)
                    return true;
                if (this.data.base_total_paid)
                    return (this.data.base_grand_total - this.data.base_total_paid) > 0
                return false;
            },

            cancel: function (orderData, comment, deferred) {
                this.getResource().cancelOrder(orderData, comment, this, deferred);
            },

            sendEmail: function (jsObject, email, deferred) {
                this.getResource().sendEmail(jsObject, email, this, deferred);
            },

            addComment: function (jsObject, comment, deferred) {
                this.getResource().addComment(jsObject, comment, this, deferred);
            }
        });
    }
);