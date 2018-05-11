/*
 * Created by Wazza Rooney on 9/6/17 5:00 PM
 * Copyright (c) 2017. All rights reserved.
 * Last modified 7/6/17 10:31 AM
 */

define(
    [
        'jquery',
        'ko',
        'ui/components/catalog/product/detail/giftvoucher'
    ],
    function ($,ko, customOptionDetail) {
        "use strict";
        return customOptionDetail.extend({
            defaults: {
                template: 'ui/catalog/product/detail/giftvoucher/dropdown'
            },
            initialize: function () {
                this._super();
            }
        });
    }
);