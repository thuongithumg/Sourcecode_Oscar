/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magestore_Webpos/js/model/staff/current-staff'
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

    function canShowOrderMenu() {

        if (!this.isHavePermission('Magestore_Webpos::manage_order')
            && !this.isHavePermission('Magestore_Webpos::manage_order_me')
            && !this.isHavePermission('Magestore_Webpos::manage_order_location')
            && !this.isHavePermission('Magestore_Webpos::manage_all_order')
        ) {
            return false;
        } else {
            return true;
        }

    }

});
