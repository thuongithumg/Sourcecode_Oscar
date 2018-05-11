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
                    if(Helper.isStoreCreditEnable()) {
                        Helper.observerEvent('sync_prepare_maps', function (event, data) {
                            if (data.maps) {
                                data.maps.push({
                                    sort_order: 7,
                                    label: 'Customer Credit',
                                    model: 'model/checkout/integration/store-credit',
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