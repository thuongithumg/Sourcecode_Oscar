/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'ko'
    ],
    function (ko) {
        'use strict';

        var shiftId = ko.observable(window.webposConfig.shiftId);


        return {
            shiftId: shiftId,

            setShiftId: function(shift) {
                shiftId(shift);
            },

            getShiftId: function () {
                return shiftId;
            },

            getShiftIdWhenCreateOrder: function () {
                if(!shiftId) {
                    
                }
                return shiftId;
            },
        };
    }
);
