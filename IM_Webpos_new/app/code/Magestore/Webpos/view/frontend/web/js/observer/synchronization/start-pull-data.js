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
        'Magestore_Webpos/js/action/setting/change-to-online-mode'
    ],
    function ($, eventManager, Helper, syncProcess, changeToOnlineMode) {
        "use strict";

        return {
            execute: function() {
                eventManager.observer('start_pull_data',function(event, eventData){
                    var id = eventData.id;
                    if($.inArray(id, syncProcess.listProcess) == -1) {
                        syncProcess.listProcess.push(id);
                    }

                    // sync process is running
                    Helper.isSynchronization(true);

                    // change mode webpos to online
                    changeToOnlineMode();
                });
            }
        }
    }
);