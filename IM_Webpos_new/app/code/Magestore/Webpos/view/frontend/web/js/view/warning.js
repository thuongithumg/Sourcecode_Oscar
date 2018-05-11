/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'mage/url',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract',
        'mage/storage',
        'Magestore_Webpos/js/lib/cookie',
        'Magestore_Webpos/js/helper/full-screen-loader',
        'mage/translate',
        'Magento_Ui/js/modal/confirm',
        'mage/validation'
    ],
    function ($, ko, Component, mageUrl, restAbstract, storage, Cookies, fullScreenLoader, Translate, confirm) {
        "use strict";

        return Component.extend({
            initialize: function () {
                var self = this;
                this._super();
                $('#checkout-loader').hide();
            },

            cancel: function () {
                var apiUrl = '/webpos/staff/logout';
                var sessionId = Cookies.get('WEBPOSSESSION');
                Cookies.remove('WEBPOSSESSION');
                this.callApi(apiUrl, sessionId);
            },

            accept: function () {
                var apiUrl = '/webpos/staff/forceLogout';
                var sessionId = Cookies.get('WEBPOSSESSION');
                this.callApi(apiUrl, sessionId);
            },

            callApi: function (apiUrl, sessionId) {
                var deferred = $.Deferred();
                fullScreenLoader.startLoader();

                restAbstract().setPush(true).setLog(false).callRestApi(
                    apiUrl + '?session=' + sessionId,
                    'post',
                    {},
                    {},
                    deferred
                );

                deferred.always(function (data) {
                    window.location.reload();
                });
            }
        });
    }
);