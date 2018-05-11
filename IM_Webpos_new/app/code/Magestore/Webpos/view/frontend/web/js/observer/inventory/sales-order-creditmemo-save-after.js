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
                    eventManager.observer('sales_order_creditmemo_save_after', function (event, eventData) {
                        return;
                        var orderData = eventData.response;
                        var submitData = eventData.submitData;
                        var productModel = ProductFactory.get();
                        var orderModel = OrderFactory.get();
                        var updateQty = false;
                        $.each(submitData.entity.items, function(index, value){
                            if(value.additionalData && value.additionalData.search('back_to_stock')!=-1){
                                $.each(orderData.items, function(orderItemIndex, orderItemValue){
                                    if(value.orderItemId == orderItemValue.item_id)
                                        productModel.updateStock(value.qty, orderItemValue.product_id);
                                    if(value.orderItemId == orderItemValue.parent_item_id)
                                        productModel.updateStock(value.qty, orderItemValue.product_id);
                                })
                            }
                        });
                    });
                }
            }
        }
);
