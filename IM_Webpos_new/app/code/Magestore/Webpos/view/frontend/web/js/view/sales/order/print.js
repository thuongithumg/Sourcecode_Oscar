/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/sales/order/action',
        'Magestore_Webpos/js/model/sales/order/total',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/helper/datetime',
        'mage/translate'
    ],
    function ($, ko, Component, orderTotal, Helper, DateHelper, $t) {
        "use strict";

        //noinspection JSDuplicatedDeclaration
        return Component.extend({
            containerId: 'container-print-order',
            totalsCode: ko.observableArray(),
            configs: ko.observableArray(),
            customerAdditionalInfomation: ko.observableArray(),
            defaults: {
                template: 'Magestore_Webpos/sales/order/print',
            },

            initialize: function () {
                this._super();
                var self = this;
                ko.pureComputed(function() {
                    return self.orderData();
                }).subscribe(function() {
                    if(self.orderData()){
                        self.initDefaultData();
                        if ($("#webpos_sales_order_receipt_barcode").length > 0) {
                            $("#webpos_sales_order_receipt_barcode").html("").barcode(self.getOrderData('increment_id'), "code128", {
                                output:"css",
                                bgColor: "#FFFFFF",
                                color: "#000000",
                                barWidth: 1,
                                barHeight: 20
                            });
                        }
                    }
                });
                
            },

            initDefaultData: function(){
                var totalsCode =[
                    {code:'subtotal',title:'Subtotal', required:true, sortOrder: 1, isPrice: true},
                    {code:'shipping_amount',title:'Shipping', required:true, sortOrder: 10, isPrice: true},
                    {code:'tax_amount',title:'Tax', required:true,  sortOrder: 20, isPrice: true},
                    {code:'discount_amount',title:'Discount', required:false,  sortOrder: 30, isPrice: true},
                    {code:'gift_cards_amount', title: 'Gift Card', required: false,  sortOrder: 35, isPrice:true},
                    {code:'grand_total',title:'Grand Total', required:true,  sortOrder: 40, isPrice: true},
                    {code:'total_paid',title:'Total Paid', required:true,  sortOrder: 50, isPrice: true},
                    {code:'total_due',title:'Total Due', required:true,  sortOrder: 60, isPrice: true}
                ];
                var eventData = {
                    totals:totalsCode
                };
                var customerAdditionalInfomation = [];
                if (this.getOrderData('billing_address') && this.getOrderData('billing_address')['telephone'] && this.getOrderData('billing_address')['telephone']!=window.webposConfig['webpos/guest_checkout/telephone']){
                    customerAdditionalInfomation.push({'label':'Phone: ','value':this.getOrderData('billing_address')['telephone']})
                } else if (this.getOrderData('shipping_address') && this.getOrderData('shipping_address')['telephone'] && this.getOrderData('shipping_address')['telephone']!=window.webposConfig['webpos/guest_checkout/telephone']){
                    customerAdditionalInfomation.push({'label':'Phone: ','value':this.getOrderData('shipping_address')['telephone']})
                }
                this.customerAdditionalInfomation(customerAdditionalInfomation);
                Helper.dispatchEvent('prepare_receipt_totals', eventData);
                totalsCode.sort(function(a, b) {
                    if(!a.sortOrder){
                        a.sortOrder = 2;
                    }
                    if(!b.sortOrder){
                        b.sortOrder = 2;
                    }
                    return parseFloat(a.sortOrder) - parseFloat(b.sortOrder);
                });
                this.totalsCode(totalsCode);

                var configs = [
                    {code:'auto_print',value:window.webposConfig["webpos/receipt/general/auto_print"]},
                    {code:'title',value:window.webposConfig["webpos/receipt/content/title"]},
                    {code:'font_type',value:window.webposConfig["webpos/receipt/content/font_type"]},
                    {code:'footer_text',value:window.webposConfig["webpos/receipt/content/footer_text"]},
                    {code:'header_text',value:window.webposConfig["webpos/receipt/content/header_text"]},
                    {code:'show_cashier_name',value:window.webposConfig["webpos/receipt/optional/show_cashier_name"]},
                    {code:'show_comment',value:window.webposConfig["webpos/receipt/optional/show_comment"]},
                    {code:'show_receipt_logo',value:window.webposConfig["webpos/receipt/optional/show_receipt_logo"]},
                    {code:'logo',value:window.webposConfig["webpos/general/webpos_logo"]}
                ];
                this.configs(configs);
            },
            
            formatPrice: function(string){
                return Helper.formatPrice(string);
            },

            getConfigData: function(code){
                if(this.configs()){
                    var config = ko.utils.arrayFirst(this.configs(), function(config){
                        return (config && config.code == code);
                    });
                    if(config){
                        return config.value;
                    }
                }
                return "";
            },

            hasRefundAmount: function(){
                return (this.getOrderData('total_refunded')>0)?true:false;
            },

            getRefundAmount: function(){
                return this.getOrderData('total_refunded')+" "+this.getOrderData('total_refunded');
            },
            
            getOrderData: function (key) {
                var self = this;
                var data = false;
                if(self.orderData()){
                    data = self.orderData();
                    if(key){
                        if(typeof data[key] != "undefined"){
                            data = data[key];
                        }else{
                            data = ""
                        }
                    }
                }
                return data;
            },
            isAutoPrint: function(){
                return (this.getConfigData('auto_print') == 1)?true:false;
            },
            getFont: function(){
                return this.getConfigData('font_type');
            },
            getTitle: function(){
                var title = $t('Invoice');
                if (this.getConfigData('title')) {
                    title = this.getConfigData('title');
                    title = title.toUpperCase();
                }
                return title;
            },
            getFooterHtml: function(){
                if ($("#webpos_sales_order_receipt_barcode").length > 0) {
                    $("#webpos_sales_order_receipt_barcode").html("").barcode(this.getOrderData('increment_id'), "code128", {
                        output:"css",
                        bgColor: "#FFFFFF",
                        color: "#000000",
                        barWidth: 1,
                        barHeight: 20
                    });
                }
                return this.getConfigData('footer_text');
            },
            getHeaderHtml: function(){
                return this.getConfigData('header_text');
            },
            hasHeaderHtml: function(){
                return (this.getConfigData('header_text'))?true:false;
            },
            isShowDeliveryDate: function(){
                return (this.getOrderData('webpos_delivery_date'))?true:false;
            },
            isShowCashierName: function(){
                return (this.getConfigData('show_cashier_name')== 1)?true:false;
            },
            isShowComment: function(){
                return (this.getConfigData('show_comment') == 1 && this.getComment().length)?true:false;
            },
            isShowLogo: function(){
                return (this.getConfigData('show_receipt_logo')== 1)?true:false;
            },
            getLogoPath: function(){
                return this.getConfigData('logo');
            },
            getIncrementId: function(){
                return "#"+this.getOrderData('increment_id');
            },
            getCreatedDate: function(){
                return DateHelper.getDate(this.getOrderData('created_at'));
            },
            getDeliveryDate: function(){
                return DateHelper.getFullDatetime(this.getOrderData('webpos_delivery_date'));
            },
            getCashierName: function(){
                return this.getOrderData('webpos_staff_name');
            },
            getCreatedTime: function(){
            	var createdAt = this.getOrderData('created_at');
                var currentTime = DateHelper.stringToCurrentTime(createdAt);
                return DateHelper.getTime(currentTime);
            },
            getComment: function(){
                var data = [];
                $.each(this.getOrderData('status_histories'), function (index, item) {
                     if(item.comment) {
                         data.push(item);
                     }
                });
                return data;
            },
            getShipping: function(){
                return this.getOrderData('shipping_description');
            },
            hasShipping: function(){
                return (this.getOrderData('shipping_amount')>0)?true:false;
            },
            getCustomerName: function(){
                return this.getOrderData('customer_firstname')+" "+this.getOrderData('customer_lastname');
            },
            hasCustomerName: function(){
                return (this.getOrderData('customer_firstname') || this.getOrderData('customer_lastname'))?true:false;
            },
            getWebposChange: function(){
                return this.getOrderData('webpos_change')+" "+this.getOrderData('webpos_change');
            },
            hasWebposChange: function(){
                return (this.getOrderData('webpos_change')>0)?true:false;
            },
            getPayment: function(){
                var payments = [];
                if(this.getOrderData('webpos_order_payments') && this.getOrderData('webpos_order_payments').length > 0){
                    $.each(this.getOrderData('webpos_order_payments'), function(index, payment) {
                        if(payment.payment_amount > 0){
                            payments.push(payment);
                        }
                    });
                }
                return payments;
            },
            hasPayment: function(){
                return (this.getPayment() && this.getPayment().length >0)?true:false;
            },
            getItems: function(){
                return this.getOrderData('items');
            },
            getOrderTotals: function(){
                var self = this;
                var totals = [];
                if(self.totalsCode() && self.totalsCode().length > 0){
                    $.each(self.totalsCode(), function(index, data) {
                        var amount = self.getOrderData(data.code);
                        if(amount || (data.required && data.required == true)){
                            var amount = self.getOrderData(data.code);
                            var value = (data.isPrice == false)?(amount +' '+data.valueLabel):self.formatPrice(amount);
                            if(data.code == 'discount_amount' &&
                                (self.getOrderData('discount_description') || self.getOrderData('coupon_code'))){
                                var total = {
                                    'label':data.title + ' ('+self.getOrderData('discount_description')+')',
                                    'value':value
                                };
                            }else{
                                var total = {
                                    'label':data.title,
                                    'value':value
                                };
                            }
                            totals.push(total);
                        }
                    });
                }
                return totals;
            },

            /**
             * return a date time with format: Thursday 4 May, 2016 15:26PM
             * @param dateString
             * @returns {string}
             */
            getFullCurrentDatetime: function (dateString) {
                var currentTime = DateHelper.stringToCurrentTime(dateString);
                return DateHelper.getFullDatetime(currentTime);
            }
            
        });
    }
);