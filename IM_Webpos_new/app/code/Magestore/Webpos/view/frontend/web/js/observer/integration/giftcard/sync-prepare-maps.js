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
                if(Helper.isGiftCardEnable()){
                    Helper.observerEvent('sync_prepare_maps', function (event, data) {
                        if (data.maps) {
                            data.maps.push({
                                sort_order: 11,
                                label: 'Gift Card Template',
                                model: 'model/checkout/integration/giftcard/giftvoucher-template',
                                push: false,
                                limitPage: 1,
                                // pageSize: 500
                            })
                        }
                    });
                }
            }
        }
    }
);