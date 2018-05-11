/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/model/resource-model/indexed-db/indexer'
    ],
    function (Indexer) {
        "use strict";
        return Indexer.extend({
            mainTable: 'stock_item_index',
            dataTable: 'stock_item',
            keyPath: 'item_id',
            indexes: [
                'product_id',
                'name',
                'sku',
            ],
        });
    }
);
