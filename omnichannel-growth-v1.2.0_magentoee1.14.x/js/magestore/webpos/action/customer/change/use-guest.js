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
        'model/checkout/cart',
        'model/customer/current-customer'
    ],
    function ($, CartModel) {
        'use strict';
        return function () {
            CartModel.removeCustomer();
            var changeCustomerPopup = $('#popup-change-customer');
            changeCustomerPopup.removeClass('fade-in');
            changeCustomerPopup.posOverlay({
                onClose: function(){
                    changeCustomerPopup.removeClass('fade-in');
                }
            });
            $('.notification-bell').show();
            $('#c-button--push-left').show();
            $('.pos-overlay').removeClass('active');
        }
    }
);
