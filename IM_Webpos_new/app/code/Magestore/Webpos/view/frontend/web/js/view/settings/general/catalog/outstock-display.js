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
            'mage/translate',
            
        ],
        function ($, ko, Component, addNotification, eventManager, localConfig, $t) {
            "use strict";

            return Component.extend({
                defaults: {
                    template: 'Magestore_Webpos/settings/general/catalog/outstock-display'
                },
                elementName: 'outstock-display',
                value: ko.observable(0),
                optionsArray: ko.observableArray([]),
                configPath: 'catalog/outstock-display',
                initialize: function () {
                    this._super();
                    /* load config data */
                    var self = this;
                    var configValue = localConfig.get(this.configPath);
                    if (configValue === null) {
                        configValue = 1;
                        localConfig.save(this.configPath, configValue);
                        var eventData = {'config': self};
                        eventManager.dispatch('webpos_config_change_after', eventData);
                    }
                    this.value(configValue);

                    this.optionsArray([{value: 0, text: $t('No')},
                        {value: 1, text: $t('Yes')}
                    ]);
                },
                saveConfig: function (data, event) {
                    var value = $('select[name="' + data.elementName + '"]').val();
                    localConfig.save(this.configPath, value);
                    /* show notification */
                    addNotification($t('Save configuration successfully!'), true, 'success', $t('Completed'));

                    var self = this;
                    /* dispatch event */
                    var eventData = {'config': self};
                    eventManager.dispatch('webpos_config_change_after', eventData);
                }
            });
        }
);