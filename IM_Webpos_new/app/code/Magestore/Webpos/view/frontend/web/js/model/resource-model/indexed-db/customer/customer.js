/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/model/resource-model/indexed-db/customer/customer-indexer',
        'Magestore_Webpos/js/model/resource-model/indexed-db/abstract'
    ],
    function (indexer , Abstract) {
        "use strict";
        return Abstract.extend({
            mainTable: 'customer',
            keyPath: 'id',
            indexes: {
                id: {unique: true},
                email: {unique: true},
                name: {},
            },
            indexer: indexer,
        });
    }
);
