/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/sales/order-factory',
    ],
    function ($, ViewManager, Event, OrderFactory) {
        "use strict";

        return {
            execute: function() {
                Event.observer('sales_order_take_payment_afterSave', function(event, data){
                    if(data.response && data.response.entity_id>0){
                        var deferedSave = $.Deferred();
                        OrderFactory.get().setData(data.response).setMode('offline').save(deferedSave);
                        ViewManager.getSingleton('view/sales/order/view/payment').resetViewData(data.response);
                        ViewManager.getSingleton('view/sales/order/view/payment').display(false);
                    }
                });
            }
        }
    }
);
