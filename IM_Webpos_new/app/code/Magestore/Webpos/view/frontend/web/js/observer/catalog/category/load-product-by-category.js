/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/view/layout',
    ],
    function ($, eventManager, ViewManager) {
        "use strict";

        return {
            /*
             * Update stock data to product list view after mass updated stock-item
             *
             */
            execute: function () {
                eventManager.observer('load_product_by_category', function (event, eventData) {
                    var catagory = ViewManager.getSingleton('view/catalog/category/cell-grid');
                    var data = eventData.catagory;
                    catagory.clickCat(data);
                    if (eventData.open_category) {
                        $('#all-categories').addClass('in');
                    }
                });
            }
        }
    }
);