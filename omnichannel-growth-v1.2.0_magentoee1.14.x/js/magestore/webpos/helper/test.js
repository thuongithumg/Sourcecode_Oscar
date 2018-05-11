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

define([
    'jquery',
    // 'helper/staff',
    'helper/datetime',
    // 'mage/translate',
    // 'helper/price',
    // 'helper/alert',
    // 'action/notification/add-notification',
    // 'model/event-manager',
    // 'model/config/local-config'
// ], function ($, Staff, HelperDateTime, Translate, HelperPrice, Alert, HelperDatetime, AddNotification, EventManager, LocalConfig) {
], function ($, HelperDateTime) {
    'use strict';
    return {
        __: function(string){
            // return Translate(string);
            return string;
        },
    };
});