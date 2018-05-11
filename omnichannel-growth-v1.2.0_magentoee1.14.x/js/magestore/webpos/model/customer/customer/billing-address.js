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

            addShipping: ko.observable(),

            /* Binding billing address information in create customer form*/
            isShowBillingSummaryForm: ko.observable(false),
            firstNameBilling: ko.observable(''),
            lastNameBilling: ko.observable(''),
            companyBilling: ko.observable(''),
            phoneBilling: ko.observable(''),
            street1Billing: ko.observable(''),
            street2Billing: ko.observable(''),
            countryBilling: ko.observable(window.webposConfig.defaultCountry),
            regionBilling: ko.observable(''),
            regionIdBilling: ko.observable(0),
            cityBilling: ko.observable(''),
            zipcodeBilling: ko.observable(''),
            vatBilling: ko.observable(''),
            billingAddressTitle: ko.observable(helper.__('Add Billing Address')),
            leftButton: ko.observable(helper.__('Cancel')),
            regionObjectBilling: ko.observable('')
            /* End binding*/
        };
    }
);