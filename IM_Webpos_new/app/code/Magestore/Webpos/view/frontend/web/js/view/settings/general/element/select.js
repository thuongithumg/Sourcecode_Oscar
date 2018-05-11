/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/settings/general/abstract',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/action/notification/add-notification'
    ],
    function ($, ko, Component, Helper, AddNoti) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/settings/general/element/select',
                elementName: '',
                configPath: '',
                defaultValue: 0,
                isVisible: true,
                optionsArray: ko.observableArray([])
            },
            initialize: function () {
                this._super();
                var self = this;
                if(self.optionsArray().length == 0){
                    self.optionsArray([{value: 0, text: Helper.__('No')},
                        {value: 1, text: Helper.__('Yes')}
                    ]);
                }
                var savedConfig = Helper.getLocalConfig(self.configPath);
                if(typeof savedConfig == 'undefined' || savedConfig == null){
                    Helper.saveLocalConfig(self.configPath, self.defaultValue);
                }
                self.value = ko.pureComputed(function(){
                    return Helper.getLocalConfig(self.configPath);
                });
            },
            saveConfig: function (data, event) {
                var value = $('select[name="' + data.elementName + '"]').val();
                Helper.saveLocalConfig(this.configPath, value);
                var message = '';
                if (data.elementName == 'os_checkout.enable_online_mode' && value == 0) {
                    message = Helper.__('Change to offline mode successfully!');
                } else {
                    message = Helper.__('Save setting successfully!');
                }
                AddNoti(message, true, "success", Helper.__('Message'));
            }
        });
    }
);