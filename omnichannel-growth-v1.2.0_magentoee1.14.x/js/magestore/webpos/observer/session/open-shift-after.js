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
        'model/session/session'
    ],
    function ($, Event, ViewManager, shift) {
        "use strict";

        return {
            execute: function() {
                Event.observer('open_shift_after',function(event,data){
                    var eventData = data.data;
                    var response = eventData[0];
                    var updateData = {};
                    updateData.entity_id = response.entity_id;
                    updateData.shift_id = response.shift_id;

                    var deferred = shift().setMode("offline").setData(updateData).update();
                    deferred.done(function (response) {
                        if(response){
                            Event.dispatch('refresh_shift_listing',response);
                            ViewManager.getSingleton('ui/components/session/sales-summary/sales-summary').setSyncSuccessful();
                        }
                    });
                });
            }
        }
    }
);