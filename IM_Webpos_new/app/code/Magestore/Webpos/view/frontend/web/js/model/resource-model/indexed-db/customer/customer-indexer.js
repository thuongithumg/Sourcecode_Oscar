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
            mainTable: 'customer_index',
            dataTable: 'customer',
            indexes: [
                'email',
                'telephone',
                'full_name',
                'group_id',
            ],
            orderBy: 'full_name',
        });
    }
);
