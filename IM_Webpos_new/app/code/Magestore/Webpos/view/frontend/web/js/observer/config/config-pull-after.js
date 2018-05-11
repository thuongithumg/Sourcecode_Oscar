
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
            /*
             * Update list view after pulled stock items
             *
             */
            execute: function () {
                eventManager.observer('config_pull_after', function (event, eventData) {
                    var items = eventData.items;
                    var webposConfig = {};
                    $.each(items, function (key, data) {
                        var path = data.path;
                        var value = data.value;
                        webposConfig[path] = value;
                    });
                    window.webposConfig = webposConfig;
                });
            }
        }
    }
);