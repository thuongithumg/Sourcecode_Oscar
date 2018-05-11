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
        'model/customer/customer/billing-address',
        'model/customer/customer/shipping-address',
        "helper/general",
        "mage/validation"
    ],
    function ($, ko, Component, billingAddressModel, shippingAddressModel, helper) {
        "use strict";
        return Component.extend({
    
            regionIdComputedBilling: '',
            countryArray: ko.observableArray(window.webposConfig.country),
            /* Template for knockout js*/
            defaults: {
                template: 'ui/checkout/customer/add-billing-address'
            },

            initialize: function () {
                this._super();
                this.regionObjectBilling = ko.pureComputed(function(){
                    return billingAddressModel.regionObjectBilling();
                });
                this.addShipping = ko.pureComputed(function(){
                    return billingAddressModel.addShipping();
                });
                this.isShowBillingSummaryForm = ko.pureComputed(function(){
                    return billingAddressModel.isShowBillingSummaryForm();
                });
                this.firstNameBilling = ko.pureComputed(function(){
                    return billingAddressModel.firstNameBilling();
                });
                this.lastNameBilling = ko.pureComputed(function(){
                    return billingAddressModel.lastNameBilling();
                });
                this.companyBilling = ko.pureComputed(function(){
                    return billingAddressModel.companyBilling();
                });
                this.phoneBilling = ko.pureComputed(function(){
                    return billingAddressModel.phoneBilling();
                });
                this.street1Billing = ko.pureComputed(function(){
                    return billingAddressModel.street1Billing();
                });
                this.street2Billing = ko.pureComputed(function(){
                    return billingAddressModel.street2Billing();
                });
                this.countryBilling = ko.pureComputed(function(){
                    return billingAddressModel.countryBilling();
                });
                this.regionBilling = ko.pureComputed(function(){
                    return billingAddressModel.regionBilling();
                });
                this.regionIdBilling = ko.pureComputed(function(){
                    return billingAddressModel.regionIdBilling();
                });
                this.cityBilling = ko.pureComputed(function(){
                    return billingAddressModel.cityBilling();
                });
                this.zipcodeBilling = ko.pureComputed(function(){
                    return billingAddressModel.zipcodeBilling();
                });
                this.vatBilling = ko.pureComputed(function(){
                    return billingAddressModel.vatBilling();
                });
                this.billingAddressTitle = ko.pureComputed(function(){
                    return billingAddressModel.billingAddressTitle();
                });
                this.leftButton = ko.pureComputed(function(){
                    return billingAddressModel.leftButton();
                });
            },

            /* Hide billing address*/
            hideBillingAddress: function () {
                var formAddBillingAddress = $('#form-customer-add-billing-address-checkout');
                var formAddCustomerCheckout = $('#form-customer-add-customer-checkout');
                if (this.billingAddressTitle == helper.__('Add Billing Address')) {
                    billingAddressModel.isShowBillingSummaryForm(false);
                } else {
                    billingAddressModel.isShowBillingSummaryForm(false);
                    this.resetFormInfo();
                }
                formAddBillingAddress.removeClass('fade-in');
                formAddBillingAddress.removeClass('show');
                formAddBillingAddress.addClass('fade');
                formAddCustomerCheckout.addClass('fade-in');
                formAddCustomerCheckout.addClass('show');
                formAddCustomerCheckout.removeClass('fade');
                $('.wrap-backover').show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();

            },

            /* Save billing address*/
            saveBillingAddress: function () {
                var self = this;
                if (this.validateBillingAddressForm()) {
                    var addBillingForm =  $('#form-customer-add-billing-address-checkout');
                    var addCustomerForm = $('#form-customer-add-customer-checkout');
                    var regionIdBillingAddress = addBillingForm.find('#billing_region_id');
                    if (regionIdBillingAddress.is(':visible')) {
                        var selected = regionIdBillingAddress.find(":selected");
                        var region = selected.html();
                        billingAddressModel.regionObjectBilling({
                            region_id: self.regionIdBilling(),
                            region : region
                        });
                        this.regionIdComputedBilling = self.regionIdBilling();
                        billingAddressModel.regionBilling(region);
                    } else {
                        billingAddressModel.regionObjectBilling({
                            region_id: 0,
                            region : self.regionBilling()
                        });
                        billingAddressModel.regionIdBilling(0);
                    }
                    addBillingForm.removeClass('fade-in');
                    addBillingForm.removeClass('show');
                    addBillingForm.addClass('fade');
                    addCustomerForm.addClass('fade-in');
                    addCustomerForm.addClass('show');
                    addCustomerForm.removeClass('fade');
                    $('.wrap-backover').show();
                    $('.notification-bell').hide();
                    $('#c-button--push-left').hide();
                    billingAddressModel.isShowBillingSummaryForm(true);
                    shippingAddressModel.isSameBillingShipping(false);
                }
            },


            /* validate billing address form */
            validateBillingAddressForm: function () {
                var form = '#form-customer-add-billing-address-checkout';
                return $(form).validation({}) && $(form).validation('isValid');
            },

            /* Reset Form*/
            resetFormInfo: function () {
                billingAddressModel.firstNameBilling('');
                billingAddressModel.lastNameBilling('');
                billingAddressModel.companyBilling('');
                billingAddressModel.phoneBilling('');
                billingAddressModel.street1Billing('');
                billingAddressModel.street2Billing('');
                billingAddressModel.countryBilling(window.webposConfig.defaultCountry);
                billingAddressModel.regionBilling('');
                billingAddressModel.regionIdBilling('');
                billingAddressModel.cityBilling('');
                billingAddressModel.zipcodeBilling('');
                billingAddressModel.vatBilling('');
                billingAddressModel.regionIdComputedBilling = '';
                billingAddressModel.billingAddressTitle(helper.__('Add Billing Address'));
                billingAddressModel.leftButton(helper.__('Cancel'));
            },

            setFirstName: function(data,event) {
                billingAddressModel.firstNameBilling(event.target.value);
            },
            setLastName: function(data,event) {
                billingAddressModel.lastNameBilling(event.target.value);
            },
            setCompany: function(data,event) {
                billingAddressModel.companyBilling(event.target.value);
            },
            setPhone: function(data,event) {
                billingAddressModel.phoneBilling(event.target.value);
            },
            setStreet1: function(data,event) {
                billingAddressModel.street1Billing(event.target.value);
            },
            setStreet2: function(data,event) {
                billingAddressModel.street2Billing(event.target.value);
            },
            setCountry: function(data,event) {
                billingAddressModel.countryBilling(event.target.value);
            },
            setRegion: function(data,event) {
                billingAddressModel.regionBilling(event.target.value);
            },
            setRegionId: function(data,event) {
                billingAddressModel.regionIdBilling(event.target.value);
            },
            setCity: function(data,event) {
                billingAddressModel.cityBilling(event.target.value);
            },
            setZipCode: function(data,event) {
                billingAddressModel.zipcodeBilling(event.target.value);
            },
            setVat: function(data,event) {
                billingAddressModel.vatBilling(event.target.value);
            }
        });
    }
);
