/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'model/collection/abstract',
        'model/resource-model/magento-rest/session/session',
        'model/resource-model/indexed-db/session/session'
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