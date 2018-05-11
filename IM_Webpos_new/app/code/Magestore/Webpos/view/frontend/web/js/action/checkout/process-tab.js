/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/checkout/multiorder',
        'Magestore_Webpos/js/action/checkout/load-order-to-checkout'
    ],
    function(
        $,
        ko,
        ViewManager,
        CartModel,
        CheckoutModel,
        multiOrder,
        Checkout
    ) {
        'use strict';
        return function (data) {
            var self = this;

            //CartModel.emptyCart();

            if(CartModel.isOnCheckoutPage()){
                ViewManager.getSingleton('view/checkout/cart').switchToCart();
            }

            if (data.entity_id !== multiOrder.currentId() && multiOrder.currentId()) {
                var holdData = CheckoutModel.getHoldOrderData();
                var oldOrder = multiOrder.currentOrderData();

                holdData.entity_id = oldOrder.entity_id;
                holdData.increment_id = oldOrder.increment_id;
                holdData.created_at = oldOrder.created_at;
                holdData.order_count = oldOrder.order_count;

                ko.utils.arrayForEach(multiOrder.itemsList(), function(order, index) {
                    if (order.entity_id === oldOrder.entity_id) {
                        var multiOrderList = multiOrder.itemsList();
                        multiOrderList[index] = holdData;
                        multiOrder.itemsList(multiOrderList);
                    }
                });

                multiOrder.currentId(data.entity_id);
                multiOrder.currentOrderData(data);

                Checkout(data);


            }
            // else {
                // multiOrder.currentId(data.entity_id);
                // multiOrder.currentOrderData(data);
                // Checkout(data);
            // }
        }
    }
);
