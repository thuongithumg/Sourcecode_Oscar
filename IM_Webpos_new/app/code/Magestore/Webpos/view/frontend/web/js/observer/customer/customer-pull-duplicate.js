/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/customer/customer-factory',
        'Magestore_Webpos/js/action/checkout/select-customer-checkout'
    ],
    function ($, ViewManager, Event, CustomerFactory, selectCustomer) {
        "use strict";

        return {
            execute: function() {
                Event.observer('customer_pull_duplicate',function(event,data){
                    if (typeof (data.data) != 'undefined' && data.data) {
                        var customerInformation = data.data;
                        if(customerInformation.email){
                            CustomerFactory.get().setMode("offline").delete('notsync_' + customerInformation.email);
                            CustomerFactory.get().setMode("offline").setData(customerInformation).save().done(function (response) {
                                if(response){
                                    Event.dispatch('customer_pull_after',[]);
                                    if (ViewManager.getSingleton('view/checkout/customer/add-customer').isAddCustomer()) {
                                        selectCustomer(response);
                                        ViewManager.getSingleton('view/checkout/customer/add-customer').isAddCustomer(false);
                                    }
                                }
                            });
                        }
                    }

                });
            }
        }
    }
);