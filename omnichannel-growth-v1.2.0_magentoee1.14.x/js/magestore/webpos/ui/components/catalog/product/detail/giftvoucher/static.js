/*
 * Created by Wazza Rooney on 9/11/17 8:55 AM
 * Copyright (c) 2017. All rights reserved.
 * Last modified 9/7/17 3:39 PM
 */

define(
    [
        'jquery',
        'ko',
        'ui/components/catalog/product/detail/giftvoucher',
    ],
    function ($,ko, customOptionDetail) {
        "use strict";
        return customOptionDetail.extend({
            defaults: {
                template: 'ui/catalog/product/detail/giftvoucher/static'
            },
            initialize: function () {
                this._super();
            },
            staticPrice: function (price) {
                this.basePriceAmount(price);
                this.defaultPriceAmount(this.convertAndFormatPrice(price));
                return this.convertAndFormatPrice(price);
            },
        });
    }
);