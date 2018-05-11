/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magestore_Webpos/js/model/shift/shift',
   
], function ($, shiftModel) {
    'use strict';


    return {
        getCurrentShiftData: getCurrentShiftData,
        parseFloat: parseFloat,
        checkHasOpenShift: checkHasOpenShift
    };

    function getCurrentShiftData() {
        var currentShiftData = {};
        var currentShift = shiftModel();
        var t = currentShift.load(window.webposConfig.shiftId);
        t.done(function (data) {
            return data;
        });
    }

    function parseFloat(value) {
        if(!value) {
            return 0;
        }
        value = parseFloat(value);
        return value;
    }

    function checkHasOpenShift(items) {
        var hasOpen = false;
        var shiftId = 0;
        items.forEach(
            function (shiftItem, index) {
                if(parseInt(shiftItem.status) == 0){
                    shiftId = shiftItem.shift_id;
                    hasOpen =  true;
                }
            }
        );
        return {hasOpen:hasOpen, shiftId:shiftId};
    }

});