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
        'jquery',
        'ko',
        'posComponent',
        'action/customer/edit/save-new-address',
        'model/customer/customer/new-address',
        'model/customer/customer/edit-customer',
        'model/customer/customer-factory',
        'model/customer/current-customer',
        'helper/general',
        'mage/validation'
    ],
    function (
        $,
        ko,
        Component,
        saveNewAddress,
        newAddressModel,
        editCustomerModel,
        CustomerFactory,
        currentCustomer,
        Helper
    ) {
        "use strict";
        return Component.extend({
            countryArray: ko.observableArray(window.webposConfig.country),
            /* Selector for control UI*/
            editCustomerForm: $('#form-edit-customer'),
            addAddressForm: $('#form-customer-add-address-checkout'),
            /* End selector for control UI*/
            firstName: ko.pureComputed(function () {
                return newAddressModel.firstName();
            }),
            lastName: ko.pureComputed(function () {
                return newAddressModel.lastName();
            }),
            company: ko.pureComputed(function () {
                return newAddressModel.company();
            }),
            phone: ko.pureComputed(function () {
                return newAddressModel.phone();
            }),
            street1: ko.pureComputed(function () {
                return newAddressModel.street1();
            }),
            street2: ko.pureComputed(function () {
                return newAddressModel.street2();
            }),
            country: ko.pureComputed(function () {
                return newAddressModel.country();
            }),
            region: ko.pureComputed(function () {
                return newAddressModel.region();
            }),
            region_id: ko.pureComputed(function () {
                return newAddressModel.region_id();
            }),
            city: ko.pureComputed(function () {
                return newAddressModel.city();
            }),
            zipcode: ko.pureComputed(function () {
                return newAddressModel.zipcode();
            }),
            vat_id: ko.pureComputed(function () {
                return newAddressModel.vat_id();
            }),
            addressTitle: ko.pureComputed(function () {
                return newAddressModel.addressTitle();
            }),
            currentEditAddressType: ko.pureComputed(function () {
                return newAddressModel.currentEditAddressType();
            }),
            /* Add address template*/
            defaults: {
                template: 'ui/checkout/customer/add-address'
            },

            /* Cancel Address*/
            cancelAddress: function () {
                this.hideAddressForm();
            },

            /* Auto run when initialize*/
            initialize: function () {
                this._super();
            },

            /* Hide Address */
            hideAddressForm: function () {
                var editCustomerForm = $('#form-edit-customer');
                var addAddressForm = $('#form-customer-add-address-checkout');
                editCustomerForm.removeClass('fade');
                editCustomerForm.addClass('fade-in');
                editCustomerForm.addClass('show');
                addAddressForm.removeClass('fade-in');
                addAddressForm.removeClass('show');
                addAddressForm.addClass('fade');
                newAddressModel.addressTitle(Helper.__('New Address'));
                editCustomerModel.currentEditAddressId(false);
                this.resetAddressForm();
            },
            /* reset address form */
            resetAddressForm: function () {
                newAddressModel.resetAddressForm();
            },
            
            saveAddress: function () {
                saveNewAddress();
            },

            setFirstName: function(data,event) {
                newAddressModel.firstName(event.target.value);
            },
            setLastName: function(data,event) {
                newAddressModel.lastName(event.target.value);
            },
            setCompany: function(data,event) {
                newAddressModel.company(event.target.value);
            },
            setPhone: function(data,event) {
                newAddressModel.phone(event.target.value);
            },
            setStreet1: function(data,event) {
                newAddressModel.street1(event.target.value);
            },
            setStreet2: function(data,event) {
                newAddressModel.street2(event.target.value);
            },
            setCountry: function(data,event) {
                newAddressModel.country(event.target.value);
            },
            setRegion: function(data,event) {
                newAddressModel.region(event.target.value);
            },
            setRegionId: function(data,event) {
                newAddressModel.region_id(event.target.value);
            },
            setCity: function(data,event) {
                newAddressModel.city(event.target.value);
            },
            setZipCode: function(data,event) {
                newAddressModel.zipcode(event.target.value);
            },
            setVat: function(data,event) {
                newAddressModel.vat_id(event.target.value);
            }

        });
    }
);
