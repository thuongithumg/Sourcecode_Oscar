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
        'posComponent',
        'helper/general',
        'helper/datetime',
        'model/checkout/checkout',
        'action/notification/add-notification',
        'model/checkout/integration/pdf-invoice-plus',
        'lib/jquery/jquery-barcode'
    ],
    function ($, ko, Component, Helper, DateHelper, CheckoutModel, AddNoti, PdfInvoicePlusModel) {
        "use strict";

        return Component.extend({
            containerId: 'checkout_success_print_receipt',
            codeCheck: ['rewardpoints_earn', 'rewardpoints_spent', 'rewardpoints_discount', 'gift_voucher_discount', 'shipping_amount', 'discount_amount', 'tax_amount', 'total_due'],
            defaults: {
                template: 'ui/checkout/checkout/receipt'
            },
            totalsCode: ko.observableArray(),
            configs: ko.observableArray(),
            customerAdditionalInfomation: ko.observableArray(),
            printWindow: ko.observable(),
            initialize: function () {
                this._super();
                this.orderData = ko.pureComputed(function(){
                    var result = CheckoutModel.createOrderResult();
                    return (result && result.increment_id)?result:false;
                });
                var self = this;
                Helper.observerEvent('print_receipt',function(event,data){
                    self.printReceipt();
                });
                Helper.observerEvent('start_new_order', function(){
                    if(self.printWindow()){
                        self.printWindow().close();
                    }
                });
                Helper.observerEvent('webpos_order_save_after', function(event, data){
                    if(data && data.increment_id){
                        self.initDefaultData();
                        if(self.isAutoPrint()){
                            self.printReceipt();
                        }
                    }
                });
                Helper.observerEvent('webpos_place_order_online_after', function(event, data){
                    var orderData = data.data;
                    if(orderData && orderData.increment_id){
                        self.initDefaultData();
                        if(self.isAutoPrint()){
                            self.printReceipt();
                        }
                    }
                });
            },
            
            initDefaultData: function(){
                var self = this;
                var discountLabel = (self.getOrderData().discount_description)?('Discount' +
                '(' + self.orderData().discount_description + ')'):'Discount';
                var totalsCode =[
                    {code:'subtotal',title:'Subtotal', required:true, sortOrder: 1, isPrice: true},
                    {code:'shipping_amount',title:'Shipping', required:true, sortOrder: 10, isPrice: true},
                    {code:'tax_amount',title:'Tax', required:true,  sortOrder: 20, isPrice: true},
                    {code:'discount_amount',title:discountLabel, required:false,  sortOrder: 30, isPrice: true},
                    {code:'grand_total',title:'Grand Total', required:true,  sortOrder: 40, isPrice: true},
                    {code:'total_paid',title:'Total Paid', required:true,  sortOrder: 50, isPrice: true},
                    {code:'total_due',title:'Total Due', required:true,  sortOrder: 60, isPrice: true}
                ];
                var customerAdditionalInfomation = [];
                var eventData = {
                    customer_id:self.getOrderData('customer_id'),
                    totals:totalsCode,
                    accountInfo:customerAdditionalInfomation
                };
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
                self.totalsCode(totalsCode);
                self.customerAdditionalInfomation(customerAdditionalInfomation);

                var configs = [
                    {code:'auto_print',value:window.webposConfig["webpos/receipt/auto_print"]},
                    {code:'font_type',value:window.webposConfig["webpos/receipt/font_type"]},
                    {code:'footer_text',value:window.webposConfig["webpos/receipt/footer_text"]},
                    {code:'header_text',value:window.webposConfig["webpos/receipt/header_text"]},
                    {code:'show_cashier_name',value:window.webposConfig["webpos/receipt/show_cashier_name"]},
                    {code:'show_comment',value:window.webposConfig["webpos/receipt/show_comment"]},
                    {code:'show_barcode',value:window.webposConfig["webpos/receipt/show_barcode"]},
                    {code:'show_receipt_logo',value:window.webposConfig["webpos/receipt/show_receipt_logo"]},
                    {code:'logo',value:window.webposConfig["webpos/general/webpos_logo"]}
                ];
                self.configs(configs);
            },
            preparePrintData: function (data) {
                if(this.codeCheck.indexOf(data.code) != -1 && parseFloat(data.amount) == 0)
                    return false;
                if(data.label == 'Gift Card'){
                    data.value = '-' + data.value;
                }
                return true;
            },
            hasGiftCard: function(label){
                if(label == 'Gift Card'){
                    return ' ('+ this.getOrderData('gift_codes') +')';
                }
                return '';
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
            
            isShowBarcode: function(){
                return (this.getConfigData('show_barcode') == 1)?true:false;
            },
            isAutoPrint: function(){
                return (this.getConfigData('auto_print') == 1)?true:false;
            },
            
            getFont: function(){
                return this.getConfigData('font_type');
            },
            getFooterHtml: function(){
                return this.getConfigData('footer_text');
            },
            getHeaderHtml: function(){
                return this.getConfigData('header_text');
            },
            hasHeaderHtml: function(){
                return (this.getConfigData('header_text'))?true:false;
            },
            isShowCashierName: function(){
                return (this.getConfigData('show_cashier_name')== 1)?true:false;
            },
            isShowComment: function(){
                return (this.getConfigData('show_comment')== 1 && this.getComment())?true:false;
            },
            isShowLogo: function(){
                return (this.getConfigData('show_receipt_logo')== 1 && this.getLogoPath())?true:false;
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
            getCashierName: function(){
                return this.getOrderData('webpos_staff_name');
            },
            getCreatedTime: function(){
                var createdAt = this.getOrderData('created_at');
                var currentTime = DateHelper.stringToCurrentTime(createdAt);
                return DateHelper.getTime(currentTime);
            },
            getComment: function(){
                return this.getOrderData('customer_note');
            },
            getFullDatetime: function (dateTime) {
                return   DateHelper.getFullDatetime(dateTime);
            },
            statusHistories: function (dateTime) {
                return  this.orderData()&&this.orderData().status_histories?this.orderData().status_histories:[];
            },
            getShipping: function(){
                return this.getOrderData('shipping_description');
            },
            getDeliveryDate: function(){
                var deliveryTime = this.getOrderData('webpos_delivery_date');
                return (deliveryTime)?DateHelper.getFullDatetime(deliveryTime):'';
            },
            hasDeliveryDate: function(){
                var deliveryTime = this.getOrderData('webpos_delivery_date');
                return (deliveryTime)?true:false;
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
                    ko.utils.arrayForEach(this.getOrderData('webpos_order_payments'), function(payment) {
                        if(payment.payment_amount > 0){
                            var data = {
                                method_title:payment.method_title,
                                payment_amount:payment.payment_amount
                            };
                            if(payment.reference_number){
                                data.method_title = payment.method_title + ' (' + payment.reference_number +')';
                            }
                            payments.push(data);
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
                    ko.utils.arrayForEach(self.totalsCode(), function(data) {
                        var amount = self.getOrderData(data.code);
                        if(data.code == 'total_due' && amount == ''){
                            amount = self.getOrderData('grand_total') - self.getOrderData('total_paid');
                            amount = (amount > 0)?amount:0;
                        }
                        if(data.code ==  'subtotal' && window.webposConfig['tax/cart_display/subtotal'] == 2){
                            amount = self.getOrderData('subtotal_incl_tax');
                        }
                        if(amount || (data.required && data.required == true)){
                            var value = (data.isPrice == false)?(amount +' '+data.valueLabel):self.formatPrice(amount);
                            var total = {
                                'label':data.title,
                                'value':value,
                                'code':data.code,
                                'amount':amount
                            };

                            if (data.code === 'tax_amount') {
                                var taxes = self.getOrderData('full_tax_info');
                                ko.utils.arrayForEach(taxes, function(tax) {
                                    total.label += ' ' + tax.percent + '%';
                                });
                            }

                            totals.push(total);
                        }
                    });
                }
                return totals;
            },
            getCustomerAdditionalInfo: function(){
                var self = this;
                if(self.customerAdditionalInfomation() && self.customerAdditionalInfomation().length > 0){
                    return self.customerAdditionalInfomation();
                }
                return [];
            },
            toHtml: function(){
                var self = this;
                var html = "";
                if($("#"+self.containerId).length > 0){
                    var settings = {
                        output:"css",
                        bgColor: "#FFFFFF",
                        color: "#000000",
                        barWidth: 1,
                        barHeight: 20
                      };
                    $("#webpos_checkout_receipt_barcode").html("").barcode(self.getOrderData('increment_id'), "code128", settings);
                    html = $("#"+self.containerId).html();
                }
                return html;
            },
            printReceipt: function(){
                var self = this;
                self.initDefaultData();
                if(Helper.isOnlineCheckout() && Helper.isPdfInvoicePlusEnable() && Helper.getPdfInvoiceTemplate()){
                    var orderId = self.getOrderData('entity_id');
                    PdfInvoicePlusModel.startPrint(orderId);
                    return true;
                }
                var print_window = window.open('', 'print_offline', 'status=1,width=500,height=700');
                var html = self.toHtml();
                if(print_window){
                    self.printWindow(print_window);
                    print_window.document.open();
                    print_window.document.write(html);
                    print_window.print();
                }else{
                    AddNoti(self.__("Your browser has blocked the automatic popup, please change your browser setting or print the receipt manually"), true, "warning", self.__('Message'));
                }
            },
            getPaynlInstoreReceipt: function(){
                var self = this;
                var receipt = '';
                if(self.getOrderData('paynl_instore_receipt')){
                    receipt = self.getOrderData('paynl_instore_receipt');
                    receipt = receipt.split(" ").join("&nbsp;");
                    receipt = receipt.split("\n").join("<br />");
                }
                return receipt;
            }
        });
    }
);