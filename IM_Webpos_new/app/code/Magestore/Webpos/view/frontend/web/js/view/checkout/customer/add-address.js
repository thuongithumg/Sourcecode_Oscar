/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/layout',
        'uiComponent',
        'Magestore_Webpos/js/model/customer/customer-factory',
        'Magestore_Webpos/js/model/directory/country',
        'Magestore_Webpos/js/model/customer/current-customer',
        'mage/translate',
        'Magestore_Webpos/js/model/google-autocomplete-address',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/region-updater',
        'mage/validation',
    ],
    function ($, ko, ViewManager, Component, CustomerFactory, countryModel, currentCustomer, Translate, GoogleAutocompleteAddress,Helper) {
        "use strict";
        return Component.extend({
            /* Ko observable for address input*/
            firstName: ko.observable(''),
            lastName: ko.observable(''),
            company: ko.observable(''),
            phone: ko.observable(''),
            street1: ko.observable(''),
            street2: ko.observable(''),
            country: ko.observable(''),
            region: ko.observable(''),
            region_id: ko.observable(0),
            city: ko.observable(''),
            zipcode: ko.observable(''),
            vatId: ko.observable(''),
            countryArray: ko.observableArray(window.webposConfig.country),
            addressTitle: ko.observable(Translate('New Address')),
            currentEditAddressType: ko.observable(null),
            /* End Observable*/
            
            /* Selector for control UI*/
            editCustomerForm: $('#form-edit-customer'),
            addAddressForm: $('#form-customer-add-address-checkout'),
            /* End selector for control UI*/

            /* Add address template*/
            defaults: {
                template: 'Magestore_Webpos/checkout/customer/add-address'
            },

            /* Cancel Address*/
            cancelAddress: function () {
                this.hideAddressForm();
            },

            /* Auto run when initialize*/
            initialize: function () {
                this.editCustomer = ViewManager.getSingleton('view/checkout/customer/edit-customer');
                this.editCustomer.addAddress(this);
                this._super();
            },

            /* Save Address */
            saveAddress: function () {
                var self = this;
                var customerDeferred;
                var newAddressData =  self.getAddressData();
                var currentCustomerData = currentCustomer.data();
                if (this.validateAddressForm()) {
                    if (ViewManager.getSingleton('view/checkout/customer/edit-customer').currentEditAddressId()) {
                        var addressIndex = -1;
                        var currentEditAddressId = ViewManager.getSingleton('view/checkout/customer/edit-customer').currentEditAddressId();
                        var allAddress = ViewManager.getSingleton('view/checkout/customer/edit-customer').addressArray();
                        $.each(allAddress, function (index, value) {
                            if (value.id == currentEditAddressId) {
                                addressIndex = index;
                                var addressData = self.getAddressData();
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
                        ViewManager.getSingleton('view/checkout/customer/edit-customer').addressArray(currentAddress);
                        currentCustomerData.addresses = currentAddress;
                    }

                    customerDeferred = CustomerFactory.get().setData(currentCustomerData).setPush(true).save();
                    self.formLoading(true);
                    customerDeferred.done(function (data) {
                        currentCustomer.setData(data);
                        self.formLoading(false);
                        ViewManager.getSingleton('view/checkout/customer/edit-customer').addressArray(data.addresses);
                        ViewManager.getSingleton('view/checkout/customer/edit-customer').showBillingPreview();
                        ViewManager.getSingleton('view/checkout/customer/edit-customer').showShippingPreview();
                        self.hideAddressForm();
                    });


                }
            },
            /*form loading */
            formLoading : function(mode){
                if(mode){
                    var e = $("<div id='customer-overlay' style='opacity: 1; background: rgba(255,255,255,0.8); position: absolute; display: none; z-index: 99999;top:0;left:0;right: 0;bottom: 0'> <span class='customer-loader'></span> </div>");
                    $('#page-loading').append(e);
                    $('#customer-overlay').show();
                }else{
                    $('#customer-overlay').remove();
                }

            },

            /* Hide Address */
            hideAddressForm: function () {
                this.editCustomerForm.removeClass('fade');
                this.editCustomerForm.addClass('fade-in');
                this.editCustomerForm.addClass('show');
                this.addAddressForm.removeClass('fade-in');
                this.addAddressForm.removeClass('show');
                this.addAddressForm.addClass('fade');
                this.addressTitle(Translate('New Address'));
                ViewManager.getSingleton('view/checkout/customer/edit-customer').currentEditAddressId(false);
                this.resetAddressForm();
            },

            /* reset address form */
            resetAddressForm: function () {
                this.firstName('');
                this.lastName('');
                this.company('');
                this.phone('');
                this.street1('');
                this.street2('');
                this.country('');
                this.region('');
                this.region_id(0);
                this.city('');
                this.zipcode('');
                this.vatId('');
                this.currentEditAddressType(null);
                $('#form-customer-add-address-checkout').find('#region').val('');
            },

            /* Validate Add Address Form */
            validateAddressForm: function () {
                var form = '#form-customer-add-address-checkout';
                return $(form).validation() && $(form).validation('isValid');
            },

            /* Get Address Data Form*/
            getAddressData: function () {
                var data = {};
                var self = this;
                data.id = 'nsync' + Date.now();
                data.firstname = this.firstName();
                data.lastname = this.lastName();
                data.company = this.company();
                data.telephone = this.phone();
                data.street = [this.street1(), this.street2()];
                data.country_id = this.country();

                var regionIdAddAddress = $('#form-customer-add-address-checkout').find('.region_id');
                if (regionIdAddAddress.is(':visible')) {
                    var selected = regionIdAddAddress.find(":selected");
                    var regionCode = selected.data('region-code');
                    var region = selected.html();
                    if(self.region_id()==''&&regionIdAddAddress.val()){
                        self.region_id(regionIdAddAddress.val());
                    }
                    data.region = {
                        region_id: self.region_id(),
                        region_code : regionCode,
                        region : region
                    };
                    data.region_id = self.region_id();
                } else {
                    data.region = {
                        region_id: 0,
                        region_code : self.region(),
                        region : self.region()
                    };
                    data.region_id = 0;
                }
                
                data.city = self.city();
                data.postcode = self.zipcode();
                data.vatId = self.vatId();
                return data;
            },

            /* Render select for region */
            _renderSelectOption: function (selectElement, key, value) {
                selectElement.append($.proxy(function () {
                    var name = value.name.replace(/[!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~]/g, '\\$&'),
                        tmplData,
                        tmpl;

                    if (value.code && $(name).is('span')) {
                        key = value.code;
                        value.name = $(name).text();
                    }

                    tmplData = {
                        value: key,
                        title: value.name,
                        isSelected: false,
                        code: value.code
                    };

                    tmpl = this.regionTmpl({
                        data: tmplData
                    });

                    return $(tmpl);
                }, this));
            },

            /* Remove select options for region */
            _removeSelectOptions: function (selectElement) {
                selectElement.find('option').each(function (index) {
                    if ($(this).val()) {
                        $(this).remove();
                    }
                });
            },

            initGoogleAddress: function(){
                if(window.webposConfig['webpos/general/suggest_address'] && window.webposConfig['webpos/general/google_api_key']) {
                    setTimeout(function(){
                        GoogleAutocompleteAddress.init('form-customer-add-address-checkout','billing');
                        GoogleAutocompleteAddress.init('form-customer-add-shipping-address-checkout','billing');
                        GoogleAutocompleteAddress.init('form-customer-add-customer-checkout','billing');
                        GoogleAutocompleteAddress.init('form-edit-customer','billing');
                        GoogleAutocompleteAddress.init('form-customer-add-address','billing');
                        GoogleAutocompleteAddress.init('form-customer-add-billing-address-checkout','billing');
                        GoogleAutocompleteAddress.init('form-customer-add-address','billing');
                    },2000);
                }
            },
        });
    }
);
