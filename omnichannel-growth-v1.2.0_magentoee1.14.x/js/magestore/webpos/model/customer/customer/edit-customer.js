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
        'mage/validation'
    ],
    function ($, ko, helper) {
        "use strict";
        return {

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
            vatCustomer: ko.observable(''),
            /* Observable customer group id*/
            group_id: ko.observable(window.webposConfig.defaultCustomerGroup),
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

            addressArrayDisplay: ko.observableArray([]),

            /* Set Data for billing preview */
            setBillingPreviewData: function (data) {
                this.previewBillingFirstname(data.firstname);
                this.previewBillingLastname(data.lastname);
                this.previewBillingCompany(data.company);
                this.previewBillingPhone(data.telephone);
                this.previewBillingStreet1(data.street[0]);
                this.previewBillingStreet2(data.street[1]);
                this.previewBillingCountry(data.country_id);
                this.previewBillingRegion(data.region.region);
                this.previewBillingRegionId(data.region_id);
                this.previewBillingCity(data.city);
                this.previewBillingPostCode(data.postcode);
                this.previewBillingVat(data.vat_id);
            },

            /* Set Data for shipping preview */
            setShippingPreviewData: function (data) {
                this.previewShippingFirstname(data.firstname);
                this.previewShippingLastname(data.lastname);
                this.previewShippingCompany(data.company);
                this.previewShippingPhone(data.telephone);
                this.previewShippingStreet1(data.street[0]);
                this.previewShippingStreet2(data.street[1]);
                this.previewShippingCountry(data.country_id);
                this.previewShippingRegion(data.region.region);
                this.previewShippingRegionId(data.region_id);
                this.previewShippingCity(data.city);
                this.previewShippingPostCode(data.postcode);
                this.previewShippingVat(data.vat_id);
            },

            /* Validate edit customer form*/
            validateEditCustomerForm: function () {
                var form = '#form-edit-customer';
                return $(form).validation({}) && $(form).validation('isValid');
            },

            /* Control UI for hide customer form */
            hideCustomerForm: function () {
                var self = this;
                var editCustomerForm = $('#form-edit-customer');
                editCustomerForm.addClass('fade');
                editCustomerForm.removeClass('fade-in');
                editCustomerForm.removeClass('show');
                editCustomerForm.posOverlay({
                    onClose: function(){
                        editCustomerForm.removeClass('fade-in');
                    }
                });

                $('.pos-overlay').removeClass('active');
                $('.wrap-backover').hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
                self.hideLoading();
            },
            showLoading: function () {
                $('#form-edit-customer .indicator').show();
            },
            hideLoading: function () {
                $('#form-edit-customer .indicator').hide();
            }
        };
    }
);