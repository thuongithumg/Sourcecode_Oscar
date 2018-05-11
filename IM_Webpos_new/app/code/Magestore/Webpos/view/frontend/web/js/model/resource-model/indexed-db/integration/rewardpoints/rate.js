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
            mainTable: 'rewardpoint_rate',
            keyPath: 'rate_id',
            indexes: {
                rate_id: {unique: true},
            },
        });
    }
);