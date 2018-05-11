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
        'model/resource-model/magento-rest/sales/order/payment',
        'model/resource-model/indexed-db/sales/order/payment',
        'model/collection/sales/order/payment',
        'eventManager'
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