/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'model/resource-model/indexed-db/abstract'
    ],
    function (Abstract) {
        "use strict";
        return Abstract.extend({
            mainTable: 'cash_transaction',
            keyPath: 'transaction_id',
            indexes: {
                transaction_id: {unique: true},
            },
        });
    }
);