/*
 * Created by Wazza Rooney on 9/6/17 5:01 PM
 * Copyright (c) 2017. All rights reserved.
 * Last modified 7/6/17 10:31 AM
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
                template: 'ui/catalog/product/detail/giftvoucher/field'
            },
            initialize: function () {
                this._super();
            },
            fromPrice: function (giftvoucherOption) {
                return this.convertAndFormatPrice(giftvoucherOption['from']);
            },
            toPrice: function (giftvoucherOption) {
                return this.convertAndFormatPrice(giftvoucherOption['to']);
            },
        });
    }
);