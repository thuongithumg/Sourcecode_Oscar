/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'jquery',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/translate',
        'Magestore_Webpos/js/model/sales/order-factory'
    ],
    function (ko, $, Event, AddNoti, __, OrderFactory) {
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
                    if (orderRawData.state === 'canceled') { self.error(); return }
                    if (orderRawData.state !== self.orderData().state) self.success(orderRawData);
                };

                self.popup().location.href = self.orderData().paynl_status_url;

                self.timer(setInterval(function () {
                    if (self.popup().closed) {
                        clearInterval(self.timer());
                        return;
                    }

                    var request = $.Deferred();
                    var collection = OrderFactory.get().setMode('online').getCollection();
                    collection.addFieldToFilter('increment_id', self.orderData().increment_id, 'eq');
                    collection.setPageSize(1);
                    collection.setCurPage(1);
                    collection.load(request);
                    request.done(callbackInterval);
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