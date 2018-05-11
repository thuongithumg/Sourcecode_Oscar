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
        return function (line1, line2) {
            var ajaxUrl;
            if (localConfig.get('hardware/pole') === '1') {
                ajaxUrl = 'http://' + localConfig.get('hardware/configuration') + ':60000';
                if (window.location.protocol !== 'https:') {
                    $.ajax({
                        url: ajaxUrl,
                        method: 'POST',
                        data: {
                            "device": 'poledisplay',
                            "line1": line1,
                            "line2": line2
                        }
                    });
                } else {
                    Cookies.set('hubUrl', ajaxUrl, { expires: 86400 });
                    Cookies.set('device', 'poledisplay', { expires: 86400 });
                    Cookies.set('line1', line1, { expires: 86400 });
                    Cookies.set('line2', line2, { expires: 86400 });
                    var connectUrl = url.build('webpos/poshub/poleHttps');
                    connectUrl = connectUrl.replace("https://","http://");
                    if (window.posHubConnect) {
                        if (!window.posHubConnect.closed) {
                            window.posHubConnect.location.href= connectUrl;
                        }
                    }
                    window.posHubConnect = window.open (connectUrl,"mywindow","menubar=1,resizable=1,width=1,height=1");
                }

            }
        }
    }
);
