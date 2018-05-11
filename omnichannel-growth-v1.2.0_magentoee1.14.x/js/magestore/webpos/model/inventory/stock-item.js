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
            'model/abstract',
            'model/resource-model/magento-rest/inventory/stock-item',
            'model/resource-model/indexed-db/inventory/stock-item',
            'model/collection/inventory/stock-item',
            'mage/translate',
            'action/notification/add-notification'
        ],
        function ($, modelAbstract, restResource, indexedDbResource, collection, $t, AddNoti) {
            "use strict";
            return modelAbstract.extend({
                event_prefix: 'stock_item',
                sync_id: 'stock_item',
                product: {},
                initialize: function () {
                    this._super();
                    this.setResource(restResource(), indexedDbResource());
                    this.setResourceCollection(collection());
                },
                /*
                 * Load stock item by product_id
                 * 
                 * @param {int} productId
                 * @returns {Deferred}
                 */
                loadByProductId: function (productId) {
                    var self = this;
                    var deferred = $.Deferred();
                    // var collection = this.getCollection();
                    // collection.reset();
                    // collection.addFieldToFilter('product_id', productId, 'eq');
                    var cdeferred = self.load(productId);
                    cdeferred.done(function (data) {
                        self.data = data;
                        return deferred.resolve(self);
                    });
                    return deferred;
                },
                /**
                 * Update Qty to stock-item then reload the stock-data in product list
                 * 
                 * @param {int} productId
                 * @param {int|float} changeQty
                 * @returns {Deferred}
                 */
                updateQty: function (productId, changeQty) {
                    var start = Date.now();
                    var deferred = $.Deferred();
                    this.setMode('offline').setPush(false).setLog(false);
                    var cdeferred = this.loadByProductId(productId);
                    var self = this;
                    cdeferred.done(function (data) {

                        var manageStock = window.webposConfig.manage_stock;
                        manageStock = manageStock > 0 ? true : false;
                        manageStock = self.data['use_config_manage_stock'] ? manageStock : self.data['manage_stock'];  
                        /* do not manage stock */
                        if(!manageStock) {
                            return;
                        }
                        
                        var backorders = WEBPOS.getConfig('cataloginventory/item_options/backorders');
                        backorders = self.data['use_config_backorders'] ? backorders : self.data['backorders'];  
                        
                        var minQty = parseFloat(WEBPOS.getConfig('cataloginventory/item_options/min_qty'));
                        self.data.qty = parseFloat(self.data.qty) + parseFloat(changeQty);
                        if (self.data.qty <= minQty && backorders == 0) {
                            /* not backorders */
                            self.data.is_in_stock = false;
                        } else {
                            self.data.is_in_stock = true;
                        }
                        self.save(deferred);
                    });
                    
                    deferred.done(function(data){

                    });
                    
                    return deferred;
                },
                /**
                 * Load stock items by product ids
                 * 
                 * @param {array} productIds
                 * @returns {Deferred}
                 */
                loadByProductIds: function (productIds) {
                    var deferred = $.Deferred();
                    if(productIds.length > 1) {
                        var collection = this.getCollection().addFieldToFilter('product_id', productIds, 'in');
                        var cdeferred = collection.load();
                        cdeferred.done(function (data) {
                            deferred.resolve(data.items);
                        });
                    }else{
                        var cdeferred = this.load(productIds[0]);
                        cdeferred.done(function (data) {
                            deferred.resolve([data]);
                        });
                    }
                    return deferred;
                },
                /*
                 * Add stock data to list of products
                 */
                addStockItemToProducts: function (products) {
                    var deferred = $.Deferred();
                    var productIds = [];
                    for (var i in products) {
                        productIds.push(products[i].id);
                    }
                    var collection = this.getCollection().addFieldToFilter('product_id', productIds, 'in');
                    var cdeferred = collection.load();
                    var self = this;
                    cdeferred.done(function (data) {
                        if (data.items) {
                            for (var i in data.items) {
                                for (var j in products) {
                                    if (products[j].id == data.items[i].product_id) {
                                        products[j].stock = data.items[i];
                                        break;
                                    }
                                }
                            }
                        }
                        return deferred.resolve(products);
                    });
                    return deferred;
                },
                /**
                 * Get product from stock-item
                 * 
                 */
                getProduct: function () {
                    return this.product;
                },
                /**
                 * Set product to stock-item
                 * 
                 */
                setProduct: function (productModel) {
                    this.product = productModel;
                    return this;
                },
                /**
                 * Get min sale qty
                 * 
                 * @param {} stockItem
                 * @param {int} customerGroup
                 * @returns {int|float}
                 */
                getMinSaleQty: function (stockItem, customerGroup) {
                    if(!stockItem){
                        return 1;
                    }
                    var minSaleQty = parseFloat(stockItem['min_sale_qty']);
                    if (stockItem['use_config_min_sale_qty']) {
                        minSaleQty = 0;
                        var minSaleQtyConfigs = WEBPOS.getConfig('cataloginventory/item_options/min_sale_qty');
                        minSaleQtyConfigs = JSON.parse(minSaleQtyConfigs);
                        var found = false;
                        var defaultMinQty = 0;
                        if (Array.isArray(minSaleQtyConfigs)) {
                            for (var i in minSaleQtyConfigs) {
                                /* check by group customer */
                                if (customerGroup == minSaleQtyConfigs[i]['group']) {
                                    minSaleQty = parseFloat(minSaleQtyConfigs[i].value);
                                    found = true;
                                }
                                /* get default value for all customer group */
                                if (minSaleQtyConfigs[i]['group'] == 32000) {
                                    defaultMinQty = parseFloat(minSaleQtyConfigs[i].value);
                                }
                            }
                            if (!found) {
                                minSaleQty = defaultMinQty;
                            }
                        } else {
                            minSaleQty = parseFloat(minSaleQtyConfigs);
                        }
                    }
                    return minSaleQty;
                },
                /**
                 * Get max sale qty
                 * 
                 * @param {} stockItem
                 * @returns {int|float}
                 */
                getMaxSaleQty: function (stockItem) {
                    if(stockItem){
                        var maxSaleQty = parseFloat(stockItem['max_sale_qty']);
                        if (stockItem['use_config_max_sale_qty']) {
                            maxSaleQty = parseFloat(WEBPOS.getConfig('cataloginventory/item_options/max_sale_qty'));
                        }
                        return maxSaleQty;
                    }else{
                        return 0;
                    }
                },                
                /**
                 * 
                 * @param {type} requestQty
                 * @param {type} childId
                 * @param {type} customerGroup
                 * @returns {}
                 */
                validateQtyInCart: function (requestQty, stockItem, customerGroup) {
                    var result = {'can_buy': false, 'message': ''};
                    if (!customerGroup) {
                        customerGroup = 0;
                    }
                    /* check max_sale_qty */
                    var maxSaleQty = this.getMaxSaleQty(stockItem);
                    if (requestQty > maxSaleQty) {
                        result['can_buy'] = false;
                        result['message'] = $t('The most you may purchase is') + ' ' + maxSaleQty;
                        return result;
                    }
                    /* check min_sale_qty */
                    var minSaleQty = this.getMinSaleQty(stockItem, customerGroup);
                    if (requestQty < minSaleQty) {
                        result['can_buy'] = false;
                        result['message'] = $t('The fewest you may purchase is') + ' ' + minSaleQty;
                        return result;
                    }
                    result['can_buy'] = true;
                    return result;
                },
                /**
                 * Get is_in_stock status
                 * 
                 * @returns {Boolean}
                 */
                isInStock: function() {
                    if(this.data['is_in_stock'] === false) {
                        return false;
                    }
                    var minQty = parseFloat(WEBPOS.getConfig('cataloginventory/item_options/min_qty'));
                    var qty = parseFloat(this.data.qty);   
                    if(qty > minQty) {
                        return true;
                    }
                    return false;
                }
            });
        }
);