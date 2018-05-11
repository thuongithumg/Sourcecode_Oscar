/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/translate'
    ],
    function ($, ko, modelAbstract, Datetime, OrderFactory, AddNoti, __) {
        "use strict";

        var DirectPost =  {
            CheckoutModel:ko.observable(),
            orderData: ko.observable(),
            openAuthorizePopup: function(){
                var processingHtml = $('#authorizenet-directpost-progress-html').html();
                this.openPopupCenter('','authorizenet_directpost_popup', processingHtml, 500, 500);
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
                if(self.orderData()){
                    var self = this;
                    self.CheckoutModel().loading(true);
                    OrderFactory.get().setPush(true).setLog(false).setMode("online").load(self.orderData().entity_id).done(function (response) {
                        self.CheckoutModel().loading(false);
                        OrderFactory.get().setMode("offline").setData(response).setPush(false).save().done(function (response) {
                            if (response) {
                                self.CheckoutModel().placeOrder(response);
                            }
                        });
                    });
                }
            },
            error: function(message){
                var self = this;
                AddNoti(message, true, "danger", __('Message'));
            }
        };
        window.directPostModel = DirectPost;

        return modelAbstract.extend({
            CheckoutModel:ko.observable(),
            orderData:ko.observable(false),
            initialize: function () {
                this._super();
                this.tmpForm = false;
                this.iframeId = '#webpos-payment-iframe';
                this.CheckoutModel.subscribe(function(model){
                    window.directPostModel.CheckoutModel(model);
                });
            },
            processPayment: function(orderId, paymentInfo){
                var self = this;
                var paymentUrl = paymentInfo.url;
                var paymentParams = paymentInfo.params;
                $.when(
                    self.requestOnlinePayment(orderId, paymentUrl, paymentParams)
                ).done(function (response) {
                    if(response == undefined){
                        self.CheckoutModel().loading(false);
                        return;
                    }
                }).fail(function () {
                    self.CheckoutModel().loading(false);
                });
            },
            requestOnlinePayment: function(orderId, apiUrl, params){
                var self = this;
                self.CheckoutModel().loading(true);
                OrderFactory.get().setPush(true).setLog(false).setMode("online").load(orderId).done(function (response) {
                    response.created_at = Datetime.getBaseSqlDatetime();
                    response.updated_at = Datetime.getBaseSqlDatetime();
                    self.orderData(response);
                    window.directPostModel.orderData(response);
                    var paymentData = self.preparePaymentRequest(params);
                    self.sendPaymentRequest(apiUrl, paymentData);
                    self.CheckoutModel().loading(false);
                });
            },
            sendPaymentRequest : function(cgiUrl, paymentData) {
                this.tmpForm = document.createElement('form');
                this.tmpForm.style.display = 'none';
                this.tmpForm.enctype = 'application/x-www-form-urlencoded';
                this.tmpForm.method = 'POST';
                document.body.appendChild(this.tmpForm);
                this.tmpForm.action = cgiUrl;
                this.tmpForm.target = 'authorizenet_directpost_popup';
                this.tmpForm.setAttribute('target', 'authorizenet_directpost_popup');

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
                    data.x_exp_date = month + '/' + year;
                }
                if($('#webpos_cc_cid') != "undefined" && $('#webpos_cc_cid').val() != "")
                    data.x_card_code = $('#webpos_cc_cid').val();
                if($('#webpos_cc_number') != "undefined" && $('#webpos_cc_number').val() != "")
                    data.x_card_num = $('#webpos_cc_number').val();
                if($('#webpos_cc_owner') != "undefined" && $('#webpos_cc_owner').val() != "")
                    data.cc_owner = $('#webpos_cc_owner').val();
                if($('#webpos_cc_type') != "undefined" && $('#webpos_cc_type').val() != "")
                    data.cc_type = $('#webpos_cc_type').val();

                return data;
            }
        });
    }
);