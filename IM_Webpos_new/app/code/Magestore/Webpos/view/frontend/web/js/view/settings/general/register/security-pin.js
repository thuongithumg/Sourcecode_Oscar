/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/settings/general/abstract',
        'Magestore_Webpos/js/action/notification/add-notification',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/config/local-config',
        'Magestore_Webpos/js/helper/full-screen-loader',
        'Magestore_Webpos/js/model/staff/staff',
        'mage/translate',
        'Magestore_Webpos/js/helper/general',
        'mage/validation'

    ],
    function ($, ko, Component, addNotification, eventManager, localConfig, loader, staff, $t, Helper) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/settings/general/register/security-pin'
            },
            elementName: 'security_pin',
            value: ko.observable(),
            optionsArray: ko.observableArray([]),
            configPath: 'hardware/security/pin',
            initialize: function () {
                this._super();
                /* load config data */
                var self = this;
                var configValue = localConfig.get(this.configPath);
                if (configValue === null) {
                    configValue = '';
                    localConfig.save(this.configPath, configValue);
                    var eventData = {'config': self};
                    eventManager.dispatch('webpos_config_change_after', eventData);
                }
                this.value(configValue);
            },
            saveConfig: function (data, event) {
                var value = $('input[name="' + data.elementName + '"]').val();
                localConfig.save(this.configPath, value);
                /* show notification */
                addNotification($t('Save configuration successfully!'), true, 'success', $t('Completed'));

                var self = this;
                /* dispatch event */
                var eventData = {'config': self};
                eventManager.dispatch('webpos_config_change_after', eventData);
            },

            savePinInformation: function () {
                var isValid = this.validatePinInformation();
                if (isValid){
                    var password = $('#current-pos-password').val();
                    var securityPin = $('#security-pin').val();
                    var staffId = window.webposConfig.staffId;
                    var posId = window.webposConfig.posId;

                    loader.startLoader();
                    var data = {
                        pin: {
                            "staff_id": staffId,
                            "pin_code": securityPin,
                            "password": password,
                            "pos_id": posId
                        }
                    };
                    var deferred = staff().setPush(true).setLog(false).changePin(data);
                    deferred.done(function (response) {
                        if (response === true) {
                            Helper.alert('success', 'Message', $t('Security PIN was successfully changed.'));
                        } else {
                            Helper.alert('danger', 'Message', $t('Wrong password. Please try again!'));
                        }

                        loader.stopLoader();
                    });
                    deferred.fail(function (err) {
                        Helper.alert('danger', 'Message', $t('Network connection failed.'));
                        loader.stopLoader();
                    });
                }
            },

            validatePinInformation: function () {
                var form = '#pin-settings-form';
                if (!$('#current-pos-password').val()){
                    Helper.alert('danger', 'Message', $t('Please enter password of your POS account to confirm changes.'));
                    return false;
                }

                if ($('#security-pin').val() && !$('#security-pin').val().match(/^\d+$/)){
                    Helper.alert('danger', 'Message', $t('Please use numbers only.'));
                    return false;
                }

                if ($('#security-pin').val().length === 0){
                    Helper.alert('danger', 'Message', $t('Please fill in a 4-digit security PIN'));
                    return false;
                }

                if ($('#security-pin').val().length !== 4){
                    Helper.alert('danger', 'Message', $t('Security PIN contains only 4 numeric characters in length'));
                    return false;
                }

                return true;
            }
        });
    }
);