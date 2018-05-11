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
        'Magestore_Webpos/js/model/google-autocomplete-address',
        'Magestore_Webpos/js/model/customer/customer-factory',
        'Magestore_Webpos/js/model/customer/group-factory',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/directory/country',
        'Magestore_Webpos/js/model/customer/complain',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/action/checkout/select-customer-checkout',
        'Magestore_Webpos/js/helper/staff',
        'Magestore_Webpos/js/action/notification/add-notification',
        'Magestore_Webpos/js/model/event-manager',
        'mage/template',
        'mage/translate',
        'Magestore_Webpos/js/action/checkout/select-billing-address',
        'Magestore_Webpos/js/action/checkout/select-shipping-address',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magento_Ui/js/modal/confirm',
        'Magestore_Webpos/js/model/customer/current-customer',
        'Magestore_Webpos/js/region-updater',
        'mage/mage',
        "mage/validation",
        'Magestore_Webpos/js/bootstrap/bootstrap',
        'Magestore_Webpos/js/bootstrap/bootstrap-switch',
        'jquery/ui',
        'jquery/validate',
        'Magestore_Webpos/js/lib/jquery.toaster',

    ],
    function (
        require,
        $,
        ko,
        ViewManager,
        uiComponent,
        GoogleAutocompleteAddress,
        CustomerFactory,
        CustomerGroupFactory,
        priceHelper,
        countryModel,
        complainModel,
        eventManager,
        selectCustomer,
        staffHelper,
        addNotification,
        Event,
        mageTemplate,
        Translate,
        selectBilling,
        selectShipping,
        datetimeHelper,

        checkoutModel,
        confirm
    ) {
        "use strict";
        ko.bindingHandlers.bootstrapSwitchOnCustomer = {
            init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
                $(element).iosCheckbox();
                $(element).on("switchchange", function (e) {
                    valueAccessor()(e.target.checked);
                });
            }
        };
        return uiComponent.extend({
            dob: ko.observable(''),
            taxvat: ko.observable(''),
            gender: ko.observable(),
            gender_label: ko.observable(''),
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

            /* Binding Country and Group data*/
            countryArray: window.webposConfig.country,
            customerGroupArray: window.webposConfig.customerGroup,
            /* End Binding*/

            /* Binding Customer Data*/
            customerData: ko.observableArray([]),
            totalSale: ko.observable(0),
            orderHistory: ko.observableArray([]),
            refundHistory: ko.observableArray([]),
            complainHistory: ko.observableArray([]),
            addressArrayData: ko.observableArray([]),
            orderArrayData: ko.observableArray([]),
            refundArrayData: ko.observableArray([]),
            customerComplainData: ko.observableArray([]),
            customerCreditData: ko.observableArray([]),
            /* End Binding*/

            /* Binding show or hide*/
            isShowAddress: ko.observable(false),
            isEditInformation: ko.observable(false),
            isShowCreateForm: ko.observable(false),
            isShowShippingAddress: ko.observable(false),
            isShowBillingAddress: ko.observable(false),
            isShowComplainForm: ko.observable(false),
            /* End Binding show or hide*/

            /* Binding Label*/
            addressLabel: ko.observable(Translate('New Address')),
            /* End Binding Label*/

            /* Binding Customer Information*/
            currentFirstName: ko.observable(''),
            currentLastName: ko.observable(''),
            currentEmail: ko.observable(''),
            creditAmount: ko.observable(0),
            currentGroupId: ko.observable(''),
            notSync: ko.observable(false),
            /* End binding*/

            /* Binding billing address information in create customer form*/
            isShowBillingSummaryForm: ko.observable(false),
            firstNameBilling: ko.observable(''),
            lastNameBilling: ko.observable(''),
            companyBilling: ko.observable(''),
            phoneBilling: ko.observable(''),
            street1Billing: ko.observable(''),
            street2Billing: ko.observable(''),
            countryBilling: ko.observable(''),
            regionBilling: ko.observable(''),
            regionIdBilling: ko.observable(0),
            cityBilling: ko.observable(''),
            zipcodeBilling: ko.observable(''),
            vatBilling: ko.observable(''),
            regionObjectBilling: {},
            regionIdComputedBilling: '',
            billingAddressTitle: ko.observable(Translate('Add Billing Address')),
            leftBillingButton: ko.observable(Translate('Cancel')),
            /* End binding*/

            /* Create Customer Form*/
            firstNameCustomer: ko.observable(''),
            lastNameCustomer: ko.observable(''),
            emailCustomer: ko.observable(''),
            groupCustomer: ko.observable(''),
            vatCustomer: ko.observable(''),
            isSubscriberCustomer: ko.observable(false),
            /* End Form*/
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
            shippingAddressTitle: ko.observable(Translate('Add Shipping Address')),
            leftShippingButton: ko.observable(Translate('Cancel')),
            /* End binding*/

            /* Binding address information in add new address form*/
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
            /* End binding */

            /* Region Updater*/
            regionObjectShipping: {},
            regionIdComputedShipping: '',
            regionTemplate: '<option value="<%- data.value %>" data-region-code="<%- data.code %>" title="<%- data.title %>" <% if (data.isSelected) { %>selected="selected"<% } %>>' +
            '<%- data.title %>' +
            '</option>',
            /* End region updater */

            /* Binding Current Edit Address Id*/
            currentEditAddressId: ko.observable(''),
            /* End binding*/

            /* Binding Customer Complain Content*/
            customerComplainContent: ko.observable(''),
            /* End binding*/

            /* Declare the template*/
            defaults: {
                template: 'Magestore_Webpos/customer/customer-view',
                addressTemplate: 'Magestore_Webpos/customer/add-address',
                customerFormTemplate: 'Magestore_Webpos/customer/add-customer',
                shippingAddressTemplate: 'Magestore_Webpos/customer/add-shipping-address',
                billingAddressTemplate: 'Magestore_Webpos/customer/add-billing-address',
                addComplainForm: 'Magestore_Webpos/customer/add-complain-form'
            },

            /* set data for one customer*/
            setData: function(data){
                this.customerData(data);
                this.currentFirstName(data.firstname);
                this.currentLastName(data.lastname);
                this.currentEmail(data.email);
                this.creditAmount(parseFloat(data.amount));
                this.currentGroupId(data.group_id);
                this.dob(data.dob);
                this.taxvat(data.taxvat);
                this.gender(data.gender);
                var gender_label = '';
                $.each(this.genderArray, function (index, el) {
                   if(el.id==data.gender){
                       this.gender_label(el.code);
                       return false;
                   }
                }.bind(this));
                var customerId = data.id.toString();
                if (customerId.indexOf('notsync') > -1) {
                    this.notSync(true);
                } else {
                    this.notSync(false);
                }
            },
            /* set address data*/
            setAddressData: function (data) {
                this.addressArrayData(data);
            },
            /* set order data*/
            setOrderData: function (data) {
                this.orderHistory(data);
            },
            /* set refund data*/
            setRefundData: function (data) {
                this.refundHistory(data)
            },
            /* set total sale data*/
            setTotalSale: function (data) {
                this.totalSale(priceHelper.convertAndFormat(data));
            },

            /* set customer complain data*/
            setCustomerComplain: function (data) {
                this.customerComplainData(data);
            },
            /* set customer complain data*/
            setCreditHistory: function (data) {
                this.customerCreditData(data);
            },
            /* format price*/
            formatPrice: function (price) {
                return priceHelper.convertAndFormat(price);
            },
            /* Delete Shipping Address in Customer Form*/
            deleteShippingAddress: function () {
                this.isShowShippingSummaryForm(false);
                this.resetShippingAddressInfo();
            },

            /* Delete Billing Address in Customer Form*/
            deleteBillingAddress: function () {
                this.isShowBillingSummaryForm(false);
                this.resetBillingAddressInfo();
            },

            /* Reset Form*/
            resetShippingAddressInfo: function () {
                this.isShowShippingSummaryForm(false);
                this.isSameBillingShipping(true);
                this.firstNameShipping('');
                this.lastNameShipping('');
                this.companyShipping('');
                this.phoneShipping('');
                this.street1Shipping('');
                this.street2Shipping('');
                this.countryShipping('');
                this.regionShipping('');
                this.regionIdShipping(0);
                this.cityShipping('');
                this.zipcodeShipping('');
                this.vatShipping('');
                this.shippingAddressTitle(Translate('Add Shipping Address'));
                this.leftShippingButton(Translate('Cancel'));

            },

            /* Reset Form*/
            resetBillingAddressInfo: function () {
                this.isShowBillingSummaryForm(false);
                this.firstNameBilling('');
                this.lastNameBilling('');
                this.companyBilling('');
                this.phoneBilling('');
                this.street1Billing('');
                this.street2Billing('');
                this.countryBilling('');
                this.regionBilling('');
                this.regionIdBilling(0);
                this.cityBilling('');
                this.zipcodeBilling('');
                this.vatBilling('');
                this.billingAddressTitle(Translate('Add Billing Address'));
                this.leftBillingButton(Translate('Cancel'));
            },

            /* Show overlay*/
            showOverlay: function () {
                $('.wrap-backover').show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            },

            /* Hide overlay*/
            hideOverlay: function () {
                $('.wrap-backover').hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
            },

            /* Show add address form */
            showForm: function (data) {
                this.resetForm();
                this.currentEditAddressId('');
                this.showOverlay();
                if (this.firstName() == '') {
                    this.firstName(this.currentFirstName());
                }
                if (this.lastName() == '') {
                    this.lastName(this.currentLastName());
                }
                this.isShowAddress(true);
                this.addressLabel(Translate('New Address'));
            },

            /* Render select option for region id*/
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

            /* Remove select option for region id*/
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
            /* Show edit address form */
            showEditForm: function (data) {
                this.firstName(data.firstname);
                this.lastName(data.lastname);
                this.company(data.company);
                this.phone(data.telephone);
                this.street1(data.street[0]);
                this.street2(data.street[1]);
                this.country(data.country_id);
                this.region(data.region.region);
                this.region_id(data.region_id);
                this.vatId(data.vat_id);
                this.city(data.city);
                this.zipcode(data.postcode);
                this.currentEditAddressId(data.id);
                this.addressLabel(Translate('Edit Address'));
                this.showOverlay();
                this.isShowAddress(true);
                $(".dob").calendar({
                    controlType: 'select',
                    dateFormat: "M/d/Y",
                    showTime: false,
                    maxDate: "-1d", changeMonth: true, changeYear: true
                });

                /* Region updater for show edit address popup*/
                var addAddressForm = $('#form-customer-add-address');
                var regionList = addAddressForm.find('.region_id');
                var regionInput = addAddressForm.find('.region');
                var requiredLabel = regionList.parents('.input-box');
                var regionJson = JSON.parse(window.webposConfig.regionJson);

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
                    regionInput.val(data.region.region);
                    regionList.prop('disabled', true).hide();
                    regionInput.prop('disabled', false).show();
                }
                regionInput.removeAttr('disabled');
                /* End region updater */
            },

            /* Show Edit Customer Information Form*/
            showEdit: function () {
                this.isEditInformation(true);
            },
            /* Hide Edit Customer Information Form*/
            hideEdit: function () {
                this.isEditInformation(false);
                this.resetFormInfo('customer-edit-form');
            },
            /* Show Create Customer Form */
            showCreateForm: function () {
                this.isShowCreateForm(true);
                this.showOverlay();
            },

            /* Save Customer Information When Edit*/
            saveInformation: function () {
                var self = this;
                if (this.validateForm('#customer-edit-form')) {
                    var customerData = this.customerData.call();
                    customerData.firstname = this.currentFirstName();
                    customerData.lastname = this.currentLastName();
                    customerData.full_name = this.currentFirstName() + ' ' + this.currentLastName();
                    customerData.email = this.currentEmail();
                    customerData.group_id = this.currentGroupId();
                    customerData.dob = this.dob();
                    customerData.taxvat = this.taxvat();
                    customerData.gender = this.gender();

                    var deferred = CustomerFactory.get()
                        .getCollection().addFieldToFilter('email', this.currentEmail(), 'eq')
                        .addFieldToFilter('id', customerData.id, 'neq')
                        .load();
                    deferred.done(function (data) {
                        var items = data.items;
                        if (items.length > 0) {
                            addNotification(Translate('The customer email is existed.'), true, 'danger', 'Error');
                        } else {
                            $.toaster(
                                {
                                    priority: 'success',
                                    title: Translate('Success'),
                                    message: Translate('The customer is saved successfully.')
                                }
                            );
                            self.saveAndPushCustomerData(customerData);
                            self.isEditInformation(false);
                        }
                    });
                }
            },

            /* Hide Add Address Form*/
            hideAddress: function () {
                var form = '#form-customer-add-address';
                $(form).validation();
                $(form).validation('clearError');
                this.isShowAddress(false);
                this.hideOverlay();
            },

            /* Save Address*/
            saveAddress: function () {
                var self = this;
                if (this.validateForm('#form-customer-add-address')) {
                    var customerData = this.customerData();
                    var newAddress = {};
                    var currentAddress = this.customerData().addresses;
                    newAddress.id = 'nsync' + Date.now();
                    newAddress.customer_id = CustomerFactory.get().customerSelected();
                    newAddress.firstname = self.firstName();
                    newAddress.lastname = self.lastName();
                    newAddress.street = [self.street1(), self.street2()];
                    newAddress.company = self.company();
                    newAddress.telephone = self.phone();
                    newAddress.country_id = self.country();
                    newAddress.city = self.city();
                    newAddress.vat_id = self.vatId();

                    if (newAddress instanceof Array && !newAddress.length) {
                        newAddress.default_billing = 1;
                        newAddress.default_shipping = 1;
                        customerData.telephone = newAddress.telephone();
                    }

                    var regionListAddress = $('#form-customer-add-address').find('.region_id');

                    if (regionListAddress.is(':visible')) {
                        var selected = regionListAddress.find(":selected");
                        var regionCode = selected.data('region-code');
                        var region = selected.html();
                        newAddress.region = {
                            region_id: self.region_id(),
                            region_code : regionCode,
                            region : region
                        };
                        newAddress.region_id = self.region_id();
                    } else {
                        newAddress.region = {
                            region_id: 0,
                            region_code : self.region(),
                            region : self.region()
                        };
                        newAddress.region_id = 0;
                    }

                    newAddress.postcode = self.zipcode();
                    if (!self.currentEditAddressId()) {
                        currentAddress.push(newAddress);
                        customerData.addresses = currentAddress;

                    } else {
                        $.each(currentAddress, function (index, value) {
                            if (value['id'] == self.currentEditAddressId()) {
                                currentAddress[index] = newAddress;
                            }
                        })
                    }
                    self.isShowAddress(false);
                    this.hideOverlay();
                    $.toaster(
                        {
                            priority: 'success',
                            title: Translate('Success'),
                            message: Translate('The customer address is saved successfully.')
                        }
                    );
                    self.saveAndPushCustomerData(customerData);
                }
            },
            /* Reset Form*/
            resetFormInfo: function (form) {
                document.getElementById(form).reset();
            },
            /* Validation Form*/
            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            },
            /* Hide Create Customer Form*/
            hideCreateCustomer: function () {
                this.isShowCreateForm(false);
                this.hideOverlay();
                this.isShowShippingAddress(false);
                this.isShowBillingAddress(false);
                this.resetFormInfo('form-add-customer-customer');
                this.resetBillingAddressInfo();
                this.resetShippingAddressInfo();
                this.isSubscriberCustomer(false);
            },
            /* Save Customer Form*/
            saveCustomerForm: function () {
                var self = this;
                if (this.validateForm('#form-add-customer-customer')) {
                    var customerData = {};
                    customerData.id = 'notsync_'+ this.emailCustomer();
                    customerData.addresses = this.getAddressData();
                    customerData.firstname = this.firstNameCustomer();
                    customerData.lastname = this.lastNameCustomer();
                    customerData.full_name = this.firstNameCustomer() + this.lastNameCustomer();
                    customerData.email = this.emailCustomer();
                    customerData.subscriber_status = this.isSubscriberCustomer();
                    customerData.group_id = this.groupCustomer();
                    customerData.dob = this.dob();
                    customerData.taxvat = this.taxvat();
                    customerData.gender = this.gender();
                    if (this.phoneBilling()) {
                        customerData.telephone = this.phoneBilling();
                    } else {
                        customerData.telephone = 'N/A';
                    }
                    if (typeof customerData['columns'] != 'undefined') {
                        delete customerData['columns'];
                    }

                    var deferred = CustomerFactory.get().getCollection().addFieldToFilter('email', this.emailCustomer(), 'eq')
                        .load();
                    deferred.done(function (data) {
                        var items = data.items;
                        if (items.length > 0) {
                            addNotification(Translate('The customer email is existed.'), true, 'danger', 'Error');
                        } else {
                            $.toaster(
                                {
                                    priority: 'success',
                                    title: Translate('Success'),
                                    message: Translate('The customer is saved successfully.')
                                }
                            );
                            self.saveAndPushCustomerData(customerData);
                            self.isShowCreateForm(false);
                            self.hideOverlay();
                            self.resetFormInfo('form-add-customer-customer');
                            self.isSubscriberCustomer(false);
                        }
                    });
                }
            },
            /* Show Shipping Address Form*/
            showShippingAddress: function () {
                var self = this;
                self.showShippingAddressControl();
            },

            /* Edit shipping address*/
            editShippingAddress: function () {
                var self = this;
                self.showShippingAddressControl();
                this.shippingAddressTitle(Translate('Edit Shipping Address'));
                this.leftShippingButton(Translate('Delete'));
            },

            showShippingAddressControl: function () {
                var shippingAddressForm = $('#form-customer-shipping-address');
                var countryList = shippingAddressForm.find('.country_id');
                var regionList = shippingAddressForm.find('.region_id');
                var regionInput = shippingAddressForm.find('.region');
                countryList.regionUpdater({
                    regionList: regionList,
                    regionInput: regionInput,
                    regionJson: JSON.parse(window.webposConfig.regionJson)
                });
                if (this.firstNameShipping() == '') {
                    this.firstNameShipping(this.firstNameCustomer());
                }
                if (this.lastNameShipping() == '') {
                    this.lastNameShipping(this.lastNameCustomer());
                }
                this.shippingAddressTitle(Translate('Add Shipping Address'));
                this.leftShippingButton(Translate('Cancel'));
                this.isShowCreateForm(false);
                this.isShowShippingAddress(true);
            },
            /* Show Billing Address Form*/
            showBillingAddress: function () {
                var self = this;
                self.showBillingAddressControl();
            },

            /* Edit billing address*/
            editBillingAddress: function () {
                var self = this;
                self.showBillingAddressControl();
                this.billingAddressTitle(Translate('Edit Billing Address'));
                this.leftBillingButton(Translate('Delete'));
            },

            showBillingAddressControl: function () {
                var billingAddressForm = $('#form-customer-billing-address');
                var countryList = billingAddressForm.find('.country_id');
                var regionList = billingAddressForm.find('.region_id');
                var regionInput = billingAddressForm.find('.region');
                countryList.regionUpdater({
                    regionList: regionList,
                    regionInput: regionInput,
                    regionJson: JSON.parse(window.webposConfig.regionJson)
                });
                this.isShowCreateForm(false);
                if (this.firstNameBilling() == '') {
                    this.firstNameBilling(this.firstNameCustomer());
                }
                if (this.lastNameBilling() == '') {
                    this.lastNameBilling(this.lastNameCustomer());
                }
                this.billingAddressTitle(Translate('Add Billing Address'));
                this.leftBillingButton(Translate('Cancel'));
                this.isShowBillingAddress(true);
            },

            /* Hide Billing Address Form*/
            hideBillingAddress: function () {
                if (this.billingAddressTitle != Translate('Add Billing Address')) {
                    this.resetBillingAddressInfo();
                }
                this.isShowCreateForm(true);
                this.isShowBillingSummaryForm(false);
                this.isShowBillingAddress(false);
            },
            /* Hide Shipping Address Form*/
            hideShippingAddress: function () {
                if (this.shippingAddressTitle != Translate('Add Shipping Address')) {
                    this.resetShippingAddressInfo();
                }
                this.isShowCreateForm(true);
                this.isShowShippingAddress(false);
                this.isShowShippingSummaryForm(false);
            },
            /* Reset Form*/
            resetForm: function() {
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
            },
            /* Save Billing Address*/
            saveBillingAddress: function () {
                var self = this;
                if (this.validateForm('#form-customer-billing-address')) {
                    this.isSameBillingShipping(false);
                    var regionIdBilling = $('#form-customer-billing-address').find('.region_id');
                    if (regionIdBilling.is(':visible')) {
                        var selected = regionIdBilling.find(":selected");
                        var regionCode = selected.data('region-code');
                        var region = selected.html();
                        self.regionObjectBilling = {
                            region_id: self.regionIdBilling(),
                            region_code : regionCode,
                            region : region
                        };
                        self.regionIdComputedBilling = self.regionIdBilling();
                    } else {
                        self.regionObjectBilling = {
                            region_id: 0,
                            region_code : self.regionBilling(),
                            region : self.regionBilling()
                        };
                        self.regionIdComputedBilling = 0;
                    }
                    this.isShowBillingSummaryForm(true);
                    this.isShowCreateForm(true);
                    this.isShowBillingAddress(false);
                    this.isSameBillingShipping(false);
                    this.showOverlay();
                }
            },
            /* Save Shipping Address*/
            saveShippingAddress: function () {
                var self = this;
                if (this.validateForm('#form-customer-shipping-address')) {
                    var regionIdShipping = $('#form-customer-shipping-address').find('.region_id');
                    if (regionIdShipping.is(':visible')) {
                        var selected = regionIdShipping.find(":selected");
                        var regionCode = selected.data('region-code');
                        var region = selected.html();
                        self.regionObjectShipping = {
                            region_id: self.regionIdShipping(),
                            region_code : regionCode,
                            region : region
                        };
                        self.regionIdComputedShipping = self.regionIdShipping();
                    } else {
                        self.regionObjectShipping = {
                            region_id: 0,
                            region_code : self.regionShipping(),
                            region : self.regionShipping()
                        };
                        self.regionIdComputedShipping = 0;
                    }
                    this.isShowShippingSummaryForm(true);
                    this.isShowCreateForm(true);
                    this.isShowShippingAddress(false);

                    if (this.isSameBillingShipping()) {
                        this.firstNameBilling(this.firstNameShipping());
                        this.lastNameBilling(this.lastNameShipping());
                        this.companyBilling(this.companyShipping());
                        this.phoneBilling(this.phoneShipping());
                        this.street1Billing(this.street1Shipping());
                        this.street2Billing(this.street2Shipping());
                        this.countryBilling(this.countryShipping());
                        this.regionBilling(this.regionShipping());
                        this.regionIdBilling(this.regionIdShipping());
                        this.cityBilling(this.cityShipping());
                        this.zipcodeBilling(this.zipcodeShipping());
                        this.vatBilling(this.vatShipping());
                        this.isShowBillingSummaryForm(true);
                    }
                    this.showOverlay();
                }
            },

            /* Get address data from form*/
            getAddressData: function () {
                var addressData = [];
                var address;
                if (this.isSameBillingShipping() && this.isShowShippingSummaryForm()) {
                    address =  this.getShippingAddressData();
                    address.default_billing = 1;
                    address.default_shipping = 1;
                    addressData.push(address);
                } else {
                    if (this.isShowBillingSummaryForm()) {
                        address =  this.getBillingAddressData();
                        address.default_billing = 1;
                        addressData.push(address);
                    }
                    if (this.isShowShippingSummaryForm()) {
                        address =  this.getShippingAddressData();
                        address.default_shipping = 1;
                        addressData.push(address);
                    }
                }
                return addressData;
            },
            /* Get billing address data from form*/
            getBillingAddressData: function () {
                var data = {};
                data.firstname = this.firstNameBilling();
                data.lastname = this.lastNameBilling();
                data.company = this.companyBilling();
                data.telephone = this.phoneBilling();
                data.street = [this.street1Billing(), this.street2Billing()];
                data.country_id = this.countryBilling();
                data.region_id = this.regionIdComputedBilling;
                data.region = this.regionObjectBilling;
                data.city = this.cityBilling();
                data.postcode = this.zipcodeBilling();
                data.vat_id = this.vatBilling();
                data.default_shipping = false;
                data.default_billing = true;
                data.id = 'nsync' + Date.now();
                return data;
            },
            /* Get shipping address data from form*/
            getShippingAddressData: function () {
                var data = {};
                data.firstname = this.firstNameShipping();
                data.lastname = this.lastNameShipping();
                data.company = this.companyShipping();
                data.telephone = this.phoneShipping();
                data.street = [this.street1Shipping(), this.street2Shipping()];
                data.country_id = this.countryShipping();
                data.region_id = this.regionIdComputedShipping;
                data.region = this.regionObjectShipping;
                data.city = this.cityShipping();
                data.postcode = this.zipcodeShipping();
                data.vat_id = this.vatShipping();
                data.default_shipping = true;
                data.default_billing = false;
                data.id = 'nsync' + Date.now();
                return data;
            },
            /* show customer complain form*/
            showComplainForm: function () {
                this.isShowComplainForm(true);
                this.showOverlay();
            },
            /* hide complain form*/
            hideComplainForm: function () {
                this.isShowComplainForm(false);
                this.hideOverlay();
                this.resetFormInfo('form-add-customer-complain');
            },
            /* save customer complain from form*/
            saveComplainForm: function () {
                var data = {};
                var self = this;
                if (this.validateForm('#form-add-customer-complain')) {
                    data.customer_email = this.customerData().email;
                    data.content = this.customerComplainContent();
                    data.created_at = this.formatDate(new Date());
                    if (typeof data['columns'] != 'undefined') {
                        delete data['columns'];
                    }
                    var deferredOffline = complainModel().setMode('offline').setData(data).setPush(true).save();
                    deferredOffline.done(function (data) {
                        var deferred = complainModel().setMode('offline').getCollection()
                            .addFieldToFilter('customer_email',  self.customerData().email, 'eq').load();
                        deferred.done(function (collectionData) {
                            self.customerComplainData(collectionData.items);
                        });
                        $.toaster(
                            {
                                priority: 'success',
                                title: Translate('Success'),
                                message: Translate('The customer\'s complaint is saved successfully.')
                            }
                        );
                    });
                    this.hideComplainForm();
                }
            },
            /* Use to checkout*/
            useToCheckout: function () {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                var currentCustomerId = CustomerFactory.get().customerSelected();
                var deferred = CustomerFactory.get().load(currentCustomerId);
                deferred.done(function (currentCustomerData) {
                    selectCustomer(currentCustomerData);
                    var addressData = currentCustomerData.addresses;
                    var isSetBilling = false;
                    var isSetShipping = false;
                    $.each(addressData, function (index, value) {
                        if (value.default_billing) {
                            checkoutModel.saveBillingAddress(value);
                            viewManager.getSingleton('view/checkout/customer/edit-customer').billingAddressId(value.id);
                            viewManager.getSingleton('view/checkout/customer/edit-customer').setBillingPreviewData(value);
                            viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewBilling(true);
                            isSetBilling = true;
                        }
                        if (value.default_shipping) {
                            checkoutModel.saveShippingAddress(value);
                            viewManager.getSingleton('view/checkout/customer/edit-customer').shippingAddressId(value.id);
                            viewManager.getSingleton('view/checkout/customer/edit-customer').setShippingPreviewData(value);
                            viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewShipping(true);
                            isSetShipping = true;
                        }
                    });
                    if (!isSetBilling) {
                        selectBilling(0);
                        viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewBilling(false);
                    }

                    if (!isSetShipping) {
                        selectShipping(0);
                        viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewShipping(false);
                    }

                    $('#checkout').click();
                });
            },
            /* Delete Customer Address*/
            deleteAddress: function (data) {
                var self = this;
                confirm({
                    content: Translate('Do you want to delete address?'),
                    actions: {
                        confirm: function () {
                            var currentAddress = self.customerData().addresses;
                            var addressIndex = -1;
                            $.each(currentAddress, function (index, value) {
                                if (value.id == data.id) {
                                    addressIndex = index;

                                }
                            });
                            currentAddress.splice(addressIndex, 1);
                            var customerData = self.customerData();
                            customerData.addresses = currentAddress;
                            self.saveAndPushCustomerData(customerData);
                        },
                        always: function (event) {
                            event.stopImmediatePropagation();
                        }
                    }
                });
            },
            /* Save and push customer data to server*/
            saveAndPushCustomerData: function (customerData) {
                var self = this;
                if (typeof customerData['columns'] != 'undefined') {
                    delete customerData['columns'];
                }
                var saveDeferred = CustomerFactory.get().setData(customerData).setPush(true).save();
                saveDeferred.done(function (data) {
                    var groupDeferred = CustomerGroupFactory.get().load(data.group_id);
                    groupDeferred.done(function(response){
                        if (response) {
                            data.group_label = response.code;
                        }
                        self.customerData(data);
                    });
                    var viewManager = require('Magestore_Webpos/js/view/layout');
                    viewManager.getSingleton('view/customer/customer-list').newCustomer(data.id);
                    self.setAddressData(customerData.addresses);
                    //Event.dispatch('customer_pull_after',[]);
                });
            },
            /* push customer complain to server*/
            pushComplainToServer: function (data) {
                var deferred = complainModel().setMode('online').setData(data).save();
                deferred.done(function () {

                });
            },

            /* Get life time title*/
            getLifeTimeTitle: function () {
                return Translate('Sales ') + '(' + window.webposConfig.order_sync_time + ') :';
            },

            /* Get order history title*/
            getOrderHistoryTitle: function () {
                return Translate('Order History In ') + window.webposConfig.order_sync_time;
            },

            /* Get refund history title*/
            getRefundHistoryTitle: function () {
                return Translate('Order Refund In ') + window.webposConfig.order_sync_time;
            },

            /* Get full date*/
            getFullDate: function (dateString) {
                return datetimeHelper.getFullDate(dateString);
            },
            /* Format Data*/
            formatDate : function(dateTime){
                return dateTime.getFullYear() + "-" + this.twoDigits(1 + dateTime.getMonth()) + "-" + this.twoDigits(dateTime.getDate()) + " " + this.twoDigits(dateTime.getHours()) + ":" + this.twoDigits(dateTime.getMinutes()) + ":" + this.twoDigits(dateTime.getSeconds());
            },
            /* Format Two Digits*/
            twoDigits : function(n){
                return n > 9 ? "" + n: "0" + n;
            },

            /* Resize address after render*/
            resizeAddress: function () {
                var allAddressDiv = $('#customer-edit-form').find('.info-address-edit');
                var maxHeight = 0;
                $.each(allAddressDiv, function (index, value) {
                    if ($(value).height() >= maxHeight) {
                        maxHeight = $(value).height();
                    }
                });
                allAddressDiv.height(maxHeight);
            },

            viewOrderDetail: function (object) {
                var orderIncrement = object.increment_id;
                eventManager.dispatch('order_search_after', [orderIncrement]);
                $('#orders_history').click();
                $('#orders_history_container #search-header-order').val(orderIncrement);
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
