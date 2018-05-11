/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/model/sales/order-factory',
    ],
    function ($, ko, modelAbstract, Datetime, OrderFactory) {
        "use strict";
        return modelAbstract.extend({
            CheckoutModel:ko.observable(),
            processPayment: function (orderId, paymentInfo) {
                var self = this;
                if(orderId) {
                    OrderFactory.get().setPush(true).setLog(false).setMode("online").load(orderId).done(function (response) {
                        response.created_at = Datetime.getBaseSqlDatetime();
                        response.updated_at = Datetime.getBaseSqlDatetime();
                        OrderFactory.get().setMode("offline").setData(response).setPush(false).save().done(function (response) {
                            if (response) {
                                self.CheckoutModel().placeOrder(response);
                            }
                            self.CheckoutModel().loading(false);
                        });
                    });
                }
            }
        });
    }
);