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
        'model/checkout/cart',
        'model/resource-model/magento-rest/checkout/cart',
        'helper/general'
    ],
    function ($, ko, CartModel, CartResource, Helper) {
        "use strict";
        var EditPopupModel = {
            itemId: ko.observable(),
            loading: ko.observable(false),
            useMultipleWarehouses: ko.observable(false),
            stocks: ko.observableArray([]),
            initialize: function(){
                var self = this;
                self.warehouses = ko.pureComputed(function(){
                    return (Helper.isInventorySuccessEnable() && self.useMultipleWarehouses())?Helper.getPluginConfig('os_inventory_success', 'warehouses'):[];
                });
                self.stocksInWarehouses = ko.pureComputed(function(){
                    var warehouses = self.warehouses();
                    var stocks = [];
                    $.each(warehouses, function(index, warehouse){
                        var stockInWarehouses = warehouse;
                        stockInWarehouses.warehouse_name_formated = warehouse.warehouse_name+' - 0 '+ Helper.__('items');
                        $.each(self.stocks(), function(index, stock){
                            if(stock.stock_id == warehouse.warehouse_id){
                                stockInWarehouses.warehouse_name_formated = stockInWarehouses.warehouse_name+' - '+parseFloat(stock.qty)+' '+ Helper.__('items');
                            }
                        })
                        stocks.push(stockInWarehouses);
                    });
                    return stocks;
                });
                return self;
            },
            setItem: function(item){
                var self = this;
                self.itemId(item.item_id());
                if(Helper.isOnlineCheckout() && self.useMultipleWarehouses()){
                    self.getStocksInWarehouses();
                }
                var eventData = {item:item, model:self};
                Helper.dispatchEvent('webpos_cart_editpopup_model_set_item_after', eventData);
            },
            getItemId: function(){
                return this.itemId();
            },
            getEditingItemId: function(){
                return this.getItemId();
            },
            getData: function(key){
                return CartModel.getItemData(this.getItemId(), key);
            },
            setData: function(key,value){
                CartModel.updateItem(this.getItemId(), key, value);
            },
            getStocksInWarehouses: function(){
                var self = this;
                var params = {
                    product_id: self.getData('child_id')
                };
                var apiRequest = $.Deferred();
                self.loading(true);
                CartResource().setPush(true).setLog(false).getStocksInWarehouses(params, apiRequest);
                apiRequest.done(function(response){
                    if(response && response.stocks){
                        self.stocks(response.stocks);
                    }
                }).always(function(){
                    self.loading(false);
                });
                return apiRequest;
            }
        };
        return EditPopupModel.initialize();
    }
);