/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'ko',
            'Magestore_Webpos/js/model/catalog/product/type/abstract',
        ],
        function ($, ko, typeAbstract) {
            "use strict";
            return typeAbstract.extend({
                childStocks: {},
                isSalable: function(childId) {
                    if(!childId) {
                        childId = this.product.data['id'];
                    }
                    return this._super(childId);
                },
                canBuy: function(requestQty, childId, customerGroup) {
                    if(!childId) {
                        childId = this.product.data['id'];
                    }
                    return this._super(requestQty, childId, customerGroup);
                },                
                getChildProductIds: function() {
                    return [this.product.data['id']];
                },                
                getChildStock: function(childId) {
                    if(this.product.data && this.product.data['id']) {
                        childId = this.product.data['id'];
                    }
                    var child = this.childStocks[childId];
                    if(!child) {
                        var stocks = this.getProduct().data.stocks;
                        if (typeof stocks == 'undefined'){
                            var stocks = this.getProduct().data.stock;
                        }
                        for (var i in stocks) {
                            if (childId) {
                                if (stocks[i].product_id == childId) {
                                    child = stocks[i];
                                    this.childStocks[childId] = child;
                                    break;
                                }
                            }
                        }  
                    }
                    return child;
                },                
            });
        }
);