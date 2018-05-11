/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(['jquery',
    'Magestore_Webpos/js/lib/cookie'
], function ($, Cookies) {
    'use strict';

    return {
        method: 'rest',
        storeCode: window.webposConfig.storeCode,
        version: 'V1',
        serviceUrl: ':method/:storeCode/:version',

        /**
         * @param {String} url
         * @param {Object} params
         * @return {*}
         */
        createUrl: function (url, params) {
            var completeUrl = this.serviceUrl + url;

            return this.bindParams(completeUrl, params);
        },

        /**
         * @param {String} url
         * @param {Object} params
         * @return {*}
         */
        bindParams: function (url, params) {
            var urlParts;

            params.method = this.method;
            params.storeCode = this.storeCode;
            params.version = this.version;

            urlParts = url.split('/');
            urlParts = urlParts.filter(Boolean);

            $.each(urlParts, function (key, part) {
                part = part.replace(':', '');

                if (params[part] != undefined) { //eslint-disable-line eqeqeq
                    urlParts[key] = params[part];
                }
            });
            var sessionId = Cookies.get('WEBPOSSESSION');
            var serviceUrl = this.addParamsToUrl(urlParts.join('/'), {'session':sessionId});
            return serviceUrl;
        },

        addParamsToUrl: function(url, params){
            $.each(params, function(key, value){
                if(key){
                    if (url.indexOf("?") != -1) {
                        url = url + '&'+key+'=' + value;
                    }
                    else {
                        url = url + '?'+key+'=' + value;
                    }
                }
            });
            return url;
        },
    };
});
