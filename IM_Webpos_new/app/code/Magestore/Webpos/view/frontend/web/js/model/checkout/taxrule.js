/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/checkout/taxrule',
        'Magestore_Webpos/js/model/resource-model/indexed-db/checkout/taxrule',
        'Magestore_Webpos/js/model/collection/checkout/taxrule'
    ],
    function ($,ko, modelAbstract, restResource, localResource, collection) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'taxrule',
            initialize: function () {
                this._super();
                this.setResource(restResource(), localResource());
                this.setResourceCollection(collection());
            }
        });
    }
);