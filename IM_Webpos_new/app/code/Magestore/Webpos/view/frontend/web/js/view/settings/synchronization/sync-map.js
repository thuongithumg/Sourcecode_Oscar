/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/helper/general'
    ],
    function ($, eventManager, Helper) {
        "use strict";
        var getSyncMap = function() {
            var maps = [
                {
                    sort_order: 160,
                    label: 'Tax rule',
                    model: 'model/checkout/taxrule',
                    limitPage: 0
                },
                {
                    sort_order: 150,
                    label: 'Tax Classes',
                    model: 'model/checkout/taxclass',
                    limitPage: 0
                },
                {
                    sort_order: 140,
                    label: 'Tax Rate',
                    model: 'model/checkout/taxrate',
                    limitPage: 0
                },
                {
                    sort_order: 130,
                    label: 'Shipping',
                    model: 'model/checkout/shipping',
                    limitPage: 0
                },
                {
                    sort_order: 120,
                    label: 'Payment',
                    model: 'model/checkout/payment',
                    limitPage: 0
                },
                {
                    sort_order: 100,
                    label: 'Country',
                    model: 'model/directory/country',
                    limitPage: 0
                },
                {
                    sort_order: 90,
                    label: 'Currency',
                    model: 'model/directory/currency',
                    limitPage: 0
                },
                {
                    sort_order: 70,
                    label: 'Customer Complaints',
                    model: 'model/customer/complain',
                    limitPage: 0
                },
                {
                    sort_order: 60,
                    label: 'Group',
                    model: 'model/customer/group',
                    limitPage: 0
                },
                {
                    sort_order: 10,
                    label: 'Configuration',
                    model: 'model/config/config',
                    limitPage: 0
                },
                {
                    sort_order: 8,
                    label: 'Swatch Option',
                    model: 'model/catalog/product/swatch',
                    limitPage: 0
                }
            ];
            //if (!Helper.isUseOnline('products')) {
                maps.push({
                    sort_order: 30,
                    label: 'Product',
                    model: 'model/catalog/product',
                    limitPage: 1,
                    filter: {
                        field: 'updated_at',
                        config: 'webpos/updated/product',
                        datetime: true,
                        mode: 'finish',
                        condition: 'gteq'
                    },
                    pageSize: 100
                });
            //}
            // if (!Helper.isUseOnline('customers')) {
                maps.push({
                    sort_order: 80,
                    label: 'Customer',
                    model: 'model/customer/customer',
                    limitPage: 1,
                    filter: {
                        field: 'updated_at',
                        config: 'webpos/updated/customer',
                        datetime: true,
                        mode: 'finish',
                        condition: 'gteq'
                    },
                    pageSize: 100
                });
            // }
            // if (!Helper.isUseOnline('stocks')) {
                maps.push({
                    sort_order: 40,
                    label: 'Stock Item',
                    model: 'model/inventory/stock-item',
                    limitPage: 1,
                    filter: {
                        field: 'updated_time',
                        config: 'webpos/updated/stock_item',
                        datetime: true,
                        mode: 'finish',
                        condition: 'gteq'
                    },
                    pageSize: 100
                });
            // }
            // if (!Helper.isUseOnline('orders')) {
                maps.push({
                    sort_order: 110,
                    label: 'Order',
                    model: 'model/sales/order',
                    limitPage: 1,
                    filter: {
                        field: 'created_at',
                        config: 'webpos/offline/order_limit',
                        is_config: true,
                        datetime: 'day',
                        condition: 'gt'
                    }
                });
            // }
            // if (!Helper.isUseOnline('categories')) {
                maps.push({
                    sort_order: 20,
                    label: 'Category',
                    model: 'model/catalog/category',
                    limitPage: 0
                });
            // }
            // if (!Helper.isUseOnline('sessions')) {
                maps.push( {
                    sort_order: 50,
                    label: 'Session',
                    model: 'model/shift/shift',
                    limitPage: 0
                });
            // }
            eventManager.dispatch('sync_prepare_maps', {'maps': maps});
            return maps;
        };
        return getSyncMap;
    }
);