/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/view/settings/general/element/select',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/config/config-factory'
    ],
    function (Select, Event, Helper, configFactory) {
        "use strict";

        return Select.extend({
            defaults: {
                elementName: 'hardware.cashdrawer-manual',
                configPath: 'hardware/cashdrawer-manual',
                defaultValue: 0
            },

            saveConfig: function (data, event) {
                this._super();
                configFactory.get().isDisplayOpenCashDrawer(event.target.value);
            }
        });
    }
);