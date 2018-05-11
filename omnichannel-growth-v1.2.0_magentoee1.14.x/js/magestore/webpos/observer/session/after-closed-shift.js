/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'eventManager',
        'ui/components/layout',
    ],
    function ($, Event, ViewManager) {
        "use strict";

        return {
            execute: function() {
                Event.observer('after_closed_shift',function(event,data){
                    ViewManager.getSingleton('ui/components/session/session/session-listing').refreshData();
                    ViewManager.getSingleton('ui/components/session/session/session-listing').canOpenShift(true);
                    ViewManager.getSingleton('ui/components/session/session/session-detail').afterClosedShift();
                });
            }
        }
    }
);