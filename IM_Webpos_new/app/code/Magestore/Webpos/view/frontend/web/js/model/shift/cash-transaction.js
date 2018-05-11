/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/shift/cash-transaction',
        'Magestore_Webpos/js/model/resource-model/indexed-db/shift/cash-transaction',
        'Magestore_Webpos/js/model/event-manager',

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