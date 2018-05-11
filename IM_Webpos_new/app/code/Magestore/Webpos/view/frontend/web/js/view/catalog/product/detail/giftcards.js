/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/catalog/product/detail-popup',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function ($,ko, detailPopup, priceHelper, EventManager) {
        "use strict";
        return detailPopup.extend({
            defaults: {
                template: 'Magestore_Webpos/catalog/product/detail/giftcards'
            },
            amount: ko.observable(),
            customAmount: ko.observable(),
            senderName: ko.observable(),
            senderEmail: ko.observable(),
            recipientName: ko.observable(),
            recipientEmail: ko.observable(),
            giftcard_message: '',
            initialize: function () {
                this._super();
                EventManager.observer('webpos_cart_item_add_before', function(event, data){
                    if(data.product_type == "giftcard"){
                        data.custom_giftcard_amount = this.customAmount();
                        data.giftcard_amount = this.amount();
                        data.giftcard_sender_name = this.senderName();
                        data.giftcard_recipient_name = this.recipientName();
                        data.giftcard_sender_email = this.senderEmail();
                        data.giftcard_recipient_email = this.recipientEmail();
                        data.giftcard_message = 'message';
                    }
                }.bind(this));
            },
            updateMessage: function () {
                var message = $('#giftcard-message').val();
                this.giftcard_message = message;
            },
            showCustomAmount: function () {
                if($('#giftcard_amount').val()=='custom'){
                    $('#custom_giftcard_amount_field').show();
                }else{
                    $('#custom_giftcard_amount_field').hide();
                }
            },
            selectAmount: function () {
                return [];
            },
            isEmailAvailable: function () {
                return this.itemData().giftcard_type!=1;
            },
            isMessageAvailable: function () {
                return true;
            },
            getDefaultSenderName: function () {
                return '';
            },
            getDefaultSenderEmail: function () {
                return '';
            },
            getDefaultValue: function (key) {
                return key;
            },
            isOpenAmountAvailable: function () {
                return (this.itemData().giftcard_amounts.length)?true:false;
            },
            selectAmounts: function () {
                return this.itemData().giftcard_amounts;
            },
            getGiftcardAmountMin: function(){
                return priceHelper.convertAndFormat(this.itemData().open_amount_min);
            },
            getGiftcardAmountMax: function(){
                return priceHelper.convertAndFormat(this.itemData().open_amount_max);
            },
            convertAndFormat: function(amount){
                return priceHelper.convertAndFormat(amount);
            }

        });
    }
);