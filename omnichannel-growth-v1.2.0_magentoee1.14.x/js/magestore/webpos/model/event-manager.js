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

define(
    [
        'jquery'
    ],
    function ($) {
        "use strict";

        return {
            dispatch: function (eventName, data, timeout) {
                $("body").eventName = '';
                if (timeout) {
                    setTimeout(function () {
                        $("body").trigger(eventName, data);
                    }, 100);
                } else $("body").trigger(eventName, data);
                return true;
            },
            observer: function (eventName, function_callback) {
                $("body").on(eventName, function_callback);
                return true;
            }
        };
    }
);