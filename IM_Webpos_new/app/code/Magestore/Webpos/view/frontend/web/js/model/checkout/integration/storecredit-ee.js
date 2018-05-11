/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract',
        'Magestore_Webpos/js/view/checkout/cart',
        'Magestore_Webpos/js/model/checkout/cart/data/cart',
        'Magestore_Webpos/js/view/settings/general/storecredit/show-storecredit-balance',
        'Magestore_Webpos/js/view/settings/general/storecredit/auto-sync-balance',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/view/checkout/checkout/payment',
        'Magestore_Webpos/js/view/checkout/checkout/payment_selected',
        'Magestore_Webpos/js/model/checkout/checkout'
    ],
    function ($, ko, modelAbstract, CartModel, restAbstract, CartView, CartData, ShowCreditBalance, AutoSyncBalance, Helper, Payment, SelectedPayments, CheckoutModel) {
        "use strict";
        if (!window.hadObserver) {
            window.hadObserver = [];
        }
        return modelAbstract.extend({
            sync_id: 'customer_credit',
            TOTAL_CODE: 'storecredit-ee',
            MODULE_CODE: 'os_storecredit_ee',
            initialize: function () {
                    this._super();
                    this.initVariable();
                    if ($.inArray(this.MODULE_CODE, window.hadObserver) < 0) {
                        this.initObserver();
                        window.hadObserver.push(this.MODULE_CODE);
                    }
            },
            getConfig: function (path) {
                var allConfig = Helper.getBrowserConfig('plugins_config');
                if (allConfig[this.MODULE_CODE]) {
                    var configs = allConfig[this.MODULE_CODE];
                    if (configs[path]) {
                        return configs[path];
                    }
                }
                return false;
            },
            getAmountForShipping: function (items) {
                var amount = {
                    base: 0,
                    amount: 0
                };
                if (items && items.length > 0) {
                    var totalBase = 0;
                    var totalAmount = 0;
                    ko.utils.arrayForEach(items, function (item) {
                        if (item.base_customercredit_discount) {
                            totalBase += item.base_customercredit_discount;
                        }
                        if (item.customercredit_discount) {
                            totalAmount += item.customercredit_discount;
                        }
                    });
                    amount.base = Helper.toBasePrice(this.appliedAmount()) - totalBase;
                    amount.amount = this.appliedAmount() - totalAmount;
                }
                return amount;
            },
            initObserver: function () {
                var self = this;

                /**
                 * Call api to update customer credit balance after refund by credit
                 */
                Helper.observerEvent('order_refund_after', function (event, data) {
                });

            },
            initVariable: function () {
                var self = this;
                self.visible = ko.pureComputed(function () {
                    return self.canUseExtension() && self.remaining() > 0 && (!self.balance() || (self.balance() && self.balanceAfterApply() > 0));
                });
            },
        });
    }
);