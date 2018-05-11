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

/*global define*/
define(
    [
        'jquery',
        'model/customer/customer/edit-customer',
        'model/customer/customer/new-address',
        'helper/general'
    ],
    function ($, editCustomerModel, newAddress, Helper) {
        'use strict';
        return function () {
            var editCustomerForm = $('#form-edit-customer');
            var addAddressForm = $('#form-customer-add-address-checkout');
            editCustomerModel.currentEditAddressId(editCustomerModel.billingAddressId());
            var data = {
                'firstname' : editCustomerModel.previewBillingFirstname(),
                'lastname' : editCustomerModel.previewBillingLastname(),
                'company' : editCustomerModel.previewBillingCompany(),
                'telephone' : editCustomerModel.previewBillingPhone(),
                'street' : [
                    editCustomerModel.previewBillingStreet1(),
                    editCustomerModel.previewBillingStreet2()
                ],
                'country_id' : editCustomerModel.previewBillingCountry(),
                'region' : {
                    'region':  editCustomerModel.previewBillingRegion()
                },
                'region_id' : editCustomerModel.previewBillingRegionId(),
                'city' : editCustomerModel.previewBillingCity(),
                'postcode' : editCustomerModel.previewBillingPostCode(),
                'vat_id': editCustomerModel.previewBillingVat()
            };
            newAddress.firstName(data.firstname);
            newAddress.lastName(data.lastname);
            newAddress.company(data.company);
            newAddress.phone(data.telephone);
            newAddress.street1(data.street[0]);
            newAddress.street2(data.street[1]);
            newAddress.country(data.country_id);

            newAddress.city(data.city);
            newAddress.zipcode(data.postcode);
            newAddress.vat_id(data.vat_id);
            editCustomerModel.currentEditAddressType('billing');
            /* Region updater for show edit address popup*/
            newAddress.region(data.region.region);
            newAddress.region_id(data.region_id);
            var regionAddress = new RegionUpdater('country_id', 'region', 'region_id',
                JSON.parse(window.webposConfig.regionJson), undefined, 'zip');
            regionAddress.regionSelectEl.setAttribute('defaultValue', data.region_id);
            regionAddress.update();
            /* End region updater */
            newAddress.addressTitle(Helper.__('Edit Address'));
            editCustomerForm.addClass('fade');
            editCustomerForm.removeClass('fade-in');
            editCustomerForm.removeClass('show');
            addAddressForm.addClass('fade-in');
            addAddressForm.addClass('show');
            addAddressForm.removeClass('fade');
        }
    }
);
