/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/model/resource-model/indexed-db/abstract'
    ],
    function (Abstract) {
        "use strict";
        return Abstract.extend({
            mainTable: 'order',
            keyPath: 'entity_id',
            indexes: {
                entity_id: {unique: true},
                increment_id: {unique: true},
                customer_email: {},
                customer_firstname: {},
                customer_lastname: {},
                customer_fullname: {},
                fulfill_online: {},
                customer_balance_amount: {},
            },
        });
    }
);