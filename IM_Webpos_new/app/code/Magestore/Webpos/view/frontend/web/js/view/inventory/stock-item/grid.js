/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'ko',
            'mageUtils',
            'Magestore_Webpos/js/model/inventory/stock-item-factory',
            'mage/translate',
            'Magestore_Webpos/js/view/base/grid/collection-grid',
            'Magestore_Webpos/js/model/event-manager',
            'Magestore_Webpos/js/action/notification/add-notification',
            'Magestore_Webpos/js/helper/alert',
            'Magestore_Webpos/js/helper/general'

        ],
        function ($, ko, utils, StockItemFactory, $t, collectionGrid, eventManager, addNotification, Alert, Helper) {
            "use strict";

            ko.bindingHandlers.bootstrapSwitchOn = {
                init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
                    $(element).iosCheckbox();
                    $(element).on("switchchange", function (e) {
                        valueAccessor()(e.target.checked);
                    });            
                }
            };

            return collectionGrid.extend({
                items: ko.observableArray([]),
                columns: ko.observableArray([]),
                locationName: ko.observable(''),
                locationAddress: ko.observable(''),
                editing: false,
                defaults: {
                    template: 'Magestore_Webpos/inventory/stock-item/grid', },
                initialize: function () {
                    this.changedData = ko.observable(false);
                    /* prepare model */
                    var mode = (Helper.isUseOnline('stocks'))?'online':'offline';
                    this.mode = mode;
                    this.model = StockItemFactory.get();
                    this.model.setMode(this.mode);
                    this.model.setPush(true);
                    /* get config data */
                    this.config = {'manage_stock': WEBPOS.getConfig('cataloginventory/item_options/manage_stock'),
                        'backorders': WEBPOS.getConfig('cataloginventory/item_options/backorders')};
                    this.locationName($t('Location') + ': ' + WEBPOS.getConfig('location_name'));
                    this.locationAddress(WEBPOS.getConfig('location_address'));

                    this._super();
                    /* listen events */
                    //this.listenLoadedLocationEvent();
                    this.listenStockItemPullAfterEvent();
                    this.listenStockItemSaveAfterEvent();
                    this.listenStockItemMassUpdateAfterEvent();
                    this.listenManageStockShowContainerAfterEvent();
                    /* translate labels */
                    this.searchPlaceHolder = $t('Name/ SKU');
                },
                /**
                 * Prepare stock item collection
                 * 
                 */
                _prepareCollection: function () {
                    this.searchAttribute = 'sku';
                    if (this.collection == null) {
                        this.collection = this.model.getCollection();
                    }
                    var mode = (Helper.isUseOnline('stocks'))?'online':'offline';
                    this.collection.setMode(mode);
                    this._removeCustomSalesFromCollection();
                    this.collection.setPageSize(this.pageSize);
                    this.collection.setCurPage(this.curPage);
                    if (this.searchKey) {
                        if(mode == 'online'){
                            this.collection.addFieldToFilter([
                                ['e.sku', "%" + this.searchKey + "%", 'like'],
                                ['cpev.value', "%" + this.searchKey + "%", 'like'],
                            ]);
                        }else{
                            this.collection.addFieldToFilter([
                                ['sku', "%" + this.searchKey + "%", 'like'],
                                ['name', "%" + this.searchKey + "%", 'like'],
                            ]);
                        }
                    }
                },
                _removeCustomSalesFromCollection: function() {
                    if(this.collection) {
                        this.collection.addFieldToFilter('sku', 'webpos-customsale', 'neq');
                    }
                    return this;
                },
                _render: function () {
                    this._super();
                    $("#search-header-stock").focus();
                },

                addItems: function (items) {
                    for (var i in items) {

                        var manageStock = items[i].use_config_manage_stock ? this.config.manage_stock : items[i].manage_stock;
                        var backOrder = items[i].use_config_backorders ? this.config.backorders : items[i].backorders;
                        backOrder = (backOrder > 0) ? true : false;
                        items[i].manage_stock = manageStock;
                        items[i].backorders = backOrder;
                        items[i].columns = this.columns;
                        items[i].qtyVal = ko.observable(items[i].qty);
                        items[i].isInStock = ko.observable(items[i].is_in_stock);
                        items[i].manageStock = ko.observable(items[i].manage_stock);
                        items[i].backOrder = ko.observable(items[i].backorders);
                        items[i].changedData = ko.observable(false);
                        items[i].finishedUpdate = ko.observable(false);
                        // items[i].className = '';
                        // if(!webposConfig.can_adjust_stock){
                        //     items[i].className = 'disabled';
                        // }
                        items[i].canAdjustStock = ko.computed( function () {
                            if(webposConfig.can_adjust_stock){
                                return false;
                            }
                            return true;
                        });
                    }
                    ko.utils.arrayPushAll(this.items, items);
                },
                changeStockData: function (data, event) {
                    if (event.target.type === 'text') {
                        if (!this.checkInputNumber(data)) {
                            /* show notification */
                            //addNotification($t('Wrong qty value') + ': "' + data.qtyVal() + '"', true, 'danger', $t('Warning:'));
                            Alert({title: 'Error', content: 'Wrong qty value' + ': "' + data.qtyVal() + '"'});
                            data.qtyVal(0);
                            return;
                        }
                        data.qty = data.qtyVal();
                        this.model.setData(data);
                        data.isInStock(this.model.isInStock());
                    }
                    data.changedData(true);
                    data.finishedUpdate(false);
                    this.changedData(true);
                    this.editing = true;
                },
                checkInputNumber: function (item) {
                    if (isNaN(item.qtyVal())) {
                        return false;
                    }
                    return true;
                },
                /**
                 * Update Stock data
                 * @param object data
                 * @param object event
                 */
                updateStock: function (data, event) {
                    data.changedData(false);
                    data.qtyVal(parseFloat(data.qtyVal()));
                    var model = StockItemFactory.get();
                    model.setMode(this.mode).setPush(true);
                    model.setData(data);
                    /* check if change stock config */
                    if (model.data['manage_stock'] !== data.manageStock()) {
                        model.data['use_config_manage_stock'] = false;
                    }
                    if (model.data['backorders'] != data.backOrder()) {
                        model.data['use_config_backorders'] = false;
                    }
                    model.data['is_in_stock'] = data.isInStock();
                    model.data['manage_stock'] = data.manageStock();
                    model.data['backorders'] = data.backOrder() ? '1' : '0';
                    model.data['qty'] = data.qtyVal();

                    var deferred = model.save();
                    deferred.done(function (response) {
                        data.finishedUpdate(true);
                    });

                    /* check editing status */
                    this.editing = false;
                    for (var i in this.items()) {
                        var item = this.items()[i];
                        if (typeof item.changedData != 'undefined' && item.changedData() === true) {
                            this.editing = true;
                            break;
                        }
                    }
                    if (this.editing === false) {
                        /* delay this action 10 seconds */
                        this.editing = true;
                        var self = this;
                        setTimeout(function () {
                            self.editing = false;
                        }, 10000);
                    }
                },
                /**
                 * Mass Update Stock data
                 * @param object data
                 * @param object event
                 */
                massUpdateStock: function (data, event) {
                    data.changedData(false);
                    var changedItems = [];
                    var items = data.items();
                    for (var i in items) {
                        if (items[i].changedData() === true) {
                            items[i].qtyVal(parseFloat(items[i].qtyVal()));
                            /* check if change stock config */
                            if (items[i]['manage_stock'] !== items[i].manageStock()) {
                                items[i]['use_config_manage_stock'] = false;
                            }
                            if (items[i]['backorders'] != items[i].backOrder()) {
                                items[i]['use_config_backorders'] = false;
                            }

                            items[i]['is_in_stock'] = items[i].isInStock();
                            items[i]['manage_stock'] = items[i].manageStock();
                            items[i]['backorders'] = items[i].backOrder() ? '1' : '0';
                            items[i]['qty'] = items[i].qtyVal();

                            items[i].changedData(false);
                            items[i].finishedUpdate(true);
                            changedItems.push(items[i]);
                        }
                    }
                    /* remove function attributes in stock data before mass-updating */
                    data.model.removeFuncBeforeMassUpdate = true;
                    data.model.massUpdate({"items": changedItems}).done(function (response) {
                        eventManager.dispatch('stock_item_page_massupdate_after', {'items': changedItems});
                    });
                    /* update editing status */
                    var self = this;
                    /* delay this action 10 seconds */
                    setTimeout(function () {
                        self.editing = false;
                    }, 10000);
                },
                /**
                 * Reload stock item data
                 */
                reloadData: function () {
                    if (this.editing === false) {
                        this.refresh = true;
                        this._prepareItems();
                    }
                },
                /**
                 * Listen webpos_load_location_after event
                 */
                listenLoadedLocationEvent: function () {
                    var self = this;
                    eventManager.observer('webpos_load_location_after', function (event, eventData) {
                        self.locationName($t('Location') + ': ' + WEBPOS.getConfig('location').display_name);
                        self.locationAddress(WEBPOS.getConfig('location').address);
                    });
                },
                /**
                 * Listen stock_item_pull_after event
                 */
                listenStockItemPullAfterEvent: function () {
                    var self = this;
                    eventManager.observer('stock_item_pull_after', function (event, eventData) {
                        self.reloadData();
                    });
                },
                /**
                 * Listen stock_item_save_after event
                 * Reload stock item list
                 */
                listenStockItemSaveAfterEvent: function () {
                    var self = this;
                    eventManager.observer('stock_item_save_after', function (event, eventData) {
                        self.reloadData();
                    });
                },
                /**
                 * Listen stock_item_massupdate_after event
                 * Reload stock item list
                 */
                listenStockItemMassUpdateAfterEvent: function () {
                    var self = this;
                    eventManager.observer('stock_item_massupdate_after', function (event, eventData) {
                        self.reloadData();
                    });
                },
                /**
                 * Listen manage_stock_show_container_after event
                 * Reload stock item list after open Manage Stock menu
                 */
                listenManageStockShowContainerAfterEvent: function () {
                    var self = this;
                    eventManager.observer('manage_stock_show_container_after', function (event, eventData) {
                        self.editing = false;
                        //self.reloadData();
                        self._render();
                    });
                },
                
                testUpdateStock: function(data, event) {
                    //var start = Date.now();
                    var stockItem = StockItemFactory.get();
                    var deferred = stockItem.updateQty(1, 10);
                    /*
                    deferred.done(function(data){
                        console.log('updateStock');
                        console.log(Date.now() - start);
                    });
                    */
                },
                filterOffline: function (element, event) {
                    if(this.mode == 'offline'){
                        this.filter(element, event);
                    }
                },
            });
        }
);