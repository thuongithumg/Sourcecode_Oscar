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
        'model/sales/order/invoice',
        'eventManager',
        'helper/alert',
        'action/notification/add-notification',
        'mage/translate'
    ],
    function($, ko, invoice,  eventmanager, alertHelper, notification, $t) {
        'use strict';
        return {
            isValid: false, 
            submitArray: [],
            submitData: {},
            orderData: {},
            items: {},
            comment: {},
            paymentList: ko.observableArray([]),
            payment: {'method_data': []},
            payment_data: {},
            
            execute: function(data, orderData, deferred, parent){
                var self = this;
                this.isValid = false;
                this.orderData = orderData;
                this.payment = {'method_data': []};
                this.submitData = {
                    'entity': {
                        "emailSent": 0,
                        'baseCurrencyCode': window.webposConfig.baseCurrencyCode,
                        'totalQty': 0,
                        'subtotal': 0,
                        'baseSubtotal': 0,
                        'taxAmount': 0,
                        'baseTaxAmount': 0,
                        'subtotalInclTax': 0,
                        'baseSubtotalInclTax': 0,
                        'discountAmount': 0,
                        'baseDiscountAmount': 0,
                        'shippingAmount': 0,
                        'baseShippingAmount': 0,
                        'shippingInclTax': 0,
                        'baseShippingInclTax': 0,
                        'shippingTaxAmount': 0,
                        'baseShippingTaxAmount': 0,
                        'grandTotal': 0,
                        'baseGrandTotal': 0,
                        'baseToGlobalRate': this.orderData.base_to_global_rate,
                        'baseToOrderRate': this.orderData.base_to_order_rate,
                        'billingAddressId': this.orderData.billing_address_id,
                        'createdAt': new Date().toISOString(),
                        'globalCurrencyCode': this.orderData.global_currency_code,
                        'orderCurrencyCode': this.orderData.order_currency_code,
                        'orderId': this.orderData.entity_id,
                        'shippingAddressId': this.orderData.billing_address_id,
                        'state': 2,
                        'storeCurrencyCode': window.webposConfig.currentCurrencyCode,
                        'storeId': this.orderData.store_id,
                        'storeToBaseRate': this.orderData.store_to_base_rate,
                        'storeToOrderRate': this.orderData.store_to_order_rate,
                        'updatedAt': new Date().toISOString(),
                        'items': [],
                        'comments': [],
                        'extensionAttributes': {}
                    }
                };
                if(!this.orderData.base_shipping_invoiced || this.orderData.base_shipping_amount - this.orderData.base_shipping_invoiced){
                    this.submitData.entity.shippingAmount = this.orderData.shipping_amount;
                    this.submitData.entity.baseShippingAmount = this.orderData.base_shipping_amount;
                    this.submitData.entity.shippingInclTax = this.orderData.shipping_incl_tax;
                    this.submitData.entity.baseShippingInclTax = this.orderData.base_shipping_incl_tax;
                    this.submitData.entity.shippingTaxAmount = this.orderData.shipping_tax_amount;
                    this.submitData.entity.baseShippingTaxAmount = this.orderData.base_shipping_tax_amount;
                }
                this.processParams(data);
                this.submitData.entity.grandTotal = parseFloat(
                    this.submitData.entity.grandTotal+
                    this.submitData.entity.subtotal+
                    this.submitData.entity.taxAmount+
                    this.submitData.entity.shippingAmount+
                    this.submitData.entity.discountAmount
                );
                this.submitData.entity.baseGrandTotal = parseFloat(
                    this.submitData.entity.baseGrandTotal+
                    this.submitData.entity.baseSubtotal+
                    this.submitData.entity.baseTaxAmount+
                    this.submitData.entity.baseShippingAmount+
                    this.submitData.entity.baseDiscountAmount
                );
                if(this.payment.method_data.length>0){
                    this.submitData.payment = {
                        'method': this.orderData.payment.method,
                        'method_data': this.payment.method_data
                    }
                }
                if(!this.isValid){
                    alertHelper({
                        priority: "danger",
                        title: "Error",
                        message: $t("Please choose an item to invoice!")
                    });
                    return;
                }
                notification($t('The invoice has been created successfully.'), true, 'success', 'Success');
                parent.orderData(null);
                parent.display(false);
                invoice().setPostData(this.submitData).setMode('online').save();
                this.saveOrderOffline(this.submitData);
            },

            saveOrderOffline: function(submitData){
                var self = this;
                if(submitData.entity.items.length>0){
                    $.each(self.orderData.items, function(orderItemIndex, orderItemValue){
                        $.each(submitData.entity.items, function(index, value){
                            if(value.orderItemId == orderItemValue.item_id){
                                orderItemValue.qty_invoiced += value.qty;
                            }
                            if(value.orderItemId == orderItemValue.parent_item_id){
                                orderItemValue.qty_invoiced += value.qty;
                            }
                        });
                    });
                }
                eventmanager.dispatch('sales_order_invoice_afterSave', {'response': this.orderData});
            },

            processParams: function(data){
                var self = this;
                $.each(data, function(index, value){
                    self.submitData = self.bindItem(self.submitData,value);
                    self.submitData = self.bindComment(self.submitData,value);
                    self.submitData = self.bindPayment(self.submitData,value);
                    self.submitData = self.bindAmount(self.submitData,value);
                    self.submitData = self.bindSendmail(self.submitData,value);
                });
            },

            bindItem: function(data, item){
                var self = this;
                if(item.name.search('items')===0){
                    if(parseFloat(item.value) > 0)
                        this.isValid = true;
                    item.name = item.name.replace("items[", "");
                    item.name = item.name.replace("]", "");
                    $.each(this.orderData.items, function(index,value){
                        if(parseInt(item.name) == parseInt(value.item_id)){
                            self.item = {};
                            self.item.orderItemId = parseInt(item.name);
                            self.item.qty = parseFloat(item.value);
                            data.entity.totalQty += self.item.qty;
                            self.item.productId = parseFloat(value.product_id);
                            self.item.name = value.name;
                            self.item.sku = value.sku;
                            self.item.price = parseFloat(value.price);
                            self.item.basePrice = parseFloat(value.base_price);
                            self.item.rowTotal = parseFloat(self.item.price*self.item.qty);
                            data.entity.subtotal += parseFloat(self.item.rowTotal);
                            self.item.baseRowTotal = parseFloat(self.item.basePrice*self.item.qty);
                            data.entity.baseSubtotal += parseFloat(self.item.baseRowTotal);
                            self.item.taxAmount = parseFloat(value.tax_amount);
                            data.entity.taxAmount += parseFloat(self.item.taxAmount);
                            data.entity.subtotalInclTax += parseFloat(self.item.rowTotal+self.item.taxAmount);
                            self.item.baseTaxAmount = parseFloat(value.base_tax_amount);
                            data.entity.baseTaxAmount += parseFloat(self.item.baseTaxAmount);
                            data.entity.baseSubtotalInclTax += parseFloat(self.item.baseRowTotal+self.item.baseTaxAmount);
                            self.item.discountAmount = parseFloat(value.discount_amount/value.qty_ordered*self.item.qty);
                            data.entity.discountAmount -= parseFloat(self.item.discountAmount);
                            self.item.baseDiscountAmount = parseFloat(value.base_discount_amount/value.qty_ordered*self.item.qty);
                            data.entity.baseDiscountAmount -= parseFloat(self.item.baseDiscountAmount);
                            self.item.priceInclTax = parseFloat(self.item.price+self.item.taxAmount);
                            self.item.basePriceInclTax = parseFloat(self.item.basePrice+self.item.baseTaxAmount);
                            self.item.rowTotalInclTax = parseFloat(self.item.priceInclTax*self.item.qty);
                            self.item.baseRowTotalInclTax = parseFloat(self.item.basePriceInclTax*self.item.qty);
                            data.entity.items.push(self.item);
                        }
                        // if(parseInt(value.parent_item_id) && parseInt(item.name) == parseInt(value.parent_item_id)){
                        //     self.item = {};
                        //     self.item.orderItemId = parseFloat(value.item_id);
                        //     self.item.qty = parseFloat(item.value);
                        //     data.entity.totalQty += self.item.qty;
                        //     self.item.productId = parseFloat(value.product_id);
                        //     data.entity.items.push(self.item);
                        // }
                    })
                }
                return data;
            },

            bindComment: function(data, item){
                if(item.name.search('comment_text')===0 && item.value!=''){
                    this.comment.comment = item.value;
                    this.comment.createdAt =  new Date().toISOString();
                    this.comment.isVisibleOnFront =  1;
                    data.entity.comments.push(this.comment);
                }
                return data;
            },

            bindPayment: function(data, item){
                if(item.name.search('payment_method')===0 && item.value!='0'){
                    this.payment_data.code = item.value;
                    var method = _.select(this.paymentList(), function (obj) {
                        return obj.value === item.value;
                    });
                    this.payment_data.title = method[0].label;
                    this.payment.method_data.push(this.payment_data);
                }
                return data;
            },

            bindAmount: function(data, item){
                if(item.name.search('invoice_amount')===0 && parseFloat(item.value)>0){
                    this.isValid = true;
                    data.invoiceAmount = item.value;
                }
                return data;
            },

            bindSendmail: function(data, item){
                if(item.name.search('send_email')===0 && parseFloat(item.value)>0){
                    data.entity.emailSent = item.value;
                }
                return data;
            },
        }
    }
);
