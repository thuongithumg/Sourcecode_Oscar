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
        'action/customer/edit/show-billing-preview',
        'action/customer/edit/show-shipping-preview',
        'action/customer/edit/delete-billing-preview',
        'action/customer/edit/delete-shipping-preview',
        'action/customer/edit/edit-billing-preview',
        'action/customer/edit/edit-shipping-preview',
        'action/customer/edit/save-form',
        'model/customer/customer/edit-customer',
        'model/customer/customer/new-address',
        'model/customer/current-customer',
        'helper/general',
        'eventManager'
    ],
    function (
        $,
        ko,
        Component,
        showBillingPreview,
        showShippingPreview,
        deleteBillingPreview,
        deleteShippingPreview,
        editBillingPreview,
        editShippingPreview,
        saveEditCustomerForm,
        editCustomerModel,
        newAddress,
        currentCustomer,
        Helper,
        eventManager
    ) {
        "use strict";
        return Component.extend({
            /* variable for control UI*/
            editCustomerForm: $('#form-edit-customer'),
            overlay:  $('.wrap-backover').hide(),

            addAddressForm: $('#form-customer-add-address-checkout'),
            regionTemplate: '<option value="<%- data.value %>" data-region-code="<%- data.code %>" title="<%- data.title %>" <% if (data.isSelected) { %>selected="selected"<% } %>>' +
            '<%- data.title %>' +
            '</option>',

            addAddress: ko.pureComputed(function () {
                return editCustomerModel.addAddress();
            }),
            items: ko.pureComputed(function () {
                return editCustomerModel.items();
            }),
            firstName: ko.pureComputed(function () {
                return editCustomerModel.firstName();
            }),
            lastName: ko.pureComputed(function () {
                return editCustomerModel.lastName();
            }),
            email: ko.pureComputed(function () {
                return editCustomerModel.email();
            }),
            group_id: ko.pureComputed(function () {
                return editCustomerModel.group_id();
            }),

            vatCustomer: ko.pureComputed(function () {
                return editCustomerModel.vatCustomer();
            }),

            customerGroupArray: ko.pureComputed(function () {
                return editCustomerModel.customerGroupArray();
            }),
            addressArray: ko.pureComputed(function () {
                return editCustomerModel.addressArray();
            }),
            isSubscriberCustomer: ko.pureComputed(function () {
                return editCustomerModel.isSubscriberCustomer();
            }),
            billingAddressId: ko.pureComputed(function () {
                return editCustomerModel.billingAddressId();
            }),
            shippingAddressId: ko.pureComputed(function () {
                return editCustomerModel.shippingAddressId();
            }),
            currentEditAddressId: ko.pureComputed(function () {
                return editCustomerModel.currentEditAddressId();
            }),
            previewBillingFirstname: ko.pureComputed(function () {
                return editCustomerModel.previewBillingFirstname();
            }),
            previewBillingLastname: ko.pureComputed(function () {
                return editCustomerModel.previewBillingLastname();
            }),
            previewBillingCompany: ko.pureComputed(function () {
                return editCustomerModel.previewBillingCompany();
            }),
            previewBillingPhone: ko.pureComputed(function () {
                return editCustomerModel.previewBillingPhone();
            }),
            previewBillingStreet1: ko.pureComputed(function () {
                return editCustomerModel.previewBillingStreet1();
            }),
            previewBillingStreet2: ko.pureComputed(function () {
                return editCustomerModel.previewBillingStreet2();
            }),
            previewBillingCountry: ko.pureComputed(function () {
                return editCustomerModel.previewBillingCountry();
            }),
            previewBillingRegion: ko.pureComputed(function () {
                return editCustomerModel.previewBillingRegion();
            }),
            previewBillingRegionId: ko.pureComputed(function () {
                return editCustomerModel.previewBillingRegionId();
            }),
            previewBillingCity: ko.pureComputed(function () {
                return editCustomerModel.previewBillingCity();
            }),
            previewBillingPostCode: ko.pureComputed(function () {
                return editCustomerModel.previewBillingPostCode();
            }),
            previewBillingVat: ko.pureComputed(function () {
                return editCustomerModel.previewBillingVat();
            }),
            isShowPreviewBilling: ko.pureComputed(function () {
                return editCustomerModel.isShowPreviewBilling();
            }),

            previewShippingFirstname: ko.pureComputed(function () {
                return editCustomerModel.previewShippingFirstname();
            }),
            previewShippingLastname: ko.pureComputed(function () {
                return editCustomerModel.previewShippingLastname();
            }),
            previewShippingCompany: ko.pureComputed(function () {
                return editCustomerModel.previewShippingCompany();
            }),
            previewShippingPhone: ko.pureComputed(function () {
                return editCustomerModel.previewShippingPhone();
            }),
            previewShippingStreet1: ko.pureComputed(function () {
                return editCustomerModel.previewShippingStreet1();
            }),
            previewShippingStreet2: ko.pureComputed(function () {
                return editCustomerModel.previewShippingStreet2();
            }),
            previewShippingCountry: ko.pureComputed(function () {
                return editCustomerModel.previewShippingCountry();
            }),
            previewShippingRegion: ko.pureComputed(function () {
                return editCustomerModel.previewShippingRegion();
            }),
            previewShippingRegionId: ko.pureComputed(function () {
                return editCustomerModel.previewShippingRegionId();
            }),
            previewShippingCity: ko.pureComputed(function () {
                return editCustomerModel.previewShippingCity();
            }),
            previewShippingPostCode: ko.pureComputed(function () {
                return editCustomerModel.previewShippingPostCode();
            }),
            previewShippingVat: ko.pureComputed(function () {
                return editCustomerModel.previewShippingVat();
            }),
            isShowPreviewShipping: ko.pureComputed(function () {
                return editCustomerModel.isShowPreviewShipping();
            }),
            editAddressType: ko.pureComputed(function () {
                return editCustomerModel.editAddressType();
            }),
            isChangeCustomerInfo: ko.pureComputed(function () {
                return editCustomerModel.isChangeCustomerInfo();
            }),
            currentEditAddressType: ko.pureComputed(function () {
                return editCustomerModel.currentEditAddressType();
            }),
            /* Template for knockout js*/
            defaults: {
                template: 'ui/checkout/customer/edit-customer'
            },

            /* Auto run when call */
            initialize: function () {
                this._super();
                var self = this;
                self.addressArrayDisplay = ko.pureComputed(function () {
                    var newAddressArray = [];
                    ko.utils.arrayMap(editCustomerModel.addressArray(), function(item) {
                        newAddressArray.push(item);
                    });
                    newAddressArray.push({
                        'id' : 0,
                        'label' : Helper.__('Use Store Address')
                    });
                    return newAddressArray;
                });
                var arrayObservable = ['#first_name_input', '#last_name_input', '#customer_email_input', '#customer_group_edit'];
                $.each(arrayObservable, function (index, value) {
                    $(value).change(function () {
                        editCustomerModel.isChangeCustomerInfo(true);
                    })
                });

                self.initObserver();
            },

            /* load data*/
            loadData: function (data) {
                editCustomerModel.group_id(data.group_id);
                editCustomerModel.firstName(data.firstname);
                editCustomerModel.lastName(data.lastname);
                editCustomerModel.email(data.email);
                if (typeof (data.addresses) != 'undefined') {
                    editCustomerModel.addressArray(data.addresses);
                } else {
                    editCustomerModel.addressArray([]);
                }
                if (data.taxvat) {
                    editCustomerModel.vatCustomer(data.taxvat);
                }
                editCustomerModel.billingAddressId(0);
                editCustomerModel.shippingAddressId(0);
                editCustomerModel.isSubscriberCustomer(false);
            },

            /* Control UI for hide customer form */
            hideCustomerForm: function () {
                editCustomerModel.hideCustomerForm();
                Helper.dispatchEvent('focus_search_input', '');
            },

            /* Save customer form */
            showLoading: function () {
                $('#form-edit-customer .indicator').show();
            },

            /* Save customer form */
            saveCustomerForm: function () {
                saveEditCustomerForm();
                Helper.dispatchEvent('focus_search_input', '');
            },

            /* Control UI show add address form*/
            showAddress: function () {
                var editCustomerForm = $('#form-edit-customer');
                var addAddressForm = $('#form-customer-add-address-checkout');
                new RegionUpdater('country_id', 'region', 'region_id',
                    JSON.parse(window.webposConfig.regionJson), undefined, 'zip');
                editCustomerForm.addClass('fade');
                editCustomerForm.removeClass('fade-in');
                editCustomerForm.removeClass('show');
                addAddressForm.addClass('fade-in');
                addAddressForm.addClass('show');
                addAddressForm.removeClass('fade');
                newAddress.addressTitle(Helper.__('Add Address'));
                newAddress.resetAddressForm();
                newAddress.firstName(currentCustomer.data().firstname);
                newAddress.lastName(currentCustomer.data().lastname);
            },

            /* Control UI show customer form*/
            showCustomerEditForm: function () {
                this.editCustomerForm.addClass('fade');
                this.editCustomerForm.removeClass('fade-in');
                this.editCustomerForm.removeClass('show');
                this.overlay.hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
                $('#form-edit-customer .indicator').hide();
            },

            setShippingId: function (data,event) {
                editCustomerModel.shippingAddressId(event.target.value);
                showShippingPreview();
            },

            setBillingId: function (data,event) {
                editCustomerModel.billingAddressId(event.target.value);
                showBillingPreview();
            },
            /* Show Shipping Preview */
            showShippingPreview: function () {
                showShippingPreview();
            },

            /* Show Billing Preview */
            showBillingPreview: function () {
                showBillingPreview();
            },

            /* Edit Shipping Preview */
            editShippingPreview: function () {
                editShippingPreview();
            },

            /* Edit Billing Preview */
            editBillingPreview: function () {
                editBillingPreview();
            },

            /* Delete Shipping Preview*/
            deleteShippingPreview: function () {
                deleteShippingPreview();
            },

            /* Delete Billing Preview*/
            deleteBillingPreview: function () {
                deleteBillingPreview();
            },

            /* Region updater for show edit address popup*/
            updateRegionForForm: function (data) {
                var regionAddress = new RegionUpdater('country_id', 'region', 'region_id',
                    JSON.parse(window.webposConfig.regionJson), undefined, 'zip');
                regionAddress.update();
            },

            /* Set Data for billing preview */
            setBillingPreviewData: function (data) {
                editCustomerModel.setBillingPreviewData(data);
            },

            /* Set Data for shipping preview */
            setShippingPreviewData: function (data) {
                editCustomerModel.setShippingPreviewData(data);
            },
            /**
             * Daniel - Added to observer some event
             */
            initObserver: function(){
                var self = this;
                eventManager.observer('checkout_select_customer_after', function (event, data){
                    if(data && data.customer){
                        self.loadData(data.customer);
                        self.showBillingPreview();
                        self.showShippingPreview();
                    }
                });
            },

            setFirstName: function(data,event) {
                editCustomerModel.firstName(event.target.value);
            },

            setLastName: function(data,event) {
                editCustomerModel.lastName(event.target.value);
            },

            setEmail: function(data,event) {
                editCustomerModel.email(event.target.value);
            },

            setGroup: function(data,event) {
                editCustomerModel.group_id(event.target.value);
            },

            setVatCustomer: function(data,event) {
                editCustomerModel.vatCustomer(event.target.value);
            },
        });
    }
);
