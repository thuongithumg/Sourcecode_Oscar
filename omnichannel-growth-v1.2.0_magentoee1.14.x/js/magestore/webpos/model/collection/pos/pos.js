/*
 *  Copyright © 2017 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'model/collection/abstract',
        'model/resource-model/magento-rest/pos/pos',
        'model/resource-model/indexed-db/pos/pos'

    ],
    function ($,ko, collectionAbstract, restResource, indexedDbResource) {
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
                this.setResource(restResource(), indexedDbResource());
            }
        });
    }
);