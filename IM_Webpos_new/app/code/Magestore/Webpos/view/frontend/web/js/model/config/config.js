/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/config/config',
        'Magestore_Webpos/js/model/resource-model/indexed-db/config/config',
        'Magestore_Webpos/js/model/collection/config/config',
        'Magestore_Webpos/js/model/config/local-config',
        'ko'
    ],
    function (modelAbstract, restResource, indexedDbResource, collection, localConfig, ko) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'config',
            isDisplayOpenCashDrawer: ko.observable(localConfig.get('hardware/cashdrawer-manual')),
            initialize: function () {
                this._super();
                this.setResource(restResource(), indexedDbResource());
                this.setResourceCollection(collection());
            }
        });
    }
);