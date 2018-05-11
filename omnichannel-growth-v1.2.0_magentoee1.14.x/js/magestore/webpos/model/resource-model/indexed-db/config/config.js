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
        'model/resource-model/indexed-db/abstract'
    ],
    function ($,Abstract) {
        "use strict";
        return Abstract.extend({
            mainTable: 'core_config_data',
            keyPath: 'path',
            indexes: {
                path: {unique: true},
                scope: {},
                scope_id: {}
            },
            queryCollectionData: function (collection, deferred) {
                if(!deferred) {
                    deferred = $.Deferred();
                }      
 
                var self = this;   

                if(typeof server == 'undefined') {
                    if(!self.loop) {
                        self.loop = 1;
                    } else {
                        self.loop++;
                    }
                    if(self.loop < 15) {
                        setTimeout(function() {self.queryCollectionData(collection, deferred)}, 1000);
                    }
                    return deferred;
                }

                self.queryParams = collection.queryParams;
                self.filterParams = self.queryParams.filterParams;
                self.paramOrFilter = self.queryParams.paramOrFilter;
                self.orderParams = self.queryParams.orderParams;
                self.pageSize = self.queryParams.pageSize;
                self.currentPage = self.queryParams.currentPage;
                if(!self.currentPage){
                    self.currentPage = 1;
                }

                $.each(self.filterParams, function( index, value ){
                    self.filterParams[index]['condition'] = value.condition.toLowerCase();
                    if(value.condition == 'like' && typeof value.value === 'string' && typeof value.value.indexOf == 'function' && value.value.indexOf('%') >= 0) {
                        self.filterParams[index]['value'] = String(value.value).toLowerCase().replace('%', '').replace('%', '');
                    }
                });
                if(self.paramOrFilter){
                    $.each(self.paramOrFilter, function( index, value ) {
                        $.each(value, function (indexFiled, valueField) {
                            self.paramOrFilter[index][indexFiled]['condition'] = valueField.condition.toLowerCase();
                            if(valueField.condition == 'like' && typeof valueField.value === 'string' && typeof valueField.value.indexOf == 'function' && valueField.value.indexOf('%') >= 0) {
                                self.paramOrFilter[index][indexFiled]['value'] = String(valueField.value).toLowerCase().replace('%', '').replace('%', '');
                            }
                        });
                    });
                }
                
                if(typeof server != 'undefined' && typeof server[self.mainTable] != 'undefined') {
                    server[self.mainTable].query()
                        .filter(function (item) {
                            return self.filterProccess(item);
                        })
                        .execute().then(function (data) {
                        $.each(self.orderParams, function (index, value) {
                            if (value.direction == 'DESC') {
                                data.sort(function (a, b) {
                                    var x = a[value.field];
                                    var y = b[value.field];
                                    if (typeof x == "string"){
                                        x = x.toLowerCase();
                                    }
                                    if (typeof y == "string"){
                                        y = y.toLowerCase();
                                    }
                                    return ((x > y) ? -1 : ((x < y) ? 1 : 0));
                                });
                            } else {
                                data.sort(function (a, b) {
                                    var x = a[value.field];
                                    var y = b[value.field];
                                    if (typeof x == "string"){
                                        x = x.toLowerCase();
                                    }
                                    if (typeof y == "string"){
                                        y = y.toLowerCase();
                                    }
                                    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
                                });
                            }
                        });

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
                    });
                }else {
                    deferred.resolve({
                        items: {},
                        search_criteria: {},
                        total_count: 0
                    });
                }
                return deferred;
            },           
            getValue: function (deferredParent, path, scopeType , scopeCode){
                var collection = {
                    queryParams: {
                        filterParams: [{
                            "field" : 'path',
                            "value" : path,
                            "condition" : 'eq'
                        }],
                        orderParams: [],
                        pageSize: '',
                        currentPage: ''
                    }
                };
                var deferred = $.Deferred();
                var self = this;
                this.queryCollectionData(collection, deferred);
                deferred.done(function(data) {
                    if(!data.total_count){
                        deferredParent.resolve(null);
                    }
                    if(data.total_count >= 1){
                        deferredParent.resolve(data.items[0].value);
                    }
                });
            }
        });
    }
);