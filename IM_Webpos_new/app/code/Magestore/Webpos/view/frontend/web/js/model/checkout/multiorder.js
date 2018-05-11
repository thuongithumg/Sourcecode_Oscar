/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/data/cart'
    ],
    function ($,ko, Helper, CartData) {
        "use strict";

        return {
            items: ko.observableArray([]),
            currentSession: ko.observable(0),
            orderCount: ko.observable(1),
            currentId: ko.observable(0),
            currentOrderData: ko.observable({}),
            isMultipleOrder: ko.observable(false),
            showDateTime: ko.observable(true),
            itemsList: ko.observableArray(),
            loading:ko.observable(false)
        };
    }
);