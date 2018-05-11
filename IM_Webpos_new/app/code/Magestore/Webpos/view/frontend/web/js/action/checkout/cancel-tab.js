/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/checkout/multiorder',
        'Magestore_Webpos/js/action/checkout/load-order-to-checkout'
    ],
    function(
        $,
        ko,
        CartModel,
        CheckoutModel,
        multiOrder,
        Checkout
    ) {
        'use strict';
        return function (data) {
            multiOrder.itemsList.remove(function(order) {
                return order.increment_id === data.increment_id;
            });
        }
    }
);
