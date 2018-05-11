/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/view/layout',
    ],
    function (Event, ViewManager) {
        "use strict";

        return {
            execute: function() {
                Event.observer('refresh_shift_listing',function(event,data){
                    ViewManager.getSingleton('view/shift/shift/shift-listing').refreshData();
                });
            }
        }
    }
);