/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/translate'
    ],
    function ($, Event, CartModel, AddNoti, Translate) {
        "use strict";

        return {
            execute: function() {
                /**/
                Event.observer('webpos_place_order_before',function(event, data){
                    $('#checkout-loader').show();
                    if(data && data.items){
                        var result = CartModel.validateItemsQty();
                        if(result !== true && result.length > 0){
                            $.each(result, function(key, message){
                                AddNoti(message, true, "danger", Translate('Error'));
                            });
                            data.validate = false;
                        }else{
                            data.validate = true;
                        }
                    }
                });
            }
        }        
    }
);