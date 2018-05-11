/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/checkout/checkout/integration/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/customer/credit-history',
        'Magestore_Webpos/js/action/notification/add-notification',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/integration/storecredit-ee-factory'
    ],
    function ($, ko, Abstract, restAbstract, creditAbstract, notification, Helper, CartModel, CreditEEFactory) {
        "use strict";
        return Abstract.extend({
            balance: ko.observable(0),
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/integration/storecredit-ee'
            },

            initialize: function () {
                this._super();
                this.model = CreditEEFactory.get();
            },
            isCreditEnable: function () {
                return Helper.isStoreCreditEEEnable();
            },
            getBalance: function (customerId) {
                var deferred = $.Deferred();
                var params = {
                    customer_id: customerId
                };
                var apiUrl = '/webpos/balance/search/:id';
                var deferred = $.Deferred();
                restAbstract().setPush(true).setLog(false).callRestApi(
                    apiUrl,
                    'post',
                    {'customer_id': customerId},
                    {'customer_id': customerId},
                    deferred
                );
                deferred.done(function (data) {
                    this.balance(data);

                }.bind(this));
            },
            apply: function () {
                var apiUrl = '/webpos/balance/apply';
                var deferred = $.Deferred();
                restAbstract().setPush(true).setLog(false).callRestApi(
                    apiUrl,
                    'post',
                    {},
                    {},
                    deferred
                );
                deferred.done(function (data) {
                    notification(Helper.__('Apply store credit successfully!'), true, 'success', Helper.__('Success'));
                    CartModel.saveCartOnline();
                });
            },
            cancel: function () {
                var apiUrl = '/webpos/balance/cancel';
                var deferred = $.Deferred();
                restAbstract().setPush(true).setLog(false).callRestApi(
                    apiUrl,
                    'post',
                    {},
                    {},
                    deferred
                );
                deferred.done(function (data) {
                    notification(Helper.__('Cancel store credit successfully!'), true, 'success', Helper.__('Success'));
                    CartModel.saveCartOnline();
                });
            }
        });
    }
);
