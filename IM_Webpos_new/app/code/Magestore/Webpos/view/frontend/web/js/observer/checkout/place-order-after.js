/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/catalog/product-factory',
        'Magestore_Webpos/js/helper/order',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/translate',
        
    ],
    function ($, ko, Event, CartModel, ProductFactory, HelperOrder, AddNoti, Translate) {
        "use strict";

        return {
            execute: function() {
                Event.observer('webpos_place_order_after',function(event,data){
                    $('#checkout-loader').hide();
                    if(data && data.increment_id){
                        var message = Translate('Order has been created successfully ') + "#"+data.increment_id;
                        AddNoti(message, true, "success", Translate('Message'));
                        HelperOrder.setLastId(data.increment_id);
                    }
                });
                Event.observer('webpos_order_save_after',function(event,data){
                    if(data && data.increment_id){
                        var Product = ProductFactory.get();
                        var childs = CartModel.getItemChildsQty();
                        if(childs && childs.length > 0){
                            ko.utils.arrayForEach(childs, function(child) {
                                Product.updateStock(-child.qty, parseInt(child.id));
                            });
                        }
                    }
                });
            }
        }        
    }
);