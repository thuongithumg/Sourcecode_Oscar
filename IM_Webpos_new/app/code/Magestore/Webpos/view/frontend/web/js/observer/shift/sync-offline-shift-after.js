/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/shift/shift',

    ],
    function ($, Event, shift, salesSummary) {
        "use strict";

        return {
            execute: function() {
                Event.observer('sync_offline_shift_after',function(event,data){
                    var response = data.response[0];
                    var deferred = shift().setMode("offline").setData(response).update();
                    deferred.done(function (response) {
                        if(response){
                            Event.dispatch('refresh_shift_listing',response);
                        }
                    });

                });
            }
        }
    }
);