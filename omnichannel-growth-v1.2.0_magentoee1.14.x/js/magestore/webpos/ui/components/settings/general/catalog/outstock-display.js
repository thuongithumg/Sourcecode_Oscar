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
        'ko',
        'ui/components/settings/general/abstract',
        'action/notification/add-notification',
        'eventManager',
        'model/config/local-config',
        'mage/translate'
    ],
    function ($, ko, Component, addNotification, eventManager, localConfig, Translate) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'ui/settings/general/catalog/outstock-display'
            },
            elementName: 'outstock-display',
            value: ko.observable(0),
            optionsArray: ko.observableArray([]),
            configPath: 'catalog/outstock-display',
            initialize: function () {
                this._super();
                /* load config data */
                var configValue = localConfig.get(this.configPath);
                if (configValue === null) {
                    configValue = 1;
                }
                this.value(configValue);

                this.optionsArray([{value: 0, text: Translate('No')},
                    {value: 1, text: Translate('Yes')}
                ]);
            },
            saveConfig: function (data, event) {
                var value = $('select[name="' + data.elementName + '"]').val();
                localConfig.save(this.configPath, value);
                /* show notification */
                addNotification(Translate('Save configuration successfully!'), true, 'success', Translate('Completed'));
                var self = this;
                /* dispatch event */
                var eventData = {'config': self};
                window.location.reload();
                //eventManager.dispatch('webpos_config_change_after', eventData);
            }
        });
    }
);