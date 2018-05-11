/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'Magestore_Webpos/js/view/layout',
    ],
    function(ViewManager) {
        'use strict';
        return function (data) {
            var addBilling = ViewManager.getSingleton('view/checkout/customer/add-billing-address');
            addBilling.firstNameBilling(data.firstNameShipping);
            addBilling.lastNameBilling(data.lastNameShipping);
            addBilling.companyBilling(data.companyShipping);
            addBilling.phoneBilling(data.phoneShipping);
            addBilling.street1Billing(data.street1Shipping);
            addBilling.street2Billing(data.street2Shipping);
            addBilling.countryBilling(data.countryShipping);
            addBilling.regionBilling(data.regionShipping);
            addBilling.regionIdBilling(data.regionIdShipping);
            addBilling.cityBilling(data.cityShipping);
            addBilling.zipcodeBilling(data.zipcodeShipping);
            addBilling.vatBilling(data.vatShipping);
            addBilling.isShowBillingSummaryForm(true);
        }
    }
);
