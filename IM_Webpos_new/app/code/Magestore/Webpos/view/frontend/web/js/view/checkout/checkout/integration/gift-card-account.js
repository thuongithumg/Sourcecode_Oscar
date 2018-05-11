/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magestore_Webpos/js/view/abstract',
    'Magento_GiftCardAccount/js/model/gift-card',
    'Magento_Catalog/js/price-utils',
    'mage/validation',
    'Magestore_Webpos/js/action/checkout/set-gift-card-information',
    'Magestore_Webpos/js/action/checkout/get-gift-card-information',
    'Magestore_Webpos/js/action/checkout/remove-giftcard',
    'Magestore_Webpos/js/helper/general',
    'Magestore_Webpos/js/model/checkout/cart/totals'
], function ($, ko, Abstract, giftCardAccount, priceUtils, validation, setGiftCardAction, getGiftCardAction, removeGiftCardAction, Helper, Totals) {
    'use strict';

    return Abstract.extend({
        defaults: {
            template: 'Magestore_Webpos/checkout/checkout/integration/gift-card-account',
            giftCartCode: ''
        },
        visible: function () {
            return (Helper.isUseOnline('checkout'))?true:false
        },
        isLoading: false,
        TotalsModel: Totals(),
        TOTAL_CODE: 'm2eeGiftcard',
        giftCardAccount: giftCardAccount,

        /** @inheritdoc */
        initObservable: function () {
            this._super().observe('giftCartCode');
            // Helper.observerEvent('webpos_order_save_after', function (event, data) {
            //     if(!Helper.isUseOnline('checkout')) {
            //         this.orderSaveAfter(data);
            //     }
            // }.bind(this));
            //
            // Helper.observerEvent('webpos_place_order_before', function (event, data) {
            //     if(!Helper.isUseOnline('checkout')) {
            //         this.placeOrderBefore(data);
            //     }
            // }.bind(this));
            return this;
        },
        orderSaveAfter: function (data) {
            var array = [];
            $.each(this.TotalsModel.totals(), function (index, el) {
                if(el.code()!='m2eeGiftcard'){
                    array.push(el);
                }
            });
            this.TotalsModel.totals(array);
            giftCardAccount.isChecked(false);
            giftCardAccount.isValid(false);
            giftCardAccount.amount(false);
            giftCardAccount.code(false);
            this.giftCartCode('');
        },
        placeOrderBefore: function (data) {
            if (data && data.increment_id && giftCardAccount.isValid()) {
                var order_data = [];
                order_data.push({
                    key: "m2ee_giftcard_code",
                    value: giftCardAccount.code()
                });

                data.sync_params.integration.push({
                    'module': 'm2ee_giftcard',
                    'event_name': 'webpos_create_order_with_m2ee_giftcard_after',
                    'order_data': order_data,
                    'extension_data': {}
                });
            }
        },

        /**
         * Set gift card.
         */
        setGiftCard: function () {
            if (this.validate()) {
                if (Helper.isUseOnline('checkout')) {
                    setGiftCardAction([this.giftCartCode()]);
                } else {
                    var deferred = $.Deferred();
                    getGiftCardAction.check(this.giftCartCode(),deferred);
                    deferred.done(function (data) {
                        var visible = giftCardAccount.isValid();
                        var amount = giftCardAccount.amount();
                        this.TotalsModel.addTotal({
                            code: this.TOTAL_CODE,
                            cssClass: 'discount',
                            title: Helper.__('Gift card') + '(' + this.giftCartCode() + ')' ,
                            value: -Helper.convertPrice(amount),
                            baseValue: -amount,
                            isVisible: visible,
                            removeAble: true,
                            actions:{
                                remove: $.proxy(this.removeGiftCard, this),
                                collect: $.proxy(this.collect, this)
                            }
                        });
                        this.TotalsModel.updateTotal(this.TOTAL_CODE,{isVisible: visible});
                    }.bind(this));
                }
            }
        },

        /**
         * Set gift card.
         */
        removeGiftCard: function (giftcardCode) {
            if (this.validate()) {
                if (Helper.isUseOnline('checkout')) {
                    removeGiftCardAction.remove(giftcardCode);
                } else {
                    var array = [];
                    $.each(this.TotalsModel.totals(), function (index, el) {
                        if(el.code()!='m2eeGiftcard'){
                            array.push(el);
                        }
                    });
                    this.TotalsModel.totals(array);
                }
                giftCardAccount.isChecked(false);
                giftCardAccount.isValid(false);
                giftCardAccount.amount(false);
                giftCardAccount.code(false);
                this.giftCartCode('');
            }
        },

        /**
         * Check balance.
         */
        checkBalance: function () {
            if (this.validate()) {
                getGiftCardAction.check(this.giftCartCode());
            }
        },

        /**
         * @param {*} price
         * @return {String|*}
         */
        getAmount: function (price) {
            return priceUtils.formatPrice(price, window.webposConfig.priceFormat);
        },

        /**
         * @return {jQuery}
         */
        validate: function () {
            var form = '#giftcard-form';
            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
