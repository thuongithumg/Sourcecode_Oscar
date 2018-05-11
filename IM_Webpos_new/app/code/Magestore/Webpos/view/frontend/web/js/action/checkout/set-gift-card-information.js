/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magestore_Webpos/js/model/checkout-url-builder',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magestore_Webpos/js/helper/alert',
    'Magestore_Webpos/js/model/checkout/checkout',
    'Magestore_Webpos/js/model/checkout/cart'
], function (
    $,
    urlBuilder,
    storage,
    errorProcessor,
    customer,
    fullScreenLoader,
    Alert,
    CheckoutModel,
    CartModel
) {
    'use strict';

    return function (giftCardCode) {
        var serviceUrl,
            payload,
            message = 'Gift Card ' + giftCardCode + ' was added.';

        /**
         * Checkout for guest and registered customer.
         */
        if (!customer.isLoggedIn()) {
            serviceUrl = urlBuilder.createUrl('/carts/guest-carts/:cartId/giftCards', {
                cartId: window.webposConfig.online_data['quote_id_mask']
            });
            payload = {
                cartId: window.webposConfig.online_data['quote_id_mask'],
                giftCardAccountData: {
                    'gift_cards': giftCardCode
                },
                section : window.webposConfig.online_data['quote_id_mask']            };
        } else {
            serviceUrl = urlBuilder.createUrl('/carts/mine/giftCards', {});
            payload = {
                cartId: window.webposConfig.online_data['quote_id_mask'],
                giftCardAccountData: {
                    'gift_cards': giftCardCode
                }
            };
        }
        CheckoutModel.loading(true);
        storage.post(
            serviceUrl, JSON.stringify(payload)
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

        });
    };
});
