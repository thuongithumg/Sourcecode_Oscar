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
            var formAddCustomerCheckout = $('#form-customer-add-customer-checkout');
            var formAddShippingAddressCheckout = $('#form-customer-add-shipping-address-checkout');
            new RegionUpdater('shipping_country_id', 'shipping_region', 'shipping_region_id',
                JSON.parse(window.webposConfig.regionJson), undefined, 'shipping_zip');
            formAddCustomerCheckout.removeClass('fade-in');
            formAddCustomerCheckout.removeClass('show');
            formAddCustomerCheckout.addClass('fade');
            formAddShippingAddressCheckout.addClass('fade-in');
            formAddShippingAddressCheckout.addClass('show');
            formAddShippingAddressCheckout.removeClass('fade');
            $('.notification-bell').hide();
            $('#c-button--push-left').hide();
        }
    }
);
