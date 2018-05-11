/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/sales/order/invoice',
        'Magestore_Webpos/js/model/resource-model/indexed-db/sales/order/invoice',
        'Magestore_Webpos/js/model/collection/sales/order/invoice',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function ($, modelAbstract, invoiceRest, invoiceIndexedDb, invoiceCollection, eventmanager) {
        "use strict";
        return modelAbstract.extend({
            postData: {},
            event_prefix: 'sales_order_invoice',
            initialize: function () {
                this._super();
                this.setResource(invoiceRest(), invoiceIndexedDb());
                this.setResourceCollection(invoiceCollection());
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