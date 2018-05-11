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
        'require',
        'jquery',
        'ko',
        'model/customer/customer/billing-address',
        'model/customer/customer/shipping-address',
        'model/customer/customer/edit-customer',
        'model/customer/customer/new-customer',
        'posComponent',
        'action/customer/add/show-shipping-address-control',
        'action/customer/add/show-billing-address-control',
        'lib/bootstrap/bootstrap',
        'lib/bootstrap/bootstrap-switch',
        'mage/validation'
    ],
    function (
        require,
        $,
        ko,
        billingModel,
        shippingModel,
        editCustomerModel,
        addCustomerModel,
        Component,
        showShippingAddressControl,
        showBillingAddressControl
    ) {
        "use strict";
        ko.bindingHandlers.bootstrapSwitchOnCustomerCheckout = {
            init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
                $(element).iosCheckbox();
                $(element).on("switchchange", function (e) {
                    addCustomerModel.isSubscriberCustomer(e.target.checked);
                });
            }
        };
        return Component.extend({
            customerGroupArray: ko.pureComputed(function () {
                return addCustomerModel.customerGroupArray();
            }),
            isAddCustomer: ko.pureComputed(function () {
                return addCustomerModel.isAddCustomer();
            }),
            firstNameCustomer: ko.pureComputed(function () {
                return addCustomerModel.firstNameCustomer();
            }),
            lastNameCustomer: ko.pureComputed(function () {
                return addCustomerModel.lastNameCustomer();
            }),
            emailCustomer: ko.pureComputed(function () {
                return addCustomerModel.emailCustomer();
            }),
            groupCustomer: ko.pureComputed(function () {
                return addCustomerModel.groupCustomer();
            }),
            vatCustomer: ko.pureComputed(function () {
                return addCustomerModel.vatCustomer();
            }),
            isSubscriberCustomer: ko.pureComputed(function () {
                return addCustomerModel.isSubscriberCustomer();
            }),
            /* End Binding */

            isShowBillingSummaryForm: ko.pureComputed(function () {
                return billingModel.isShowBillingSummaryForm();
            }),
            firstNameBilling: ko.pureComputed(function () {
                return billingModel.firstNameBilling();
            }),
            lastNameBilling: ko.pureComputed(function () {
                return billingModel.lastNameBilling();
            }),
            companyBilling: ko.pureComputed(function () {
                return billingModel.companyBilling();
            }),
            phoneBilling: ko.pureComputed(function () {
                return billingModel.phoneBilling();
            }),
            street1Billing: ko.pureComputed(function () {
                return billingModel.street1Billing();
            }),
            street2Billing: ko.pureComputed(function () {
                return billingModel.street2Billing();
            }),
            countryBilling: ko.pureComputed(function () {
                return billingModel.countryBilling();
            }),
            regionBilling: ko.pureComputed(function () {
                return billingModel.regionBilling();
            }),
            regionIdBilling: ko.pureComputed(function () {
                return billingModel.regionIdBilling();
            }),
            cityBilling: ko.pureComputed(function () {
                return billingModel.cityBilling();
            }),
            zipcodeBilling: ko.pureComputed(function () {
                return billingModel.zipcodeBilling();
            }),
            vatBilling: ko.pureComputed(function () {
                return billingModel.vatBilling();
            }),
            regionObjectBilling: ko.pureComputed(function () {
                return billingModel.regionObjectBilling();
            }),
            /* End binding*/

            /* Binding shipping address information in create customer form*/
            isShowShippingSummaryForm: ko.pureComputed(function () {
                return shippingModel.isShowShippingSummaryForm();
            }),
            firstNameShipping: ko.pureComputed(function () {
                return shippingModel.firstNameShipping();
            }),
            lastNameShipping: ko.pureComputed(function () {
                return shippingModel.lastNameShipping();
            }),
            companyShipping: ko.pureComputed(function () {
                return shippingModel.companyShipping();
            }),
            phoneShipping: ko.pureComputed(function () {
                return shippingModel.phoneShipping();
            }),
            street1Shipping: ko.pureComputed(function () {
                return shippingModel.street1Shipping();
            }),
            street2Shipping: ko.pureComputed(function () {
                return shippingModel.street2Shipping();
            }),
            countryShipping: ko.pureComputed(function () {
                return shippingModel.countryShipping();
            }),
            regionShipping: ko.pureComputed(function () {
                return shippingModel.regionShipping();
            }),
            regionIdShipping: ko.pureComputed(function () {
                return shippingModel.regionIdShipping();
            }),
            cityShipping: ko.pureComputed(function () {
                return shippingModel.cityShipping();
            }),
            zipcodeShipping: ko.pureComputed(function () {
                return shippingModel.zipcodeShipping();
            }),
            vatShipping: ko.pureComputed(function () {
                return shippingModel.vatShipping();
            }),
            regionObjectShipping: ko.pureComputed(function () {
                return shippingModel.regionObjectShipping();
            }),

            /* Selector for control UI*/
            formAddCustomerCheckout: $('#form-customer-add-customer-checkout'),
            formAddShippingAddressCheckout: $('#form-customer-add-shipping-address-checkout'),
            /* End selector*/

            /* Template for koJS*/
            defaults: {
                template: 'ui/checkout/customer/add-customer'
            },

            cancelCustomerForm: function () {
                addCustomerModel.cancelCustomerForm();
            },

            saveCustomerForm: function () {
                addCustomerModel.save();
            },

            showShippingAddress: function () {
                var self = this;
                shippingModel.firstNameShipping(addCustomerModel.firstNameCustomer());
                shippingModel.lastNameShipping(addCustomerModel.lastNameCustomer());
                self.showShippingAddressControl();
            },

            /* Edit shipping address*/
            editShippingAddress: function () {
                var self = this;
                self.showShippingAddressControl();
                shippingModel.shippingAddressTitle(this.__('Edit Shipping Address'));
                shippingModel.leftButton(this.__('Delete'));
            },

            showBillingAddress: function () {
                var self = this;
                billingModel.firstNameBilling(addCustomerModel.firstNameCustomer());
                billingModel.lastNameBilling(addCustomerModel.lastNameCustomer());
                self.showBillingAddressControl();
            },

            editBillingAddress: function () {
                var self = this;
                self.showBillingAddressControl();
                billingModel.billingAddressTitle(this.__('Edit Billing Address'));
            },

            /* Delete billing address*/
            deleteBillingAddress: function () {
                addCustomerModel.deleteBillingAddress();
            },

            /* Delete shipping address*/
            deleteShippingAddress: function () {
                addCustomerModel.deleteShippingAddress();
            },


            showShippingAddressControl: function () {
                showShippingAddressControl();
            },

            showBillingAddressControl: function () {
                showBillingAddressControl();
            },

            setFirstName: function(data,event) {
                addCustomerModel.firstNameCustomer(event.target.value);
            },

            setLastName: function(data,event) {
                addCustomerModel.lastNameCustomer(event.target.value);
            },

            setEmail: function(data,event) {
                addCustomerModel.emailCustomer(event.target.value);
            },

            setGroup: function(data,event) {
                addCustomerModel.groupCustomer(event.target.value);
            },

            setVatCustomer: function(data,event) {
                addCustomerModel.vatCustomer(event.target.value);
            },

            setSubscriber: function(data,event) {
                addCustomerModel.isSubscriberCustomer(event.target.value);
            }
        });
    }
);