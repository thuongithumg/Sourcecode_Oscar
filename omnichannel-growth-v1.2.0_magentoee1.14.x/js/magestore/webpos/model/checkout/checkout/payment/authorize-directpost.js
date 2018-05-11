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
        'ko',
        'jquery',
        'eventManager',
        'action/notification/add-notification',
        'mage/translate'
    ],
    function (ko, $, Event, AddNoti, __) {
        "use strict";
        var DirectPost =  {
            SUCCESS_EVENT_NAME: 'authorizenet_directpost_success',
            ERROR_EVENT_NAME: 'authorizenet_directpost_error',
            orderData: ko.observable(),
            callback: false,
            initialize : function(data) {
                this.iframeId = (data && data.iframeId)?iframeId:'webpos-payment-iframe';
                this.cgiUrl = (data && data.cgiUrl)?cgiUrl:window.webposConfig['authorizenet_directpost_cgi_url'];
                this.paymentRequestSent = false;
                this.tmpForm = false;
                return this;
            },
            saveOrderSuccess : function(data, callback) {
                if (data.directpost) {
                    this.orderData(data);
                    this.orderIncrementId = data.directpost.x_invoice_num;
                    var paymentData = {};
                    for ( var key in data.directpost) {
                        if(data.directpost.hasOwnProperty(key)) {
                            paymentData[key] = data.directpost[key];
                        }
                    }
                    var preparedData = this.preparePaymentRequest(paymentData);
                    this.callback = callback;
                    this.sendPaymentRequest(preparedData);
                }
            },
            preparePaymentRequest : function(data) {
                if ($('#webpos_cc_cid').length > 0) {
                    data.x_card_code = $('#webpos_cc_cid').val();
                }
                var year = $('#webpos_cc_exp_year').val();
                if (year.length > 2) {
                    year = year.substring(2);
                }
                var month = parseInt($('#webpos_cc_exp_month').val(), 10);
                if (month < 10) {
                    month = '0' + month;
                }

                data.x_exp_date = month + '/' + year;
                data.x_card_num = $('#webpos_cc_number').val();

                return data;
            },
            sendPaymentRequest : function(preparedData) {
                this.recreateIframe();
                this.tmpForm = document.createElement('form');
                this.tmpForm.style.display = 'none';
                this.tmpForm.enctype = 'application/x-www-form-urlencoded';
                this.tmpForm.method = 'POST';
                document.body.appendChild(this.tmpForm);
                this.tmpForm.action = this.cgiUrl;
                // this.tmpForm.target = $('#'+this.iframeId).attr('name');
                this.tmpForm.target = 'authorizenet_directpost_popup';
                // this.tmpForm.setAttribute('target', $('#'+this.iframeId).attr('name'));
                this.tmpForm.setAttribute('target', 'authorizenet_directpost_popup');

                for ( var param in preparedData) {
                    this.tmpForm.appendChild(this.createHiddenElement(param, preparedData[param]));
                }

                this.paymentRequestSent = true;
                this.tmpForm.submit();
            },
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
            createHiddenElement : function(name, value) {
                var field;
                if (isIE) {
                    field = document.createElement('input');
                    field.setAttribute('type', 'hidden');
                    field.setAttribute('name', name);
                    field.setAttribute('value', value);
                } else {
                    field = document.createElement('input');
                    field.type = 'hidden';
                    field.name = name;
                    field.value = value;
                }

                return field;
            },

            recreateIframe : function() {
                if ($('#'+this.iframeId).length > 0) {
                    var nextElement = $('#'+this.iframeId).next();
                    var src = $('#'+this.iframeId).attr('src');
                    var name = $('#'+this.iframeId).attr('name');
                    $('#'+this.iframeId).remove();
                    var iframe = '<iframe id="' + this.iframeId +
                        '" allowtransparency="true" frameborder="0"  name="' + name +
                        '" style="display:none;width:100%;background-color:transparent" src="' + src + '" />';
                    Element.insert(nextElement[0], {'before':iframe});
                }
            },

            success: function(){
                var self = this;
                if(self.orderData()){
                    Event.dispatch(self.SUCCESS_EVENT_NAME,{orderData: self.orderData()});
                    if(self.callback){
                        self.callback(self.orderData());
                    }
                }
            },

            error: function(message){
                var self = this;
                Event.dispatch(self.ERROR_EVENT_NAME,'');
                AddNoti(message, true, "danger", __('Message'));
            }
        };
        window.directPostModel = DirectPost.initialize();
        return DirectPost;
    }
);