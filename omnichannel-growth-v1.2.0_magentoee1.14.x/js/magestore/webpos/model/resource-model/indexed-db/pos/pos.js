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
            mainTable: 'webpos_pos',
            keyPath: 'pos_id',
            indexes: {
                pos_id: {unique: true},
            },
        });
    }
);