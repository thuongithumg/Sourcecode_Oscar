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
            off_id_auto: '',
            mainTable: 'session',
            keyPath: 'shift_id',
            indexes: {
                shift_id: {unique: true}
            },
        });
    }
);