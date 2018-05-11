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

/*global define*/
define(
    [
        'jquery',
        'ko',
        'model/sales/order/creditmemo',
        'model/catalog/product',
        'helper/alert',
        'action/notification/add-notification',
        'helper/general',
        'model/appConfig',
        'model/resource-model/magento-rest/integration/storecredit/store-credit'
    ],
    function($, ko, creditmemo, ProductModel, alertHelper, notification, Helper, AppConfig, onlineResource) {
        'use strict';
        return {
            submitArray: [],
            submitData: {
                "entity": {
                    "orderId": 0,
                    "invoice_id": 0,
                    "adjustmentNegative": 0,
                    "adjustmentPositive": 0,
                    "emailSent": 0,
                    "shippingAmount": 0,
                    "items": [],
                    "comments": []
                }
            },
            orderData: {},
            item: {},
            comment: {},
            isExist: false,
            
            execute: function(data, orderData, deferred, parent){
                var self = this;
                this.orderData = orderData;
                var invoice_id = '';
                var itemQty = '';
                var stock = '';
                var canRefund = false;
                var allQty = false;
                var creditAmount = $('#credit-amount')[0].value;
                $('.refund-input-qty').each(function (index, value) {
                    var id = value.id;
                    var itemId = id.replace('item_', '');
                    var stockItem = $('.check_fund_' + itemId)[0].checked;
                    itemQty += itemId + '/' + value.value + '$refund$';
                    stock += itemId + '/' + stockItem + '$refund$';
                });

                if (itemQty == '' && stock == '') {
                    $.each(this.orderData.items, function (index, value) {
                        itemQty += value.item_id + '/' + 0 + '$refund$';
                        stock += value.item_id + '/' + false + '$refund$';
                    });
                }

                if (this.orderData.invoice_id)
                    invoice_id = this.orderData.invoice_id;
                this.submitData = {
                    "entity": {
                        "orderId": this.orderData.entity_id,
                        "invoice_id": invoice_id,
                        "increment_id": this.orderData.increment_id,
                        "adjustmentPositive": 0,
                        "adjustmentNegative": 0,
                        "emailSent": 0,
                        "shippingAmount": 0,
                        "baseCurrencyCode": window.webposConfig.baseCurrencyCode,
                        "storeCurrencyCode": window.webposConfig.currentCurrencyCode,
                        "items": [],
                        "comments": [],
                        "qty": itemQty,
                        "stock": stock
                    }
                };
                $.each(data, function(index, value){
                    self.submitData = self.bindItem(self.submitData,value);
                    self.submitData = self.bindComment(self.submitData,value);
                    self.submitData = self.bindAdditionalData(self.submitData,value);

                    if(value.name == 'refund_cash' && value.value == '1'){
                        self.submitData.entity.refund_by_cash = 1;
                    }
                });
                $.each(this.submitData.entity.items, function (index, value) {
                    if (value.qty > 0){
                        allQty = true;
                    }
                });

                if (this.submitData.entity.adjustmentNegative > 0 || this.submitData.entity.adjustmentPositive > 0 || allQty == true
                || this.submitData.entity.shippingAmount > 0) {
                    canRefund = true;
                }

                if (this.submitData.entity.adjustmentNegative > 0 && this.submitData.entity.adjustmentPositive <= 0 && allQty == false) {
                    alertHelper({priority: "danger", title:Helper.__('Error'), message: Helper.__('The credit memo\'s total must be positive.')});
                    return;
                }

                if (parseFloat(this.submitData.entity.adjustmentNegative - this.submitData.entity.adjustmentPositive) > parseFloat(orderData.grand_total)) {
                    alertHelper({priority: "danger", title:Helper.__('Error'), message: Helper.__('The credit memo\'s total must be positive.')});
                    return;
                }

                if (this.submitData.entity.adjustmentNegative > 0 && this.submitData.entity.adjustmentPositive > 0 &&
                    (this.submitData.entity.adjustmentPositive - this.submitData.entity.adjustmentNegative > orderData.grand_total) && allQty == false) {
                    alertHelper({priority: "danger", title:Helper.__('Error'), message: Helper.__('The credit memo\'s total must be positive.')});
                    return;
                }

                if ( this.submitData.entity.shippingAmount > this.orderData.shipping_amount ) {
                    alertHelper({
                        priority: "danger",
                        title:'Error',
                        message: Helper.__('The refundable shipping amount is limited at %1').replace('%1', (this.orderData.shipping_amount - this.orderData.base_shipping_refunded))});
                    return;
                }

                if (canRefund == false) {
                    alertHelper({
                        priority: "danger",
                        title: "Error",
                        message: ("Data Refund Invalid!")
                    });
                    return;
                }

                $.each(data, function(index, value) {
                    if (value.name == 'adjustment_positive' || value.name == 'adjustment_negative'
                        ||  value.name == 'shipping_amount' || value.name == 'tax_amount') {
                        value.value = Helper.toBasePrice(value.value);
                    }
                    self.submitData = self.bindItem(self.submitData,value);
                    self.submitData = self.bindAdditionalData(self.submitData,value);
                });
                var resultSaveOffline = this.saveOrderOffline(this.submitData, data);
                    // if(resultSaveOffline!=2){
                    //     var html = '';
                    //     if(resultSaveOffline==0)
                    //         html = Helper.__('The credit memo\'s total must be positive.');
                    //     else
                    //         html = Helper.__('The refundable amount is limited at %1')
                    //         .replace('%1', Helper.convertAndFormatPrice(
                    //                 ((this.orderData.base_total_paid?this.orderData.base_total_paid:0)-
                    //                 (this.orderData.webpos_base_change?this.orderData.webpos_base_change:0))-
                    //                 (this.orderData.base_total_refunded?this.orderData.base_total_refunded:0)
                    //             ),
                    //             window.webposConfig.currentCurrencyCode,
                    //             this.orderData.base_currency_code);
                    //     alertHelper({priority: "danger", title:'Error', message: html});
                    //     // notification(Helper.__(html), true, 'error', Helper.__('Error'));
                    //     return false;
                    // }
                    parent.orderData(null);
                    parent.display(false);
                    Helper.dispatchEvent('sales_order_creditmemo_afterSave', {'response': this.orderData});
                    Helper.dispatchEvent('order_refund_after', {'response': this.orderData});
                    creditmemo().setPostData(this.submitData).setMode('online').save(deferred);
                    var params = {
                        order_id: this.orderData.entity_id,
                        increment_id: this.orderData.increment_id,
                        customer_id: this.orderData.customer_id,
                        amount: creditAmount
                    };
                    onlineResource().setPush(true).setLog(false).refund(params,deferred);

                    deferred.done(function (data) {
                        notification(Helper.__('A creditmemo has been created!'), true, 'success', Helper.__('Success'));
                        var viewManager = require('ui/components/layout');

                    }).fail(function (reason) {
                        notification($t(reason.responseText), true, 'danger', ('Error'));
                    });

                    this.returnStock(this.submitData);

            },

            saveOrderOffline: function(submitData, data){
                var self = this;
                var subtotal = 0;
                var totalDiscount = 0;
                if(submitData.entity.items.length>0){
                    $.each(self.orderData.items, function(orderItemIndex, orderItemValue){
                        $.each(submitData.entity.items, function(index, value){
                            if(value.orderItemId == orderItemValue.item_id){
                                var baseRowTotal = orderItemValue.base_row_invoiced - orderItemValue.base_amount_refunded;
                                subtotal += (baseRowTotal / (orderItemValue.qty_invoiced - orderItemValue.qty_refunded) * value.qty)
                                    + orderItemValue.base_tax_amount/orderItemValue.qty_invoiced*value.qty;
                                var baseDiscount = orderItemValue.base_discount_invoiced -
                                    (orderItemValue.base_discount_refunded?orderItemValue.base_discount_refunded:0);
                                totalDiscount += (baseDiscount/
                                (orderItemValue.qty_invoiced)*value.qty);
                                orderItemValue.qty_refunded += value.qty;
                            }
                            if(value.orderItemId == orderItemValue.parent_item_id){
                                orderItemValue.qty_refunded += value.qty;
                            }
                        });
                        if(Helper.isRewardPointsEnable() && orderItemValue.rewardpoints_base_discount > 0){
                            totalDiscount += parseFloat(orderItemValue.rewardpoints_base_discount);
                        }
                        if(Helper.isGiftCardEnable() && orderItemValue.base_gift_voucher_discount > 0){
                            totalDiscount += parseFloat(orderItemValue.base_gift_voucher_discount);
                        }
                    });
                }
                var grandTotal = subtotal - totalDiscount + this.submitData.entity.adjustmentPositive + this.submitData.entity.shippingAmount
                -this.submitData.entity.adjustmentNegative - parseFloat(this.orderData.shipping_discount_amount);

                var refundedAmount = grandTotal+(parseFloat(this.orderData.base_total_refunded)?parseFloat(this.orderData.base_total_refunded):0);
                var maxRefundAmount = (parseFloat(this.orderData.base_total_paid)?parseFloat(this.orderData.base_total_paid):0)-
                    (parseFloat(this.orderData.webpos_base_change)?parseFloat(this.orderData.webpos_base_change):0);

                refundedAmount = parseFloat(refundedAmount.toFixed(2));
                maxRefundAmount = parseFloat(maxRefundAmount.toFixed(2));

                if( (refundedAmount - maxRefundAmount > 0.5)){
                    if(submitData.entity.items.length>0){
                        $.each(self.orderData.items, function(orderItemIndex, orderItemValue){
                            $.each(submitData.entity.items, function(index, value){
                                if(value.orderItemId == orderItemValue.item_id){
                                    orderItemValue.qty_refunded -= value.qty;
                                }
                                if(value.orderItemId == orderItemValue.parent_item_id){
                                    orderItemValue.qty_refunded -= value.qty;
                                }
                            });
                        });
                    }
                    return 1;
                }
                this.orderData.base_total_refunded=this.orderData.base_total_refunded?
                    this.orderData.base_total_refunded+grandTotal:grandTotal;

                return 2;
            },
            
            returnStock: function(submitData){
                var self = this;
                $.each(submitData.entity.items, function(index, value){
                    if(value.additionalData && value.additionalData.search('back_to_stock')!=-1){
                        $.each(self.orderData.items, function(orderItemIndex, orderItemValue){
                            if(value.orderItemId == orderItemValue.item_id)
                                ProductModel().updateStock(value.qty, orderItemValue.product_id);
                            if(value.orderItemId == orderItemValue.parent_item_id)
                                ProductModel().updateStock(value.qty, orderItemValue.product_id);
                        })
                    }
                });
                
            },

            bindItem: function(data, item){
                var self = this;
                this.item = {};
                item.name = item.name.replace("items[", "");
                if(item.name.search('\\[qty\\]')!==-1){
                    item.name = item.name.replace("][qty]", "");
                    $.each(data.entity.items, function(index, value){
                        if(value.orderItemId == item.name){
                            value.qty = parseFloat(item.value);
                            self.isExist = true;
                        }
                    });
                    if(!this.isExist){
                        this.item.orderItemId = parseInt(item.name);
                        this.item.qty = parseFloat(item.value);
                        data.entity.items.push(this.item);
                    }
                }

                if(item.name.search('\\[back_to_stock\\]')!==-1){
                    item.name = item.name.replace("][back_to_stock]", "");
                    $.each(data.entity.items, function(index, value){
                        if(value.orderItemId == item.name){
                            value.additionalData = 'back_to_stock';
                            self.isExist = true;
                        }
                    });
                    if(!this.isExist) {
                        this.item.orderItemId = parseInt(item.name);
                        this.item.additionalData = 'back_to_stock';
                        data.entity.items.push(this.item);
                    }
                }
                this.isExist = false;
                return data;
            },

            bindComment: function(data, item){
                if(item.name.search('comment_text')===0 && item.value){
                    this.comment.comment = item.value;
                    data.entity.comments.push(this.comment);
                }
                return data;
            },

            bindAdditionalData: function(data, item){
                var value = parseFloat(item.value)>0?parseFloat(item.value):0
                switch (item.name){
                    case 'adjustment_positive':
                        data.entity.adjustmentPositive = value;
                        break;
                    case 'shipping_amount':
                        data.entity.shippingAmount = value;
                        break;
                    case 'adjustment_negative':
                        data.entity.adjustmentNegative = value;
                        break;
                    case 'send_email':
                        data.entity.emailSent = value;
                        break;
                }
                return data;
            }
        }
    }
);
