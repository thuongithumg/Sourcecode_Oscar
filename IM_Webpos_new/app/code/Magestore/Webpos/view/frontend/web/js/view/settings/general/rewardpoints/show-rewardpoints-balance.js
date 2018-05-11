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
                elementName: 'os_reward_points.show_customer_points_balance_on_receipt',
                configPath: 'os_reward_points/show_customer_points_balance_on_receipt'
            }
        });
    }
);