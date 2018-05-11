/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/view/settings/general/element/select',
    ],
    function (Select) {
        "use strict";

        return Select.extend({
            defaults: {
                elementName: 'os_reward_points.auto_sync_balance_when_checkout',
                configPath: 'os_reward_points/auto_sync_balance_when_checkout'
            }
        });
    }
);