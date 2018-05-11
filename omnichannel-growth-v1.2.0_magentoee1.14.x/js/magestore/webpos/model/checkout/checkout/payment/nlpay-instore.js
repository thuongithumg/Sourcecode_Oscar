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
        'mage/translate',
        'model/url-builder',
        'mage/storage',
        'lib/cookie',
    ],
    function (ko, $, Event, AddNoti, __, urlBuilder, storage, Cookies) {
        "use strict";
        var NlPayInstore =  {
            CODE: 'pay_payment_instore',
            MULTIPLE_PAYMENT_CODE: 'multipaymentforpos',
            SUCCESS_EVENT_NAME: 'nlpay_instore_success',
            ERROR_EVENT_NAME: 'nlpay_instore_error',
            orderData: ko.observable(),
            popup: ko.observable(),
            timer: ko.observable(),
            callback: false,
            initialize : function() {
                return this;
            },
            saveOrderSuccess : function(data, callback) {
                var self = this;
                self.orderData(data);
                self.callback = callback;

                var callbackInterval = function(response){
                    if (!response.items.length) return;
                    var orderRawData = response.items[0];

                    if (!orderRawData.is_waiting) {
                        if (orderRawData.state === 'canceled') { self.error(); return }
                        self.success(orderRawData);
                    }
                };

                self.popup().location.href = self.orderData().paynl_status_url;

                self.timer(setInterval(function () {
                    if (self.popup().closed) {
                        clearInterval(self.timer());
                        return;
                    }

                    var serviceUrl = urlBuilder.createUrl('webpos/order/isWaitPayNlResponse', {});
                    var sessionId = Cookies.get('WEBPOSSESSION');
                    serviceUrl = serviceUrl + '?isBrowser=1&session=' + sessionId;

                    storage.post(
                        serviceUrl, JSON.stringify({
                            order_id: self.orderData().entity_id
                        })
                    ).done(
                        function (response) {
                            callbackInterval(response);
                        }
                    ).fail(
                        function (response) {

                        }
                    ).always(
                        function (response) {
                        }
                    );


                }, 1000));
            },
            openPopup: function(url){
                var self = this;
                //open popup
                var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
                var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

                var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
                var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

                var w = 500;
                var h = 500;
                var left = ((width / 2) - (w / 2)) + dualScreenLeft;
                var top = ((height / 2) - (h / 2)) + dualScreenTop;

                var popupStatus = window.open(url, 'Paynl Instore Status', 'width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
                popupStatus.document.open();
                popupStatus.document.write($('#authorizenet-directpost-progress-html').html());
                self.popup(popupStatus);
            },
            closePopup: function () {
                var self = this;
                clearInterval(self.timer());
                self.popup().close();
            },

            success: function(orderData){
                var self = this;
                self.orderData(orderData);
                self.closePopup();
                Event.dispatch(self.SUCCESS_EVENT_NAME,{orderData: self.orderData()});
                if(self.callback){
                    self.callback(self.orderData());
                }
            },

            error: function(message){
                var self = this;
                self.closePopup();
                Event.dispatch(self.ERROR_EVENT_NAME,'');
                AddNoti(message, true, "danger", __('Cancel order'));
            }
        };
        window.NlPayInstoreModel = NlPayInstore.initialize();
        return NlPayInstore;
    }
);