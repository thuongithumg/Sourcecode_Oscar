/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'eventManager',
        'ui/components/layout',
    ],
    function (Event, ViewManager) {
        "use strict";

        return {
            execute: function() {
                Event.observer('refresh_session_listing',function(event,data){
                    ViewManager.getSingleton('ui/components/session/session/session-listing').refreshData();
                });
            }
        }
    }
);