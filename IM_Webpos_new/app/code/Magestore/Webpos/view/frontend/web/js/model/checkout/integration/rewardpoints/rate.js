/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/integration/rewardpoints/rate',
        'Magestore_Webpos/js/model/resource-model/indexed-db/integration/rewardpoints/rate',
        'Magestore_Webpos/js/model/collection/integration/rewardpoints/rate'
    ],
    function ($, modelAbstract, restResource, indexedDbResource, collection) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'rewardpoint_rates',
            initialize: function () {
                this._super();
                this.setResource(restResource(), indexedDbResource());
                this.setResourceCollection(collection());
            },
        });
    }
);