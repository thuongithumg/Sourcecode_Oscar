/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/customer/group',
        'Magestore_Webpos/js/model/resource-model/indexed-db/customer/group',
        'Magestore_Webpos/js/model/collection/customer/group'
    ],
    function ($, modelAbstract, customerGroupRest, customerGroupIndexedDb, customerGroupCollection) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'group',
            initialize: function () {
                this._super();
                this.setResource(customerGroupRest(), customerGroupIndexedDb());
                this.setResourceCollection(customerGroupCollection());
            },
        });
    }
);