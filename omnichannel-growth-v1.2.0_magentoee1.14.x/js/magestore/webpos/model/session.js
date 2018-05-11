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
        'jquery',
        'lib/cookie',
        'model/appConfig',
        'eventManager'
    ],
    function ($, Cookies, AppConfig, Event) {
        "use strict";

        return {
            getId: function () {
                var deferred = $.Deferred();
                deferred.resolve(Cookies.get(AppConfig.SESSION_KEY));
                return deferred;
            },
            clear: function(){
                Cookies.remove(AppConfig.SESSION_KEY);
                Event.dispatch(AppConfig.EVENT.CLEAR_SESSION_AFTER, '')
            }
        };
    }
);