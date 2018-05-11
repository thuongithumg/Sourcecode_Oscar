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
            off_id_auto: '',
            mainTable: 'shift',
            keyPath: 'shift_id',
            indexes: {
                shift_id: {unique: true}
            },
        });
    }
);