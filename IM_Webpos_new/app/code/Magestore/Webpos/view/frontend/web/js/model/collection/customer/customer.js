/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/collection/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/customer/customer',
        'Magestore_Webpos/js/model/resource-model/indexed-db/customer/customer',
        'Magestore_Webpos/js/helper/general'
    ],
    function ($,ko, collectionAbstract, customerRest, customerIndexedDb, Helper) {
        "use strict";

        return collectionAbstract.extend({
            /* Query Params*/
            queryParams: {
                filterParams: [],
                orderParams: [],
                pageSize: '',
                currentPage: '',
                paramToFilter: [],
                paramOrFilter: []
            },              
            initialize: function () {
                this._super();
                this.setResource(customerRest(), customerIndexedDb());
                this.mode = (Helper.isUseOnline('customers'))?'online':'offline';
            }
        });
    }
);