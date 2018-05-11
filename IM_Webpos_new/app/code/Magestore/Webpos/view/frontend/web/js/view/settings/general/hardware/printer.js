/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/view/settings/general/element/select',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/helper/general'
    ],
    function (Select, Event, Helper) {
        "use strict";

        return Select.extend({
            defaults: {
                elementName: 'hardware.printer',
                configPath: 'hardware/printer',
                defaultValue: 0
            }
        });
    }
);