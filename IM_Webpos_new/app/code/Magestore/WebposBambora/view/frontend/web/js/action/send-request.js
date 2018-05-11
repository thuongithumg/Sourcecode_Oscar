/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'mage/translate',
        'Magestore_Webpos/js/action/hardware/connect',
        'Magestore_Webpos/js/helper/alert',
        'Magestore_Webpos/js/model/config/local-config',
        'mage/url',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/helper/full-screen-loader',
        'Magestore_Webpos/js/lib/cookie'
    ],
    function ($, $t, connect, Alert, localConfig, url, CheckoutModel, fullScreenLoader, Cookies) {
        'use strict';
        return function (type, amount, currency, deferred) {
            var SALE_REQUEST = 1;
            var CHECK_REQUEST = 2;
            var CANCEL_REQUEST = 3;
            var ajaxUrl;
            var ip = localConfig.get('hardware/bambora/ip');
            var port = localConfig.get('hardware/bambora/port');
            var cookieRegistry = [];
            var cookieName = 'bamboraResponse';
            if (!deferred) {
                var deferred = $.Deferred();
            }

            ajaxUrl = 'http://' + localConfig.get('hardware/configuration') + ':60000';
            if (window.location.protocol !== 'https:') {
                window.xhrRequest = $.ajax({
                    url: ajaxUrl,
                    method: 'POST',
                    timeout: parseInt(window.webposConfig.bambora_timeout) * 1000,
                    data: {
                        "device": 'bambora',
                        'type': type,
                        "amount": Math.round(amount * 100),
                        "currency": currency,
                        "ip": localConfig.get('hardware/bambora/ip'),
                        "port": localConfig.get('hardware/bambora/port')
                    },
                    success: function (response) {
                        if (type === CANCEL_REQUEST) {
                            Alert({
                                priority: "warning",
                                title: "Message",
                                message: $t('Transaction has been cancelled.')
                            });
                        }
                        deferred.resolve(response);
                    },
                    error: function (response, xhr) {
                        if (xhr === "timeout") {
                            if (type === SALE_REQUEST) {
                                $.ajax({
                                    url: ajaxUrl,
                                    method: 'POST',
                                    timeout: 10 * 1000,
                                    data: {
                                        "device": 'bambora',
                                        'type': CANCEL_REQUEST,
                                        "amount": Math.round(amount * 100),
                                        "currency": currency,
                                        "ip": localConfig.get('hardware/bambora/ip'),
                                        "port": localConfig.get('hardware/bambora/port')
                                    },
                                    success: function (response) {
                                        Alert({
                                            priority: "warning",
                                            title: "Message",
                                            message: $t('Transaction has been cancelled.')
                                        });
                                    },
                                    error: function (response) {

                                    }
                                });
                                deferred.reject("timeout");
                            } else {
                                deferred.reject(response);
                            }
                        } else if (xhr !== 'abort') {
                            deferred.reject(response);
                        }
                    }
                });
            } else {
                Cookies.set('hubUrl', ajaxUrl, {expires: 86400});
                Cookies.set('device', 'bambora', {expires: 86400});
                Cookies.set('timeout', window.webposConfig.bambora_timeout, {expires: 86400});
                Cookies.set('type', type, {expires: 86400});
                Cookies.set('amount', amount, {expires: 86400});
                Cookies.set('currency', currency, {expires: 86400});
                Cookies.set('ip', ip, {expires: 86400});
                Cookies.set('port', port, {expires: 86400});
                if (type === CANCEL_REQUEST) {
                    return;
                }
                cookieRegistry[cookieName] = "";
                Cookies.remove('bamboraResponse');
                setInterval(function () {
                    if (cookieRegistry[cookieName]) {
                        if (Cookies.get(cookieName) !== cookieRegistry[cookieName]) {
                            cookieRegistry[cookieName] = Cookies.get(cookieName);
                            Cookies.set('hubUrl', '', {expires: 86400});
                            Cookies.set('device', '', {expires: 86400});
                            Cookies.set('timeout', '', {expires: 86400});
                            Cookies.set('type', '', {expires: 86400});
                            Cookies.set('amount', '', {expires: 86400});
                            Cookies.set('currency', '', {expires: 86400});
                            Cookies.set('ip', '', {expires: 86400});
                            Cookies.set('port', '', {expires: 86400});
                        }

                        deferred.resolve(cookieRegistry[cookieName]);
                    } else {
                        cookieRegistry[cookieName] = Cookies.get(cookieName);
                    }
                }, 100);
                var connectUrl = url.build('webposbambora/poshub/saleshttps');
                connectUrl = connectUrl.replace("https://", "http://");
                if (window.posHubConnect) {
                    if (!window.posHubConnect.closed) {
                        window.posHubConnect.location.href = connectUrl;
                    }
                }
                window.posHubConnect = window.open(connectUrl, "mywindow", "menubar=1,resizable=1,width=1,height=1");
            }
            return deferred;
        }
    }
);
