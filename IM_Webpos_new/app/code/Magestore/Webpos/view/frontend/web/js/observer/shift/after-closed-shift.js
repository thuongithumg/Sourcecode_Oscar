/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/view/layout',
    ],
    function ($, Event, ViewManager) {
        "use strict";

        return {
            execute: function() {
                Event.observer('after_closed_shift',function(event,data){
                    ViewManager.getSingleton('view/shift/shift/shift-listing').refreshData();
                    ViewManager.getSingleton('view/shift/shift/shift-listing').canOpenShift(true);
                    ViewManager.getSingleton('view/shift/shift/shift-detail').afterClosedShift();
                });
            }
        }
    }
);