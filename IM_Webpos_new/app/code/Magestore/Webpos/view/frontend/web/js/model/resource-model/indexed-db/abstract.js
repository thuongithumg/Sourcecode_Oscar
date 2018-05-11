/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/resource-model/offline-abstract',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function ($, Element, eventManager) {
        "use strict";
        return Element.extend({
            mainTable: '',
            keyPath: '',
            indexes: {},
            initialize: function () {
                this._super();
            },
            save: function (model, deferred) {
                if (!deferred) {
                    deferred = $.Deferred();
                }
                var self = this;
                if (typeof model.getData() == 'undefined' || model.getData() == '' || model.getData() == null)
                    return;
                try {
                    var data = self.prepareSaveData(model.getData());
                    var id = data[self.keyPath];
                    if (!id) {
                        id = self.off_id_auto + self.mainTable + '_' + new Date().getTime();
                        data[self.keyPath] = id;
                        model.setData(data);
                    }
                    server[self.mainTable].update(data)
                        .catch(function (err) {
                            deferred.reject(err);
                            return deferred;
                        })
                        .then(function (e) {
                            if (self.indexer) {
                                self.indexer().reindex();
                            }
                            deferred.resolve(e[0]);
                        });
                } catch (err) {
                    deferred.reject(err);
                }
                //return model;
                return deferred;
            },
            update: function (model, deferred) {
                if (!deferred) {
                    deferred = $.Deferred();
                }
                var self = this;
                if (typeof model.getData() == 'undefined' || model.getData() == '' || model.getData() == null)
                    return;
                try {
                    var data = self.prepareSaveData(model.getData());
                    var id = data[self.keyPath];
                    if (!id) {
                        id = self.off_id_auto + self.mainTable + '_' + new Date().getTime();
                        data[self.keyPath] = id;
                        model.setData(data);
                    }
                    server[self.mainTable].add(data)
                        .catch(function () {
                            server[self.mainTable].query().filter(self.keyPath, id).modify(data).execute()
                                .catch(function (err) {
                                    deferred.reject(err);
                                    return deferred;
                                })
                                .then(function (e) {
                                    if (self.indexer) {
                                        self.indexer().reindex();
                                    }
                                    deferred.resolve(e[0]);
                                });
                            return deferred;
                        })
                        .then(function (e) {
                            deferred.resolve(e[0]);
                        });
                } catch (err) {
                    deferred.reject(err);
                }
                //return model;
                return deferred;
            },
            load: function (id, deferred) {
                if (!deferred) {
                    deferred = $.Deferred();
                }
                var self = this;

                if (typeof server == 'undefined') {
                    if (!self.loop) {
                        self.loop = 1;
                    } else {
                        self.loop++;
                    }
                    if (self.loop < 10) {
                        setTimeout(function () {
                            self.load(id, deferred)
                        }, 3000);
                    }
                    return deferred;
                }

                if (typeof id == 'undefined' || id == '' || id == null) {
                    deferred.resolve({});
                }
                try {
                    server[self.mainTable].query(self.keyPath).only(id).execute()
                        .catch(function (err) {
                            deferred.reject(err);
                            return deferred;
                        })
                        .then(function (p) {
                            if (typeof p[0] != 'undefined' && p[0][self.keyPath] == id) {
                                deferred.resolve(p[0]);
                            } else {
                                deferred.resolve({});
                            }
                        });
                } catch (err) {
                    deferred.reject(err);
                }
                return deferred;
            },

            delete: function (id, deferred) {
                if (!deferred) {
                    deferred = $.Deferred();
                }
                var self = this;
                if (typeof id == 'undefined' || id == '' || id == null) {
                    deferred.resolve(0);
                }
                try {
                    server[self.mainTable].remove(id)
                        .catch(function (err) {
                            deferred.reject(err);
                            return deferred;
                        })
                        .then(function (key) {
                            if (self.indexer) {
                                self.indexer().reindex();
                            }
                            deferred.resolve(key);
                        });
                } catch (err) {
                    deferred.reject(err);
                }
                //return true;
                return deferred;
            },

            clear: function (deferred) {
                if (!deferred) {
                    deferred = $.Deferred();
                }
                var self = this;
                try {
                    server[self.mainTable].clear()
                        .catch(function (err) {
                            deferred.reject(err);
                            return deferred;
                        })
                        .then(function (key) {
                            if (self.indexer) {
                                self.indexer().reindex();
                            }
                            deferred.resolve(key);
                        });
                } catch (err) {
                    deferred.reject(err);
                }
                //return true;
                return deferred;
            },

            massUpdate: function (datas, deferred, removeFuncBeforeMassUpdate) {
                if (!deferred) {
                    deferred = $.Deferred();
                }
                var self = this;
                if (typeof datas == 'undefined' || datas == '' || datas == null || datas.length <= 0) {
                    deferred.resolve({
                        updated: 0,
                        total: 0
                    });
                }
                if (datas.items && datas.items.length) {
                    var index = datas.items.length;
                    var check = 0;
                    datas.items.forEach(function (data) {
                        if (removeFuncBeforeMassUpdate) {
                            data = self.prepareSaveData(data);
                        }
                        try {
                            server[self.mainTable].update(data)
                                .catch(function (err) {
                                    deferred.reject(err);
                                    check = check + 1;
                                    eventManager.dispatch(self.mainTable + 'pull_duplicate', {
                                        'data': data,
                                        'error': err
                                    });
                                    return deferred;
                                })
                                .then(function () {
                                    check = check + 1;
                                    if (index == check) {
                                        if (self.indexer) {
                                            self.indexer().reindex();
                                        }
                                        deferred.resolve({
                                            updated: index,
                                            total: datas.total_count
                                        });
                                    }
                                });
                        } catch (err) {
                            deferred.reject(err);
                            return deferred;
                        }
                    });
                } else {
                    deferred.resolve({
                        updated: 0,
                        total: 0
                    });
                }
                return deferred;
            },

            filterProccess: function (data) {
                var self = this;
                var check = true;
                if (data.columns) {
                    data.columns = '';
                }
                eventManager.dispatch(self.mainTable + '_prepare_data_for_offline_filter', {
                    'data': data
                });
                if (self.filterParams) {
                    $.each(self.filterParams, function (index, value) {
                        self.filterParams[index]['condition'] = value.condition.toLowerCase();
                        if (value.condition == 'like' && typeof value.value === 'string' && typeof value.value.indexOf == 'function' && value.value.indexOf('%') >= 0) {
                            self.filterParams[index]['value'] = String(value.value).toLowerCase().replace('%', '').replace('%', '');
                        }
                        if (value.condition == 'like') {
                            if ((String(data[value.field]).toLowerCase().indexOf(String(value.value))) < 0) {
                                check = false;
                            }
                        } else if (value.condition.toLowerCase() == 'eq') {
                            if (data[value.field] != value.value) {
                                check = false;
                            }
                        } else if (value.condition.toLowerCase() == 'neq') {
                            if (data[value.field] == value.value) {
                                check = false;
                            }
                        } else if (value.condition.toLowerCase() == 'gt') {
                            if (data[value.field] <= value.value) {
                                check = false;
                            }
                        } else if (value.condition.toLowerCase() == 'lt') {
                            if (data[value.field] >= value.value) {
                                check = false;
                            }
                        } else if (value.condition.toLowerCase() == 'gteq') {
                            if (data[value.field] < value.value) {
                                check = false;
                            }
                        } else if (value.condition.toLowerCase() == 'lteq') {
                            if (data[value.field] > value.value) {
                                check = false;
                            }
                        } else if (value.condition.toLowerCase() == 'in') {
                            if ($.isArray(value.value) && value.value.indexOf(data[value.field]) < 0) {
                                check = false;
                            } else {
                            }
                        } else if (value.condition.toLowerCase() == 'nin') {
                            if ($.isArray(value.value) && value.value.indexOf(data[value.field]) >= 0) {
                                check = false;
                            } else {
                            }
                        } else {
                            console.log('donnot support this filter: ' + value.condition.toLowerCase());
                        }
                        if (!check) {
                            return check;
                        }
                    });
                }
                if (!check) {
                    return check;
                }
                if (self.paramOrFilter) {
                    $.each(self.paramOrFilter, function (index, value) {
                        var checkOr = false;
                        $.each(value, function (indexFiled, valueField) {
                            self.paramOrFilter[index][indexFiled]['condition'] = valueField.condition.toLowerCase();
                            if (valueField.condition == 'like' && typeof valueField.value === 'string' && typeof valueField.value.indexOf == 'function' && valueField.value.indexOf('%') >= 0) {
                                self.paramOrFilter[index][indexFiled]['value'] = String(valueField.value).toLowerCase().replace('%', '').replace('%', '');
                            }
                        });
                        $.each(value, function (indexFiled, valueField) {
                            if (valueField.condition == 'like') {
                                if ((String(data[valueField.field]).toLowerCase().indexOf(String(valueField.value))) >= 0) {
                                    checkOr = true;
                                }
                            } else if (valueField.condition.toLowerCase() == 'eq') {
                                if (data[valueField.field] == valueField.value) {
                                    checkOr = true;
                                }
                            } else if (valueField.condition.toLowerCase() == 'neq') {
                                if (data[valueField.field] != valueField.value) {
                                    checkOr = true;
                                }
                            } else if (valueField.condition.toLowerCase() == 'gt') {
                                if (data[valueField.field] > valueField.value) {
                                    checkOr = true;
                                }
                            } else if (valueField.condition.toLowerCase() == 'lt') {
                                if (data[valueField.field] < valueField.value) {
                                    checkOr = true;
                                }
                            } else if (valueField.condition.toLowerCase() == 'gteq') {
                                if (data[value.field] >= valueField.value) {
                                    checkOr = true;
                                }
                            } else if (valueField.condition.toLowerCase() == 'lteq') {
                                if (data[valueField.field] <= valueField.value) {
                                    checkOr = true;
                                }
                            } else if (valueField.condition.toLowerCase() == 'in') {
                                if ($.isArray(value.value) && value.value.indexOf(data[value.field]) >= 0) {
                                    checkOr = true;
                                }
                            } else if (valueField.condition.toLowerCase() == 'nin') {
                                if ($.isArray(value.value) && value.value.indexOf(data[value.field]) < 0) {
                                    checkOr = true;
                                }
                            } else {
                                console.log('donnot support this filter: ' + value.condition.toLowerCase());
                            }
                        });
                        if (!checkOr) {
                            check = false;
                        }
                    });
                }
                return check;
            },

            queryCollectionData: function (collection, deferred) {
                if (!deferred) {
                    deferred = $.Deferred();
                }
                var self = this;

                if (typeof server == 'undefined') {
                    if (!self.loop) {
                        self.loop = 1;
                    } else {
                        self.loop++;
                    }
                    if (self.loop < 4) {
                        setTimeout(function () {
                            self.queryCollectionData(collection, deferred)
                        }, 1000);
                    }
                    return deferred;
                }

                self.queryParams = collection.queryParams;
                self.filterParams = self.queryParams.filterParams;
                self.paramOrFilter = self.queryParams.paramOrFilter;
                self.orderParams = self.queryParams.orderParams;
                self.pageSize = self.queryParams.pageSize;
                self.currentPage = self.queryParams.currentPage;
                if (!self.currentPage) {
                    self.currentPage = 1;
                }

                if (self.indexer) {
                    var cacheKey = JSON.stringify(self.filterParams) + JSON.stringify(self.paramOrFilter);
                    self.indexer().search(function(item) {
                        return self.filterProccess(item);
                    }, self.pageSize, self.currentPage, cacheKey, self.orderParams)
                        .done(function(data) {
                            if (0 === data.result.length) {
                                return deferred.resolve({
                                    items: [],
                                    search_criteria: self.queryParams,
                                    total_count: data.total
                                });
                            }
                            // Load Real Data
                            var ordered = data.result,
                                result = new Array(ordered.length),
                                range = ordered.slice(0).sort(function(a, b) {return a - b;}),
                                first = range[0],
                                last = range[range.length - 1];
                            server[self.mainTable].query().bound(first, last)
                                .success(function(cursor, results) {
                                    if (!cursor) {
                                        return;
                                    }
                                    var res = 'value' in cursor ? cursor.value : cursor.key;
                                    result[ordered.indexOf(res[self.keyPath])] = res;
                                    if (res[self.keyPath] === range[0]) {
                                        range.shift();
                                    }
                                    if (range.length) {
                                        cursor['continue'](range[0]);
                                    } else {
                                        cursor['continue']();
                                    }
                                }).execute()
                                .catch(function (err) {
                                    deferred.reject(err);
                                }).then(function () {
                                    result = result.filter(function(val){return val});
                                    deferred.resolve({
                                        items: result,
                                        search_criteria: self.queryParams,
                                        total_count: data.total
                                    });
                                });
                        }).always(function() {
                            deferred.always(function() {
                                self.cleanData();
                                self.destroy();
                            });
                        });
                    return deferred;
                }

                if (typeof server != 'undefined' && typeof server[self.mainTable] != 'undefined') {
                    server[self.mainTable].query()
                        .filter(function (item) {
                            return self.filterProccess(item);
                        })
                        .execute()
                        .catch(function (err) {
                            deferred.reject(err);
                            return deferred;
                        })
                        .then(function (data) {
                            if(self.orderParams) {
                                $.each(self.orderParams, function (index, value) {
                                    if (value.direction == 'DESC') {
                                        data.sort(function (a, b) {
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
                                        data.sort(function (a, b) {
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
                            }
                            var result = data;
                            if (self.pageSize) {
                                var from = (self.currentPage - 1) * self.pageSize;
                                var to = self.currentPage * self.pageSize - 1;
                                if (data.length - 1 < to) {
                                    to = data.length - 1;
                                }
                                if (from == to) {
                                    result = data.slice(from);
                                } else {
                                    result = data.slice(from, to + 1);
                                }
                            }
                            deferred.resolve({
                                items: result,
                                search_criteria: self.queryParams,
                                total_count: (data.length)
                            });
                            deferred.always(function (response) {
                                self.cleanData();
                                self.destroy();
                            });
                        });
                } else {
                    deferred.resolve({
                        items: [],
                        search_criteria: {},
                        total_count: 0
                    });
                }
                return deferred;
            }
        });
    }
);
