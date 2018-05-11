/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/*global define*/
define(
    [
        'jquery',
        'model/checkout/checkout',
        'model/customer/current-customer'
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
                var billingAddress = {};
                $.each(allAddress, function (index, value) {
                    if (typeof (value.id) != 'undefined' && value.id == addressId) {
                        billingAddress = allAddress[index];
                    }
                });
                if(checkoutModel)
                    checkoutModel.saveBillingAddress(billingAddress);
            } else {
                if (customerData().id) {
                    if(checkoutModel)
                        checkoutModel.saveBillingAddress({
                            'id' : 0,
                            'firstname': customerData().firstname,
                            'lastname': customerData().lastname
                    });
                } else {
                    if(checkoutModel)
                        checkoutModel.saveBillingAddress({
                            'id' : 0,
                            'firstname': window.webposConfig['webpos/guest_checkout/first_name'],
                            'lastname': window.webposConfig['webpos/guest_checkout/last_name']
                    });
                }

            }
        }
    }
);
