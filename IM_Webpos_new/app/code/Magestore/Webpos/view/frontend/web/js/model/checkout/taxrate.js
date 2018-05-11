/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/checkout/taxrate',
        'Magestore_Webpos/js/model/resource-model/indexed-db/checkout/taxrate',
        'Magestore_Webpos/js/model/collection/checkout/taxrate'
    ],
    function ($,ko, modelAbstract, restResource, localResource, collection) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'taxrate',
            initialize: function () {
                this._super();
                this.setResource(restResource(), localResource());
                this.setResourceCollection(collection());
            },
            getTaxRate: function(billingData,deferred){
                if(billingData && billingData.country_id){
                    this.getCollection().addFieldToFilter('tax_country_id',billingData.country_id,'eq');
                }
                if(billingData && billingData.postcode){
                    this.getCollection().addFieldToFilter(
                        [
                            ['tax_postcode', billingData.postcode, 'eq'],
                            ['tax_postcode', "*", 'eq']
                        ]
                    );
                }
                if(billingData && billingData.region_id){
                    this.getCollection().addFieldToFilter('tax_region_id',billingData.region_id,'eq');
                }
                this.getCollection().load(deferred);
            }
        });
    }
);