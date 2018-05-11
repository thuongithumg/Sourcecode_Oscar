/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'Magestore_Webpos/js/model/checkout-url-builder',
    'mage/storage',
    'Magento_GiftCardAccount/js/model/gift-card',
    'Magestore_Webpos/js/model/customer/customer',
    'Magestore_Webpos/js/model/checkout/checkout',
], function (ko, urlBuilder, storage, giftCardAccount, customer, CheckoutModel) {
    'use strict';

    return {
        isLoading: ko.observable(false),

        /**
         * @param {*} giftCardCode
         */
        check: function (giftCardCode,deferred) {
            var self = this,
                serviceUrl;

            this.isLoading(true);

            if (true||!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/carts/guest-carts/:cartId/checkGiftCard/:giftCardCode', {
                    cartId: window.webposConfig.online_data['quote_id_mask'],
                    giftCardCode: giftCardCode
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/checkGiftCard/:giftCardCode', {
                    giftCardCode: giftCardCode
                });

            }
            CheckoutModel.loading(true);
            storage.get(
                serviceUrl, false
            ).done(function (response) {
                giftCardAccount.isChecked(true);
                giftCardAccount.code(giftCardCode);
                giftCardAccount.amount(response);
                giftCardAccount.isValid(true);
                if(deferred)
                    deferred.resolve(response);
            }).fail(function (response) {
                giftCardAccount.isValid(false);
                CheckoutModel.loading(false);
            }).always(function () {
                CheckoutModel.loading(false);
            });
        }
    };
});
