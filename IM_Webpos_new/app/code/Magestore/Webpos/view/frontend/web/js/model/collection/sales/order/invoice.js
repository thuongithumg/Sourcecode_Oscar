/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/collection/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/sales/order/invoice',
        'Magestore_Webpos/js/model/resource-model/indexed-db/sales/order/invoice'

    ],
    function ($,ko, collectionAbstract, invoiceRest, invoiceIndexedDb) {
        "use strict";

        return collectionAbstract.extend({
            /* Set Mode For Collection*/
            mode: 'offline',
            /* Query Params*/
            queryParams: {
                filterParams: [],
                orderParams: [],
                pageSize: '',
                currentPage: '',
                paramToFilter: []
            },
            
            initialize: function () {
                this._super();
                this.setResource(invoiceRest(), invoiceIndexedDb());
            }
        });
    }
);