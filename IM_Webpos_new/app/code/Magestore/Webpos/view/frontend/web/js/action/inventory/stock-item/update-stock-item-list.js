/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
        [
            'Magestore_Webpos/js/view/inventory/stock-item/grid',
            
        ],
        /* update product list after changed stock items */
        function (stockItemListView) {
            'use strict';
            return function (stockItems) {
                stockItemListView();
            }
        }
);
