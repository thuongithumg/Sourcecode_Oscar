/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

define(
    [
        'jquery',
        'model/abstract',
        'model/resource-model/magento-rest/sales/order/shipment',
        'model/resource-model/indexed-db/sales/order/shipment',
        'model/collection/sales/order/shipment'
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