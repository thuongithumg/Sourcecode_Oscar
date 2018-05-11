/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer store credit(balance) application
 */

define([
    'jquery',
    'ko',
    'mage/storage',
    'mage/translate',
    'Magestore_Webpos/js/model/resource-model/magento-rest/abstract'
], function (
    $,
    ko,
    storage,
    $t,
    restAbstract
) {
    'use strict';

    return function () {
        var message = $t('Your store credit was successfully applied');

        var apiUrl = '/carts/mine/balance/apply';
        restAbstract().setPush(true).setLog(false).callRestApi(
            apiUrl,
            'post',
            {},
            {
                'staff': staff
            },
            deferred
        );
        // return storage.post(
        //     urlBuilder.createUrl('/carts/mine/balance/apply', {})
        // ).done(function (response) {
        //     var deferred;
        //     if (response) {
        //     }
        // }).fail(function (response) {
        //
        //
        // });
    };
});
