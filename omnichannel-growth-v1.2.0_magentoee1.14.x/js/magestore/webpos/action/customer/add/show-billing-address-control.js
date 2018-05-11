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
        'jquery'
    ],
    function ($) {
        'use strict';
        return function () {
            var billingAddressForm = $('#form-customer-add-billing-address-checkout');
            var formAddCustomerCheckout = $('#form-customer-add-customer-checkout');
            new RegionUpdater('billing_country_id', 'billing_region', 'billing_region_id',
                JSON.parse(window.webposConfig.regionJson), undefined, 'billing_zip');
            formAddCustomerCheckout.removeClass('fade-in');
            formAddCustomerCheckout.removeClass('show');
            formAddCustomerCheckout.addClass('fade');
            billingAddressForm.addClass('fade-in');
            billingAddressForm.addClass('show');
            billingAddressForm.removeClass('fade');
            $('.notification-bell').hide();
            $('#c-button--push-left').hide();
        }
    }
);
