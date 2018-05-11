/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'model/abstract',
        'model/resource-model/magento-rest/pos/pos',
        'model/resource-model/indexed-db/pos/pos',
        'model/collection/pos/pos'
    ],
    function ($, modelAbstract, restResource, indexedDbResource, collection) {
        "use strict";
        return modelAbstract.extend({
            initialize: function () {
                this._super();
                this.setResource(restResource(), indexedDbResource());
                this.setResourceCollection(collection());
            }
        });
    }
);