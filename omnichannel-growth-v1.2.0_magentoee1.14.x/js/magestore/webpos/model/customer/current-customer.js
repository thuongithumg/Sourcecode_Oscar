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

define(
    [
        'ko',
        'model/checkout/cart',
        'model/checkout/checkout',
        'helper/general'
    ],
    function (ko, CartModel, CheckoutModel, Helper) {
        'use strict';
        var data = ko.observableArray([]);
        var fullName = ko.observable('Guest');
        var customerId = ko.observable(0);
        var customerEmail = ko.observable('');
        var vatCustomer = ko.observable('');
        var object = {
            data: data,
            fullName: fullName,
            customerId: customerId,
            customerEmail: customerEmail,
            vatCustomer: vatCustomer,
            setCustomerId: function(cusId) {
                customerId(cusId);
                CartModel.addCustomer({
                    id:cusId,
                    email:data.email,
                    group_id:data.group_id,
                    taxvat:data.taxvat
                });
            },

            getCustomerId: function () {
                return CartModel.customerId();
            },

            setData: function (customerData) {
                data(customerData);
            },

            getData: function () {
                return data;
            },

            setCustomerEmail: function (email) {
                customerEmail(email)
            },

            getCustomerEmail: function () {
                return customerEmail;
            },

            setFullName: function (fName) {
                fullName(fName);
            },

            getFullName: function () {
                return fullName;
            },

            saveBillingAddress: function( data ){
                CheckoutModel.saveBillingAddress( data );
            },

            saveShippingAddress: function( data ){
                CheckoutModel.shippingAddress( data );
            },

            setVatCustomer: function (vatId) {
                vatCustomer(vatId);
            },

            getVatCustomer: function () {
                return vatCustomer;
            }
        };
        return object;
    }
);
