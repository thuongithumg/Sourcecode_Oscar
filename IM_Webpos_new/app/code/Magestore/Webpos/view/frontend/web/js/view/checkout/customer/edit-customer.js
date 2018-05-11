/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/model/customer/customer-factory',
        'Magento_Ui/js/modal/confirm',
        'Magestore_Webpos/js/model/customer/current-customer',
        'Magestore_Webpos/js/action/checkout/select-billing-address',
        'Magestore_Webpos/js/action/checkout/select-shipping-address',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/template',
        'mage/translate',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/helper/general',
        "mage/mage",
        "mage/validation",
        'Magestore_Webpos/js/bootstrap/bootstrap',
        'Magestore_Webpos/js/bootstrap/bootstrap-switch',
        'Magestore_Webpos/js/lib/jquery.toaster',
        
    ],
    function (
        $,
        ko,
        colGrid,
        CustomerFactory,
        confirm,
        currentCustomer,
        selectBilling,
        selectShipping,
        eventManager,
        addNotification,
        mageTemplate,
        Translate,
        checkoutModel,
        CartModel,
        Helper
    ) {
        "use strict";
        return colGrid.extend({
            /* Add address object*/
            addAddress: ko.observable(),
            /* Observable items*/
            items: ko.observableArray([]),
            /* Observable customer first name*/
            firstName: ko.observable(''),
            /* Observable customer last name*/
            lastName: ko.observable(''),
            /* Observable customer email*/
            email: ko.observable(''),
            /* Observable customer group id*/
            group_id: ko.observable(''),
            /* Observable customer group array*/
            customerGroupArray: ko.observableArray(window.webposConfig.customerGroup),
            /* Observable customer address array*/
            addressArray: ko.observableArray([]),
            /* Observable customer subscriber or not*/
            isSubscriberCustomer: ko.observable(true),
            /* Observable choose billing address and shipping address id */
            billingAddressId: ko.observable(0),
            shippingAddressId: ko.observable(0),
            currentEditAddressId: ko.observable(null),

            dob: ko.observable(''),
            taxvat: ko.observable(''),
            gender: ko.observable(''),
            showDob: window.webposConfig['customer/address/dob_show'] != null,
            showTaxvat: window.webposConfig['customer/address/taxvat_show'] != null,
            showGender: window.webposConfig['customer/address/gender_show'] != null,
            requireDob: window.webposConfig['customer/address/dob_show'] == 'req',
            requireTaxvat: window.webposConfig['customer/address/taxvat_show'] == 'req',
            requireGender: window.webposConfig['customer/address/gender_show'] == 'req',
            genderArray: [
                {id:1,code:'Male'},
                {id:2,code:'Female'},
                {id:1,code:'Not Specified'}
            ],

            /* variable for control UI*/
            editCustomerForm: $('#form-edit-customer'),
            overlay:  $('.wrap-backover').hide(),

            addAddressForm: $('#form-customer-add-address-checkout'),
            regionTemplate: '<option value="<%- data.value %>" data-region-code="<%- data.code %>" title="<%- data.title %>" <% if (data.isSelected) { %>selected="selected"<% } %>>' +
            '<%- data.title %>' +
            '</option>',

            /* Preview Billing Data*/
            previewBillingFirstname: ko.observable(''),
            previewBillingLastname: ko.observable(''),
            previewBillingCompany: ko.observable(''),
            previewBillingPhone: ko.observable(''),
            previewBillingStreet1: ko.observable(''),
            previewBillingStreet2: ko.observable(''),
            previewBillingCountry: ko.observable(''),
            previewBillingRegion: ko.observable(''),
            previewBillingRegionId: ko.observable(0),
            previewBillingCity: ko.observable(''),
            previewBillingPostCode: ko.observable(''),
            previewBillingVat: ko.observable(''),
            isShowPreviewBilling: ko.observable(false),
            /* End Preview Billing Data*/

            /* Preview Shipping Data*/
            previewShippingFirstname: ko.observable(''),
            previewShippingLastname: ko.observable(''),
            previewShippingCompany: ko.observable(''),
            previewShippingPhone: ko.observable(''),
            previewShippingStreet1: ko.observable(''),
            previewShippingStreet2: ko.observable(''),
            previewShippingCountry: ko.observable(''),
            previewShippingRegion: ko.observable(''),
            previewShippingRegionId: ko.observable(0),
            previewShippingCity: ko.observable(''),
            previewShippingPostCode: ko.observable(''),
            previewShippingVat: ko.observable(''),
            isShowPreviewShipping: ko.observable(false),
            /* End Preview Shipping Data*/

            /* Observable to edit address type*/
            editAddressType: ko.observable(null),

            /* Save online or not*/
            isChangeCustomerInfo: ko.observable(false),

            currentEditAddressType: ko.observable(null),

            /* Template for knockout js*/
            defaults: {
                template: 'Magestore_Webpos/checkout/customer/edit-customer'
            },

            /* Auto run when call */
            initialize: function () {
                this._super();
                var self = this;
                self.addressArrayDisplay = ko.pureComputed(function () {
                    var newAddressArray = [];
                    ko.utils.arrayMap(self.addressArray(), function(item) {
                        newAddressArray.push(item);
                    });
                    newAddressArray.push({
                        'id' : 0,
                        'label' : Translate('Use Store Address')
                    });
                    return newAddressArray;
                });
                var arrayObservable = ['#first_name_input', '#last_name_input', '#customer_email_input',
                    '.customer_group'];
                $.each(arrayObservable, function (index, value) {
                   $(value).change(function () {
                       self.isChangeCustomerInfo(true);
                   })
                });
                $(".dob").calendar({
                    controlType: 'select',
                    dateFormat: "M/d/Y",
                    showTime: false,
                    maxDate: "-1d", changeMonth: true, changeYear: true
                });
            },

            /* load data*/
            loadData: function (data) {
                var self = this;
                self.group_id(data.group_id);
                self.firstName(data.firstname);
                self.lastName(data.lastname);
                self.email(data.email);
                self.dob(data.dob);
                self.taxvat(data.taxvat);
                self.gender(data.gender);
                if (typeof (data.addresses) != 'undefined') {
                    self.addressArray(data.addresses);
                } else {
                    self.addressArray([]);
                }
                self.billingAddressId(0);
                self.shippingAddressId(0);
                self.isSubscriberCustomer(false);
            },

            /* Control UI for hide customer form */
            hideCustomerForm: function () {
                var self = this;
                self.editCustomerForm.addClass('fade');
                self.editCustomerForm.removeClass('fade-in');
                self.editCustomerForm.removeClass('show');
                self.isChangeCustomerInfo(false);
                self.addAddress.call().resetAddressForm();
                $('.wrap-backover').hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
            },

            /* Save customer form */
            saveCustomerForm: function () {
                var self = this;
                if (this.validateEditCustomerForm()) {
                    var currentData = currentCustomer.data();
                    currentData.firstname = this.firstName();
                    currentData.lastname = this.lastName();
                    currentData.email = this.email();
                    currentData.group_id = this.group_id();
                    currentData.subscriber_status = this.isSubscriberCustomer();
                    currentData.full_name = this.firstName() + ' ' + this.lastName();
                    CartModel.addCustomer(currentData);
                    var customerDeferred = CustomerFactory.get().load(currentData.id);
                    customerDeferred.done(function (data) {
                        var addressData = data.addresses;
                        if(addressData && addressData.length > 0) {
                            $.each(addressData, function (index, value) {
                                var address = addressData[index];
                                address.default_billing = false;
                                address.default_shipping = false;
                                if (value.id == self.billingAddressId() && value.id != 0) {

                                    if (typeof address.default_billing == 'undefined' || !address.default_billing) {
                                        address.default_billing = true;
                                        self.isChangeCustomerInfo(true);
                                    }
                                }
                                if (value.id == self.shippingAddressId() && value.id != 0) {
                                    if (typeof address.default_shipping == 'undefined' || !address.default_shipping) {
                                        address.default_shipping = true;
                                        self.isChangeCustomerInfo(true);
                                    }
                                }
                                addressData[index] = address;
                            });
                        }
                        currentData.addresses = addressData;
                        currentCustomer.fullName(self.firstName() + ' ' + self.lastName());

                        if (typeof currentData['columns'] != 'undefined') {
                            delete currentData['columns'];
                        }

                        var billingAddressId = self.billingAddressId();
                        var shippingAddressId = self.shippingAddressId();

                        // selectBilling(self.billingAddressId());
                        // selectShipping(self.shippingAddressId());
                        var currentCustomerId = currentData.id;
                        //if (self.isChangeCustomerInfo()) {
                        var customerCollection =  CustomerFactory.get().getCollection();
                        var mode = (Helper.isUseOnline('customers'))?'online':'offline';
                        var idFieldName = (Helper.isUseOnline('customers'))?'entity_id':'id';
                        var deferred = customerCollection.setMode(mode).addFieldToFilter('email', self.email(), 'eq')
                                .addFieldToFilter(idFieldName, currentCustomerId, 'neq')
                                .load();
                            deferred.done(function (data) {
                                var items = data.items;
                                if (items.length > 0) {
                                    addNotification(Translate('The customer email is existed.'), true, 'danger', 'Error');
                                } else {

                                    delete currentData['default_billing'];
                                    delete currentData['default_shipping'];


                                    var deferred = CustomerFactory.get().setData(currentData).setPush(true).save();

                                    deferred.done(function (customerDataAfterSave) {
                                        var allAddress = customerDataAfterSave.addresses;
                                        currentCustomer.setData(customerDataAfterSave);
                                        if (billingAddressId!=0) {
                                            var billingAddress = {};
                                            $.each(allAddress, function (index, value) {
                                                if (typeof (value.id) != 'undefined' && value.id == billingAddressId) {
                                                    billingAddress = allAddress[index];
                                                }
                                            });
                                            checkoutModel.saveBillingAddress(billingAddress);
                                        } else {
                                            if (customerDataAfterSave.id) {
                                                checkoutModel.saveBillingAddress({
                                                    'id' : 0,
                                                    'firstname': customerDataAfterSave.firstname,
                                                    'lastname': customerDataAfterSave.lastname
                                                });
                                            } else {
                                                checkoutModel.saveBillingAddress({
                                                    'id' : 0,
                                                    'firstname': '',
                                                    'lastname': ''
                                                });
                                            }
                                        }
                                        if (shippingAddressId!=0) {
                                            var shippingAddress = {};
                                            $.each(allAddress, function (index, value) {
                                                if (typeof (value.id) != 'undefined' && value.id == shippingAddressId) {
                                                    shippingAddress = allAddress[index];
                                                }
                                            });
                                            checkoutModel.saveShippingAddress(shippingAddress);
                                        } else {
                                            if (customerDataAfterSave.id) {
                                                checkoutModel.saveShippingAddress({
                                                    'id' : 0,
                                                    'firstname': customerDataAfterSave.firstname,
                                                    'lastname': customerDataAfterSave.lastname
                                                });
                                            } else {
                                                checkoutModel.saveShippingAddress({
                                                    'id' : 0,
                                                    'firstname': '',
                                                    'lastname': ''
                                                });
                                            }
                                        }
                                        eventManager.dispatch('customer_pull_after',[]);
                                        $.toaster(
                                            {
                                                priority: 'success',
                                                title: Translate('Success'),
                                                message: Translate('The customer is saved successfully.')
                                            }
                                        );
                                        if(CartModel.isOnCheckoutPage() && Helper.isUseOnline('checkout')){
                                            CartModel.saveCartOnline();
                                        }
                                    });
                                }
                            });
                        //}

                    });


                    self.hideCustomerForm();
                }

            },

            /* Control UI show add address form*/
            showAddress: function () {
                var self = this;
                this.addAddressForm.find('.country_id').regionUpdater({
                    regionList: self.addAddressForm.find('.region_id'),
                    regionInput: self.addAddressForm.find('.region'),
                    regionJson: JSON.parse(window.webposConfig.regionJson)
                });
                this.editCustomerForm.addClass('fade');
                this.editCustomerForm.removeClass('fade-in');
                this.editCustomerForm.removeClass('show');
                this.addAddressForm.addClass('fade-in');
                this.addAddressForm.addClass('show');
                this.addAddressForm.removeClass('fade');
                self.addAddress.call().addressTitle(Translate('Add Address'));
                self.addAddress.call().resetAddressForm();
                self.addAddress.call().firstName(currentCustomer.data().firstname);
                self.addAddress.call().lastName(currentCustomer.data().lastname);

            },
            /* Validate edit customer form*/
            validateEditCustomerForm: function () {
                var form = '#form-edit-customer';
                return $(form).validation({}) && $(form).validation('isValid');
            },
            /* Control UI show customer form*/
            showCustomerEditForm: function () {
                this.editCustomerForm.addClass('fade');
                this.editCustomerForm.removeClass('fade-in');
                this.editCustomerForm.removeClass('show');
                this.overlay.hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
            },

            /* Show Shipping Preview */
            showShippingPreview: function () {
                var self = this;
                this.addAddressForm.find('.country_id').regionUpdater({
                    regionList: self.addAddressForm.find('.region_id'),
                    regionInput: self.addAddressForm.find('.region'),
                    regionJson: JSON.parse(window.webposConfig.regionJson)
                });

                if (self.shippingAddressId() != 0) {
                    $.each(self.addressArray(), function (index, value) {
                        if (value.id && value.id == self.shippingAddressId()) {
                            self.setShippingPreviewData(value);
                            self.addAddress.call().firstName(value.firstname);
                            self.addAddress.call().lastName(value.lastname);
                            self.addAddress.call().company(value.company);
                            self.addAddress.call().phone(value.telephone);
                            self.addAddress.call().street1(value.street[0]);
                            self.addAddress.call().street2(value.street[1]);
                            self.addAddress.call().country(value.country_id);
                            self.addAddress.call().region(value.region.region);
                            self.addAddress.call().region_id(value.region_id);
                            self.addAddress.call().city(value.city);
                            self.addAddress.call().zipcode(value.postcode);
                            self.addAddress.call().vatId(value.vat_id);

                            self.updateRegionForForm(value);
                            self.isShowPreviewShipping(true);
                        }
                    });
                } else {
                    self.isShowPreviewShipping(false);
                }
            },

            /* Show Billing Preview */
            showBillingPreview: function () {
                var self = this;
                this.addAddressForm.find('.country_id').regionUpdater({
                    regionList: self.addAddressForm.find('.region_id'),
                    regionInput: self.addAddressForm.find('.region'),
                    regionJson: JSON.parse(window.webposConfig.regionJson)
                });

                if (self.billingAddressId() != 0) {
                    $.each(self.addressArray(), function (index, value) {
                        if (value.id == self.billingAddressId()) {
                            self.setBillingPreviewData(value);
                            self.addAddress.call().firstName(value.firstname);
                            self.addAddress.call().lastName(value.lastname);
                            self.addAddress.call().company(value.company);
                            self.addAddress.call().phone(value.telephone);
                            self.addAddress.call().street1(value.street[0]);
                            self.addAddress.call().street2(value.street[1]);
                            self.addAddress.call().country(value.country_id);
                            self.addAddress.call().region(value.region.region);
                            self.addAddress.call().region_id(value.region_id);
                            self.addAddress.call().city(value.city);
                            self.addAddress.call().zipcode(value.postcode);
                            self.addAddress.call().vatId(value.vat_id);

                            self.updateRegionForForm(value);
                            self.isShowPreviewBilling(true);
                        }
                    });
                } else {
                    self.isShowPreviewBilling(false);
                }
            },

            /* Edit Shipping Preview */
            editShippingPreview: function () {
                var self = this;
                self.currentEditAddressId(self.shippingAddressId());
                var data = {
                    'firstname' : self.previewShippingFirstname(),
                    'lastname' : self.previewShippingLastname(),
                    'company' : self.previewShippingCompany(),
                    'telephone' : self.previewShippingPhone(),
                    'street' : [
                        self.previewShippingStreet1(),
                        self.previewShippingStreet2()
                    ],
                    'country_id' : self.previewShippingCountry(),
                    'region' : {
                        'region':  self.previewShippingRegion()
                    },
                    'region_id' : self.previewShippingRegionId(),
                    'city' : self.previewShippingCity(),
                    'postcode' : self.previewShippingPostCode(),
                    'vat_id': self.previewShippingVat()
                };
                self.addAddress.call().firstName(data.firstname);
                self.addAddress.call().lastName(data.lastname);
                self.addAddress.call().company(data.company);
                self.addAddress.call().phone(data.telephone);
                self.addAddress.call().street1(data.street[0]);
                self.addAddress.call().street2(data.street[1]);
                self.addAddress.call().country(data.country_id);
                // self.addAddress.call().region(data.region.region);
                // self.addAddress.call().region_id(data.region_id);
                self.addAddress.call().city(data.city);
                self.addAddress.call().zipcode(data.postcode);
                self.addAddress.call().vatId(data.vat_id);
                self.currentEditAddressType('shipping');

                /* Region updater for show edit address popup*/
                self.updateRegionForForm(data);
                self.addAddress.call().addressTitle(Translate('Edit Address'));
                this.editCustomerForm.addClass('fade');
                this.editCustomerForm.removeClass('fade-in');
                this.editCustomerForm.removeClass('show');
                this.addAddressForm.addClass('fade-in');
                this.addAddressForm.addClass('show');
                this.addAddressForm.removeClass('fade');
                /* End region updater */

            },

            /* Edit Billing Preview */
            editBillingPreview: function () {
                var self = this;
                self.currentEditAddressId(self.billingAddressId());
                var data = {
                    'firstname' : self.previewBillingFirstname(),
                    'lastname' : self.previewBillingLastname(),
                    'company' : self.previewBillingCompany(),
                    'telephone' : self.previewBillingPhone(),
                    'street' : [
                        self.previewBillingStreet1(),
                        self.previewBillingStreet2()
                    ],
                    'country_id' : self.previewBillingCountry(),
                    'region' : {
                        'region':  self.previewBillingRegion()
                    },
                    'region_id' : self.previewBillingRegionId(),
                    'city' : self.previewBillingCity(),
                    'postcode' : self.previewBillingPostCode(),
                    'vat_id': self.previewBillingVat()
                };
                self.addAddress.call().firstName(data.firstname);
                self.addAddress.call().lastName(data.lastname);
                self.addAddress.call().company(data.company);
                self.addAddress.call().phone(data.telephone);
                self.addAddress.call().street1(data.street[0]);
                self.addAddress.call().street2(data.street[1]);
                self.addAddress.call().country(data.country_id);
                self.addAddress.call().city(data.city);
                self.addAddress.call().zipcode(data.postcode);
                self.addAddress.call().vatId(data.vat_id);
                self.currentEditAddressType('billing');
                /* Region updater for show edit address popup*/
                self.updateRegionForForm(data);
                /* End region updater */
                self.addAddress.call().addressTitle(Translate('Edit Address'));
                this.editCustomerForm.addClass('fade');
                this.editCustomerForm.removeClass('fade-in');
                this.editCustomerForm.removeClass('show');
                this.addAddressForm.addClass('fade-in');
                this.addAddressForm.addClass('show');
                this.addAddressForm.removeClass('fade');
            },

            /* Delete Shipping Preview*/
            deleteShippingPreview: function () {
                var self = this;
                confirm({
                    content: Translate('Do you want to delete address?'),
                    actions: {
                        confirm: function () {
                            var currentData = currentCustomer.data();
                            var currentEmail = currentData.email;
                            var customerCollection = CustomerFactory.get().getCollection().addFieldToFilter('email', currentEmail, 'eq')
                                .load();
                            customerCollection.done(function (data) {
                                var collectionData = data.items;
                                if (collectionData.length > 0) {
                                    var addressIndex = -1;
                                    var customerModelData = collectionData[0];
                                    var address = customerModelData.addresses;
                                    $.each(address, function (index, value) {
                                        if (value.id == self.shippingAddressId()) {
                                            addressIndex = index;
                                        }
                                    });
                                    address.splice(addressIndex, 1);
                                    customerModelData.addresses = address;
                                    var customerDeferred = CustomerFactory.get().setMode('offline').setData(customerModelData).setPush(true).save();
                                    customerDeferred.done(function (data) {
                                        currentCustomer.setData(data);
                                        self.addressArray(address);
                                        if (self.billingAddressId() == self.shippingAddressId()) {
                                            self.billingAddressId(0);
                                            self.showBillingPreview();
                                        }
                                        self.shippingAddressId(0);
                                        self.showShippingPreview();
                                        if(Helper.isUseOnline('customers')){
                                            CustomerFactory.get().setMode('online');
                                        }
                                    });
                                }
                            });
                        },
                        always: function (event) {
                            event.stopImmediatePropagation();
                        }
                    }
                });
            },

            /* Delete Billing Preview*/
            deleteBillingPreview: function () {
                var self = this;
                confirm({
                    content: Translate('Do you want to delete address?'),
                    actions: {
                        confirm: function () {
                            var currentData = currentCustomer.data();
                            var currentEmail = currentData.email;
                            var customerCollection = CustomerFactory.get().getCollection().addFieldToFilter('email', currentEmail, 'eq')
                                .load();
                            customerCollection.done(function (data) {
                                var collectionData = data.items;
                                if (collectionData.length > 0) {
                                    var addressIndex = -1;
                                    var customerModelData = collectionData[0];
                                    var address = customerModelData.addresses;
                                    $.each(address, function (index, value) {
                                        if (value.id == self.billingAddressId()) {
                                            addressIndex = index;
                                        }
                                    });
                                    address.splice(addressIndex, 1);
                                    customerModelData.addresses = address;
                                    var customerDeferred = CustomerFactory.get().setMode('offline').setData(customerModelData).setPush(true).save();
                                    customerDeferred.done(function (data) {
                                        currentCustomer.setData(data);
                                        self.addressArray(address);
                                        if (self.shippingAddressId() == self.billingAddressId()) {
                                            self.shippingAddressId(0);
                                            self.showShippingPreview();
                                        }
                                        self.billingAddressId(0);
                                        self.showBillingPreview();
                                        if(Helper.isUseOnline('customers')){
                                            CustomerFactory.get().setMode('online');
                                        }
                                    });
                                }
                            });
                        },
                        always: function (event) {
                            event.stopImmediatePropagation();
                        }
                    }
                });

            },

            /* Region updater for show edit address popup*/
            updateRegionForForm: function (data) {
                var regionJson = JSON.parse(window.webposConfig.regionJson);
                var regionList = this.addAddressForm.find('.region_id');
                var regionInput = this.addAddressForm.find('.region');
                var requiredLabel = regionList.parents('div.field');
                this._checkRegionRequired(data.country_id);
                if (typeof regionJson[data.country_id] != 'undefined') {
                    this.regionTmpl = mageTemplate(this.regionTemplate);
                    this._removeSelectOptions(regionList);
                    $.each(regionJson[data.country_id], $.proxy(function (key, value) {
                        this._renderSelectOption(regionList, key, value);
                    }, this));

                    if (this.isRegionRequired) {
                        regionList.addClass('required-entry').removeAttr('disabled');
                        requiredLabel.addClass('required');
                    } else {
                        regionList.removeClass('required-entry validate-select').removeAttr('data-validate');
                        requiredLabel.removeClass('required');
                    }


                    regionList.prop('disabled', false).show();
                    regionList.val(data.region_id);
                    regionInput.prop('disabled', true).hide();
                } else {
                    if (this.isRegionRequired) {
                        regionInput.addClass('required-entry').removeAttr('disabled');
                        requiredLabel.addClass('required');
                    }

                    regionInput.val(data.region.region);
                    regionList.prop('disabled', true).hide();
                    regionInput.prop('disabled', false).show();
                }
                regionInput.removeAttr('disabled');
            },

            /* Render select options for region*/
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

            /* Remove select options */
            _removeSelectOptions: function (selectElement) {
                selectElement.find('option').each(function (index) {
                    if ($(this).val()) {
                        $(this).remove();
                    }
                });
            },
            /**
             * Check if the selected country has a mandatory region selection
             *
             * @param {String} country - Code of the country - 2 uppercase letter for country code
             * @private
             */
            _checkRegionRequired: function (country) {
                var self = this;
                this.isRegionRequired = false;
                var regionJson = JSON.parse(window.webposConfig.regionJson);

                $.each(regionJson.config.regions_required, function (index, elem) {
                    if (elem == country) {
                        self.isRegionRequired = true;
                    }
                });
            },

            /* Set Data for billing preview */
            setBillingPreviewData: function (data) {
                var self = this;
                self.previewBillingFirstname(data.firstname);
                self.previewBillingLastname(data.lastname);
                self.previewBillingCompany(data.company);
                self.previewBillingPhone(data.telephone);
                self.previewBillingStreet1(data.street[0]);
                self.previewBillingStreet2(data.street[1]);
                self.previewBillingCountry(data.country_id);
                self.previewBillingRegion(data.region.region);
                self.previewBillingRegionId(data.region_id);
                self.previewBillingCity(data.city);
                self.previewBillingPostCode(data.postcode);
                self.previewBillingVat(data.vat_id);
            },

            /* Set Data for shipping preview */
            setShippingPreviewData: function (data) {
                var self = this;
                self.previewShippingFirstname(data.firstname);
                self.previewShippingLastname(data.lastname);
                self.previewShippingCompany(data.company);
                self.previewShippingPhone(data.telephone);
                self.previewShippingStreet1(data.street[0]);
                self.previewShippingStreet2(data.street[1]);
                self.previewShippingCountry(data.country_id);
                self.previewShippingRegion(data.region.region);
                self.previewShippingRegionId(data.region_id);
                self.previewShippingCity(data.city);
                self.previewShippingPostCode(data.postcode);
                self.previewShippingVat(data.vat_id);
            }
        });
    }
);
