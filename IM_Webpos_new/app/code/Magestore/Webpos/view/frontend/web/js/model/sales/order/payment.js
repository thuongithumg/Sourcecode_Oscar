/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/sales/order/payment',
        'Magestore_Webpos/js/model/resource-model/indexed-db/sales/order/payment',
        'Magestore_Webpos/js/model/collection/sales/order/payment',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function ($, modelAbstract, paymentRest, paymentIndexedDb, paymentCollection, eventmanager) {
        "use strict";
        return modelAbstract.extend({
            postData: {},
            data: '',
            event_prefix: 'sales_order_take_payment',
            initialize: function () {
                this._super();
                this.setResource(paymentRest(), paymentIndexedDb());
                this.setResourceCollection(paymentCollection());
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