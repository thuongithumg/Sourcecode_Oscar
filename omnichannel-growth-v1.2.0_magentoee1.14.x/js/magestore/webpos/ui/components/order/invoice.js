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
        'underscore',
        'model/sales/order-factory',
        'mage/translate',
        'ui/components/order/action',
        'model/sales/order/invoice',
        'eventManager',
        'action/sales/order/invoice/create',
        'helper/alert',
        'ui/lib/modal/confirm',
        'helper/price',
        'helper/general',
        'model/checkout/checkout/payment'
    ],
    function ($, ko, _, OrderFactory, $t, Component, invoice, eventmanager, createInvoiceAction, Alert,
              Confirm, priceHelper, Helper, PaymentModel) {
        "use strict";

        return Component.extend({
            invoiceByAmount: ko.observable(false),
            isVisible: ko.observable(false),
            classIn: ko.observable(''),
            stypeDisplay: ko.observable('none'),
            formId: 'invoice-popup-form',
            submitArray: [],
            submitData: {},
            items: {},
            comment: {},
            paymentList: ko.observableArray([]),
            payment: {'method_data': []},
            payment_data: {},
            paymentMethod: ko.observable(),
            subtotalElement: '#invoice-popup-form td[id^=invoice_item_subtotal_]',
            rowtotalElement: '#invoice-popup-form td[id^=invoice_item_rowtotal_]',
            invoiceAmountId: 'input-invoice-amount',
            invoiceAmount: ko.observable(0),
            // invoiceAmountText: ko.observable(priceHelper.formatPrice(0)),
            defaults: {
                template: 'ui/order/invoice',
            },
            initialize: function () {
                var self = this;
                this._super();
                this.getListPayment();
                this.collectTotals();
                this.invoiceByAmount(false);
                this.listenEvents();
            },

            display: function (isShow) {
                var self = this;
                /* check invoiceable total */
                if (isShow) {
                    if (this.invoiceable() == 0 && this.orderData().grand_total > 0) {
                        if(this.orderData().base_total_due > 0) {
                            Alert({
                                priority:'warning',
                                title:'Warning',
                                message: $t('You must take payment on this order before creating invoice')
                            });
                        } else {
                            Alert({
                                priority:'warning',
                                title:'Warning',
                                message: $t('You can not create invoice for this order')
                            });
                        }
                        return;
                    }
                    this.isVisible(true);
                    this.stypeDisplay('block');
                    this.classIn('in');
                    $('#' + this.invoiceAmountId).focus();
                    $('#' + this.invoiceAmountId).trigger('focus');
                    $('.notification-bell').hide();
                    $('#c-button--push-left').hide();
                } else {
                    this.isVisible(false);
                    this.stypeDisplay('none');
                    this.classIn('');
                    $('#' + this.invoiceAmountId).val('');
                    $('.notification-bell').show();
                    $('#c-button--push-left').show();
                }
            },

            /**
             * Validate qty for invoice items
             *
             * @param {order item} data
             * @param html event
             */
            validateQty: function (data, event) {
                //var self = this;
                var qty = event.target.value;

                var maxQty = data.qty_ordered - data.qty_invoiced - data.qty_canceled;
                if (qty == '' || isNaN(qty) || parseInt(qty) !== parseFloat(qty) || parseFloat(qty) < 0 || parseFloat(qty) > maxQty) {
                    qty = maxQty;
                }
                /*
                 if(event.type=='keyup')
                 if(qty>0)
                 $('#invoice-popup-form input[name=invoice_amount]').val('');
                 */
                qty = this.getInvoiceableQty(data, qty);

                /*
                 $(event.currentTarget).val(qty);
                 $('#invoice_item_subtotal_'+data.item_id).attr('price',(qty*data.base_price));
                 $('#invoice_item_subtotal_'+data.item_id).attr('tax',qty*(data.base_tax_amount/data.qty_ordered));
                 $('#invoice_item_subtotal_'+data.item_id).attr('discount',qty*(data.base_discount_amount/data.qty_ordered));
                 $('#invoice_item_subtotal_'+data.item_id).text(this.convertAndFormatPrice((qty*data.base_price)));
                 $('#invoice_item_rowtotal_'+data.item_id).attr('price',((qty*data.base_price)-data.discount_amount/data.qty_ordered*(qty)));
                 $('#invoice_item_rowtotal_'+data.item_id).text(this.convertAndFormatPrice(((qty*data.base_price)-data.discount_amount/data.qty_ordered*(qty))));
                 this.invoiceSubtotal = 0;
                 this.invoiceTaxAmount = 0;
                 this.invoiceDiscountAmount = 0;
                 $(this.subtotalElement).each(function(index,value){
                 self.invoiceSubtotal += parseFloat($(value).attr('price'));
                 self.invoiceTaxAmount += parseFloat($(value).attr('tax'));
                 self.invoiceDiscountAmount += parseFloat($(value).attr('discount'));
                 });
                 $('#invoice_subtotal').text(this.convertAndFormatPrice(this.invoiceSubtotal));
                 $('#invoice_grandtotal').text(this.convertAndFormatPrice(this.invoiceSubtotal+this.getShippingAmount()+this.invoiceTaxAmount-this.invoiceDiscountAmount));
                 */
            },

            /**
             * Calculate invoice amount after click invoice amount text field
             *
             * @param data
             * @param event
             */
            calculateAmount: function (data, event) {
                if (event.target.value == '' || event.target.value == 0) {
                    event.target.value = this.currencyConvert(this.orderData().base_total_due);
                    $(event.target).trigger('change');
                }
                $(event.target).select();
            },

            /**
             * add Invoice amount and calculate invoice item for invoice
             *
             * @param data
             * @param event
             */
            addInvoiceAmount: function (data, event) {
                var self = this;
                if (event.target.value && parseFloat(event.target.value) > 0) {
                    var maxQty = 0;
                    $.each(this.orderData().items, function (index, value) {
                        if (value.parent_item_id)
                            return;
                        $('#invoice_item_qty_' + value.item_id).val(0);
                        $('#invoice_item_qty_' + value.item_id).trigger('change');
                    });
                    $.each($('#invoice-popup-form input[id^=invoice_item_qty_]'), function (index, value) {
                        $(value).val(0);
                        $(value).trigger('keyup');
                    });
                    var baseAmount = this.getPriceHelper().currencyConvert(
                        event.target.value,
                        window.webposConfig.currentCurrencyCode,
                        this.orderData().base_currency_code
                    );
                    var baseTotalPaid = this.orderData().base_total_paid ? this.orderData().base_total_paid : 0;
                    var maxInvoiceAmount = parseFloat(this.orderData().base_grand_total) - parseFloat(baseTotalPaid);
                    maxInvoiceAmount = maxInvoiceAmount > 0 ? maxInvoiceAmount : 0;
                    baseAmount = baseAmount > maxInvoiceAmount ? maxInvoiceAmount : baseAmount;
                    event.target.value = parseFloat(this.currencyConvert(baseAmount)).toFixed(2);
                    this.subtotal(event.target.value);
                    this.grandtotal(event.target.value);
                    self.invoiceByAmount(true);
                } else {
                    event.target.value = '';
                    self.invoiceByAmount(false);
                    $.each(this.orderData().items, function (index, value) {
                        if (value.parent_item_id)
                            return;
                        maxQty = value.qty_ordered - value.qty_invoiced - value.qty_canceled;
                        $('#invoice_item_qty_' + value.item_id).val(maxQty);
                        $('#invoice_item_qty_' + value.item_id).trigger('change');
                    });
                }
            },

            /**
             * get Subtotal for invoice item after load order
             *
             * @param {order item}data
             * @param {int} qty
             * @returns {number}
             */
            getSubtotalItem: function (data, qty) {
                qty = qty ? qty : data.qty_ordered - data.qty_invoiced - data.qty_canceled;
                return qty * data.base_price;
            },

            /**
             * get Tax amount for invoice item after load order
             *
             * @param {order item}data
             * @param {int} qty
             * @returns {number}
             */
            getTaxAmountItem: function (data, qty) {
                qty = qty ? qty : data.qty_ordered - data.qty_invoiced - data.qty_canceled;
                return qty * (data.base_tax_amount / data.qty_ordered);
            },

            /**
             * get discount amount for invoice item after load order
             *
             * @param {order item}data
             * @param {int} qty
             * @returns {number}
             */
            getDiscountAmountItem: function (data, qty) {
                qty = qty ? qty : data.qty_ordered - data.qty_invoiced - data.qty_canceled;
                return qty * (data.base_discount_amount / data.qty_ordered);
            },

            /**
             * get row total amount for invoice item after load order
             *
             * @param {order item}data
             * @param {int} qty
             * @returns {number}
             */
            getRowTotalItem: function (data, qty) {
                return this.getSubtotalItem(data, qty) + this.getTaxAmountItem(data, qty) - this.getDiscountAmountItem(data, qty);
            },

            /**
             * get Subtotal for invoice after load order
             *
             * @returns {number}
             */
            getSubtotal: function () {
                if (this.invoiceByAmount() == true) {
                    return this.toBasePrice(this.subtotal());
                }
                var subtotal = 0;
                if (this.orderData().items)
                    $.each(this.orderData().items, function (index, value) {
                        if (!value.parent_item_id && parseFloat(value.qty_ordered) - parseFloat(value.qty_invoiced) - parseFloat(value.qty_canceled) > 0)
                            subtotal += value.base_price * (value.qty_ordered - value.qty_invoiced - value.qty_canceled);
                    });
                return subtotal;
            },

            /**
             * get shipping amount for invoice after load order
             *
             * @returns {number}
             */
            getShippingAmount: function () {
                if (this.orderData().base_shipping_invoiced)
                    return 0;
                else if (this.orderData().base_shipping_amount)
                    return parseFloat(this.orderData().base_shipping_amount);
                else
                    return 0;
            },

            /**
             * get shipping discount amount for invoice after load order
             *
             * @returns {number}
             */
            getShippingDiscountAmount: function () {
                if (this.orderData().base_shipping_invoiced)
                    return 0;
                else if (this.orderData().base_shipping_discount_amount)
                    return parseFloat(this.orderData().base_shipping_discount_amount);
                else
                    return 0;
            },

            /**
             * get Grand Total for invoice after load order
             *
             * @returns {number}
             */
            getGrandTotal: function () {
                var base_tax_amount = 0, base_discount_amount = 0;
                if (this.orderData().items)
                    $.each(this.orderData().items, function (index, value) {
                        if (!value.parent_item_id && parseFloat(value.qty_ordered) - parseFloat(value.qty_invoiced) - parseFloat(value.qty_canceled) > 0) {
                            base_tax_amount += value.base_tax_amount / value.qty_ordered * (value.qty_ordered - value.qty_invoiced - value.qty_canceled);
                            base_discount_amount += value.base_discount_amount / value.qty_ordered * (value.qty_ordered - value.qty_invoiced - value.qty_canceled);
                        }
                    });
                return this.getSubtotal() + this.getShippingAmount() + base_tax_amount - base_discount_amount;
            },

            /**
             * get List Payment for invoice
             */
            getListPayment: function () {
                var self = this;
                var deferred = $.Deferred();
                var paidPayments = PaymentModel.getWebposPaidPayments();
                var payments = [{value: '0', label: ('--- Please select ---')}];
                if (paidPayments) {
                    $.each(paidPayments, function (index, value) {
                        if (!value.is_pay_later) {
                            payments.push({value: value.code, label: value.title});
                        }
                    });
                }
                self.paymentList(payments);
                createInvoiceAction.paymentList(payments);
            },

            /**
             *
             *
             * @param data
             * @param event
             */
            validateAmount: function (data, event) {
                var value = event.target.value;
                this.invoiceAmount(priceHelper.toPositiveNumber(value));
            },

            saveInvoiceOffline: function () {

            },

            /**
             * submit invoice
             */
            submit: function () {
                var self = this;
                if (priceHelper.comparePrice(self.grandTotal().toFixed(2), self.invoiceable().toFixed(2)) > 0) {
                    Alert({
                        priority:'warning',
                        title: 'Warning',
                        message: $t('You cannot create invoice over the total paid amount of this order.')
                    });
                } else {
                    Confirm({
                        content: ('Are you sure you want to submit this invoice?'),
                        actions: {
                            confirm: function () {
                                self.submitArray = $('#' + self.formId).serializeArray();
                                //$('#invoice-popup-form input[name=invoice_amount]').val('');
                                self.saveInvoiceOffline(self.submitArray);
                                var deferred = $.Deferred();
                                var data = self.orderData();
                                createInvoiceAction.execute(self.submitArray, data, deferred, self);
                                self.paymentMethod("");
                            },
                            always: function (event) {
                                event.stopImmediatePropagation();
                            }
                        }
                    });
                }
            },
            selectPayment: function (data, event) {
                this.paymentMethod(event.target.value);
            },
            /**
             * Listen events
             *
             */
            listenEvents: function () {
                var self = this;
                eventmanager.observer('sales_order_invoice_afterSave', function (event, data) {
                    if (data.response && data.response.entity_id > 0) {
                        var deferedSave = $.Deferred();
                        OrderFactory.get().setData(data.response).setMode('offline').save(deferedSave);
                        self.parentView().updateOrderListData(data.response);
                    }
                });

                eventmanager.observer('sales_order_list_load_order', function (event, data) {
                    self.collectItemTotals(data.order);
                });

                eventmanager.observer('sales_order_set_data_view_action', function (event, data) {
                    self.initInvoiceQtyItems();
                });
            },
            collectItemTotals: function (orderData) {

                if (!orderData)
                    return;

                for (var i in orderData.items) {
                    var item = orderData.items[i];
                    /* init invoice qty of item */
                    item.qtyToInvoice = ko.observable(0);

                    /* collect subtotal of item */
                    item.subTotalItem = ko.pureComputed(function () {
                        return parseFloat(this.qtyToInvoice()) * parseFloat(this.base_price);
                    }.bind(item));

                    /* collect tax of item */
                    item.taxAmountItem = ko.pureComputed(function () {
                        return parseFloat(this.qtyToInvoice()) * parseFloat(this.base_tax_amount) / parseFloat(this.qty_ordered);
                    }.bind(item));

                    /* collect discount of item */
                    item.discountAmountItem = ko.pureComputed(function () {
                        return parseFloat(this.qtyToInvoice()) * parseFloat(this.base_discount_amount) / parseFloat(this.qty_ordered);
                    }.bind(item));

                    /* collect row total of item */
                    item.rowTotalItem = ko.pureComputed(function () {
                        var total = this.subTotalItem() + this.taxAmountItem() - this.discountAmountItem();
                        if(total > 0){
                            if(this.rewardpoints_discount){
                                total -= this.rewardpoints_discount;
                            }
                            if(this.gift_voucher_discount) {
                                total -= this.gift_voucher_discount;
                            }
                        }
                        return total;
                    }.bind(item));

                    /* collect external discount of item */
                    item.points_base_discount = ko.pureComputed(function () {
                        var total = this.subTotalItem() + this.taxAmountItem() - this.discountAmountItem();
                        return (total > 0)?this.rewardpoints_base_discount:0;
                    }.bind(item));
                    item.voucher_base_discount = ko.pureComputed(function () {
                        var total = this.subTotalItem() + this.taxAmountItem() - this.discountAmountItem();
                        return (total > 0)?this.base_gift_voucher_discount:0;
                    }.bind(item));
                }
            },
            collectTotals: function (orderData) {
                var self = this;

                /* collect invoiceable total */
                this.invoiceable = ko.pureComputed(function () {
                    if (self.orderData.call()) {
                        var baseTotalInvoiced = self.orderData().base_total_invoiced ? self.orderData().base_total_invoiced : 0;
                        var baseTotalPaid = self.orderData().base_total_paid ? self.orderData().base_total_paid : 0;
                        var baseTotalRefunded = self.orderData().base_total_refunded ? self.orderData().base_total_refunded : 0;
                        
                        var invoiceableAmount = parseFloat(baseTotalPaid) - parseFloat(baseTotalInvoiced);
                        var invoiceable = (invoiceableAmount > baseTotalPaid)?baseTotalPaid:invoiceableAmount;
                        if (self.orderData().rewardpoints_discount){
                            var rewardpoints_discount = self.orderData().rewardpoints_discount ? self.orderData().rewardpoints_discount : 0;
                            invoiceable += rewardpoints_discount;
                        }
                        if (self.orderData().gift_voucher_discount){
                            var gift_voucher_discount = self.orderData().gift_voucher_discount ? self.orderData().gift_voucher_discount : 0;
                            invoiceable += gift_voucher_discount;
                        }
                        if (self.orderData().customercredit_discount){
                            var customercredit_discount = self.orderData().customercredit_discount ? self.orderData().customercredit_discount : 0;
                            invoiceable += customercredit_discount;
                        }
                        return invoiceable;
                    } else {
                        return 0;
                    }
                });
                /* collect subTotal */
                this.subTotal = ko.pureComputed(function () {
                    var subtotal = 0;
                    if (self.orderData.call()) {
                        $.each(self.orderData.call().items, function (index, value) {
                            subtotal += parseFloat(value.base_price) * value.qtyToInvoice();
                        });
                    }
                    return subtotal;
                });
                /* collect tax amount */
                this.taxAmount = ko.pureComputed(function () {
                    var base_tax_amount = 0;
                    if (self.orderData.call()) {
                        $.each(self.orderData.call().items, function (index, value) {
                            base_tax_amount += parseFloat(value.base_tax_amount) / parseFloat(value.qty_ordered) * value.qtyToInvoice();
                        });
                    }
                    return base_tax_amount;
                });
                /* collect discount amount */
                this.discountAmount = ko.pureComputed(function () {
                    var base_discount_amount = 0;
                    if (self.orderData.call()) {
                        $.each(self.orderData.call().items, function (index, value) {
                            base_discount_amount += parseFloat(value.base_discount_amount) / parseFloat(value.qty_ordered) * value.qtyToInvoice();
                        });
                        base_discount_amount += self.getShippingDiscountAmount();
                    }
                    return base_discount_amount;
                });
                /* collect grandTotal */
                this.grandTotal = ko.pureComputed(function () {
                    var grandtotal = 0;
                    if (self.orderData.call()) {
                        $.each(self.orderData.call().items, function (index, value) {
                            grandtotal += value.rowTotalItem();
                        });
                        grandtotal = (grandtotal > 0)?grandtotal:0;
                        grandtotal += self.getShippingAmount();
                    }
                    if(Helper.isRewardPointsEnable()) {
                        var rewardpoints_discount = self.orderData() && self.orderData().rewardpoints_discount ? self.orderData().rewardpoints_discount : 0;
                        if (rewardpoints_discount) {
                            grandtotal -= rewardpoints_discount;
                        }
                    }
                    if(self.orderData().grand_total > grandtotal){
                        grandtotal = self.orderData().grand_total;
                    }
                    /* round the grandtotal up to 4 numbers after ',' */
                    // grandtotal = Math.round(grandtotal * 10000) / 10000;
                    return grandtotal;
                });
                if(Helper.isRewardPointsEnable()) {
                    this.pointsDiscount = ko.pureComputed(function () {
                        var discount = 0;
                        if (self.orderData.call()) {
                            $.each(self.orderData.call().items, function (index, value) {
                                if(value.qtyToInvoice() > 0){
                                    discount += value.rewardpoints_discount;
                                }
                            });
                        }
                        var rewardpoints_discount = self.orderData()&&self.orderData().rewardpoints_discount ? self.orderData().rewardpoints_discount : 0;
                        if(!discount && rewardpoints_discount){
                            discount = rewardpoints_discount;
                        }
                        return -1*discount;
                    });
                    this.pointsDiscount.subscribe(function(value){
                        var hasPoint = (value < 0)?true:false;
                        self.hasRewardpoints(hasPoint);
                    });
                }
                if(Helper.isGiftCardEnable()) {
                    this.voucherDiscount = ko.pureComputed(function () {
                        var discount = 0;
                        if (self.orderData.call()) {
                            $.each(self.orderData.call().items, function (index, value) {
                                if(value.qtyToInvoice() > 0) {
                                    discount += value.gift_voucher_discount;
                                }
                            });
                        }
                        return -discount;
                    });
                    this.voucherDiscount.subscribe(function(value){
                        var hasVoucher = (value < 0)?true:false;
                        self.hasGiftcard(hasVoucher);
                    });
                }
            },
            /**
             * Get remaining qty need to invoice
             *
             * @returns {int|float}
             */
            getInvoiceQty: function (item) {
                var qty = parseFloat(item.qty_ordered) - parseFloat(item.qty_invoiced) - parseFloat(item.qty_canceled);
                qty = qty > 0 ? qty : 0;
                return qty;
            },
            setInvoiceQty: function (item, invoiceQty) {
                item.qtyToInvoice(invoiceQty);
            },
            /**
             * Initial invoice qty of items
             *
             */
            initInvoiceQtyItems: function () {
                var self = this;
                var orderData = this.orderData();
                if (!orderData)
                    return;
                // var minDecimalValue = WEBPOS.getConfig('general/min_decimal_value');
                var minDecimalValue = 0;
                if (orderData.items) {
                    $.each(orderData.items, function (index, value) {
                        if (!value.parent_item_id && self.getInvoiceQty(value) > 0) {
                            /* get max qty to invoice */
                            var invoiceQty = self.getInvoiceQty(value);
                            self.setInvoiceQty(value, invoiceQty);
                            self.orderData(orderData);
                            /* compare grandTotal with invoiceable */
                            if (priceHelper.comparePrice(self.grandTotal(), self.invoiceable()) > 0) {
                                for (var i = 1; i <= invoiceQty; i++) {
                                    /* decrease invoiceQty in item */
                                    self.setInvoiceQty(value, invoiceQty - i);
                                    self.orderData(orderData);
                                    /* compare grandTotal with invoiceable again */
                                    if (priceHelper.comparePrice(self.grandTotal(), self.invoiceable()) < 1) {
                                        break;
                                    }
                                }
                            }
                        }
                    });
                }
            },
            /**
             * Validate grandTotal of invoice after added items to invoice
             *
             */
            getInvoiceableQty: function (item, invoiceQty) {
                var self = this;
                var orderData = this.orderData();
                var correctInvoiceQty = invoiceQty;
                var isInvoiceOverTotalPaid = false;
                // var minDecimalValue = WEBPOS.getConfig('general/min_decimal_value');
                var minDecimalValue = 0;
                $.each(orderData.items, function (index, value) {
                    if (value.item_id == item.item_id) {
                        /* set qty to invoice */
                        self.setInvoiceQty(value, invoiceQty);
                        self.orderData(orderData);
                        /* compare grandTotal with invoiceable */
                        if (priceHelper.comparePrice(self.grandTotal().toFixed(2), self.invoiceable().toFixed(2)) > 0) {
                            isInvoiceOverTotalPaid = true;
                            for (var i = 1; i <= invoiceQty; i++) {
                                /* decrease invoiceQty in item */
                                self.setInvoiceQty(value, invoiceQty - i);
                                self.orderData(orderData);
                                /* compare grandTotal with invoiceable again */
                                if (priceHelper.comparePrice(self.grandTotal(), self.invoiceable()) < 1) {
                                    correctInvoiceQty = value.qtyToInvoice();
                                    break;
                                }
                            }
                        }
                    }
                });
                if (isInvoiceOverTotalPaid) {
                    Alert({
                        priority:'warning',
                        title: 'Warning',
                        message: $t('You must take payment on this order to create invoice for more items.')
                    });
                }
                return correctInvoiceQty;
            },
            hasRewardpoints: ko.observable(false),
            hasGiftcard: ko.observable(false)
        });
    }
);