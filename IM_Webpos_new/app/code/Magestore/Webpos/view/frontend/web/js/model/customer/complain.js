/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/customer/complain',
        'Magestore_Webpos/js/model/resource-model/indexed-db/customer/complain',
        'Magestore_Webpos/js/model/collection/customer/complain'
    ],
    function ($, modelAbstract, complainRest, complainIndexedDb, complainCollection) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'customer_complain',
            initialize: function () {
                this._super();
                this.setResource(complainRest(), complainIndexedDb());
                this.setResourceCollection(complainCollection());
            }
        });
    }
);