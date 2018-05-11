/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/sales/order/creditmemo',
        'Magestore_Webpos/js/model/resource-model/indexed-db/sales/order/creditmemo',
        'Magestore_Webpos/js/model/collection/sales/order/creditmemo'
    ],
    function ($, modelAbstract, creditmemoRest, creditmemoIndexedDb, creditmemoCollection) {
        "use strict";
        return modelAbstract.extend({
            postData: {},
            event_prefix: 'sales_order_creditmemo',
            
            initialize: function () {
                this._super();
                this.setResource(creditmemoRest(), creditmemoIndexedDb());
                this.setResourceCollection(creditmemoCollection());
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