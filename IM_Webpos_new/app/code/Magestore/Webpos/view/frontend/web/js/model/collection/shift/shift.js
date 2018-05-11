/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/collection/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/shift/shift',
        'Magestore_Webpos/js/model/resource-model/indexed-db/shift/shift'
    ],
    function ($,ko, collectionAbstract, restResource, indexeddbResource) {
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
                this.setResource(restResource(), indexeddbResource());
            },
        });
    }
);