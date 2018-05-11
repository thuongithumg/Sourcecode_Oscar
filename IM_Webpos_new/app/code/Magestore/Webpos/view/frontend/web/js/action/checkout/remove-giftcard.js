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
    'Magestore_Webpos/js/model/checkout/cart'
], function (ko, urlBuilder, storage, giftCardAccount, customer, CheckoutModel, CartModel) {
    'use strict';

    return {
        isLoading: ko.observable(false),

        /**
         * @param {*} giftCardCode
         */
        remove: function (giftCardCode) {
            var self = this,
                serviceUrl;

            this.isLoading(true);

            if (true||!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/carts/guest-carts/:cartId/giftCards/:giftCardCode', {
                    cartId: window.webposConfig.online_data['quote_id_mask'],
                    giftCardCode: giftCardCode
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/giftCards/:giftCardCode', {
                    giftCardCode: giftCardCode
                });

            }
            CheckoutModel.loading(true);
            storage.delete(
                serviceUrl, false
            ).done(function (response) {
                CartModel.saveCartBeforeCheckoutOnline();
                CheckoutModel.loading(false);
            }).fail(function (response) {
                CheckoutModel.loading(false);
                Alert({
                    priority: "warning",
                    title: "Warning",
                    message: response.responseJSON.message
                });
            }).always(function () {
                CheckoutModel.loading(false);
            });
        }
    };
});
