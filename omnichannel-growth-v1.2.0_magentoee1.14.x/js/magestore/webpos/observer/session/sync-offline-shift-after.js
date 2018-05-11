/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'eventManager',
        'model/session/session'
    ],
    function ($, Event, shift) {
        "use strict";

        return {
            execute: function() {
                Event.observer('sync_offline_shift_after',function(event,data){
                    var response = data.response[0];
                    var deferred = shift().setMode("offline").setData(response).update();
                    deferred.done(function (response) {
                        if(response){
                            Event.dispatch('refresh_session_listing',response);
                        }
                    });

                });
            }
        }
    }
);