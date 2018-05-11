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
        'ko',
        "helper/general",
        'model/customer/current-customer',
        'model/customer/customer/edit-customer',
        'model/customer/customer/new-address',
        'model/customer/customer-factory',
        'action/customer/edit/show-billing-preview',
        'action/customer/edit/show-shipping-preview'
    ],
    function ($, ko, helper, currentCustomer, editCustomer, newAddress, CustomerFactory, showBillingPreview, showShippingPreview) {
        'use strict';
        return function () {
            var customerDeferred;
            var newAddressData =  newAddress.getAddressData();
            var currentCustomerData = currentCustomer.data();
            if (newAddress.validateAddressForm()) {
                if (editCustomer.currentEditAddressId()) {
                    var addressIndex = -1;
                    var currentEditAddressId = editCustomer.currentEditAddressId();
                    var allAddress = editCustomer.addressArray();
                    $.each(allAddress, function (index, value) {
                        if (value.id == currentEditAddressId) {
                            addressIndex = index;
                            var addressData = newAddress.getAddressData();
                            addressData.id = value.id;
                            allAddress[index] = addressData;
                        }
                    });
                    currentCustomerData.addresses = allAddress;
                } else {
                    var currentAddress = currentCustomerData.addresses;
                    if (currentAddress instanceof Array) {
                        currentAddress.push(newAddressData);
                    } else {
                        currentAddress = [];
                        currentAddress.push(newAddressData);
                    }
                    editCustomer.addressArray(currentAddress);
                    currentCustomerData.addresses = currentAddress;
                }

                // if (helper.isOnlineCheckout()) {;
                //     customerDeferred = CustomerFactory.get().setMode('online').setData(currentCustomerData).setPush(true).save();
                // } else {
                    customerDeferred = CustomerFactory.get().setMode('offline').setData(currentCustomerData).setPush(false).save();
                // }

                customerDeferred.done(function (data) {
                    currentCustomer.setData(data);
                    editCustomer.addressArray(currentCustomerData.addresses);
                    showShippingPreview();
                    showBillingPreview();
                });

                newAddress.hideAddressForm();
            }
        }
    }
);
