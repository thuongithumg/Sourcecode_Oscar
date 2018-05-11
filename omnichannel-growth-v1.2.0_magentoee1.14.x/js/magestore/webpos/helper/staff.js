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

define([
    'jquery',
    'model/staff/current-staff'
], function ($, currentStaff) {
    'use strict';

    return {
        getStaffId: getStaffId,
        isHavePermission: isHavePermission,
        getCustomerGroupOfStaffNumber: getCustomerGroupOfStaffNumber,
        getCustomerGroupOfStaff: getCustomerGroupOfStaff,
        getStaffName: getStaffName,
        canShowOrderMenu: canShowOrderMenu,
        getMaximumDiscountPercent: getMaximumDiscountPercent,
    };

    function getStaffId() {
        return currentStaff.staffId();
    }

    function isHavePermission(resource) {
        var allResourceAccess = currentStaff.staffResourceAccess();
        if ($.inArray(resource, allResourceAccess) > -1 || $.inArray('Magestore_Webpos::all', allResourceAccess) > -1) {
            return 1;
        }
        return 0;
    }

    function getCustomerGroupOfStaffNumber() {
        return currentStaff.customerGroupOfStaff().split(",").map(Number);
    }

    function getCustomerGroupOfStaff() {
        return currentStaff.customerGroupOfStaff().split(",");
    }

    function getStaffName() {
        return currentStaff.staffName();
    }

    function getMaximumDiscountPercent() {
        return currentStaff.getMaximumDiscountPercent();
    }

    /* @return boolean */
    function canShowOrderMenu() {
        //return true;
        return !(!this.isHavePermission('Magestore_Webpos::manage_order_me')
            && !this.isHavePermission('Magestore_Webpos::manage_order_other_staff')
            && !this.isHavePermission('Magestore_Webpos::manage_all_order')
        );
    }

});
