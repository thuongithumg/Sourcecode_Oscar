/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/*jshint browser:true jquery:true*/
/*global alert*/
define(
    ['jquery'],
    function($) {
        return {
            serviceUrl: 'api/rest/',
            method: 'rest',
            createUrl: function(url, params) {
                var completeUrl = this.serviceUrl + url;
                return this.bindParams(completeUrl, params);
            },
            bindParams: function(url, params) {
                params.method = this.method;
                var urlParts = url.split("/");
                urlParts = urlParts.filter(Boolean);

                $.each(urlParts, function(key, part) {
                    part = part.replace(':', '');
                    if (params[part] != undefined) {
                        urlParts[key] = params[part];
                    }
                });
                return urlParts.join('/');
            }
        };
    }
);
