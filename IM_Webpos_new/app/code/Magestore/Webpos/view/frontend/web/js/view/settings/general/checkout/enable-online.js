/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/view/settings/general/element/select',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/translate'
    ],
    function ($, Select, Event, Helper, addNotification, $t) {
        "use strict";

        return Select.extend({
            defaults: {
                elementName: 'os_checkout.enable_online_mode',
                configPath: 'os_checkout/enable_online_mode',
                isVisible: false,
                defaultValue: Helper.getOnlineConfig('use_online_default')
            },
            initialize: function () {
                this._super();
                Event.dispatch('checkout_mode_configuration_change', '');
            },
            saveConfig: function (data, event) {
                this._super();
                var value = $('select[name="' + data.elementName + '"]').val();
                Event.dispatch('checkout_mode_configuration_change', '');
            }
        });
    }
);