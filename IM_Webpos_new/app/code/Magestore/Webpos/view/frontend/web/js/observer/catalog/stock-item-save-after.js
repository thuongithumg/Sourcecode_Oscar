/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'Magestore_Webpos/js/model/event-manager',
            'Magestore_Webpos/js/action/inventory/stock-item/update-product-list',
        ],
        function ($, eventManager, updateProductList) {
            "use strict";

            return {
                /*
                 * Update stock data to product list view after saved stock-item 
                 * 
                 */
                execute: function () {
                    eventManager.observer('stock_item_save_after', function (event, eventData) {
                        var model = eventData.model;
                        var items = [model.data];
                        updateProductList(items);
                    });
                }
            }
        }
);
