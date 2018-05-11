/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko'
    ],
    function (ko) {
        'use strict';

        var staffId = ko.observable(window.webposConfig.staffId);
        var staffResourceAccess = ko.observableArray(window.webposConfig.staffResourceAccess);
        var customerGroupOfStaff = ko.observable(window.webposConfig.customerGroupOfStaff);
        var staffName = ko.observable(window.webposConfig.staffName);
        var maximum_discount_percent = ko.observable(window.webposConfig.maximum_discount_percent);

        return {
            staffId: staffId,
            staffName: staffName,
            staffResourceAccess: staffResourceAccess,
            customerGroupOfStaff: customerGroupOfStaff,
            maximum_discount_percent: maximum_discount_percent,

            setStaffId: function(staff) {
                staffId(staff);
            },

            getStaffId: function () {
                return staffId;
            },

            setStaffResourceAccess: function(resource) {
                staffResourceAccess(resource);
            },

            getStaffResourceAccess: function () {
                return staffResourceAccess;
            },

            getCustomerGroupOfStaff: function () {
                return customerGroupOfStaff;
            },
            
            getStaffName: function () {
                return staffName;
            },
            
            setStaffName: function (name) {
                staffName(name);
            },
            
            getMaximumDiscountPercent: function () {
                return maximum_discount_percent;
            }

        };
    }
);
