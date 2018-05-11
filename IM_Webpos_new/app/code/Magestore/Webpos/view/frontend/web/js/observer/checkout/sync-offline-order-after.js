/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/translate',
        
    ],
    function ($, Event, OrderFactory, AddNoti, Translate) {
        "use strict";

        return {
            execute: function() {
                Event.observer('sync_offline_order_after',function(event,data){
                    var response = data.response;
                    if(response.increment_id && response.status != 'holded'){
                        OrderFactory.get().delete(response.increment_id).done(function(){
                            OrderFactory.get().getCollection().addFieldToFilter('increment_id',response.increment_id,'eq').load().done(function(data){
                                if(data.items && data.items.length < 1){
                                    OrderFactory.create().setMode("offline").setData(response).save().done(function(){
                                        Event.dispatch('show_container_after',"orders_history");
                                    });
                                }
                            });
                        });
                    }
                });
                Event.observer('sync_offline_order_after_error',function(event,data){
                    if(data.action){
                        var orderParams = data.action.payload;
                        var extension_data = orderParams.extension_data;
                        var orderId = "";
                        $.each(extension_data, function(index,field){
                            if(field && field.key == "webpos_order_id"){
                                orderId = field.value;
                            }
                        });
                        if(data.action && data.action.error){
                            if(orderId != ""){
                                var message = Translate("Cannot sync order");
                                message += ":#"+orderId;
                                AddNoti(message, true, 'danger', Translate('Error'));
                                AddNoti(data.action.error, false, 'danger', Translate('Error'));
                                OrderFactory.get().setMode('offline').load(orderId).done(function(order){
                                    order.error = 1;
                                    OrderFactory.create().setData(order).setMode('offline').save().done(function(response){
                                        if(response){
                                            Event.dispatch('order_pull_after',response);
                                        }
                                    });
                                });
                            }
                        }
                    }
                });
            }
        }        
    }
);