/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'Magestore_Webpos/js/model/catalog/product/type/composite',
        ],
        function ($, typeComposite) {
            "use strict";
            return typeComposite.extend({
                childStocks: {},
                getChildProductIds: function () {
                    var childIds = [];
                    var options = this.product.data['grouped_options'];
                    for(var i in options) {
                        childIds.push(parseInt(options[i].id));
                    }
                    return childIds;
                }
            });
        }
);