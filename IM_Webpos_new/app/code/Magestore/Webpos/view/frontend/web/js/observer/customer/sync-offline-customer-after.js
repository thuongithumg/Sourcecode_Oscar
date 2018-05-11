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
                Event.observer('customer_afterSave',function(event,data){
                    var response = data.response;
                    if(response.email){
                        CustomerFactory.get().setMode("offline").delete('notsync_' + response.email);
                        CustomerFactory.get().setMode("offline").setPush(false).setData(response).save().done(function (response) {
                            if(response){
                                Event.dispatch('customer_pull_after',[]);
                                if (ViewManager.getSingleton('view/checkout/customer/add-customer').isAddCustomer()) {
                                    selectCustomer(response);
                                    var addressData = response.addresses;
                                    var isSetBilling = false;
                                    var isSetShipping = false;
                                    $.each(addressData, function (index, value) {
                                        if (value.default_billing) {
                                            ViewManager.getSingleton('view/checkout/customer/edit-customer').billingAddressId(value.id);
                                            ViewManager.getSingleton('view/checkout/customer/edit-customer').setBillingPreviewData(value);
                                            ViewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewBilling(true);
                                            isSetBilling = true;
                                        }
                                        if (value.default_shipping) {
                                            ViewManager.getSingleton('view/checkout/customer/edit-customer').shippingAddressId(value.id);
                                            ViewManager.getSingleton('view/checkout/customer/edit-customer').setShippingPreviewData(value);
                                            ViewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewShipping(true);
                                            isSetShipping = true;
                                        }
                                    });
                                    if (!isSetBilling) {
                                        ViewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewBilling(false);
                                    }
                                    if (!isSetShipping) {
                                        ViewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewShipping(false);
                                    }
                                    ViewManager.getSingleton('view/checkout/customer/add-customer').isAddCustomer(false);
                                } else {
                                    ViewManager.getSingleton('view/checkout/customer/edit-customer').addressArray(response.addresses);
                                }
                            }
                        });
                    }
                });
            }
        }
    }
);