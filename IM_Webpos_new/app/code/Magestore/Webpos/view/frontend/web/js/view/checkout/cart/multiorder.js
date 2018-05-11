/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/base/list/collection-list',
        'Magestore_Webpos/js/model/checkout/multiorder',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/action/checkout/load-order-to-checkout',
        'Magestore_Webpos/js/action/checkout/add-new-session',
        'Magestore_Webpos/js/action/checkout/process-tab',
        'Magestore_Webpos/js/action/checkout/cancel-tab',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/helper/general',
    ],
    function ($, ko, listAbstract, multiOrder, CartModel, Checkout, addNewSession, processTab, cancelTab, datetimeHelper,Helper) {
        "use strict";
        return listAbstract.extend({
            loading: ko.pureComputed(function () {
                return multiOrder.loading();
            }),
            items: multiOrder.itemsList,
            currentId: multiOrder.currentId,
            defaults: {
                template: 'Magestore_Webpos/checkout/cart/multiorder',
            },

            initialize: function () {
                this._super();
                var self = this;
                Helper.observerEvent('affter_save_cart_online', function (event, data) {
                    self.afterSwitchCart(data);
                });
            },

            _prepareItems: function () {

            },

            afterSwitchCart: function(data){
                multiOrder.loading(false);
            },

            /**
             * return a date time with format: 15:26 PM
             * @param dateString
             * @returns {string}
             */
            getTime: function(dateString) {
                var currentTime = datetimeHelper.stringToCurrentTime(dateString);
                return datetimeHelper.getTime(currentTime);
            },

            render: function() {
                this._render();
            },
            
            processItem: function (data) {
                if (data.entity_id !== multiOrder.currentId() && multiOrder.currentId()) {
                    multiOrder.loading(true);
                }
                processTab(data);
            },

            addNewSession: function () {
                addNewSession();

            },

            createNewEmptyOrder: function () {

            },


            removeSession: function (data) {
                cancelTab(data);
                if (data.increment_id === multiOrder.currentId() || !multiOrder.currentId()) {
                    CartModel.emptyCart();
                    if (multiOrder.itemsList().length > 0) {
                        var items = multiOrder.itemsList();
                        var firstItem = items[0];
                        multiOrder.currentId(firstItem['increment_id']);
                        multiOrder.currentOrderData(firstItem);
                        Checkout(firstItem);
                    } else {
                        multiOrder.currentId(0);
                        multiOrder.currentOrderData({});
                    }
                }
            },

            selectCurrentOrder: function (data) {
                multiOrder.currentId(data.entity_id);
                multiOrder.currentOrderData(data);
                Checkout(data);
            }


        });
    }
);
