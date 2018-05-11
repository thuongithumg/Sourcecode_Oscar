/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/customer/current-customer'
    ],
    function(
        $,
        checkoutModel,
        currentCustomer
    ) {
        'use strict';
        return function (addressId) {
            var customerData = currentCustomer.getData();
            var allAddress = customerData().addresses;
            if (addressId!=0) {
                var shippingAddress = {};
                $.each(allAddress, function (index, value) {
                    if (typeof (value.id) != 'undefined' && value.id == addressId) {
                        shippingAddress = allAddress[index];
                    }
                });
                checkoutModel.saveShippingAddress(shippingAddress);
            } else {
                if (customerData().id) {
                    checkoutModel.saveShippingAddress({
                        'id' : 0,
                        'firstname': customerData().firstname,
                        'lastname': customerData().lastname
                    });
                } else {
                    checkoutModel.saveShippingAddress({
                        'id' : 0,
                        'firstname': window.webposConfig['webpos/guest_checkout/first_name'],
                        'lastname': window.webposConfig['webpos/guest_checkout/last_name']
                    });
                }
            }
        }
    }
);
