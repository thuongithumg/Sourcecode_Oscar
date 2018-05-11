/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/shift/shift'
    ],
    function ($, Event, ViewManager, shift) {
        "use strict";

        return {
            execute: function() {
                Event.observer('open_shift_after',function(event,data){
                    var response = data.response[0];
                    var updateData = {};
                    updateData.entity_id = response.entity_id;
                    updateData.shift_id = response.shift_id;

                    var deferred = shift().setMode("offline").setData(updateData).update();
                    deferred.done(function (response) {
                        if(response){
                            Event.dispatch('refresh_shift_listing',response);
                            ViewManager.getSingleton('view/shift/sales-summary/sales-summary').setSyncSuccessful();
                        }
                    });
                });
            }
        }
    }
);