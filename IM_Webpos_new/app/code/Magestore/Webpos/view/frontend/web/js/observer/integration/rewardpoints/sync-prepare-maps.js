/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'Magestore_Webpos/js/helper/general'
        ],
        function (Helper) {
            "use strict";

            return {
                execute: function () {
                    if(Helper.isRewardPointsEnable()){
                        Helper.observerEvent('sync_prepare_maps', function (event, data) {
                            if (data.maps) {
                                data.maps.push({
                                    sort_order: 8,
                                    label: 'Customer Points',
                                    model: 'model/checkout/integration/reward-points',
                                    push: false,
                                    limitPage: 1,
                                    // pageSize: 500
                                });
                                data.maps.push({
                                    sort_order: 9,
                                    label: 'Reward Point Rates',
                                    model: 'model/checkout/integration/rewardpoints/rate',
                                    push: false,
                                    limitPage: 1,
                                    // pageSize: 500
                                });
                            }
                        });
                    }
                }
            }
        }
);