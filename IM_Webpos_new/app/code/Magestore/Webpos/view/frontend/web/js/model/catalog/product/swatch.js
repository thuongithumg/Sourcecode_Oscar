/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/catalog/product/swatch',
        'Magestore_Webpos/js/model/resource-model/indexed-db/catalog/product/swatch',
        'Magestore_Webpos/js/model/collection/catalog/product/swatch'
    ],
    function ($,ko, modelAbstract, restResource, indexedDbResource, collection) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'swatch',
            initialize: function () {
                this._super();
                this.setResource(restResource(), indexedDbResource());
                this.setResourceCollection(collection());
            }
        });
    }
);