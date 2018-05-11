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
    ],
    function ($, editCustomerModel, newAddress) {
        'use strict';
        return function () {
            var self = this;
            new RegionUpdater('country_id', 'region', 'region_id',
                JSON.parse(window.webposConfig.regionJson), undefined, 'zip');

            if (editCustomerModel.billingAddressId() != 0) {
                $.each(editCustomerModel.addressArray(), function (index, value) {
                    if (value.id == editCustomerModel.billingAddressId()) {
                        editCustomerModel.setBillingPreviewData(value);
                        newAddress.firstName(value.firstname);
                        newAddress.lastName(value.lastname);
                        newAddress.company(value.company);
                        newAddress.phone(value.telephone);
                        newAddress.street1(value.street[0]);
                        newAddress.street2(value.street[1]);
                        newAddress.country(value.country_id);
                        newAddress.region(value.region.region);
                        newAddress.region_id(value.region_id);
                        newAddress.city(value.city);
                        newAddress.zipcode(value.postcode);
                        newAddress.vat_id(value.vat_id);

                        var regionAddress = new RegionUpdater('country_id', 'region', 'region_id',
                            JSON.parse(window.webposConfig.regionJson), undefined, 'zip');
                        regionAddress.update();
                        editCustomerModel.isShowPreviewBilling(true);
                    }
                });
            } else {
                editCustomerModel.isShowPreviewBilling(false);
            }
        }
    }
);
