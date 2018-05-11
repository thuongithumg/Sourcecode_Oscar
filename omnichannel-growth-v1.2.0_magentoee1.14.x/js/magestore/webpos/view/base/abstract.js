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

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'helper/price',
        // 'model/directory/country',
        'helper/datetime'
        // 'action/notification/add-notification',
    ],
    function ($, ko, Component, priceHelper,
              // countryModel,
              datetimeHelper
              // notification
    ) {
        "use strict";
        return Component.extend({
            setData: function() {

            },

            formatPrice: function(price){
                return priceHelper.formatPrice(price);
            },

            convertAndFormatPrice: function(price){
                return priceHelper.convertAndFormat(price);
            },

            // getCountryName: function(code, deferred){
            //     countryModel().load(code, deferred);
            //
            // },

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

            // addNotification: function(message, isShowToaster, priority, title){
            //     return notification(message, isShowToaster, priority, title);
            // }
        });
    }
);