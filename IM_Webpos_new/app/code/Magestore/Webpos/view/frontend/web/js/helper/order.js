/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magestore_Webpos/js/helper/staff',
    'Magestore_Webpos/js/helper/price',
    'Magestore_Webpos/js/helper/datetime'
], function ($, Staff, HelperPrice, HelperDateTime) {
    'use strict';
    return {
        generateId:generateUniqueId,
        setLastId:setLastId
    };
    
    function generateUniqueId(){
        var currentTime = HelperDateTime.getBaseCurrentTime();
        var staffId = HelperPrice.toNumber(Staff.getStaffId());
        var offlineId = "WP"+staffId+""+currentTime;
        return offlineId;
    }
    
    function generateOfflineIncrementId(){
        var lastId = HelperPrice.toNumber(window.webposConfig.last_offline_order_id);
        if(lastId > 0){
            var newIdNumber = lastId + 1;
            var offlineId = "WP"+newIdNumber;
            return offlineId;
        }else{
            var staffId = HelperPrice.toNumber(Staff.getStaffId());
            var newIdNumber = staffId * 100000000 + lastId + 1;
            var offlineId = "WP"+newIdNumber;
            return offlineId;
        }
    }
    
    function setLastId(id){
        window.webposConfig.last_offline_order_id = id;
    }
});