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
        'model/customer/current-customer',
        'model/customer/customer/edit-customer'
    ],
    function ($, ko, helper, currentCustomer, editCustomer) {
        "use strict";
        return {
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
            vat_id: ko.observable(''),
            addressTitle: ko.observable(helper.__('New Address')),
            currentEditAddressType: ko.observable(null),
            /* End Observable*/

            resetAddressForm: function () {
                this.firstName('');
                this.lastName('');
                this.company('');
                this.phone('');
                this.street1('');
                this.street2('');
                this.country('');
                this.region('');
                this.region_id('');
                this.city('');
                this.zipcode('');
                this.vat_id('');
                this.currentEditAddressType(null);
                $('#form-customer-add-address-checkout').find('#region').val('');
            },

            /* Get Address Data Form*/
            getAddressData: function () {
                var data = {};
                var self = this;
                data.id = Date.now();
                data.firstname = this.firstName();
                data.lastname = this.lastName();
                data.company = this.company();
                data.telephone = this.phone();
                data.street = [this.street1(), this.street2()];
                data.country_id = this.country();

                var regionIdAddAddress = $('#form-customer-add-address-checkout').find('#region_id');
                if (regionIdAddAddress.is(':visible')) {
                    var selected = regionIdAddAddress.find(":selected");
                    var region = selected.html();
                    data.region = {
                        region_id: self.region_id(),
                        region : region
                    };
                    data.region_id = self.region_id();
                } else {
                    data.region = {
                        region_id: 0,
                        region : self.region()
                    };
                    data.region_id = 0;
                }

                data.city = self.city();
                data.postcode = self.zipcode();
                data.vat_id = self.vat_id();
                return data;
            },


            /* Validate Add Address Form */
            validateAddressForm: function () {
                var form = '#form-customer-add-address-checkout';
                return $(form).validation() && $(form).validation('isValid');
            },

            /* Hide Address */
            hideAddressForm: function () {
                var editCustomerForm = $('#form-edit-customer');
                var addAddressForm = $('#form-customer-add-address-checkout');
                editCustomerForm.removeClass('fade');
                editCustomerForm.addClass('fade-in');
                editCustomerForm.addClass('show');
                addAddressForm.removeClass('fade-in');
                addAddressForm.removeClass('show');
                addAddressForm.addClass('fade');
                this.addressTitle(helper.__('New Address'));
                editCustomer.currentEditAddressId(false);
                this.resetAddressForm();
            },
        };
    }
);