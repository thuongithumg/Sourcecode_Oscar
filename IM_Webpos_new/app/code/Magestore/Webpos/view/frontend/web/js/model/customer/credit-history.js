/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/customer/credit-history',
        'Magestore_Webpos/js/model/resource-model/indexed-db/customer/credit-history',
        'Magestore_Webpos/js/model/collection/customer/credit-history'
    ],
    function ($, modelAbstract, creditRest, creditIndexedDb, creditCollection) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'customer_complain',
            initialize: function () {
                this._super();
                this.setResource(creditRest(), creditIndexedDb());
                this.setResourceCollection(creditCollection());
            }
        });
    }
);