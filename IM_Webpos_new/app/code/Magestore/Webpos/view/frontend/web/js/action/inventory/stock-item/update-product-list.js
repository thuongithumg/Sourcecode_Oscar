/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
        [
            'Magestore_Webpos/js/view/layout',
            'Magestore_Webpos/js/model/catalog/product-factory',
            'Magestore_Webpos/js/helper/general'
        ],
        /* update product list after changed stock items */
        function (ViewManager, ProductFactory, Helper) {
            'use strict';
            return function (stockItems) {
                var viewManager = require('Magestore_Webpos/js/view/layout');              
                var indexedItems = {};
                for (var i in stockItems) {
                    indexedItems[stockItems[i].sku] = stockItems[i];
                }
  
                var productListViewModel = viewManager.getSingleton('view/catalog/product-list');
                var products = productListViewModel.items();
                for (var i in products) {
                    var updatedStock = false;
                    var product = products[i];
                    for (var j in product.stocks) {
                        var sku = product.stocks[j].sku;
                        /* find match product by sku */
                        if (indexedItems[sku]) {
                            /* update stock data in product */
                            product.stocks[j] = indexedItems[sku];
                            updatedStock = true;
                        }
                    }
                    if(typeof indexedItems[product.sku] != 'undefined'){
                        product.qty = indexedItems[product.sku].qty;
                    }
                    if (updatedStock || Helper.isStockOnline()) {
                        /* update isalable status of product */
                        var productModel = ProductFactory.get();
                        productModel.setData(product);
                        product.isShowOutStock = !productModel.isSalable();
                    }
                }
                /* bind products to view model */
                productListViewModel.loadLocalConfig();
                productListViewModel.refresh = true;
                /* do not reload product collection --> error if do not display out-stock products */
                //productListViewModel.collection = null;
                //productListViewModel._prepareItems();                
                productListViewModel.setItems(products);
            }
        }
             
);
