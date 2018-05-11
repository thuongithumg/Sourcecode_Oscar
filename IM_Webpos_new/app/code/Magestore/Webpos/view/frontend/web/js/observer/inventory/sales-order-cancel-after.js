/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'Magestore_Webpos/js/model/event-manager',
            'Magestore_Webpos/js/model/catalog/product-factory',
            'Magestore_Webpos/js/model/sales/order-factory',
        ],
        function ($, eventManager, ProductFactory, OrderFactory) {
            "use strict";

            return {
                /*
                 * Update stock data after canceled order
                 * 
                 */
                execute: function () {
                    eventManager.observer('sales_order_cancel_after', function (event, eventData) {
                        var orderData = eventData.response;
                        var productModel = ProductFactory.get();
                        var orderModel = OrderFactory.get();
                        var updateQty = false;
                        var isCanceled = true;
                        for (var i in orderData.items) {
                            var item = orderData.items[i];
                            /* calculate return qty */
                            var returnQty = item.qty_ordered - item.qty_invoiced - item.qty_shipped - item.qty_canceled;
                            returnQty = Math.max(returnQty, 0);
                            if (returnQty > 0) {
                                updateQty = true;
                                /* return qty to stock */
                                productModel.updateStock(returnQty, item.product_id);
                                /* update qty_canceled in order item */
                                orderData.items[i].qty_canceled += returnQty;
                            }
                            if((orderData.items[i].qty_canceled+orderData.items[i].qty_refunded) < 
                                orderData.items[i].qty_ordered){
                                isCanceled = false;
                            }
                        }
                        if(isCanceled){
                            orderData.status = 'canceled';
                            orderData.state = 'canceled';
                        }
                        if(updateQty) {
                            /* save order data to local */
                            orderModel.setData(orderData).save();
                        }
                    });
                }
            }
        }
);
