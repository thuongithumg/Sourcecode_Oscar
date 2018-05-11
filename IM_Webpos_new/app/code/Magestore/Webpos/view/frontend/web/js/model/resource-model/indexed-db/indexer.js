/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/resource-model/offline-abstract'
    ],
    function ($, Element) {
        "use strict";
        return Element.extend({
            mainTable: '',
            dataTable: '',
            keyPath: 'id',
            indexes: [],
            orderBy: false,
            paging: 90000,
            initialize: function () {
                this._super();
            },
            search: function(filter, pageSize, currentPage, cacheKey, orderParams) {
                var self = this,
                    result = [],
                    total = 0,
                    deferred = $.Deferred();
                currentPage = currentPage ? currentPage : 1;
                var resolveResult = function() {
                    result.cacheKey = cacheKey;
                    $[self.mainTable] = result;
                    if(orderParams && result.length && typeof result[0] === 'object') {
                        $.each(orderParams, function (index, value) {
                            if (value.direction == 'DESC') {
                                result.sort(function (a, b) {
                                    var x = a[value.field];
                                    var y = b[value.field];
                                    if (typeof x == "string") {
                                        x = x.toLowerCase();
                                    }
                                    if (typeof y == "string") {
                                        y = y.toLowerCase();
                                    }
                                    return ((x > y) ? -1 : ((x < y) ? 1 : 0));
                                });
                            } else {
                                result.sort(function (a, b) {
                                    var x = a[value.field];
                                    var y = b[value.field];
                                    if (typeof x == "string") {
                                        x = x.toLowerCase();
                                    }
                                    if (typeof y == "string") {
                                        y = y.toLowerCase();
                                    }
                                    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
                                });
                            }
                        });
                        for (var i = result.length - 1; i >= 0; i--) {
                            result[i] = result[i].id;
                        }
                    }
                    // Resolve Result
                    if (pageSize) {
                        var from = (currentPage - 1) * pageSize,
                            to = currentPage * pageSize - 1;
                        if (result.length - 1 < to) {
                            to = result.length - 1;
                        }
                        if (from == to) {
                            result = result.slice(from);
                        } else {
                            result = result.slice(from, to + 1);
                        }
                    }
                    deferred.resolve({
                        result: result,
                        total: total,
                    });
                };
                if ((self.mainTable in $) && (cacheKey === $[self.mainTable].cacheKey)) {
                    result = $[self.mainTable];
                    total = result.length;
                    resolveResult();
                    return deferred;
                }
                self.indexData().always(function() {
                    // Start search
                    server[self.mainTable].query().all()
                        .success(function(cursor) {
                            if (!cursor) {
                                return;
                            }
                            // Process search
                            var res = cursor.value.value;
                            for (var i = 0, n = res.length; i < n; i++) {
                                var item = res[i];
                                if (filter(item)) {
                                    total++;
                                    if (orderParams) {
                                        result.push(item);
                                    } else {
                                        result.push(item.id);
                                    }
                                }
                            }
                            // Next item
                            cursor['continue']();
                        }).execute()
                        .catch(function(err) {
                            deferred.reject(err);
                        }).then(resolveResult);
                });
                return deferred;
            },
            reindex: function() {
                var self = this,
                    deferred = $.Deferred();
                // Clear Index Data
                server[self.mainTable].clear()
                    .catch(function(err) {
                        deferred.reject(err);
                    }).then(function() {
                        if (self.mainTable in $) {
                             $[self.mainTable] = [];
                        }
                        self.indexData().always(function() {
                            deferred.resolve();
                        });
                    });
                return deferred;
            },
            /**
             * -------------------
             * Private Methods
             */
            indexData: function() {
                var self = this,
                    deferred = $.Deferred();
                // Check data is indexed
                server[self.mainTable].query().all()
                    .count()
                    .execute()
                    .catch(function(err) {
                        deferred.reject(err);
                    }).then(function(count) {
                        if (count > 0) {
                            // Indexed
                            deferred.resolve(count);
                        } else {
                            // Start indexing
                            self.indexingData().always(function(res) {
                                deferred.resolve(res);
                            });
                        }
                    });
                return deferred;
            },
            indexingData: function() {
                var self = this,
                    id = 1,
                    data = [],
                    deferred = $.Deferred();
                // Read original data
                server[self.dataTable].query(self.orderBy).all()
                    .success(function(cursor) {
                        if (!cursor) {
                            return;
                        }
                        // Process index
                        var item = {},
                            i, field, n = self.indexes.length,
                            result = 'value' in cursor ? cursor.value : cursor.key;
                        item.id = result[self.keyPath];
                        for (i = 0; i < n; i++) {
                            field = self.indexes[i];
                            item[field] = result[field];
                        }
                        data.push(item);
                        if (data.length >= self.paging) {
                            self.importDataIndex(id++, data);
                            data = [];
                        }
                        // Next item
                        cursor['continue']();
                    }).execute()
                    .catch(function(err) {
                        deferred.reject(err);
                    }).then(function() {
                        if (data.length) {
                            // Import last
                            self.importDataIndex(id, data).then(function() {
                                deferred.resolve();
                            });
                        } else {
                            // All done
                            deferred.resolve();
                        }
                    });
                return deferred;
            },
            importDataIndex: function(id, data) {
                return server[this.mainTable].update({
                    id: id,
                    value: data,
                });
            },
        });
    }
);
