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
        'posComponent',
        'model/checkout/checkout/shipping',
        'helper/general',
        'mage/calendar'
    ],
    function ($, ko, Component, ShippingModel, Helper) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'ui/checkout/checkout/shipping',
            },
            items: ShippingModel.items,
            isSelected: ShippingModel.isSelected,
            initialize: function () {
                this._super();
                this.initObserver();
            },
            initObserver: function(){
                var self = this;
                Helper.observerEvent('go_to_checkout_page', function(){
                    if(!Helper.isOnlineCheckout()){
                        ShippingModel.resetShipping();
                    }
                });
            },
            setShippingMethod: function (data) {
                ShippingModel.saveShippingMethod(data);
            },
            getShippingPrice: function(price, priceType){
                return ShippingModel.formatShippingPrice(price, priceType);
            },
            useDeliveryTime: function () {
                return ShippingModel.useDeliveryTime();
            },
            initDate: function () {
                var currentDate = new Date();
                var year = currentDate.getFullYear();
                var month = currentDate.getMonth();
                var day = currentDate.getDate();
                $("#delivery_date").calendar({
                    showsTime: true,
                    controlType: 'select',
                    timeFormat: 'HH:mm TT',
                    showTime: false,
                    minDate: new Date(year, month, day, '00', '00', '00', '00'),
                });
            }
        });
    }
);