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
        "helper/general",
        'model/customer/customer/billing-address',
        'model/customer/customer/shipping-address',
        'model/customer/customer/edit-customer',
        'model/customer/customer-factory',
        'model/checkout/checkout',
        'action/notification/add-notification',
        'action/checkout/select-customer-checkout',
        'eventManager',
        'mage/validation'
    ],
    function ($,
              ko,
              generalHelper,
              billingModel,
              shippingModel,
              editCustomerModel,
              CustomerFactory,
              checkoutModel,
              addNotification,
              selectCustomer,
              Event) {
        "use strict";
        return {
            /* Binding billing address information in create customer form*/
            customerGroupArray: ko.observableArray(window.webposConfig.customerGroup),
            defaultCustomerGroup: ko.observable(window.webposConfig.defaultCustomerGroup),
            isAddCustomer: ko.observable(false),
            /* Create Customer Form*/
            firstNameCustomer: ko.observable(''),
            lastNameCustomer: ko.observable(''),
            emailCustomer: ko.observable(''),
            groupCustomer: ko.observable(window.webposConfig.defaultCustomerGroup),
            vatCustomer: ko.observable(''),
            isSubscriberCustomer: ko.observable(false),
            /* End Form*/

            deleteBillingAddress: function () {
                billingModel.isShowBillingSummaryForm(false);
                billingModel.firstNameBilling('');
                billingModel.lastNameBilling('');
                billingModel.companyBilling('');
                billingModel.phoneBilling('');
                billingModel.street1Billing('');
                billingModel.street2Billing('');
                billingModel.countryBilling(window.webposConfig.defaultCountry);
                billingModel.regionBilling('');
                billingModel.regionIdBilling(0);
                billingModel.cityBilling('');
                billingModel.zipcodeBilling('');
                billingModel.vatBilling('');
            },

            deleteShippingAddress: function () {
                shippingModel.isShowShippingSummaryForm(false);
                shippingModel.firstNameShipping('');
                shippingModel.lastNameShipping('');
                shippingModel.companyShipping('');
                shippingModel.phoneShipping('');
                shippingModel.street1Shipping('');
                shippingModel.street2Shipping('');
                shippingModel.countryShipping(window.webposConfig.defaultCountry);
                shippingModel.regionShipping('');
                shippingModel.regionIdShipping(0);
                shippingModel.cityShipping('');
                shippingModel.zipcodeShipping('');
                shippingModel.vatShipping('');
            },

            /* Get billing address data*/
            getBillingAddressData: function () {
                var data = {};
                data.id = Date.now();
                data.firstname = billingModel.firstNameBilling();
                data.lastname = billingModel.lastNameBilling();
                data.street = [
                    billingModel.street1Billing(), billingModel.street2Billing()
                ];
                data.telephone = billingModel.phoneBilling();
                data.company = billingModel.companyBilling();
                data.country_id = billingModel.countryBilling();
                data.city = billingModel.cityBilling();
                data.postcode = billingModel.zipcodeBilling();
                data.region_id = billingModel.regionIdBilling();
                data.region = billingModel.regionObjectBilling();
                data.vat_id = billingModel.vatBilling();
                return data;
            },

            /* Get shipping address data*/
            getShippingAddressData: function () {
                var data = {};
                data.id = Date.now();
                data.firstname = shippingModel.firstNameShipping();
                data.lastname = shippingModel.lastNameShipping();
                data.street = [
                    shippingModel.street1Shipping(), shippingModel.street2Shipping()
                ];
                data.telephone = shippingModel.phoneShipping();
                data.company = shippingModel.companyShipping();
                data.country_id = shippingModel.countryShipping();
                data.city = shippingModel.cityShipping();
                data.postcode = shippingModel.zipcodeShipping();
                data.region_id = shippingModel.regionIdShipping();
                data.region = shippingModel.regionObjectShipping();
                data.vat_id = shippingModel.vatShipping();
                return data;
            },

            save: function () {
                var data = {};
                var self = this;
                data.id = 'notsync_' + this.emailCustomer();
                data.firstname = this.firstNameCustomer();
                data.lastname = this.lastNameCustomer();
                data.full_name = this.firstNameCustomer() + ' ' + this.lastNameCustomer();
                data.email = this.emailCustomer();
                data.group_id = this.groupCustomer();
                data.subscriber_status = this.isSubscriberCustomer();
                data.taxvat = this.vatCustomer();
                data.addresses = [];
                if (!billingModel.isShowBillingSummaryForm() && !shippingModel.isShowShippingSummaryForm()) {
                    data.addresses = [];
                    checkoutModel.saveShippingAddress({
                        'id': 0,
                        'firstname': data.firstname,
                        'lastname': data.lastname
                    });
                    checkoutModel.saveBillingAddress({
                        'id': 0,
                        'firstname': data.firstname,
                        'lastname': data.lastname
                    });
                } else {
                    var shippingAddressData = this.getShippingAddressData();
                    var billingAddressData = this.getBillingAddressData();
                    if (shippingModel.isSameBillingShipping()) {
                        shippingAddressData.default_billing = true;
                        shippingAddressData.default_shipping = true;
                        data.addresses.push(shippingAddressData);
                        checkoutModel.saveBillingAddress(shippingAddressData);
                        checkoutModel.saveShippingAddress(shippingAddressData);
                    } else {
                        if (shippingModel.isShowShippingSummaryForm()) {
                            shippingAddressData.default_shipping = true;
                            data.addresses.push(shippingAddressData);
                            checkoutModel.saveShippingAddress(shippingAddressData);
                            if (billingModel.isShowBillingSummaryForm()) {
                                checkoutModel.saveBillingAddress(this.getBillingAddressData());
                            } else {
                                checkoutModel.saveBillingAddress({
                                    'id': 0,
                                    'firstname': data.firstname,
                                    'lastname': data.lastname
                                });
                            }
                        }
                        if (billingModel.isShowBillingSummaryForm()) {
                            billingAddressData.default_billing = true;
                            data.addresses.push(billingAddressData);
                            checkoutModel.saveBillingAddress(billingAddressData);
                            if (shippingModel.isShowShippingSummaryForm()) {
                                checkoutModel.saveShippingAddress(shippingAddressData);
                            } else {
                                checkoutModel.saveShippingAddress({
                                    'id': 0,
                                    'firstname': data.firstname,
                                    'lastname': data.lastname
                                });
                            }
                        }
                        if (!billingModel.isShowBillingSummaryForm() && !shippingModel.isShowShippingSummaryForm()) {
                            checkoutModel.saveShippingAddress({
                                'id': 0,
                                'firstname': data.firstname,
                                'lastname': data.lastname
                            });
                            checkoutModel.saveBillingAddress({
                                'id': 0,
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
                    var deferred;
                    $('#form-customer-add-customer-checkout .indicator').show();
                    if (generalHelper.isOnlineCheckout()) {
                        deferred = CustomerFactory.get().setMode('online')
                            .getCollection().addFieldToFilter('email', this.emailCustomer(), 'eq')
                            .load();
                    } else {
                        deferred = CustomerFactory.get().setMode('offline')
                            .getCollection().addFieldToFilter('email', this.emailCustomer(), 'eq')
                            .load();
                    }

                    deferred.done(function (filterData) {
                        var items = filterData.items;
                        if (items.length > 0) {
                            $('#form-customer-add-customer-checkout .indicator').hide();
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
                                        editCustomerModel.billingAddressId(value.id);
                                        editCustomerModel.setBillingPreviewData(value);
                                        editCustomerModel.isShowPreviewBilling(true);
                                        isSetBilling = true;
                                    }
                                    if (value.default_shipping) {
                                        editCustomerModel.shippingAddressId(value.id);
                                        editCustomerModel.setShippingPreviewData(value);
                                        editCustomerModel.isShowPreviewShipping(true);
                                        isSetShipping = true;
                                    }
                                });
                                if (!isSetBilling) {
                                    editCustomerModel.isShowPreviewBilling(false);
                                }

                                if (!isSetShipping) {
                                    editCustomerModel.isShowPreviewShipping(false);
                                }
                                self.isAddCustomer(true);
                                Event.dispatch('customer_pull_after');
                                self.cancelCustomerForm();
                                $.toaster(
                                    {
                                        priority: 'success',
                                        title: generalHelper.__('Success'),
                                        message: generalHelper.__('The customer is saved successfully.')
                                    }
                                );
                            });

                        }
                    });
                }
            },

            cancelCustomerForm: function () {
                var customerForm = $('#form-customer-add-customer-checkout');
                customerForm.removeClass('fade-in');
                customerForm.removeClass('show');
                customerForm.addClass('fade');
                // customerForm.validation();
                // customerForm.validation('clearError');
                this.resetFormInfo('form-customer-add-customer-checkout');
                this.deleteShippingAddress();
                this.deleteBillingAddress();
                billingModel.isShowBillingSummaryForm(false);
                shippingModel.isShowShippingSummaryForm(false);
                shippingModel.isSameBillingShipping(true);
                $('.pos-overlay').removeClass('active');
                $('.notification-bell').show();
                $('#c-button--push-left').show();
                $('.wrap-backover').hide();
                $('#form-customer-add-customer-checkout .indicator').hide();
            },

            /* Validate Customer Form*/
            validateCustomerForm: function () {
                var form = '#form-customer-add-customer-checkout';
                return $(form).validation({}) && $(form).validation('isValid');
            },

            /* Reset Form*/
            resetFormInfo: function (form) {
                document.getElementById(form).reset();
                $('#' + form + ' #customer_group').val(window.webposConfig.defaultCustomerGroup);
            }
        };
    }
);