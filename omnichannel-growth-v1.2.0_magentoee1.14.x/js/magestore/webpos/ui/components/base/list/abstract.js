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
        'ko',
        'ui/components/base/abstract',
    ],
    function ($, ko, viewAbstract) {
        "use strict";

        return viewAbstract.extend({
            items: ko.observableArray([]),
            columns: ko.observableArray([]),
            isShowHeader: false,
            isSearchable: true,
            pageSize: 10,
            curPage: 1,

            defaults: {
                template: 'base/list/abstract'
            },
            initialize: function () {
                this._super();
                this.searchKey = null;
                this.refresh = true;
                this.isLoading = false;
                //this._render(); //TODO
            },
            _render: function() {
                this._prepareColumns();
                this._prepareItems();
            },

            _prepareItems: function () {

            },
            _prepareColumns: function () {

            },

            addItems: function (items) {
                for (var i in items) {
                    items[i].columns = this.columns;
                }
                ko.utils.arrayPushAll(this.items, items);
            },
            setItems: function (items) {
                if(this.refresh === true) {
                    this.resetData();
                }
                this.addItems(items);
            },
            resetData: function () {
                // this.destroyViewModel();
                this.items([]);
            },
            destroyViewModel:function () {
                this.destroy();
                this.clear();
            },
            addColumn: function (column) {
                column.grid = this;
                var exited = false;
                for(var i in this.columns()) {
                    if(this.columns()[i].rowText == column.rowText) {
                        exited = true;
                        break;
                    }
                }
                if(!exited) {
                    this.columns.push(column);
                }
            },
            filter: function () {

            },
            canLoad: function() {
                return !this.isLoading;
            },
            startLoading: function() {
                this.isLoading = true;
            },
            finishLoading: function() {
                this.isLoading = false;
            }
        });
    }
);
