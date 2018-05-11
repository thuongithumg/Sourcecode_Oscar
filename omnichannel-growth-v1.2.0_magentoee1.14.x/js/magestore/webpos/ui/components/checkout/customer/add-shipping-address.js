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
        'model/customer/customer/shipping-address',
        'model/customer/customer/billing-address',
        'helper/general',
        "mage/validation"
    ],
    function ($, ko, Component, shippingModel, billingModel, helper) {
        "use strict";
        return Component.extend({
            regionIdComputedShipping: '',
            countryArray: ko.observableArray(window.webposConfig.country),
            /* Selector for control UI*/
            addShippingAddressCheckoutForm:  $('#form-customer-add-shipping-address-checkout'),
            addCustomerCheckoutForm: $('#form-customer-add-customer-checkout'),
            overlay: $('.wrap-backover'),
            /* End Selector*/

            /* Template for koJS*/
            defaults: {
                template: 'ui/checkout/customer/add-shipping-address'
            },

            /* Auto run when call */
            initialize: function () {
                this._super();
                this.regionObjectShipping = ko.pureComputed(function(){
                    return shippingModel.regionObjectShipping();
                });
                this.isShowShippingSummaryForm = ko.pureComputed(function(){
                    return shippingModel.isShowShippingSummaryForm();
                });
                this.firstNameShipping = ko.pureComputed(function(){
                    return shippingModel.firstNameShipping();
                });
                this.lastNameShipping = ko.pureComputed(function(){
                    return shippingModel.lastNameShipping();
                });
                this.companyShipping = ko.pureComputed(function(){
                    return shippingModel.companyShipping();
                });
                this.phoneShipping = ko.pureComputed(function(){
                    return shippingModel.phoneShipping();
                });
                this.street1Shipping = ko.pureComputed(function(){
                    return shippingModel.street1Shipping();
                });
                this.street2Shipping = ko.pureComputed(function(){
                    return shippingModel.street2Shipping();
                });
                this.countryShipping = ko.pureComputed(function(){
                    return shippingModel.countryShipping();
                });
                this.regionShipping = ko.pureComputed(function(){
                    return shippingModel.regionShipping();
                });
                this.regionIdShipping = ko.pureComputed(function(){
                    return shippingModel.regionIdShipping();
                });
                this.cityShipping = ko.pureComputed(function(){
                    return shippingModel.cityShipping();
                });
                this.zipcodeShipping = ko.pureComputed(function(){
                    return shippingModel.zipcodeShipping();
                });
                this.vatShipping = ko.pureComputed(function(){
                    return shippingModel.vatShipping();
                });
                this.isSameBillingShipping = ko.pureComputed(function(){
                    return shippingModel.isSameBillingShipping();
                });
                this.shippingAddressTitle = ko.pureComputed(function(){
                    return shippingModel.shippingAddressTitle();
                });
                this.leftButton = ko.pureComputed(function(){
                    return shippingModel.leftButton();
                });
            },
            

            /* Hide Shipping Address*/
            hideShippingAddress: function () {
                var addShippingAddressCheckoutForm = $('#form-customer-add-shipping-address-checkout');
                var addCustomerCheckoutForm = $('#form-customer-add-customer-checkout');
                if (this.shippingAddressTitle == this.__('Add Shipping Address')) {
                    shippingModel.isShowShippingSummaryForm(false);
                } else {
                    shippingModel.isShowShippingSummaryForm(false);
                    this.resetFormInfo();
                }
                addShippingAddressCheckoutForm.removeClass('fade-in');
                addShippingAddressCheckoutForm.removeClass('show');
                addShippingAddressCheckoutForm.addClass('fade');
                addCustomerCheckoutForm.addClass('fade-in');
                addCustomerCheckoutForm.addClass('show');
                addCustomerCheckoutForm.removeClass('fade');
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            },

            /* Save Shipping Address*/
            saveShippingAddress: function () {
                var self = this;
                var regionShippingAddress = $('#form-customer-add-shipping-address-checkout #shipping_region_id');
                if (this.validateShippingAddressForm()) {

                    if (regionShippingAddress.is(':visible')) {
                        var selected = regionShippingAddress.find(":selected");
                        var region = selected.html();
                        shippingModel.regionObjectShipping({
                            region_id: self.regionIdShipping(),
                            region : region
                        });
                        shippingModel.regionShipping(region);
                    } else {
                        shippingModel.regionObjectShipping({
                            region_id: 0,
                            region : self.regionShipping()
                        });
                        shippingModel.regionIdShipping(0);
                    }
                    
                    var addShippingAddressCheckoutForm = $('#form-customer-add-shipping-address-checkout');
                    var addCustomerCheckoutForm = $('#form-customer-add-customer-checkout');
                    addShippingAddressCheckoutForm.removeClass('fade-in');
                    addShippingAddressCheckoutForm.removeClass('show');
                    addShippingAddressCheckoutForm.addClass('fade');
                    addCustomerCheckoutForm.addClass('fade-in');
                    addCustomerCheckoutForm.addClass('show');
                    addCustomerCheckoutForm.removeClass('fade');
                    $('.notification-bell').hide();
                    $('#c-button--push-left').hide();
                    if (this.isSameBillingShipping()) {
                        this.copyInformationToBilling();
                    }
                    shippingModel.isShowShippingSummaryForm(true);
                }
            },

            /* Validate Shipping Address Form*/
            validateShippingAddressForm: function () {
                var form = '#form-customer-add-shipping-address-checkout';
                return $(form).validation({}) && $(form).validation('isValid');
            },

            /* Copy Information To Billing*/
            copyInformationToBilling: function () {
                billingModel.firstNameBilling(this.firstNameShipping());
                billingModel.lastNameBilling(this.lastNameShipping());
                billingModel.companyBilling(this.companyShipping());
                billingModel.phoneBilling(this.phoneShipping());
                billingModel.street1Billing(this.street1Shipping());
                billingModel.street2Billing(this.street2Shipping());
                billingModel.countryBilling(this.countryShipping());
                billingModel.regionBilling(this.regionShipping());
                billingModel.regionIdBilling(this.regionIdShipping());
                billingModel.cityBilling(this.cityShipping());
                billingModel.zipcodeBilling(this.zipcodeShipping());
                billingModel.vatBilling(this.vatShipping());
                billingModel.isShowBillingSummaryForm(true);
            },

            /* Reset Form*/
            resetFormInfo: function () {
                shippingModel.firstNameShipping('');
                shippingModel.lastNameShipping('');
                shippingModel.companyShipping('');
                shippingModel.phoneShipping('');
                shippingModel.street1Shipping('');
                shippingModel.street2Shipping('');
                shippingModel.countryShipping(window.webposConfig.defaultCountry);
                shippingModel.regionShipping('');
                shippingModel.regionIdShipping('');
                shippingModel.cityShipping('');
                shippingModel.zipcodeShipping('');
                shippingModel.vatShipping('');
                shippingModel.isSameBillingShipping(true);
                shippingModel.regionIdComputedShipping = '';
                shippingModel.shippingAddressTitle(helper.__('Add Shipping Address'));
                shippingModel.leftButton(helper.__('Cancel'));
            },

            setFirstName: function(data,event) {
                shippingModel.firstNameShipping(event.target.value);
            },
            setLastName: function(data,event) {
                shippingModel.lastNameShipping(event.target.value);
            },
            setCompany: function(data,event) {
                shippingModel.companyShipping(event.target.value);
            },
            setPhone: function(data,event) {
                shippingModel.phoneShipping(event.target.value);
            },
            setStreet1: function(data,event) {
                shippingModel.street1Shipping(event.target.value);
            },
            setStreet2: function(data,event) {
                shippingModel.street2Shipping(event.target.value);
            },
            setCountry: function(data,event) {
                shippingModel.countryShipping(event.target.value);
            },
            setRegion: function(data,event) {
                shippingModel.regionShipping(event.target.value);
            },
            setRegionId: function(data,event) {
                shippingModel.regionIdShipping(event.target.value);
            },
            setCity: function(data,event) {
                shippingModel.cityShipping(event.target.value);
            },
            setZipCode: function(data,event) {
                shippingModel.zipcodeShipping(event.target.value);
            },
            setVat: function(data,event) {
                shippingModel.vatShipping(event.target.value);
            },
            setIsSameBillingShipping: function(data,event) {
                shippingModel.isSameBillingShipping(event.target.checked);
            },
        });
    }
);
