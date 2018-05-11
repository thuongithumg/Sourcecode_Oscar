/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/helper/staff',
        'Magestore_Webpos/js/action/menu/lock-register',
        'Magestore_Webpos/js/helper/full-screen-loader'
    ],
    function ($, ko, Component, HelperStaff, LockRegister, Loader) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/menu/lock-pos-popup'
            },
            canLockRegister: (webposConfig['is_allow_to_lock'] == 1 && HelperStaff.isHavePermission('Magestore_Webpos::lock_unlock_register')),
            is_display: ko.observable(false),
            pinBoxes: ko.observableArray(['', '', '', '']),
            lockRegisterInputId: 'lock-register-input',
            lockScreenInputId: 'lock-screen-input',
            posLockScreen: 'webpos_lockscreen',
            initialize: function () {
                this._super();
            },
            open: function () {
                this.is_display(true);
                $('#' + this.lockRegisterInputId).focus();
            },
            close: function () {
                this.is_display(false);
                this.resetPin();
                $('#c-mask').click();
            },
            focusPin: function () {
                $('#' + this.lockRegisterInputId).focus();
            },
            enterPin: function (element, event) {
                var pin = event.target.value;
                this.bindPin(pin);
                if (pin.length >= 4) {
                    pin = pin.substr(0, 4);
                    event.target.value = pin;
                    this.submitPin(pin);
                }
            },
            bindPin: function (pin) {
                this.resetPin();
                var pinArray = pin.split('');
                var pinBoxesValue = [];
                for (var index = 0; index < 4; index++) {
                    pinBoxesValue[index] = pinArray[index] ? '*' : '';
                }
                this.pinBoxes(pinBoxesValue);
            },
            resetPin: function () {
                this.pinBoxes(['', '', '', '']);
            },
            submitPin: function (pin) {
                $('#' + this.lockRegisterInputId).attr('disabled', 'disabled');
                var deferred = $.Deferred();
                Loader.startLoader();
                LockRegister.lockPos(pin, deferred);
                deferred.done(function (response) {
                    if (response == false) {
                        $('#' + this.lockRegisterInputId).removeAttr('disabled');
                        $('#' + this.lockScreenInputId).removeAttr('disabled');
                        $('#' + this.lockRegisterInputId).val('');
                        $('#' + this.lockRegisterInputId).focus();
                        this.resetPin();
                    }
                    if (response == true) {
                        this.close();
                        this.resetPin();
                        $('#' + this.lockScreenInputId).removeAttr('disabled');
                        $('#' + this.posLockScreen).show();
                        $('#' + this.lockScreenInputId).val('');
                        $('#' +  + this.lockScreenInputId).focus();

                    }
                    Loader.stopLoader();
                }.bind(this));
            }
        });
    }
);
