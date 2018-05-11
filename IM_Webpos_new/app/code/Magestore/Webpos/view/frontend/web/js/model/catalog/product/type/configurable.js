/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'underscore',
            'Magestore_Webpos/js/model/catalog/product/type/composite',
        ],
        function ($, _, typeComposite) {
            "use strict";
            return typeComposite.extend({
                childStocks: {},
                getChildProductIds: function () {
                    var childIds = [];
                    var jsonConfig = this.getProduct().data.json_config;
                    jsonConfig = eval('(' + jsonConfig + ')');
                    var attributes = jsonConfig.attributes;
                    if (attributes) {
                        for (var attributeId in attributes) {
                            var options = attributes[attributeId].options;
                            for (var optionId in options) {
                                for (var i in options[optionId].products) {
                                    childIds.push(parseInt(options[optionId].products[i]));
                                }
                            }
                        }
                    }
                    childIds = _.uniq(childIds);
                    return childIds;
                }
            });
        }
);