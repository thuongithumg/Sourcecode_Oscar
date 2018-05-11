/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/checkout/taxclass',
        'Magestore_Webpos/js/model/resource-model/indexed-db/checkout/taxclass',
        'Magestore_Webpos/js/model/collection/checkout/taxclass'
    ],
    function ($,ko, modelAbstract, restResource, localResource, collection) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'taxclass',
            initialize: function () {
                this._super();
                this.setResource(restResource(), localResource());
                this.setResourceCollection(collection());
            },
            getProductTaxClasses: function(deferred){
                this.getCollection().addFieldToFilter('class_type','PRODUCT','eq');
                this.getCollection().load(deferred);
            },
            getCustomerTaxClasses: function(deferred){
                this.getCollection().addFieldToFilter('class_type','CUSTOMER','eq');
                this.getCollection().load(deferred);
            }
        });
    }
);