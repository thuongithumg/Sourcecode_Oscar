/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/translate'
    ],
    function ($, ko, Datetime, Helper, OrderFactory, AddNoti, __) {
        "use strict";

        var PaypalPayflowpro =  {
            CODE: 'payflowpro_integration',
            CheckoutModel:ko.observable(),
            quoteId:ko.observable(),
            openAuthorizePopup: function(){
                var processingHtml = $('#authorizenet-directpost-progress-html').html();
                this.openPopupCenter('','paypal_payflowpro_popup', processingHtml, 500, 500);
            },
            openPopupCenter: function(url, title, content, w, h) {
                var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
                var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

                var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
                var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

                var left = ((width / 2) - (w / 2)) + dualScreenLeft;
                var top = ((height / 2) - (h / 2)) + dualScreenTop;
                var authorizeWindow = window.open(url, title, 'width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

                authorizeWindow.document.open();
                authorizeWindow.document.write(content);
            },
            success: function(){
                var self = this;
                if(self.quoteId()){
                    var self = this;
                    if(Helper.isUseOnline('checkout')){
                        self.CheckoutModel().placeOrderOnline();
                    }else{
                        self.CheckoutModel().submitQuoteOnline(self.quoteId());
                    }
                }
            },
            error: function(message){
                var self = this;
                AddNoti(message, true, "danger", __('Message'));
            },
            initialize: function () {
                var self = this;
                self.tmpForm = false;
                self.iframeId = '#webpos-payment-iframe';
                Helper.observerEvent('prepare_payment_additional_data', function(event,data){
                    if(data.code == self.CODE){
                        var additional_data = data.additional_data;
                        additional_data = self.preparePaymentRequest(additional_data);
                        data.additional_data = additional_data;
                    }
                });
                return self;
            },
            processPayment: function(quoteId, paymentInfo){
                var self = this;
                window.payflowproModel.quoteId(quoteId);

                if(paymentInfo.error_messages){
                    PaypalPayflowpro.error(paymentInfo.error_messages);
                }
                if(paymentInfo.error){
                    self.CheckoutModel().loading(false);
                    return false;
                }
                var paymentUrl = paymentInfo.url;
                var paymentParams = paymentInfo.params;
                $.when(
                    self.requestOnlinePayment(quoteId, paymentUrl, paymentParams)
                ).done(function (response) {
                    if(response == undefined){
                        self.CheckoutModel().loading(false);
                        return;
                    }
                }).fail(function () {
                    self.CheckoutModel().loading(false);
                });
            },
            requestOnlinePayment: function(quoteId, apiUrl, params){
                var self = this;
                var paymentData = self.preparePaymentRequest(params);
                self.sendPaymentRequest(apiUrl, paymentData);
            },
            sendPaymentRequest : function(cgiUrl, paymentData) {
                this.tmpForm = document.createElement('form');
                this.tmpForm.style.display = 'none';
                this.tmpForm.enctype = 'application/x-www-form-urlencoded';
                this.tmpForm.method = 'POST';
                document.body.appendChild(this.tmpForm);
                this.tmpForm.action = cgiUrl;
                this.tmpForm.target = 'paypal_payflowpro_popup';
                this.tmpForm.setAttribute('target', 'paypal_payflowpro_popup');

                for ( var param in paymentData) {
                    this.tmpForm.appendChild(this.createHiddenElement(param, paymentData[param]));
                }
                this.tmpForm.submit();
            },
            createHiddenElement : function(name, value) {
                var field;
                // if (isIE) {
                //     field = document.createElement('input');
                //     field.setAttribute('type', 'hidden');
                //     field.setAttribute('name', name);
                //     field.setAttribute('value', value);
                // } else {
                field = document.createElement('input');
                field.type = 'hidden';
                field.name = name;
                field.value = value;
                // }

                return field;
            },
            preparePaymentRequest : function(data) {
                if($('#webpos_cc_exp_month') != "undefined" && $('#webpos_cc_exp_month').val() != "" &&
                    $('#webpos_cc_exp_year') != "undefined" && $('#webpos_cc_exp_year').val() != "") {
                    var year = $('#webpos_cc_exp_year').val();
                    if (year.length > 2) {
                        year = year.substring(2);
                    }
                    var month = parseInt($('#webpos_cc_exp_month').val(), 10);
                    if (month < 10) {
                        month = '0' + month;
                    }
                    data.expdate = month + year;
                }
                if($('#webpos_cc_cid') != "undefined" && $('#webpos_cc_cid').val() != "")
                    data.csc = $('#webpos_cc_cid').val();
                if($('#webpos_cc_number') != "undefined" && $('#webpos_cc_number').val() != "")
                    data.acct = $('#webpos_cc_number').val();
                if($('#webpos_cc_owner') != "undefined" && $('#webpos_cc_owner').val() != "")
                    data.cc_owner = $('#webpos_cc_owner').val();
                if($('#webpos_cc_type') != "undefined" && $('#webpos_cc_type').val() != "")
                    data.cc_type = $('#webpos_cc_type').val();
                return data;
            }
        };
        PaypalPayflowpro.initialize();
        window.payflowproModel = PaypalPayflowpro;
        return PaypalPayflowpro;
    }
);