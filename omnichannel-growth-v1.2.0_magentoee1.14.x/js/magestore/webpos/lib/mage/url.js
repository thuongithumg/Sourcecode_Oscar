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
/*jshint regexdash:true eqnull:true browser:true jquery:true*/
define([], function () {
    var baseUrl = '';
    return {
        setBaseUrl: function (url) {
            baseUrl = url;
        },
        build: function(path) {
            if (path.indexOf(baseUrl) != -1) {
                return path;
            }
            return baseUrl + path;
        }
    }
});
