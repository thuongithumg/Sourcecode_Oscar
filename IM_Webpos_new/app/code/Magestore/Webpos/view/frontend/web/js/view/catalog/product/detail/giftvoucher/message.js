/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/giftvoucher/giftvoucher',
        'uiComponent',
        'mage/calendar'
    ],
    function ($,ko, giftCard, Component) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/catalog/product/detail/giftvoucher/message'
            },
            initialize: function () {
                this._super();
            },

            enablePostOffice: giftCard.enablePostOffice,

            sendToPostal: giftCard.sendToPostal,

            sendToFriend: giftCard.sendToFriend,

            postOfficeDate: giftCard.postOfficeDate,


            defaultCheckedPostal: giftCard.defaultCheckedPostal,

            defaultCheckedSender: giftCard.defaultCheckedSender,

            timezones: ko.observableArray(window.webposConfig.timezones),

            characterRemaining: ko.computed(function () {
                var customMessage = giftCard.customMessage();
                var customMessageLength = customMessage.length;
                return (window.webposConfig.messageMaxLength - customMessageLength);
            }),

            selectedTimezone: giftCard.selectedTimezone,

            isPhysicalType: ko.pureComputed(function () {
                if (giftCard.type() === 1) {
                    return true;
                } else {
                    return false;
                }
            }),
            isVirtualType: ko.pureComputed(function () {
                if (giftCard.type() === 2) {
                    return true;
                } else {
                    return false;
                }
            }),
            isCombineType: ko.pureComputed(function () {
                if (giftCard.type() === 3) {
                    return true;
                } else {
                    return false;
                }
            }),

            senderName: giftCard.senderName,
            recipientName:giftCard.recipientName,
            recipientEmail: giftCard.recipientEmail,
            customMessage: giftCard.customMessage,
            dayToSend: giftCard.dayToSend,
            isGetNotificationEmail: giftCard.isGetNotificationEmail,

            initDate: function () {
                var currentDate = new Date();
                var year = currentDate.getFullYear();
                var month = currentDate.getMonth();
                var day = currentDate.getDate();
                var self = this;
                $("#day_to_send").calendar({
                    dateFormat: "mm/dd/yy",
                    minDate: new Date(),
                    controlType: 'select',
                    showAnim: "",
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    showWeek: true,
                    timeFormat: '',
                    showTime: false,
                    showHour: false,
                    showMinute: false
                });
            },
        });
    }
);