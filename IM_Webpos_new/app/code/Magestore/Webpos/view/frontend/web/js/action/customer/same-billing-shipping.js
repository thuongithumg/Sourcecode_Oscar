/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'Magestore_Webpos/js/view/checkout/customer/add-shipping-address'
    ],
    function(addShipping) {
        'use strict';
        return function (data) {
            addShipping().firstNameShipping(data.firstNameBilling);
            addShipping().lastNameShipping(data.lastNameBilling);
            addShipping().companyShipping(data.companyBilling);
            addShipping().phoneShipping(data.phoneBilling);
            addShipping().street1Shipping(data.street1Billing);
            addShipping().street2Shipping(data.street2Billing);
            addShipping().countryShipping(data.countryBilling);
            addShipping().regionShipping(data.regionBilling);
            addShipping().regionIdShipping(data.regionIdBilling);
            addShipping().cityShipping(data.cityBilling);
            addShipping().zipcodeShipping(data.zipcodeBilling);
            addShipping().vatShipping(data.vatBilling);
            addShipping().isShowShippingSummaryForm(true);
        }
    }
);
