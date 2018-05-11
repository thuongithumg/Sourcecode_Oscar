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
        'Magestore_Webpos/js/model/checkout/cart/discountpopup',
        'Magestore_Webpos/js/helper/datetime'
    ],
    function(
        $,
        ko,
        CartModel,
        CheckoutModel,
        multiOrder,
        DiscountModel,
        DateTime
    ) {
        'use strict';
        return function () {
            var self = this;
            var holdData = CheckoutModel.getHoldOrderData();
            var oldOrder = multiOrder.currentOrderData();
            var multiOrderList = multiOrder.itemsList();
            if (typeof oldOrder.entity_id !== 'undefined' && oldOrder.entity_id) {
                holdData.entity_id = oldOrder.entity_id;
                holdData.increment_id = oldOrder.increment_id;
                holdData.created_at = oldOrder.created_at;
                holdData.order_count = oldOrder.order_count;

                ko.utils.arrayForEach(multiOrderList, function(order, index) {
                    if (order.entity_id === oldOrder.entity_id) {

                        multiOrderList[index] = holdData;

                    }
                });
                CartModel.removeAllCartItem();
                DiscountModel.cartBaseDiscountAmount(0);
                DiscountModel.cartDiscountAmount(0);
                DiscountModel.cartDiscountName('');
                DiscountModel.appliedDiscount(false);
                DiscountModel.appliedPromotion(false);
                DiscountModel.cartDiscountType('');
                DiscountModel.cartDiscountPercent(0);
                var currentTime = $.now();
                var entity_id = currentTime.toString();
                var increment_id = currentTime.toString();
                var created_at = DateTime.getBaseSqlDatetime();

                multiOrder.orderCount(multiOrder.orderCount() + 1);

                multiOrderList.push({
                    'entity_id' :entity_id,
                    'increment_id' : increment_id,
                    'created_at' : created_at,
                    'order_count' : multiOrder.orderCount()
                });
                multiOrder.itemsList(multiOrderList);
                multiOrder.currentId(entity_id);
                multiOrder.currentOrderData({
                    'entity_id' :entity_id,
                    'increment_id' : increment_id,
                    'created_at' : created_at,
                    'order_count' : multiOrder.orderCount()
                });
            } else {
                if (multiOrderList.length === 0) {
                    var holdData = CheckoutModel.getHoldOrderData();
                    if(typeof holdData.order_count === 'undefined') {
                        holdData.order_count = multiOrder.orderCount();
                    }

                    multiOrderList.push(holdData);

                    CartModel.removeAllCartItem();
                    DiscountModel.cartBaseDiscountAmount(0);
                    DiscountModel.cartDiscountAmount(0);
                    DiscountModel.cartDiscountName('');
                    DiscountModel.appliedDiscount(false);
                    DiscountModel.appliedPromotion(false);
                    DiscountModel.cartDiscountType('');
                    DiscountModel.cartDiscountPercent(0);
                    DiscountModel.cartDiscountPercent(0);
                    var currentTime = $.now();
                    var entity_id = currentTime.toString();
                    var increment_id = currentTime.toString();
                    var created_at = DateTime.getBaseSqlDatetime();

                    multiOrder.orderCount(multiOrder.orderCount() + 1);

                    multiOrderList.push({
                        'entity_id' :entity_id,
                        'increment_id' : increment_id,
                        'created_at' : created_at,
                        'order_count' : multiOrder.orderCount()
                    });
                    multiOrder.itemsList(multiOrderList);
                    multiOrder.currentId(entity_id);
                    multiOrder.currentOrderData({
                        'entity_id' :entity_id,
                        'increment_id' : increment_id,
                        'created_at' : created_at,
                        'order_count' : multiOrder.orderCount()
                    });
                } else {
                    if(typeof holdData.order_count === 'undefined') {
                        holdData.order_count = multiOrder.orderCount();
                    }
                    multiOrder.itemsList.push(holdData);
                    multiOrder.currentId(holdData.entity_id);
                    multiOrder.currentOrderData(holdData);
                }

            }
            var viewManager = require('Magestore_Webpos/js/view/layout');
            viewManager.getSingleton('view/checkout/cart/discountpopup').apply();
        }
    }
);
