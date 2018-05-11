/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/catalog/product-factory',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function($, ko, CheckoutModel, CartModel, ViewManager, OrderFactory, TotalsFactory, Helper, ProductFactory, eventManager) {
        'use strict';
        return {
            execute: function(){
                var data = CheckoutModel.getHoldOrderData();
                OrderFactory.get().setMode('offline').setData(data).save().done(function (response) {
                    if(response){
                        var syncOnholdOrder =  Helper.getLocalConfig('os_checkout/sync_order_onhold');
                        if(syncOnholdOrder == true){
                            var Product = ProductFactory.get();
                            var childs = CartModel.getItemChildsQty();
                            if(childs && childs.length > 0){
                                ko.utils.arrayForEach(childs, function(child) {
                                    Product.updateStock(-child.qty, parseInt(child.id));
                                });
                            }
                            CheckoutModel.syncOrder(response,"checkout");
                        }
                        eventManager.dispatch('order_hold_after', response.entity_id);

                        if(Helper.isUseOnline('checkout')){
                            CartModel.removeCartOnline();
                        }else{
                            CartModel.emptyCart();
                        }
                    }
                });
            }
        }
    }
);
