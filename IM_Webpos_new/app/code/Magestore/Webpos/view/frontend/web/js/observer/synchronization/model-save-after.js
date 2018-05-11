/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function ($, eventManager) {
        "use strict";

        return {
            execute: function() {
                eventManager.observer('model_save_after',function(event, eventData){
                    var model = eventData.model;
                    /* check mode */
                    if(model.getMode() == 'online') {
                        return;
                    }
                    /* auto push is false */
                    if(!model.push) {
                        return;
                    }
                    /* has no online resource */
                    if(!model.getResourceOnline()){
                        return;
                    }
                    /* auto push to server */
                    model.prepareBeforeSave().getResourceOnline().save(model);
                });
            }
        }        
    }
);