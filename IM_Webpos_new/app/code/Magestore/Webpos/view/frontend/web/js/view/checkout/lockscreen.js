/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/checkout-url-builder',
        'mage/storage',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/translate',
        'Magestore_Webpos/js/helper/full-screen-loader'
    ],
    function ($, ko, Component, Event, urlBuilder, storage, notification, $t, Loader) {
        "use strict";

        return Component.extend({
            statusPos: ko.observable(''),
            defaults: {
                template: 'Magestore_Webpos/checkout/lock-register/lockscreen'
            },
            is_display: ko.observable(false),
            pinBoxes: ko.observableArray(['', '', '', '']),
            lockRegisterInputId: 'lock-screen-input',
            posLockScreen: 'webpos_lockscreen',
            initialize: function () {
                var self = this;
                $('#' + this.lockRegisterInputId).focus();
                if (webposConfig['is_allow_to_lock'] == 1) {
                    self.checkPos();
                }
                this._super();
            },
            checkPos: function () {
                var self = this;
                var currentPosId = window.webposConfig.posId;
                var checkPosUrl = urlBuilder.createUrl('/webpos/checkpos', {});
                var deferred = $.Deferred();
                storage.post(
                    checkPosUrl, JSON.stringify({posId: currentPosId})
                ).done(function (response) {
                    self.statusPos(response);
                    if (response) {
                        self.lockScreen();
                    } else {
                        self.hideScreen();
                    }
                }).fail(function (response) {
                });
                return self.statusPos();
            },
            close: function () {
                this.is_display(false);
                this.resetPin();
            },
            focusPin: function () {
                $('#' + this.lockRegisterInputId).focus();
            },
            enterPin: function (element, event) {
                var pin = event.target.value;
                this.bindPin(pin);
                if (pin.length >= 4) {
                    this.submitPin(pin);
                }
            },
            lockScreen: function () {
                $('#' + this.posLockScreen).show();
                $('#lock-screen-input').val('');
                $('#lock-screen-input').focus();
            },
            hideScreen: function () {
                $('#' + this.posLockScreen).hide();
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
            hideLoadingAjaxRequest: function () {
                $('.indicator').hide();
                $('.spinner').hide();
            },
            submitPin: function (pin) {
                $('#' + this.lockRegisterInputId).attr('disabled', 'disabled');
                var self = this;
                var currentPosId = window.webposConfig.posId;
                var lockUrl = urlBuilder.createUrl('/webpos/unlockpos', {});
                var deferred = $.Deferred();
                Loader.startLoader();
                return storage.post(
                    lockUrl, JSON.stringify({pin: pin, posId: currentPosId})
                ).done(function (response) {
                    response = JSON.parse(response);
                    if (response.success == true) {
                        self.hideScreen();
                        self.resetPin();
                        Loader.stopLoader();
                        self.hideLoadingAjaxRequest();
                    } else {
                        $('#' + self.lockRegisterInputId).removeAttr('disabled');
                        $('#' + self.lockRegisterInputId).val('');
                        $('#' + self.lockRegisterInputId).focus();
                        notification(response.message, true, 'danger', 'Error');
                        self.resetPin();
                        Loader.stopLoader();
                    }
                }).fail(function (response) {
                    if (!navigator.onLine) {
                        notification($t('Network connection failed.'), true, 'danger', 'Error');
                        $('#' + self.lockRegisterInputId).removeAttr('disabled');
                        $('#' + self.lockRegisterInputId).val('');
                        $('#' + self.lockRegisterInputId).focus();
                        self.resetPin();
                        Loader.stopLoader();
                    }
                });
                return deferred;
            }
        });
    }
);