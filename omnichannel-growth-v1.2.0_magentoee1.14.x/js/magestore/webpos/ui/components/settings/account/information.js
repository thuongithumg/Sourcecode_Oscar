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
    'ko',
    'jquery',
    'posComponent',
    'model/staff/staff',
    'action/notification/add-notification',
    'helper/full-screen-loader'
], function (ko, $, Component, staff, addNotification, loader) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ui/settings/account/information'
        },
        initialize: function () {
            this._super();
        },
        staff: {
            name: window.webposConfig.staffName
        }
    });
});