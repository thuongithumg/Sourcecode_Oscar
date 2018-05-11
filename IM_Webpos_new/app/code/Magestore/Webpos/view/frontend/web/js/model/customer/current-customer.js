/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/checkout'
    ],
    function (ko, CartModel, CheckoutModel) {
        'use strict';
        var data = ko.observableArray([]);
        var fullName = ko.observable('Guest');
        var customerId = ko.observable(0);
        var customerEmail = ko.observable('');
        var object = {
            data: data,
            fullName: fullName,
            customerId: customerId,
            customerEmail: customerEmail,
            setCustomerId: function(cusId) {
                customerId(cusId);
                CartModel.addCustomer({
                    id:cusId, 
                    email:data.email,
                    group_id:data.group_id
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
            }
        };
        CartModel.CartCustomerModel(object);
        return object;
    }
);
