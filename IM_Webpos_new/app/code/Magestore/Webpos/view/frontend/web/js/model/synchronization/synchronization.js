/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/indexed-db/synchronization/synchronization'
    ],
    function (modelAbstract, indexedDbResource) {
        "use strict";
        return modelAbstract.extend({
            initialize: function () {
                this._super();
                this.setResource(null, indexedDbResource());
            }
        });
    }
);