/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'posComponent',
        'helper/price',
        'helper/datetime',
        'action/notification/add-notification'
    ],
    function ($, ko, Component, priceHelper, datetimeHelper, notification) {
        "use strict";
        return Component.extend({
            setData: function() {

            },

            formatPrice: function(price){
                return priceHelper.formatPrice(price);
            },


            /**
             * return a date with format: Thursday 4 May, 2016
             *
             * @param dateString
             * @returns {string}
             */
            getFullDate: function (dateString) {
                return datetimeHelper.getFullDate(dateString);
            },


            /**
             * return a date time with format: Thursday 4 May, 2016 15:26PM
             * @param dateString
             * @returns {string}
             */
            getFullDatetime: function (dateString) {
                return datetimeHelper.getFullDatetime(dateString);
            },

            /**
             * return a date time with format: Thursday 4 May, 2016 15:26PM
             * @param dateString
             * @returns {string}
             */
            getFullCurrentDatetime: function (dateString) {
                var currentTime = datetimeHelper.stringToCurrentTime(dateString);
                return datetimeHelper.getFullDatetime(currentTime);
            },

            addNotification: function(message, isShowToaster, priority, title){
                return notification(message, isShowToaster, priority, title);
            },

            convertToCurrentTime: function(dateString){
                return (typeof dateString == 'string')?datetimeHelper.stringToCurrentTime(dateString):datetimeHelper.toCurrentTime(dateString);
            }
        });
    }
);