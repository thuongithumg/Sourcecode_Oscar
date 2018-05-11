/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'uiComponent',
        'Magestore_Webpos/js/model/staff/staff',
        'Magestore_Webpos/js/action/notification/add-notification',
        'Magestore_Webpos/js/helper/full-screen-loader',
        'mage/translate',
        "mage/validation",
    ],
    function ($, Component, staff, addNotification, loader, Translate) {
        "use strict";

        return Component.extend({
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
    }
);