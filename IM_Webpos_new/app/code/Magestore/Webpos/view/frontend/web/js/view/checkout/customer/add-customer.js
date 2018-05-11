/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'require',
        'jquery',
        'ko',   
        'Magestore_Webpos/js/view/layout',
        'uiComponent',
        'Magestore_Webpos/js/model/customer/customer-factory',
        'Magestore_Webpos/js/action/checkout/select-customer-checkout',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/action/notification/add-notification',
        "mage/translate",
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/lib/jquery.toaster',
        "mage/mage",
        "mage/validation",
        'Magestore_Webpos/js/bootstrap/bootstrap',
        'Magestore_Webpos/js/bootstrap/bootstrap-switch',
        
    ],

    function (
        require,
        $, 
        ko, 
        ViewManager,
        Component, 
        CustomerFactory,
        selectCustomer, 
        Event, 
        addNotification, 
        Translate, 
        checkoutModel
    ) {
        "use strict";
        ko.bindingHandlers.bootstrapSwitchOnCustomerCheckout = {
            init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
                //$(element).iosCheckbox();
                $(element).on("switchchange", function (e) {
                    valueAccessor()(e.target.checked);
                });
                $(".dob").calendar({
                    controlType: 'select',
                    dateFormat: "M/d/Y",
                    showTime: false,
                    maxDate: "-1d", changeMonth: true, changeYear: true
                });
            }
        };
        return Component.extend({
            /* Binding billing address information in create customer form*/
            customerGroupArray: ko.observableArray(window.webposConfig.customerGroup),
            genderArray: [
                {id:1,code:'Male'},
                {id:2,code:'Female'},
                {id:1,code:'Not Specified'}
                ],

            isAddCustomer: ko.observable(false),

            isShowBillingSummaryForm: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').isShowBillingSummaryForm();
            }),
            firstNameBilling: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').firstNameBilling();
            }),
            lastNameBilling: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').lastNameBilling();
            }),
            companyBilling: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').companyBilling();
            }),
            phoneBilling: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').phoneBilling();
            }),
            street1Billing: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').street1Billing();
            }),
            street2Billing: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').street2Billing();
            }),
            countryBilling: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').countryBilling();
            }),
            regionBilling: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').regionBilling();
            }),
            regionIdBilling: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').regionIdBilling();
            }),
            cityBilling: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').cityBilling();
            }),
            zipcodeBilling: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').zipcodeBilling();
            }),
            vatBilling: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').vatBilling();
            }),
            regionObjectBilling: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-billing-address').regionObjectBilling();
            }),
            /* End binding*/

            /* Create Customer Form*/
            firstNameCustomer: ko.observable(''),
            lastNameCustomer: ko.observable(''),
            emailCustomer: ko.observable(''),
            groupCustomer: ko.observable(''),
            vatCustomer: ko.observable(''),
            dob: ko.observable(''),
            taxvat: ko.observable(''),
            gender: ko.observable(''),
            showDob: window.webposConfig['customer/address/dob_show'] != null,
            showTaxvat: window.webposConfig['customer/address/taxvat_show'] != null,
            showGender: window.webposConfig['customer/address/gender_show'] != null,
            requireDob: window.webposConfig['customer/address/dob_show'] == 'req',
            requireTaxvat: window.webposConfig['customer/address/taxvat_show'] == 'req',
            requireGender: window.webposConfig['customer/address/gender_show'] == 'req',
            isSubscriberCustomer: ko.observable(false),
            /* End Form*/

            /* Binding shipping address information in create customer form*/
            isShowShippingSummaryForm: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').isShowShippingSummaryForm();
            }),
            firstNameShipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').firstNameShipping();
            }),
            lastNameShipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').lastNameShipping();
            }),
            companyShipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').companyShipping();
            }),
            phoneShipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').phoneShipping();
            }),
            street1Shipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').street1Shipping();
            }),
            street2Shipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').street2Shipping();
            }),
            countryShipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').countryShipping();
            }),
            regionShipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').regionShipping();
            }),
            regionIdShipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').regionIdShipping();
            }),
            cityShipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').cityShipping();
            }),
            zipcodeShipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').zipcodeShipping();
            }),
            vatShipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').vatShipping();
            }),
            regionObjectShipping: ko.pureComputed(function () {
                return require('Magestore_Webpos/js/view/layout').getSingleton('view/checkout/customer/add-shipping-address').regionObjectShipping();
            }),
                
            /* End binding*/

            /* Selector for control UI*/
            formAddCustomerCheckout: $('#form-customer-add-customer-checkout'),
            formAddShippingAddressCheckout: $('#form-customer-add-shipping-address-checkout'),
            /* End selector*/

            /* Template for koJS*/
            defaults: {
                template: 'Magestore_Webpos/checkout/customer/add-customer'
            },

            initDate: function () {
                var currentDate = new Date();
                var year = currentDate.getFullYear();
                var month = currentDate.getMonth();
                var day = currentDate.getDate();
                $("#new_customer_dob").calendar({
                    showsTime: true,
                    controlType: 'select',
                    timeFormat: 'HH:mm',
                    showTime: false,
                    minDate: new Date(year, month, day, '00', '00', '00', '00'),
                });
            },

            /* Cancel customer form */
            cancelCustomerForm: function () {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                var customerForm = $('#form-customer-add-customer-checkout');
                customerForm.removeClass('fade-in');
                customerForm.removeClass('show');
                customerForm.addClass('fade');
                customerForm.validation();
                customerForm.validation('clearError');
                this.resetFormInfo('form-customer-add-customer-checkout');
                this.deleteShippingAddress();
                this.deleteBillingAddress();
                viewManager.getSingleton('view/checkout/customer/add-billing-address').isShowBillingSummaryForm(false);
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').isShowShippingSummaryForm(false);
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').isSameBillingShipping(true);
                $('.wrap-backover').hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
            },

            /* Save customer form*/
            saveCustomerForm: function () {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                var data = {};
                var self = this;
                data.id = 'notsync_' + this.emailCustomer();
                data.firstname = this.firstNameCustomer();
                data.lastname = this.lastNameCustomer();
                data.full_name = this.firstNameCustomer() + ' ' + this.lastNameCustomer();
                data.email = this.emailCustomer();
                data.group_id = this.groupCustomer();
                data.subscriber_status = this.isSubscriberCustomer();
                data.addresses = [];
                data.dob = this.dob();
                data.taxvat = this.taxvat();
                data.gender = this.gender();
                // data.additional_attributes = [{dob:this.dob()},{taxvat:this.taxvat()},{gender:this.gender()}];
                if (!this.isShowBillingSummaryForm() && !this.isShowShippingSummaryForm()) {
                    data.addresses = [];
                    checkoutModel.saveShippingAddress({
                        'id' : 0,
                        'firstname': data.firstname,
                        'lastname': data.lastname
                    });
                    checkoutModel.saveBillingAddress({
                        'id' : 0,
                        'firstname': data.firstname,
                        'lastname': data.lastname
                    });
                } else {
                    var shippingAddressData = this.getShippingAddressData();
                    var billingAddressData = this.getBillingAddressData();
                    if (viewManager.getSingleton('view/checkout/customer/add-shipping-address').isSameBillingShipping()) {
                        shippingAddressData.default_billing = true;
                        shippingAddressData.default_shipping = true;
                        data.addresses.push(shippingAddressData);
                        checkoutModel.saveBillingAddress(shippingAddressData);
                        checkoutModel.saveShippingAddress(shippingAddressData);
                    } else {
                        if (this.isShowShippingSummaryForm()) {
                            shippingAddressData.default_shipping = true;
                            data.addresses.push(shippingAddressData);
                            checkoutModel.saveShippingAddress(shippingAddressData);
                            if (this.isShowBillingSummaryForm()) {
                                checkoutModel.saveBillingAddress(this.getBillingAddressData());
                            } else {
                                checkoutModel.saveBillingAddress({
                                    'id' : 0,
                                    'firstname': data.firstname,
                                    'lastname': data.lastname
                                });
                            }
                        }
                        if (this.isShowBillingSummaryForm()) {
                            billingAddressData.default_billing = true;
                            data.addresses.push(billingAddressData);
                            checkoutModel.saveBillingAddress(billingAddressData);
                            if (this.isShowShippingSummaryForm()) {
                                checkoutModel.saveShippingAddress(shippingAddressData);
                            } else {
                                checkoutModel.saveShippingAddress({
                                    'id' : 0,
                                    'firstname': data.firstname,
                                    'lastname': data.lastname
                                });
                            }
                        }
                        if (!this.isShowBillingSummaryForm() && !this.isShowShippingSummaryForm()) {
                            checkoutModel.saveShippingAddress({
                                'id' : 0,
                                'firstname': data.firstname,
                                'lastname': data.lastname
                            });
                            checkoutModel.saveBillingAddress({
                                'id' : 0,
                                'firstname': data.firstname,
                                'lastname': data.lastname
                            });
                        }
                    }
                }

                var telephone;
                if (data.addresses.length > 0) {
                    var addresses = data.addresses;
                    telephone = addresses[0].telephone;
                } else {
                    telephone = 'N/A';
                }
                data.telephone = telephone;
                if (this.validateCustomerForm()) {
                    var deferred = CustomerFactory.get()
                        .getCollection().addFieldToFilter('email', this.emailCustomer(), 'eq')
                        .load();
                    deferred.done(function (filterData) {
                        var items = filterData.items;
                        if (items.length > 0) {
                            addNotification('The customer email is existed.', true, 'danger', 'Error');
                        } else {
                            if (typeof data['columns'] != 'undefined') {
                                delete data['columns'];
                            }
                            var saveDeferred = CustomerFactory.get().setData(data).setPush(true).save();
                            saveDeferred.done(function (dataOffline) {
                                selectCustomer(dataOffline);
                                var addressData = dataOffline.addresses;
                                var isSetBilling = false;
                                var isSetShipping = false;
                                $.each(addressData, function (index, value) {
                                    if (value.default_billing) {
                                        viewManager.getSingleton('view/checkout/customer/edit-customer').billingAddressId(value.id);
                                        viewManager.getSingleton('view/checkout/customer/edit-customer').setBillingPreviewData(value);
                                        viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewBilling(true);
                                        isSetBilling = true;
                                    }
                                    if (value.default_shipping) {
                                        viewManager.getSingleton('view/checkout/customer/edit-customer').shippingAddressId(value.id);
                                        viewManager.getSingleton('view/checkout/customer/edit-customer').setShippingPreviewData(value);
                                        viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewShipping(true);
                                        isSetShipping = true;
                                    }
                                });
                                if (!isSetBilling) {
                                    viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewBilling(false);
                                }

                                if (!isSetShipping) {
                                    viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewShipping(false);
                                }
                                self.isAddCustomer(true);
                                Event.dispatch('customer_pull_after');
                                $.toaster(
                                    {
                                        priority: 'success',
                                        title: Translate('Success'),
                                        message: Translate('The customer is saved successfully.')
                                    }
                                );
                            });
                            self.cancelCustomerForm();
                        }
                    });
                }
            },

            /* Validate Customer Form*/
            validateCustomerForm: function () {
                var form = '#form-customer-add-customer-checkout';
                return $(form).validation({}) && $(form).validation('isValid');
            },

            /* Show shipping address*/
            showShippingAddress: function () {
                var self = this;
                self.showShippingAddressControl();
            },

            /* Edit shipping address*/
            editShippingAddress: function () {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                var self = this;
                self.showShippingAddressControl();
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').shippingAddressTitle(Translate('Edit Shipping Address'));
                var temporaryData = {
                                    'firstNameShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').firstNameShipping(),
                                    'lastNameShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').lastNameShipping(),
                                    'companyShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').companyShipping(),
                                    'phoneShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').phoneShipping(),
                                    'street1Shipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').street1Shipping(),
                                    'street2Shipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').street2Shipping(),
                                    'countryShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').countryShipping(),
                                    'regionShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').regionShipping(),
                                    'regionIdShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').regionIdShipping(),
                                    'cityShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').cityShipping(),
                                    'zipcodeShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').zipcodeShipping(),
                                    'vatShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').vatShipping(),
                                    'isSameBillingShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').isSameBillingShipping(),
                                    'regionIdComputedShipping': viewManager.getSingleton('view/checkout/customer/add-shipping-address').regionIdComputedShipping,
                                };
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').temporaryData(temporaryData);
                //viewManager.getSingleton('view/checkout/customer/add-shipping-address').leftButton(Translate('Delete'));
            },

            /* Show billing address*/
            showBillingAddress: function () {
                var self = this;
                self.showBillingAddressControl();
            },

            /* Edit billing address*/
            editBillingAddress: function () {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                var self = this;
                self.showBillingAddressControl();
                viewManager.getSingleton('view/checkout/customer/add-billing-address').billingAddressTitle(Translate('Edit Billing Address'));
                var temporaryData = {
                    'firstNameBilling': viewManager.getSingleton('view/checkout/customer/add-billing-address').firstNameBilling(),
                    'lastNameBilling': viewManager.getSingleton('view/checkout/customer/add-billing-address').lastNameBilling(),
                    'companyBilling': viewManager.getSingleton('view/checkout/customer/add-billing-address').companyBilling(),
                    'phoneBilling': viewManager.getSingleton('view/checkout/customer/add-billing-address').phoneBilling(),
                    'street1Billing': viewManager.getSingleton('view/checkout/customer/add-billing-address').street1Billing(),
                    'street2Billing': viewManager.getSingleton('view/checkout/customer/add-billing-address').street2Billing(),
                    'countryBilling': viewManager.getSingleton('view/checkout/customer/add-billing-address').countryBilling(),
                    'regionBilling': viewManager.getSingleton('view/checkout/customer/add-billing-address').regionBilling(),
                    'regionIdBilling': viewManager.getSingleton('view/checkout/customer/add-billing-address').regionIdBilling(),
                    'cityBilling': viewManager.getSingleton('view/checkout/customer/add-billing-address').cityBilling(),
                    'zipcodeBilling': viewManager.getSingleton('view/checkout/customer/add-billing-address').zipcodeBilling(),
                    'vatBilling': viewManager.getSingleton('view/checkout/customer/add-billing-address').vatBilling(),
                    'regionIdComputedBilling': viewManager.getSingleton('view/checkout/customer/add-billing-address').regionIdComputedBilling,
                };
                viewManager.getSingleton('view/checkout/customer/add-billing-address').temporaryData(temporaryData);
            },

            /* Delete billing address*/
            deleteBillingAddress: function () {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                viewManager.getSingleton('view/checkout/customer/add-billing-address').isShowBillingSummaryForm(false);
                viewManager.getSingleton('view/checkout/customer/add-billing-address').firstNameBilling('');
                viewManager.getSingleton('view/checkout/customer/add-billing-address').lastNameBilling('');
                viewManager.getSingleton('view/checkout/customer/add-billing-address').companyBilling('');
                viewManager.getSingleton('view/checkout/customer/add-billing-address').phoneBilling('');
                viewManager.getSingleton('view/checkout/customer/add-billing-address').street1Billing('');
                viewManager.getSingleton('view/checkout/customer/add-billing-address').street2Billing('');
                viewManager.getSingleton('view/checkout/customer/add-billing-address').countryBilling('');
                viewManager.getSingleton('view/checkout/customer/add-billing-address').regionBilling('');
                viewManager.getSingleton('view/checkout/customer/add-billing-address').regionIdBilling(0);
                viewManager.getSingleton('view/checkout/customer/add-billing-address').cityBilling('');
                viewManager.getSingleton('view/checkout/customer/add-billing-address').zipcodeBilling('');
                viewManager.getSingleton('view/checkout/customer/add-billing-address').vatBilling('');
                if (viewManager.getSingleton('view/checkout/customer/add-billing-address').temporaryData)
                    viewManager.getSingleton('view/checkout/customer/add-billing-address').temporaryData([]);
            },

            /* Delete shipping address*/
            deleteShippingAddress: function () {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').isShowShippingSummaryForm(false);
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').firstNameShipping('');
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').lastNameShipping('');
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').companyShipping('');
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').phoneShipping('');
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').street1Shipping('');
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').street2Shipping('');
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').countryShipping('');
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').regionShipping('');
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').regionIdShipping(0);
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').cityShipping('');
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').zipcodeShipping('');
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').vatShipping('');
                if (viewManager.getSingleton('view/checkout/customer/add-shipping-address').temporaryData)
                    viewManager.getSingleton('view/checkout/customer/add-shipping-address').temporaryData([]);
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').isSameBillingShipping(false)
            },

            /* Get billing address data*/
            getBillingAddressData: function () {
                var data = {};
                data.id = 'nsync' + Date.now();
                data.firstname = this.firstNameBilling();
                data.lastname = this.lastNameBilling();
                data.street = [
                    this.street1Billing(), this.street2Billing()
                ];
                data.telephone = this.phoneBilling();
                data.company = this.companyBilling();
                data.country_id = this.countryBilling();
                data.city = this.cityBilling();
                data.postcode = this.zipcodeBilling();
                data.region_id = this.regionIdBilling();
                data.region = this.regionObjectBilling();
                return data;
            },

            /* Get shipping address data*/
            getShippingAddressData: function () {
                var data = {};
                data.id = 'nsync' + Date.now();
                data.firstname = this.firstNameShipping();
                data.lastname = this.lastNameShipping();
                data.street = [
                    this.street1Shipping(), this.street2Shipping()
                ];
                data.telephone = this.phoneShipping();
                data.company = this.companyShipping();
                data.country_id = this.countryShipping();
                data.city = this.cityShipping();
                data.postcode = this.zipcodeShipping();
                data.region_id = this.regionIdShipping();
                data.region = this.regionObjectShipping();
                return data;
            },

            /* Reset Form*/
            resetFormInfo: function (form) {
                document.getElementById(form).reset();
            },

            /* Show Shipping Address */
            showShippingAddressControl: function () {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                var self = this;
                var shippingAddressForm = $('#form-customer-add-shipping-address-checkout');
                var countryId = shippingAddressForm.find('.country_id');
                var regionList = shippingAddressForm.find('.region_id');
                var region = shippingAddressForm.find('.region');
                countryId.regionUpdater({
                    regionList: regionList,
                    regionInput: region,
                    regionJson: JSON.parse(window.webposConfig.regionJson)
                });

                if (viewManager.getSingleton('view/checkout/customer/add-shipping-address').firstNameShipping() == '') {
                    viewManager.getSingleton('view/checkout/customer/add-shipping-address').firstNameShipping(self.firstNameCustomer());
                }
                if (viewManager.getSingleton('view/checkout/customer/add-shipping-address').lastNameShipping() == '') {
                    viewManager.getSingleton('view/checkout/customer/add-shipping-address').lastNameShipping(self.lastNameCustomer());
                }
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').shippingAddressTitle(Translate('Add Shipping Address'));
                viewManager.getSingleton('view/checkout/customer/add-shipping-address').leftButton(Translate('Cancel'));
                this.formAddCustomerCheckout.removeClass('fade-in');
                this.formAddCustomerCheckout.removeClass('show');
                this.formAddCustomerCheckout.addClass('fade');

                this.formAddShippingAddressCheckout.addClass('fade-in');
                this.formAddShippingAddressCheckout.addClass('show');
                this.formAddShippingAddressCheckout.removeClass('fade');
            },

            /* Show Billing Address*/
            showBillingAddressControl: function () {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                var self = this;
                var billingAddressForm = $('#form-customer-add-billing-address-checkout');
                var countryId = billingAddressForm.find('.country_id');
                var regionList = billingAddressForm.find('.region_id');
                var region = billingAddressForm.find('.region');
                countryId.regionUpdater({
                    regionList: regionList,
                    regionInput: region,
                    regionJson: JSON.parse(window.webposConfig.regionJson)
                });
                if (viewManager.getSingleton('view/checkout/customer/add-billing-address').firstNameBilling() == '') {
                    viewManager.getSingleton('view/checkout/customer/add-billing-address').firstNameBilling(self.firstNameCustomer());
                }
                if (viewManager.getSingleton('view/checkout/customer/add-billing-address').lastNameBilling() == '') {
                    viewManager.getSingleton('view/checkout/customer/add-billing-address').lastNameBilling(self.lastNameCustomer());
                }
                viewManager.getSingleton('view/checkout/customer/add-billing-address').billingAddressTitle(Translate('Add Billing Address'));
                viewManager.getSingleton('view/checkout/customer/add-billing-address').leftButton(Translate('Cancel'));
                this.formAddCustomerCheckout.removeClass('fade-in');
                this.formAddCustomerCheckout.removeClass('show');
                this.formAddCustomerCheckout.addClass('fade');
                billingAddressForm.addClass('fade-in');
                billingAddressForm.addClass('show');
                billingAddressForm.removeClass('fade');
                $('.wrap-backover').show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            }
        });
    }
);
