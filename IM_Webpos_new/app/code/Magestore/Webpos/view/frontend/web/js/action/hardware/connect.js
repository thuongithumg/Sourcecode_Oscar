/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'Magestore_Webpos/js/model/config/local-config',
        'mage/url',
        'Magestore_Webpos/js/lib/cookie'

    ],
    function ($, localConfig, url, Cookies) {
        'use strict';
        return function (deviceType, data) {
            var deferred = $.Deferred();
            var ajaxUrl;
            ajaxUrl = 'http://' + localConfig.get('hardware/configuration') + ':60000';
            if (window.location.protocol !== 'https:') {
                $.ajax({
                    url: ajaxUrl,
                    method: 'POST',
                    data: {
                        "device": deviceType,
                        "data": data
                    },

                    success: function (response) {
                        deferred.resolve(response);
                    },

                    error: function (response) {
                        deferred.reject(response);
                    }
                });
                return deferred;
            } else {
                Cookies.set('hubUrl', ajaxUrl, { expires: 86400 });
                Cookies.set('device', deviceType, { expires: 86400 });
                Cookies.set('data', data, { expires: 86400 });
                var connectUrl = url.build('webpos/poshub/posHubHttps');
                connectUrl = connectUrl.replace("https://","http://");
                if (window.posHubConnect) {
                    if (!window.posHubConnect.closed) {
                        window.posHubConnect.location.href= connectUrl;
                    }
                }
                window.posHubConnect = window.open (connectUrl,"mywindow","menubar=1,resizable=1,width=1,height=1");
                return deferred;
            }


        }
    }
);
