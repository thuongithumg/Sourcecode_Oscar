/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'ko',
            'underscore',
            'Magestore_Webpos/js/model/event-manager',
            'Magestore_Webpos/js/model/catalog/product-factory',
            'Magestore_Webpos/js/model/inventory/stock-item-factory',
            'Magestore_Webpos/js/helper/general'
        ],
        function ($, ko, _,  eventManager, ProductFactory, StockItemFactory, Helper) {
            "use strict";

            return {
                execute: function () {
                    eventManager.observer('catalog_product_collection_load_after', function (event, eventData) {
                        if(Helper.isStockOnline()){
                            return true;
                        }
                        var collection = eventData.collection;
                        var items = collection.data.items;
                        var childIds = [];
                        
                        /* prepare child ids */
                        var product = ProductFactory.get();
                        
                        for (var i in items) {    
                            product.setData(items[i]);
                            items[i].childs = product.getTypeInstance().getChildProductIds();
                            childIds = childIds.concat(items[i].childs);
                            childIds = childIds.concat([items[i].id]);
          
                        }
                        childIds = _.uniq(childIds);
                        /* there is no product id */
                        if (childIds.length == 0) {
                            return;
                        }
                        
                        collection.deferred = $.Deferred();
                        
                        /* load child stocks */
                        var stockItem = StockItemFactory.get().setMode('offline');
                        var ccdeferred = stockItem.loadByProductIds(childIds);
                        var self = this;
                        /* set child stocks to product */
                        ccdeferred.done(function (stockItems) {
                            var stockArray = {};
                            for (var i in stockItems) {
                                var stockItem = stockItems[i];
                                stockArray[stockItem.product_id] = stockItem;
                            }
                            var productModel = ProductFactory.get();
                            for (var i in items) {
                                items[i].isShowOutStock = false;
                                items[i].stocks = [];
                                for (var j in items[i].childs) {
                                    var childId = items[i].childs[j];
                                    /* set stock data to product item */
                                    if (stockArray[childId]) {
                                        items[i].stocks.push(stockArray[childId]);
                                    }
                                }
                                /* set stock data to composite product */
                                if (stockArray[items[i].id]) {
                                    items[i].stock = stockArray[items[i].id];
                                }
                                /* calculate is_in_stock status */
                                productModel.setData(items[i]);
                                var isOutStock = !productModel.isSalable();
                                items[i].isShowOutStock = isOutStock;
                            }
                            stockArray = {};
                            stockItems = {};
                            /* do not render product list again */
                            collection.data.items = items;

                            /* done deferred */
                            collection.deferred.resolve(collection);
                        });
                    });
                }
            }
        }
);