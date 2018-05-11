/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/view/checkout/customer/add-billing-address',
        'mage/translate',
        "mage/validation"
    ],
    function ($, ko, Component, addBilling, Translate) {
        "use strict";
        return Component.extend({

            countryArray: ko.observableArray(window.webposConfig.country),
            /* Binding shipping address information in create customer form*/
            isShowShippingSummaryForm: ko.observable(false),
            firstNameShipping: ko.observable(''),
            lastNameShipping: ko.observable(''),
            companyShipping: ko.observable(''),
            phoneShipping: ko.observable(''),
            street1Shipping: ko.observable(''),
            street2Shipping: ko.observable(''),
            countryShipping: ko.observable(''),
            regionShipping: ko.observable(''),
            regionIdShipping: ko.observable(0),
            cityShipping: ko.observable(''),
            zipcodeShipping: ko.observable(''),
            vatShipping: ko.observable(''),
            isSameBillingShipping: ko.observable(true),
            regionObjectShipping: ko.observable(''),
            regionIdComputedShipping: '',
            shippingAddressTitle: ko.observable(Translate('Add Shipping Address')),
            leftButton: ko.observable(Translate('Cancel')),
            /* End binding*/

            /* old data */
            temporaryData : ko.observableArray(),

            /* Selector for control UI*/
            addShippingAddressCheckoutForm:  $('#form-customer-add-shipping-address-checkout'),
            addCustomerCheckoutForm: $('#form-customer-add-customer-checkout'),
            overlay: $('.wrap-backover'),
            /* End Selector*/

            /* Template for koJS*/
            defaults: {
                template: 'Magestore_Webpos/checkout/customer/add-shipping-address'
            },

            /* Auto run when call */
            initialize: function () {
                this.addBilling = addBilling();
                this.addBilling.addShipping(this);
                this._super();
            },
            
            /* Hide Shipping Address*/
            hideShippingAddress: function () {
                if (this.shippingAddressTitle == Translate('Add Shipping Address')) {
                    this.isShowShippingSummaryForm(false);
                } else {
                    if(this.validateData()){
                        this.isShowShippingSummaryForm(true);
                    }else{
                        this.isShowShippingSummaryForm(false);
                    }
                    //this.resetFormInfo();
                    this.revertFormInfo();
                }
                this.addShippingAddressCheckoutForm.removeClass('fade-in');
                this.addShippingAddressCheckoutForm.removeClass('show');
                this.addShippingAddressCheckoutForm.addClass('fade');
                this.addCustomerCheckoutForm.addClass('fade-in');
                this.addCustomerCheckoutForm.addClass('show');
                this.addCustomerCheckoutForm.removeClass('fade');
                this.overlay.show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            },

            /* Save Shipping Address*/
            saveShippingAddress: function () {
                var self = this;
                var regionShippingAddress = $('#form-customer-add-shipping-address-checkout .region_id');
                if (this.validateShippingAddressForm()) {
                    if (regionShippingAddress.is(':visible')) {
                        var selected = regionShippingAddress.find(":selected");
                        var regionCode = selected.data('region-code');
                        var region = selected.html();
                        this.regionObjectShipping({
                            region_id: self.regionIdShipping(),
                            region_code : regionCode,
                            region : region
                        });
                    } else {
                        this.regionObjectShipping({
                            region_id: 0,
                            region_code : self.regionShipping(),
                            region : self.regionShipping()
                        });
                        self.regionIdShipping(0);
                    }
                    this.addShippingAddressCheckoutForm.removeClass('fade-in');
                    this.addShippingAddressCheckoutForm.removeClass('show');
                    this.addShippingAddressCheckoutForm.addClass('fade');
                    this.addCustomerCheckoutForm.addClass('fade-in');
                    this.addCustomerCheckoutForm.addClass('show');
                    this.addCustomerCheckoutForm.removeClass('fade');
                    this.overlay.show();
                    $('.notification-bell').hide();
                    $('#c-button--push-left').hide();
                    if (this.isSameBillingShipping()) {
                        this.copyInformationToBilling();
                    }
                    this.isShowShippingSummaryForm(true);
                }

            },

            /* Validate Shipping Address Form*/
            validateShippingAddressForm: function () {
                var form = '#form-customer-add-shipping-address-checkout';
                return $(form).validation({}) && $(form).validation('isValid');
            },

            /* Copy Information To Billing*/
            copyInformationToBilling: function () {
                addBilling().firstNameBilling(this.firstNameShipping());
                addBilling().lastNameBilling(this.lastNameShipping());
                addBilling().companyBilling(this.companyShipping());
                addBilling().phoneBilling(this.phoneShipping());
                addBilling().street1Billing(this.street1Shipping());
                addBilling().street2Billing(this.street2Shipping());
                addBilling().countryBilling(this.countryShipping());
                addBilling().regionBilling(this.regionShipping());
                addBilling().regionIdBilling(this.regionIdShipping());
                addBilling().cityBilling(this.cityShipping());
                addBilling().zipcodeBilling(this.zipcodeShipping());
                addBilling().vatBilling(this.vatShipping());
                addBilling().isShowBillingSummaryForm(true);
                addBilling().regionObjectBilling(this.regionObjectShipping());
            },
            
            /* Reset Form*/
            resetFormInfo: function () {
                this.firstNameShipping('');
                this.lastNameShipping('');
                this.companyShipping('');
                this.phoneShipping('');
                this.street1Shipping('');
                this.street2Shipping('');
                this.countryShipping('');
                this.regionShipping('');
                this.regionIdShipping('');
                this.cityShipping('');
                this.zipcodeShipping('');
                this.vatShipping('');
                this.isSameBillingShipping(true);
                this.regionIdComputedShipping = '';
                this.shippingAddressTitle(Translate('Add Shipping Address'));
                this.leftButton(Translate('Cancel'));
            },
            revertFormInfo: function(){
                var data = this.temporaryData();
                if(data){
                    this.firstNameShipping(data.firstNameShipping);
                    this.lastNameShipping(data.lastNameShipping);
                    this.companyShipping(data.companyShipping);
                    this.phoneShipping(data.phoneShipping);
                    this.street1Shipping(data.street1Shipping);
                    this.street2Shipping(data.street2Shipping);
                    this.countryShipping(data.countryShipping);
                    this.regionShipping(data.regionShipping);
                    this.regionIdShipping(data.regionIdShipping);
                    this.cityShipping(data.cityShipping);
                    this.zipcodeShipping(data.zipcodeShipping);
                    this.vatShipping(data.vatShipping);
                    this.isSameBillingShipping(data.isSameBillingShipping);
                    this.regionIdComputedShipping = data.regionIdComputedShipping;
                }
            },
            validateData: function(){
                if(!this.firstNameShipping()
                    || !this.lastNameShipping()
                    || !this.phoneShipping()
                    || !this.street1Shipping()
                    || !this.cityShipping()
                    || !this.zipcodeShipping()
                ){
                   return false;
                }
                return true;
            }
        });
    }
);
