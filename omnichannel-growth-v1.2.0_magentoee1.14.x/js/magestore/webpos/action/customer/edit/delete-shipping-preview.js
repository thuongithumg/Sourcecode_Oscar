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
        'helper/general',
        'ui/lib/modal/confirm',
        'model/customer/customer-factory',
        'model/customer/current-customer',
        'model/customer/customer/edit-customer',
        'action/customer/edit/show-billing-preview',
        'action/customer/edit/show-shipping-preview'
    ],
    function ($, generalHelper, Confirm, CustomerFactory, currentCustomer, editCustomer, showBillingPreview, showShippingPreview) {
        'use strict';
        return function () {
            Confirm({
                content: generalHelper.__('Do you want to delete address?'),
                actions: {
                    confirm: function () {
                        var currentData = currentCustomer.data();
                        var currentEmail = currentData.email;
                        var customerCollection;
                        if (generalHelper.isOnlineCheckout()) {
                            customerCollection = CustomerFactory.get().setMode('online').getCollection().addFieldToFilter('email', currentEmail, 'eq')
                                .load();
                        } else{
                            customerCollection = CustomerFactory.get().setMode('offline').getCollection().addFieldToFilter('email', currentEmail, 'eq')
                                .load();
                        }
                        customerCollection.done(function (data) {
                            var collectionData = data.items;
                            if (collectionData.length > 0) {
                                var addressIndex = -1;
                                var customerModelData = collectionData[0];
                                var address = customerModelData.addresses;
                                $.each(address, function (index, value) {
                                    if (value.id == editCustomer.shippingAddressId()) {
                                        addressIndex = index;
                                    }
                                });
                                address.splice(addressIndex, 1);
                                customerModelData.addresses = address;
                                var customerDeferred;
                                if (generalHelper.isOnlineCheckout()) {
                                    customerDeferred = CustomerFactory.get().setMode('online').setData(customerModelData).setPush(true).save();
                                } else {
                                    customerDeferred = CustomerFactory.get().setMode('offline').setData(customerModelData).setPush(true).save();
                                }
                                customerDeferred.done(function (data) {
                                    currentCustomer.setData(data);
                                    editCustomer.addressArray(address);
                                    if (editCustomer.billingAddressId() == editCustomer.shippingAddressId()) {
                                        editCustomer.billingAddressId(0);
                                        showBillingPreview();
                                    }
                                    editCustomer.shippingAddressId(0);
                                    showShippingPreview();
                                });
                            }
                        });
                    },
                    always: function (event) {
                        event.stopImmediatePropagation();
                    }
                }
            });
        }
    }
);
