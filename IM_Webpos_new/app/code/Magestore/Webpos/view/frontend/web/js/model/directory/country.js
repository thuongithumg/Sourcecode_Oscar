/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/directory/currency',
        'Magestore_Webpos/js/model/resource-model/indexed-db/directory/country',
        'Magestore_Webpos/js/model/collection/directory/country'
    ],
    function ($, modelAbstract, customerRest, customerIndexedDb, customerCollection) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'country',
            initialize: function () {
                this._super();
                this.setResource(customerRest(), customerIndexedDb());
                this.setResourceCollection(customerCollection());
            }
        });
    }
);