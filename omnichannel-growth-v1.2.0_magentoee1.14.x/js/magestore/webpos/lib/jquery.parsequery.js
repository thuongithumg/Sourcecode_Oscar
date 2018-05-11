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
/*jshint jquery:true */
/*global window:true */
define([
    "jquery"
], function($){

    $.parseQuery = function(options) {
        var config = {query: window.location.search || ""},
            params = {};

        if (typeof options === 'string') {
            options = {query: options};
        }
        $.extend(config, $.parseQuery, options);
        config.query = config.query.replace(/^\?/, '');

        $.each(config.query.split(config.separator), function(i, param) {
            var pair = param.split('='),
                key = config.decode(pair.shift(), null).toString(),
                value = config.decode(pair.length ? pair.join('=') : null, key);

            if (config.array_keys(key)) {
                params[key] = params[key] || [];
                params[key].push(value);
            } else {
                params[key] = value;
            }
        });

        return params;
    };

    $.parseQuery.decode = $.parseQuery.default_decode = function(string) {
        return decodeURIComponent((string || "").replace('+', ' '));
    };

    $.parseQuery.array_keys = function() {
        return false;
    };

    $.parseQuery.separator = "&";

});
