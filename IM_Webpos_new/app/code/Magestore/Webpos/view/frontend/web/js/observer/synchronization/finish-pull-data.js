/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/synchronization/process',
        'Magestore_Webpos/js/action/setting/change-to-default-mode'
    ],
    function ($, eventManager, Helper, syncProcess, changeToDefaultMode) {
        "use strict";

        return {
            execute: function() {
                eventManager.observer('finish_pull_data',function(event, eventData){
                    var id = eventData.id;
                    if($.inArray(id, syncProcess.listProcess) != -1) {
                        syncProcess.listProcess.splice($.inArray(id, syncProcess.listProcess),1);
                    }

                    /* check if all sync process finish */
                    if(syncProcess.listProcess.length == 0) {
                        /* change to default mode */
                        changeToDefaultMode();

                        // sync process is finishing
                        Helper.isSynchronization(false);
                    }
                });
            }
        }
    }
);