/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'Magestore_Webpos/js/helper/general'
    ],
    function (ko, Helper) {
        "use strict";

        return {
            checkedItemId: ko.observableArray([]),
            execute: function () {
                if (Helper.isRewardPointsEnable() || Helper.isStoreCreditEnable() || Helper.isGiftCardEnable() || Helper.isStoreCreditEEEnable()) {
                    Helper.observerEvent('sales_order_list_load_order', function (event, data) {
                        if (data && data.order && data.order.items) {
                            if (((!data.order.rewardpoints_spent || data.order.rewardpoints_spent < 0.0001) &&
                                (!data.order.base_gift_voucher_discount || data.order.base_gift_voucher_discount > -0.0001)) ||
                                data.order.state == 'closed' ||
                                data.order.state == 'canceled' ||
                                (data.order.state != 'payment_review' && data.order.state == 'holded')
                            ) {
                                return false;
                            }
                            this.checkedItemId([]);
                            for (var i = 0, j = data.order.items.length; i < j; i++) {
                                if (data.order.items[i].parent_item_id && data.order.items[i].parent_item_id > 0) {
                                    this.checkedItemId.push(data.order.items[i].parent_item_id);
                                    this.checkedItemId.push(data.order.items[i].item_id);
                                    if ((data.order.items[i].qty_invoiced ? data.order.items[i].qty_invoiced : 0) -
                                        (data.order.items[i].qty_refunded ? data.order.items[i].qty_refunded : 0) -
                                        (data.order.items[i].qty_canceled ? data.order.items[i].qty_canceled : 0)
                                    ) {
                                        data.order.forced_can_creditmemo = true;
                                        break;
                                    }
                                }
                            }
                            if (data.order.forced_can_creditmemo == true) {
                                return true;
                            }
                            for (var i = 0, j = data.order.items.length; i < j; i++) {
                                if (this.checkedItemId().indexOf(data.order.items[i].item_id) === -1) {
                                    if ((data.order.items[i].qty_invoiced ? data.order.items[i].qty_invoiced : 0) -
                                        (data.order.items[i].qty_refunded ? data.order.items[i].qty_refunded : 0) -
                                        (data.order.items[i].qty_canceled ? data.order.items[i].qty_canceled : 0)
                                    ) {
                                        data.order.forced_can_creditmemo = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }.bind(this));
                }
            }
        }
    }
);