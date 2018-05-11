/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/sales/order/shipment',
        'Magestore_Webpos/js/model/resource-model/indexed-db/sales/order/shipment',
        'Magestore_Webpos/js/model/collection/sales/order/shipment'
    ],
    function ($, modelAbstract, shipmentRest, shipmentIndexedDb, shipmentCollection) {
        "use strict";
        return modelAbstract.extend({
            postData: {},
            event_prefix: 'sales_order_shipment',
            
            initialize: function () {
                this._super();
                this.setResource(shipmentRest(), shipmentIndexedDb());
                this.setResourceCollection(shipmentCollection());
            },
            
            setPostData: function(data){
                this.postData = data;
                return this;
            },
            
            getPostData: function(){
                return this.postData;   
            },
        });
    }
);