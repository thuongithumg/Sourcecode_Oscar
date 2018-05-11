/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'ko',
            'Magestore_Webpos/js/model/catalog/product-factory',            
            'Magestore_Webpos/js/view/base/grid/cell-grid'
        ],
        function ($, ko, ProductFactory, cellGrid) {
            "use strict";

            return cellGrid.extend({
                defaults: {
                    template: 'Magestore_Webpos/catalog/catalog-list',
                },
                initialize: function () {
                    this.model = ProductFactory.get();
                    this._super();
                },
                _prepareCollection: function () {
                    this.filterAttribute = 'sku';
                    if(this.collection == null) {
                        this.collection = this.model.getCollection();
                    }
                    this.pageSize = 31;
                    this.collection.setPageSize(this.pageSize);
                    this.collection.setCurPage(this.curPage);
                    if (this.searchKey) {
                        this.collection.addFieldToFilter(this.filterAttribute, "%" + this.searchKey + "%", 'like');
                    }
                }
            });
        }
);