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
            editCustomerModel.currentEditAddressId(editCustomerModel.shippingAddressId());
            var data = {
                'firstname' : editCustomerModel.previewShippingFirstname(),
                'lastname' : editCustomerModel.previewShippingLastname(),
                'company' : editCustomerModel.previewShippingCompany(),
                'telephone' : editCustomerModel.previewShippingPhone(),
                'street' : [
                    editCustomerModel.previewShippingStreet1(),
                    editCustomerModel.previewShippingStreet2()
                ],
                'country_id' : editCustomerModel.previewShippingCountry(),
                'region' : {
                    'region':  editCustomerModel.previewShippingRegion()
                },
                'region_id' : editCustomerModel.previewShippingRegionId(),
                'city' : editCustomerModel.previewShippingCity(),
                'postcode' : editCustomerModel.previewShippingPostCode(),
                'vat_id': editCustomerModel.previewShippingVat()
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
            editCustomerModel.currentEditAddressType('shipping');

            /* Region updater for show edit address popup*/
            var regionAddress = new RegionUpdater('country_id', 'region', 'region_id',
                JSON.parse(window.webposConfig.regionJson), undefined, 'zip');
            regionAddress.regionSelectEl.setAttribute('defaultValue', data.region_id);
            regionAddress.update();
            newAddress.region(data.region.region);
            newAddress.region_id(data.region_id);

            newAddress.addressTitle(Helper.__('Edit Address'));
            editCustomerForm.addClass('fade');
            editCustomerForm.removeClass('fade-in');
            editCustomerForm.removeClass('show');
            addAddressForm.addClass('fade-in');
            addAddressForm.addClass('show');
            addAddressForm.removeClass('fade');
            /* End region updater */
        }
    }
);
