/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'jquery',
        'Magestore_Webpos/js/model/checkout/integration/giftcard/giftvoucher-template-factory',
    ], function (ko, $, GiftvoucherTemplateFactory) {
        return {

            TYPE_PHYSICAL: 1,
            TYPE_VIRTUAL: 2,
            TYPE_COMBINE: 3,

            SELECT_PRICE_TYPE_FIX: 1,
            SELECT_PRICE_TYPE_RANGE: 2,
            SELECT_PRICE_TYPE_DROPDOWN: 3,

            PRICE_TYPE_SAME_VALUE: 1,
            PRICE_TYPE_FIXED: 2,
            PRICE_TYPE_PERCENT: 3,


            senderName: ko.observable(''),
            customMessage: ko.observable(''),
            selectedTemplate: ko.observable(''),
            selectedImage: ko.observable(''),
            selectedTemplateImage: ko.observable(''),
            recipientName: ko.observable(''),
            recipientEmail: ko.observable(''),
            recipientAddress: ko.observable(''),

            baseGcValue: ko.observable(''),
            baseGcCurrency: ko.observable(''),

            expiredDate: ko.observable(''),
            imageSelectedValue: ko.observable(''),


            dayToSend: ko.observable(''),

            choosePrice: ko.observable(''),


            isGetNotificationEmail: ko.observable(true),

            templates: ko.observableArray([]),
            images: ko.observableArray([]),

            giftAmountStatic: ko.observable(''),

            giftAmountFrom: ko.observable(''),
            giftAmountTo: ko.observable(''),

            giftAmountOption: ko.observableArray([]),

            giftCardValue: ko.observable(''),
            giftCardPrice: ko.observable(''),

            amount: ko.observable(''),

            type: ko.observable(),

            selectPriceType: ko.observable(),

            priceType: ko.observable(),

            postOfficeDate: ko.observable(window.webposConfig.postOfficeDate),

            sendToPostal: ko.observable(),

            enablePostOffice: ko.observable(parseInt(window.webposConfig.enablePostOffice)),

            sendToFriend: ko.observable(),

            selectedTimezone: ko.observable(),

            defaultNotifySuccess: ko.observable(true),

            defaultCheckedPostal: ko.observable(),

            defaultCheckedSender: ko.observable(),

            isTrue: function(value) {
                if (typeof(value) == 'string') {
                    value = value.toLowerCase();
                }
                switch (value) {
                    case true:
                    case "true":
                    case 1:
                    case "1":
                    case "on":
                    case "yes":
                        return true;
                    default:
                        return false;
                }
            },

            chooseFirstImageOfTemplate: function (selectedTemplate) {
                var images = selectedTemplate.images;
                if (images) {
                    var imageArray = images.split(',');
                    var imageArrayUrl = [];
                    $.each(imageArray, function (index, value) {
                        imageArrayUrl.push(window.webposConfig.imageBaseUrl + '/' + value);
                    });
                    this.selectedImage(imageArrayUrl[0]);
                    this.selectedTemplateImage(selectedTemplate.giftcard_template_id + '-' + imageArrayUrl[0]);
                } else {
                    this.selectedImage('');
                    this.selectedTemplateImage('');
                }
            },
            
            chooseImage: function (templateId) {
                var self = this;
                this.images([]);
                var allTemplate = this.templates();
                $.each(allTemplate, function (index, value) {
                    if (value.giftcard_template_id === templateId) {
                        imagesList = value.images;
                        var array = imagesList.split(",");
                        $.each(array, function (arrayIndex, arrayValue) {
                            self.images.push(window.webposConfig.imageBaseUrl + '/' + arrayValue);
                        });

                    }
                });
            },

            resetGiftvoucherInfo: function () {
                this.images([]);
                this.templates([]);
                this.selectedImage('');
                this.selectedTemplate('');
                this.selectedTemplateImage('');
                this.senderName('');
                this.recipientName('');
                this.recipientEmail('');
                this.selectedTimezone('');
                this.customMessage('');
                this.giftCardValue('');
                this.sendToPostal(0);
                this.postOfficeDate('');
                this.dayToSend('');

            },

            selectImage: function (id, data) {
                this.selectedImage(data);
                this.selectedTemplateImage(id);
            },

            selectTemplate: function (data) {
                this.selectedTemplate(data);
                this.chooseFirstImageOfTemplate(data);
            },

            currentTemplateId: ko.observable()
        }
    }
);