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
    'helper/full-screen-loader',
    'mage/translate'
], function (ko, $, Component, staff, addNotification, loader, Translate) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ui/settings/account/content'
        },
        initialize: function () {
            this._super();
        },
        saveStaffInformation: function () {
            if (this.validateStaffInfoForm()) {
                loader.startLoader();
                var data = {
                    staff: {
                        username: $('#name').val(),
                        old_password: $('#current-password').val(),
                        password: $('#password').val(),
                    }
                };
                var deferred = staff().setPush(true).setLog(false).changePassWord(data);
                deferred.done(function (response) {
                    var data = JSON.parse(response);
                    if (data.error == '1') {
                        addNotification(data.message, true, 'danger', 'Error');
                    } else {
                        $('#c-menu--push-left .admin-name').html($('#name').val());
                        addNotification(data.message, true, 'success', 'success');
                    }
                    loader.stopLoader();
                });
                deferred.fail(function (err) {
                    if (err.statusText == 'error' && err.status == 0) {
                        checkNetWork = false;
                        addNotification(Translate('Cannot connect to your server!'), true, 'danger', 'Error');
                    } else {
                        addNotification(err.message, true, 'danger', 'Error');
                    }
                    loader.stopLoader();
                });
                return true;
            }
            return false;
        },
        validateStaffInfoForm: function () {
            var form = '#staff-settings-form';
            if ($('#password').val()) {
                $('#current-password').addClass('required-entry');
            } else {
                $('#current-password').removeClass('required-entry');
            }
            return $(form).validation() && $(form).validation('isValid');
        }
    });
});