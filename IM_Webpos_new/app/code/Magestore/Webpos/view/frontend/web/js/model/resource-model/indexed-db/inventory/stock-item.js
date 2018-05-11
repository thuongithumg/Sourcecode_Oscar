/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/resource-model/indexed-db/inventory/stock-item-indexer',
        'Magestore_Webpos/js/model/resource-model/indexed-db/abstract'
    ],
    function ($ , indexer , Abstract) {
        "use strict";
        return Abstract.extend({
            mainTable: 'stock_item',
            keyPath: 'item_id',
            indexes: {
                item_id: {unique: true},
                sku: {unique: true},
                name: {},
            },
            indexer: indexer,
            queryCollectionData: function (collection, deferred) {
                if (!deferred) {
                    deferred = $.Deferred();
                }
                var self = this,
                    filterParams = collection.queryParams.filterParams;
                if (1 === filterParams.length && 'product_id' === filterParams[0].field && 'in' === filterParams[0].condition) {
                    var range = filterParams[0].value.sort(function(a, b) {return a - b;}),
                        first = range[0],
                        last = range[range.length - 1],
                        result = [];
                    server[self.mainTable].query('product_id').bound(first, last)
                        .success(function(cursor, results) {
                            if (!cursor) {
                                return;
                            }
                            var res = 'value' in cursor ? cursor.value : cursor.key;
                            result.push(res);
                            if (res[self.keyPath] === range[0]) {
                                range.shift();
                            }
                            if (range.length && range[0] > cursor.key) {
                                cursor['continue'](range[0]);
                            } else {
                                cursor['continue']();
                            }
                        }).execute()
                        .catch(function (err) {
                            deferred.reject(err);
                        }).then(function () {
                            deferred.resolve({
                                items: result,
                                search_criteria: collection.queryParams,
                                total_count: result.length,
                            });
                        });
                    return deferred;
                }
                return Abstract.prototype.queryCollectionData.call(self, collection, deferred);
            },
        });
    }
);