/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/catalog/product-factory',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/checkout/multiorder',
        'Magestore_Webpos/js/action/cart/switch-order'
    ],
    function ($, CheckoutModel, OrderFactory, Helper, ProductFactory, EventManager, multiOrder, Checkout) {
        'use strict';
        return function (orderData) {
            if(orderData){
                if((orderData.status == "onhold") || (orderData.status == "holded")){
                    var syncOnholdOrder =  Helper.getLocalConfig('os_checkout/sync_order_onhold');
                    if(syncOnholdOrder == true || orderData.status == 'holded'){
                        var params = (orderData.initData)?orderData.initData:(orderData.webpos_init_data)?JSON.parse(orderData.webpos_init_data):'';
                        if (params && params.items && params.items.length > 0) {
                            $.each(params.items, function () {
                                var child_id = (this.child_id)?this.child_id:this.id;
                                var Product = ProductFactory.get();
                                Product.updateStock(this.qty, parseInt(child_id));
                            });
                        }
                    }
                    var syncOnholdOrder =  Helper.getLocalConfig('os_checkout/sync_order_onhold');
                    if(syncOnholdOrder == true){
                        CheckoutModel.unholdOrder(orderData.increment_id);
                    }

                    var deferred = OrderFactory.get().delete(orderData.entity_id);
                    deferred.done(function (response) {
                        EventManager.dispatch('order_hold_after', response.entity_id);

                    });
                }
            }
        }
    }
);
