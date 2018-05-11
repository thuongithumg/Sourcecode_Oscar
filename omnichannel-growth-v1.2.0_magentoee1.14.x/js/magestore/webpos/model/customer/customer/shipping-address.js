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
        "helper/general"
    ],
    function ($, ko, helper) {
        "use strict";
        return {
            /* Binding shipping address information in create customer form*/
            isShowShippingSummaryForm: ko.observable(false),
            firstNameShipping: ko.observable(''),
            lastNameShipping: ko.observable(''),
            companyShipping: ko.observable(''),
            phoneShipping: ko.observable(''),
            street1Shipping: ko.observable(''),
            street2Shipping: ko.observable(''),
            countryShipping: ko.observable(window.webposConfig.defaultCountry),
            regionShipping: ko.observable(''),
            regionIdShipping: ko.observable(0),
            cityShipping: ko.observable(''),
            zipcodeShipping: ko.observable(''),
            vatShipping: ko.observable(''),
            isSameBillingShipping: ko.observable(false),

            shippingAddressTitle: ko.observable(helper.__('Add Shipping Address')),
            leftButton: ko.observable(helper.__('Cancel')),
            regionObjectShipping: ko.observable('')
            /* End binding*/
        };
    }
);