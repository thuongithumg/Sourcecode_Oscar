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
            mainTable: 'product_index',
            dataTable: 'product',
            indexes: [
                'barcode_string',
                'search_string',
                'category_ids',
                'name'
            ],
        });
    }
);
