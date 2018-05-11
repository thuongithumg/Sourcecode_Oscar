/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

define(
    [
        'jquery',
        'ko'
    ],
    function ($, ko) {
        "use strict";
        return {

            itemData: ko.observable({}),
            styleOfPopup: ko.observable('view_detail'),

            qtyAddToCart: ko.observable(1),
            defaultPriceAmount: ko.observable(),
            basePriceAmount: ko.observable(),
            configurableProductIdResult: ko.observable(),
            configurableOptionsResult: ko.observable(),
            configurableLabelResult: ko.observable(),
            groupedProductResult: ko.observableArray([]),
            bundleOptionsValueResult: ko.observableArray([]),
            bundleOptionsQtyResult: ko.observableArray([]),
            bundleChildsQtyResult: ko.observableArray([]),
            bundleOptionsLableResult: ko.observableArray([]),
            customOptionsValueResult: ko.observableArray([]),
            customOptionsLableResult: ko.observableArray([]),
            creditProductResult: ko.observableArray([]),
            creditValue: ko.observable(),
            bundleItem: ko.observable(),
            groupItem: ko.observable(),
            /* End binding*/
        };
    }
);