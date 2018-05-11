/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'model/abstract',
        'model/resource-model/magento-rest/session/cash-transaction',
        'model/resource-model/indexed-db/session/cash-transaction',
        'eventManager',

    ],
    function ($,ko, modelAbstract, onlineResource, indexedDbResource, Event) {
        "use strict";
        return modelAbstract.extend({
            push:true,
            initialize: function () {
                this._super();
                this.setResource(onlineResource(), indexedDbResource());
                //this.setResourceCollection(collection());
            },
        });
    }
);