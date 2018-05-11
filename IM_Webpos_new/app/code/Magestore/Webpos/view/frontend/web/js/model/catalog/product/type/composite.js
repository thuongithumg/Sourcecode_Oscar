/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'ko',
            'underscore',
            'Magestore_Webpos/js/model/catalog/product/type/abstract',
        ],
        function ($, ko, _, typeAbstract) {
            "use strict";
            return typeAbstract.extend({
                getChildStock: function(childId) {
                    var child = this.childStocks[childId];
                    if(!child) {
                        if(childId == this.getProduct().data.id) {
                            /* get stock of parent product */
                            this.childStocks[childId] = this.getProduct().data.stock;
                            child = this.childStocks[childId];
                        } else {
                            /* get stock of child */
                            var stocks = this.getProduct().data.stocks;
                            for (var i in stocks) {
                                if (childId) {
                                    if (stocks[i].product_id == childId) {
                                        child = stocks[i];
                                        this.childStocks[childId] = child;
                                        break;
                                    }
                                }
                            }
                            if (typeof this.childStocks[childId] == 'undefined' && typeof window.webposStockItemData != 'undefined' && typeof window.webposStockItemData[childId] != 'undefined'){
                                child = this.childStocks[childId] = window.webposStockItemData[childId];
                            }
                        }
                    }
                    return child;
                },
                isSalable: function (childId) {
                    /* check isalable of child */
                    if(childId) {
                        return this._super(childId);
                    }
                    /* check isalable of parent */
                    //if(!this.salableChecked) {
                        this.is_salable = false;
                        /* check isalable of parent stock */
                        if(this.getChildStock(this.getProduct().data.id)) {
                            /* check out-stock of composite product */
                            this.getProduct().data.stock.qty=9999;
                            if(!this.isSalable(this.getProduct().data.id)) {
                               this.is_salable = false;
                               this.salableChecked = true;
                               return this.is_salable;
                            }
                        }
                        /* composite product is in-stock */
                         /* then check isalable of childs */
                        for(var i in this.getProduct().data.childs) {
                            var childId = this.getProduct().data.childs[i];
                            /* check isalable of one child */
                            if(this.isSalable(childId)) {
                                this.is_salable = true;
                                break;
                            }
                        }
                        
                        this.salableChecked = true;
                   // }
                    return this.is_salable;
                },         
            });
        }
);