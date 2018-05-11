/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/helper/general'
    ],
    function ($, eventManager, Helper) {
        'use strict';

        return function () {
            var configPath = 'os_checkout/enable_online_mode';
            if(Helper.getLocalConfig(configPath) != 1) {
                Helper.saveLocalConfig(configPath, 1);
                eventManager.dispatch('checkout_mode_configuration_change', '');
            }
        }
    }
);