/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [   
            'require',
            'ko',
            'jquery',
            'uiElement',
            'mageUtils',
            'mage/translate',
            'Magestore_Webpos/js/model/inventory/stock-item-factory',
            'Magestore_Webpos/js/helper/general'
        ],
        function (require, ko, $, Element, utils, $t, StockItemFactory, Helper) {
            "use strict";
            return Element.extend({
                childStocks: {},
                isStockOnline: ko.pureComputed(function(){
                    return (Helper.isUseOnline('products') && Helper.isUseOnline('stocks'));
                }),
                initialize: function () {
                    this._super();
                },
                
                /**
                 * Get isablable status
                 * 
                 * @returns {Boolean}
                 */
                isSalable: function (childId) {
                    if (typeof childId == 'undefined') {
                        this.is_salable = true;
                        //this.salableChecked = true;
                    }
                    //if (!this.salableChecked) {
                        var minQty = parseFloat(WEBPOS.getConfig('cataloginventory/item_options/min_qty'));
                        this.is_salable = false;
                        var stockItem = this.getChildStock(childId);

                        if (!stockItem) {
                            /* do not found stock data */
                            this.is_salable = false;
                            this.salableChecked = true;
                            return this.is_salable;
                        }
                        var qty = this.getQty(childId);
                        var isManageStock = this.isManageStock(childId);
                        var isInStock = this.isInStock(childId);
                        var isBackorder = this.isBackorder(childId);
                        if (!isManageStock) {
                            /* do not manage stock */
                            this.is_salable = true;
                        } else {
                            /* check available qty */
                            if (qty > minQty && isInStock) {
                                this.is_salable = true;
                            } else {
                                /* allow backorders */
                                if (isBackorder > 0) {
                                    this.is_salable = true;
                                } else {
                                    /* do not allow backorders  */
                                    this.is_salable = false;
                                }
                            }
                        }
                        //this.salableChecked = true;
                    //}
                    return this.is_salable;
                },
                canBuy: function (requestQty, childId, customerGroup) {
                    var self = this;
                    var result = {'can_buy': false, 'message': ''};
                    var minQty = parseFloat(WEBPOS.getConfig('cataloginventory/item_options/min_qty'));
                    this.can_buy = false;
                    var stockItem = this.getChildStock(childId);
                    var inStockQty = this.getQty(childId);
                    var qty = inStockQty- parseFloat(requestQty);
                    var isManageStock = this.isManageStock(childId);
                    var isInStock = this.isInStock(childId);
                    var isBackorder = this.isBackorder(childId);

                    if (!isManageStock) {
                        /* do not manage stock */
                        this.can_buy = true;
                    } else {
                        /* check available qty */
                        if (qty >= minQty && isInStock) {
                            this.can_buy = true;
                        } else {
                            /* allow backorders */
                            if (isBackorder > 0) {
                                this.can_buy = true;
                            } else {
                                /* do not allow backorders */
                                this.can_buy = false;
                            }
                        }
                    }

                    if (this.can_buy === true) {
                        var stockModel = StockItemFactory.get();
                        result = stockModel.validateQtyInCart(requestQty, stockItem, customerGroup);
                        this.can_buy = result.can_buy;
                    } else {
                        result['can_buy'] = false;
                        if (isInStock && requestQty > minQty + 1) {
                            var productName = this.getProduct().data['product_name'];
                            result['message'] = utils.template($t('We don\'t have as many "${ $.productName }" as you requested. The current in-stock qty is "${ $.inStockQty}"'),
                                    {productName: productName, inStockQty: inStockQty});
                        } else {
                            result['message'] = $t('This product is currently out of stock');
                        }
                    }
                    if(!stockItem && !self.isStockOnline()){
                        result['stock_error'] = true;
                    }
                    return result;
                },
                /**
                 * Get qty of child product
                 * 
                 * @param {int} childId
                 * @returns {int|float}
                 */
                getQty: function (childId) {
                    var qty = 0;
                    var child = this.getChildStock(childId);
                    qty = child ? child.qty : qty;
                    return parseFloat(qty);
                },
                /**
                 * Get min_sale_qty of child product
                 * 
                 * @param {int} childId
                 * @param {int} customerGroup
                 * @returns {int|float}
                 */                
                getMinSaleQty: function(childId, customerGroup) {
                    var stockModel = StockItemFactory.get();
                    var stockItem = this.getChildStock(childId);
                    return stockModel.getMinSaleQty(stockItem, customerGroup);                    
                },
                /**
                 * Get max_sale_qty of child product
                 * 
                 * @param {int} childId
                 * @returns {int|float}
                 */                
                getMaxSaleQty: function(childId) {
                    var stockModel = StockItemFactory.get();
                    var stockItem = this.getChildStock(childId);
                    return stockModel.getMaxSaleQty(stockItem);                    
                },                
                /**
                 * Update qty of child product
                 * 
                 * @param {int|float} changeQty
                 * @param {int} childId
                 */
                updateStock: function (changeQty, childId) {
                    var childStock = this.getChildStock(childId);
                    /* do not manage stock */
                    if (!this.isManageStock(childId)) {
                        return;
                    }
                    var minQty = parseFloat(WEBPOS.getConfig('cataloginventory/item_options/min_qty'));
                    childStock.qty = parseFloat(childStock.qty) + parseFloat(changeQty);
                    if (childStock.qty <= minQty) {
                        childStock.is_in_stock = false;
                    }
                    var stockModel = StockItemFactory.get();
                    /* save stock data to local database */
                    stockModel.setMode('offline').setPush(false);
                    stockModel.setData(childStock).save();
                },
                /**
                 * Mange stock of product or not
                 * 
                 * @return {Boolean};
                 */
                isManageStock: function (childId) {
                    var manageStock = WEBPOS.getConfig('cataloginventory/item_options/manage_stock');
                    manageStock = manageStock > 0 ? true : false;
                    var child = this.getChildStock(childId);
                    if (child) {
                        manageStock = child['use_config_manage_stock'] ? manageStock : child['manage_stock'];
                    }
                    return manageStock;
                },
                /**
                 * Allow to backorder or not
                 * 
                 * @returns {Boolean}
                 */
                isBackorder: function (childId) {
                    var backorders = WEBPOS.getConfig('cataloginventory/item_options/backorders');
                    var child = this.getChildStock(childId);
                    if (child) {
                        backorders = child['use_config_backorders'] ? backorders : child['backorders'];
                    }
                    return backorders;
                },
                /**
                 * Allow to backorder or not
                 * 
                 * @returns {Boolean}
                 */
                isInStock: function (childId) {
                    var isInStock = false;
                    var child = this.getChildStock(childId);
                    if (child) {
                        isInStock = child['is_in_stock'];
                    }
                    return isInStock;
                },
                getChildStock: function (childId) {
                    return {};
                },
                /**
                 * 
                 * @returns {object}
                 */
                getProduct: function () {
                    return this.product;
                },
                /**
                 * 
                 * @param {object} product
                 * @returns {object}
                 */
                setProduct: function (product) {
                    this.product = product;
                    return this;
                },
                getChildProductIds: function () {
                    return []
                },
                refreshStockData: function () {
                    this.salableChecked = false;
                    this.childStocks = {};
                }
            });
        }
);