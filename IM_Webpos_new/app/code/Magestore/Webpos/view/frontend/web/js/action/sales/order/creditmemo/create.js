/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/sales/order/creditmemo',
        'Magestore_Webpos/js/model/catalog/product',
        'Magestore_Webpos/js/helper/alert',
        'Magestore_Webpos/js/action/notification/add-notification',
        'Magestore_Webpos/js/helper/general'
    ],
    function($, ko, creditmemo, ProductModel, alertHelper, notification, Helper) {
        'use strict';
        return {
            submitArray: [],
            submitData: {
                "entity": {
                    "orderId": 0,
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
                var canRefund = false;
                var allQty = false;
                this.submitData = {
                    "entity": {
                        "orderId": this.orderData.entity_id,
                        "adjustmentPositive": 0,
                        "adjustmentNegative": 0,
                        "emailSent": 0,
                        "shippingAmount": 0,
                        'baseCurrencyCode': window.webposConfig.baseCurrencyCode,
                        'storeCurrencyCode': window.webposConfig.currentCurrencyCode,
                        "items": [],
                        "comments": [],
                        "refund_points": 0
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

                if (this.submitData.entity.adjustmentNegative > 0 || this.submitData.entity.adjustmentPositive > 0
                    || allQty == true || this.submitData.entity.shippingAmount > 0) {
                    canRefund = true;
                }

                if (this.submitData.entity.adjustmentNegative > 0 && this.submitData.entity.adjustmentPositive <= 0 && allQty == false) {
                    alertHelper({
                        priority: "danger", title:Helper.__('Error'),
                        message: Helper.__('The credit memo\'s total must be positive.')});
                    return;
                }

                if ( this.submitData.entity.shippingAmount > this.orderData.shipping_amount ) {
                    var shippingRefund = parseFloat(this.orderData.base_shipping_refunded)?parseFloat(this.orderData.base_shipping_refunded):0;
                    alertHelper({
                        priority: "danger",
                        title:'Error',
                        message: Helper.__('The refundable shipping amount is limited at %1').replace('%1',
                            parseFloat(this.orderData.shipping_amount - shippingRefund))});
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
                    self.submitData = self.bindComment(self.submitData,value);
                    self.submitData = self.bindAdditionalData(self.submitData,value);
                });
               // if(this.submitData.entity.items.length > 0){
                var resultSaveOffline = this.saveOrderOffline(this.submitData);
                if (!orderData.customer_balance_amount) {
                    if(resultSaveOffline!=2){
                        var html = '';
                        if(resultSaveOffline==0)
                            html = Helper.__('The credit memo\'s total must be positive.');
                        else
                            html = Helper.__('The refundable amount is limited at %1')
                                .replace('%1', Helper.convertAndFormatPrice(
                                    ((this.orderData.base_total_paid?this.orderData.base_total_paid:0)-
                                        (this.orderData.webpos_base_change?this.orderData.webpos_base_change:0))-
                                    (this.orderData.base_total_refunded?this.orderData.base_total_refunded:0)
                                    ),
                                    window.webposConfig.currentCurrencyCode,
                                    this.orderData.base_currency_code);
                        alertHelper({title:'Error', content: html});
                        return false;
                    }
                }
                notification(Helper.__('A creditmemo has been created!'), true, 'success', Helper.__('Success'));
                parent.orderData(null);
                parent.display(false);
                //Helper.dispatchEvent('sales_order_creditmemo_afterSave', {'response': this.orderData});
                Helper.dispatchEvent('order_refund_after', {'response': this.orderData});
                creditmemo().setPostData(this.submitData).setMode('online').save(deferred);
                this.returnStock(this.submitData);
               // }
            },

            saveOrderOffline: function(submitData){
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
                                (orderItemValue.qty_invoiced-orderItemValue.qty_refunded)*value.qty);
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
                var grandTotal = subtotal - totalDiscount + this.submitData.entity.adjustmentPositive +
                    this.submitData.entity.shippingAmount - this.submitData.entity.adjustmentNegative;
                var refundedAmount = grandTotal+(this.orderData.base_total_refunded?this.orderData.base_total_refunded:0);
                var maxRefundAmount = (this.orderData.base_total_paid?this.orderData.base_total_paid:0)-
                    (this.orderData.webpos_base_change?this.orderData.webpos_base_change:0);
                if(Helper.isRewardPointsEnable() && this.orderData.rewardpoints_base_discount){
                    maxRefundAmount -= parseFloat(this.orderData.rewardpoints_base_discount);
                }
                refundedAmount = parseFloat(refundedAmount.toFixed(2));
                maxRefundAmount = parseFloat(maxRefundAmount.toFixed(2));
                if( (refundedAmount - maxRefundAmount > 0.01)){
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
                    case  'refund_points':
                        data.entity.refund_points = value;
                    case  'refund_earned_points':
                        data.entity.refund_earned_points = value;
                }
                return data;
            }
        }
    }
);
