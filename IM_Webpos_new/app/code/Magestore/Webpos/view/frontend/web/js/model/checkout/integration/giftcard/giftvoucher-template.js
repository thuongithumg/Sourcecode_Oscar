/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/integration/giftcard/giftvoucher-template',
        'Magestore_Webpos/js/model/resource-model/indexed-db/integration/giftcard/giftvoucher-template',
        'Magestore_Webpos/js/model/collection/integration/giftcard/giftvoucher-template'
    ],
    function ($, ko, modelAbstract, giftTemplateRest, giftTemplateIndexedDb, giftTemplateCollection) {
        "use strict";

        return modelAbstract.extend({
            event_prefix: 'giftvoucher_template',
            sync_id:'giftvoucher_template',
            initialize: function () {
                this._super();
                this.setResource(giftTemplateRest(), giftTemplateIndexedDb());
                this.setResourceCollection(giftTemplateCollection());
            }
        });
    }
);