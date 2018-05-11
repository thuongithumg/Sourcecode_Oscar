/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/catalog/product/detail-popup',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/giftvoucher/giftvoucher'
    ],
    function ($,ko, detailPopup, priceHelper, giftvoucher) {
        "use strict";
        return detailPopup.extend({
            defaults: {
                template: 'Magestore_Webpos/catalog/product/detail/giftvoucher'
            },

            giftCardType: giftvoucher.type
        });
    }
);