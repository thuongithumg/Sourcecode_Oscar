/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
        [
            'Magestore_Webpos/js/view/catalog/product-list',
            
        ],
        /* update product list after changed stock items */
        function (productListView) {
            'use strict';
            return function () {
                var productListViewModel = productListView();
                /* refresh product list */
                productListViewModel.loadLocalConfig();
                productListViewModel.refresh = true;
                productListViewModel._prepareItems();
            }
        }
             
);
