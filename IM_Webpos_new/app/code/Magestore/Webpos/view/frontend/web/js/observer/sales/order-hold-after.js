/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/checkout/multiorder'
    ],
    function ($, Event, multiOrder) {
        "use strict";

        return {
            execute: function() {
                // Event.observer('order_hold_after', function(event, data){
                //     multiOrder.currentSession(data);
                //     multiOrder.items.push({
                //         number: data
                //     });
                // });
            }
        }
    }
);
