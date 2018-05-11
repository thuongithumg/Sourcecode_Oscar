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
            var sections = Helper.getOnlineConfig('sections');
            var isOffline = ($.inArray('none', sections.split(',')) >= 0);
            if(isOffline) {
                Helper.saveLocalConfig(configPath, 0);
                eventManager.dispatch('checkout_mode_configuration_change', '');
            }
        }
    }
);