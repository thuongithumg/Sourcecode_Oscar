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
        'model/checkout/cart/items/item',
        'model/checkout/cart/items/item/interface',
        'helper/general'
    ],
    function ($, ko, Item, ItemInterface, Helper) {
        "use strict";
        var Items = {
            hasGiftvoucherItem: ko.observable(false),
            apply_tax_after_discount: (Helper.getBrowserConfig('tax/calculation/apply_after_discount') == 1)?true:false,
            items: ko.observableArray(),
            initialize: function () {
                var self = this;
                self.isEmpty = ko.pureComputed(function(){
                    return (self.items().length > 0)?false:true;
                });
                Helper.observerEvent('load_items_online_after', function(event, data){
                    if(data && data.items){
                        self.updateItemsFromQuote(data.items);
                    }
                });
                return self;
            },
            getItems: function(){
                return this.items();
            },
            getAddedItem: function(data){
                var isNew = false;
                if(typeof data.item_id != "undefined"){
                    var foundItem = ko.utils.arrayFirst(this.items(), function(item) {
                        return (item[ItemInterface.ITEM_ID]() == data.item_id);
                    });
                    if(foundItem){
                        return foundItem;
                    }
                }else{
                    if(typeof data.hasOption !== "undefined"){
                        var foundItem = ko.utils.arrayFirst(this.items(), function(item) {
                            return (
                                (
                                    data.hasOption === false &&
                                    item[ItemInterface.PRODUCT_ID]() == data.product_id &&
                                    item[ItemInterface.TYPE_ID]() != 'giftvoucher'
                                ) ||
                                (
                                    data.hasOption === true &&
                                    item[ItemInterface.PRODUCT_ID]() == data.product_id &&
                                    item[ItemInterface.OPTIONS_LABEL]() == data.options_label
                                ) ||
                                (
                                    data.hasOption === false &&
                                    item[ItemInterface.TYPE_ID]() == 'giftvoucher' &&
                                    item[ItemInterface.ITEM_GIFTCARD_AMOUNT]() == data[ItemInterface.ITEM_GIFTCARD_AMOUNT] &&
                                    item[ItemInterface.ITEM_GIFTCARD_CAN_SHIP]() == data[ItemInterface.ITEM_GIFTCARD_CAN_SHIP]
                                )
                            );
                        });
                        if(foundItem){
                            return foundItem;
                        }
                    }
                }
                return isNew;
            },
            addItem: function(data){
                Helper.dispatchEvent('webpos_cart_item_add_before', data);
                var item = this.getAddedItem(data);
                if(item === false){
                    data.item_id = (data.item_id)?data.item_id:$.now();
                    data.qty = (data.qty)?data.qty:1;
                    var item = new Item();
                    item.init(data);
                    this.items.push(item);
                }else{
                    var qty = item[ItemInterface.QTY]();
                    qty += data.qty;
                    this.setItemData(item[ItemInterface.ITEM_ID](), "qty", qty);
                }

                this.hasGiftvoucherItem(item.type_id() === 'giftvoucher');
                Helper.dispatchEvent('webpos_cart_item_add_after', {data:data, item:item});
            },
            getItem: function(itemId){
                var item = false;
                var foundItem = ko.utils.arrayFirst(this.items(), function(item) {
                    return (item[ItemInterface.ITEM_ID]() == itemId);
                });
                if(foundItem){
                    item = foundItem;
                }
                return item;
            },
            getItemData: function(itemId, key){
                var item = this.getItem(itemId);
                if(item != false && typeof item[key] != "undefined"){
                    return item[key]();
                }
                return "";
            },
            setItemData: function(itemId, key, value){
                var item = this.getItem(itemId);
                if(item != false){
                    item.setData(key,value);
                }
            },
            removeItem: function(itemId){
                this.items.remove(function (item) {
                    return item[ItemInterface.ITEM_ID]() == itemId;
                });
            },
            totalItems: function(){
                var total = 0;
                if(this.items().length > 0){
                    ko.utils.arrayForEach(this.items(), function(item) {
                        total += parseFloat(item[ItemInterface.QTY]());
                    });
                }
                return total;
            },
            totalShipableItems: function(){
                var total = 0;
                if(this.items().length > 0){
                    var shipItems = ko.utils.arrayFilter(this.items(), function(item) {
                        return (item[ItemInterface.IS_VIRTUAL]() == false);
                    });
                    if(shipItems.length > 0){
                        ko.utils.arrayForEach(shipItems, function(item) {
                            total += item[ItemInterface.QTY]();
                        });
                    }
                }
                return total;
            },
            getMaxDiscountAmount: function(taxAfterDiscount){
                var self = this;
                var max = 0;
                var appliedDiscount = 0;
                if(self.items().length > 0){
                    taxAfterDiscount = (typeof taxAfterDiscount != 'undefined') ? taxAfterDiscount : self.apply_tax_after_discount;
                    ko.utils.arrayForEach(self.items(), function (item) {
                        max += (taxAfterDiscount == false)?(item[ItemInterface.ROW_TOTAL]() + item[ItemInterface.TAX_AMOUNT]()):item[ItemInterface.ROW_TOTAL]();
                    });
                }
                max -= appliedDiscount;
                return max;
            },
            getMaxItemDiscountAmount: function(item_id, taxAfterDiscount){
                var self = this;
                var max = 0;
                var item = self.getItem(item_id);
                if(item !== false){
                    taxAfterDiscount = (typeof taxAfterDiscount != undefined)?taxAfterDiscount:self.apply_tax_after_discount;
                    max = (taxAfterDiscount == false)?(item[ItemInterface.ROW_TOTAL]() + item[ItemInterface.TAX_AMOUNT]()):item[ItemInterface.ROW_TOTAL]();
                }
                return max;
            },
            updateItemsFromQuote: function(quoteItems){
                if(quoteItems){
                    var self = this;
                    $.each(quoteItems, function(itemId, itemData){
                        if(itemData.offline_item_id || itemData.move_from_shopping_cart){
                            var unitPrice = (itemData.base_original_price)?itemData.base_original_price:((Helper.isProductPriceIncludesTax())?itemData.base_price_incl_tax:(itemData.base_price_incl_tax - itemData.base_tax_amount));
                            var elementItemId = (itemData.offline_item_id == itemId)?itemId:itemData.offline_item_id;
                            var data = {};
                            data[ItemInterface.ITEM_ID] = elementItemId;
                            data[ItemInterface.UNIT_PRICE] = parseFloat(unitPrice);
                            data[ItemInterface.ITEM_DISCOUNT_AMOUNT] = itemData.discount_amount;
                            data[ItemInterface.ITEM_BASE_DISCOUNT_AMOUNT] = itemData.base_discount_amount;
                            data[ItemInterface.PRODUCT_ID] = itemData.product_id;
                            data[ItemInterface.CHILD_ID] = (itemData.child_id)?itemData.child_id:itemData.product_id;
                            data[ItemInterface.PRODUCT_NAME] = itemData.name;
                            data[ItemInterface.QTY] = parseFloat(itemData.qty);
                            data[ItemInterface.SKU] = itemData.sku;
                            data[ItemInterface.PARENT_SKU] = itemData.parent_sku;
                            data[ItemInterface.OPTIONS_DATA] = itemData.options_data;
                            data[ItemInterface.TAX_RATE] = parseFloat(itemData.tax_percent);
                            data[ItemInterface.ONLINE_BASE_TAX_AMOUNT] = parseFloat(itemData.base_tax_amount);
                            data[ItemInterface.IS_VIRTUAL] = itemData.is_virtual;
                            data[ItemInterface.HAS_ERROR] = itemData.has_error;
                            data[ItemInterface.QTY_INCREMENT] = itemData.qty_increment;
                            data[ItemInterface.MAXIMUM_QTY] = itemData.maximum_qty;
                            data[ItemInterface.MINIMUM_QTY] = itemData.minimum_qty;
                            data[ItemInterface.IMAGE_URL] = itemData.image_url;
                            data[ItemInterface.SAVED_ONLINE_ITEM] = true;
                            if(itemData.product_type !== "customsale") {
                                data[ItemInterface.IS_CUSTOM_SALE] = false;
                            }else{
                                data[ItemInterface.IS_CUSTOM_SALE] = true;
                            }
                            if(itemData.warehouse_id) {
                                data[ItemInterface.WAREHOUSE_ID] = itemData.warehouse_id;
                            }
                            var eventData = {data:data, quote_item_data:itemData};
                            Helper.dispatchEvent('webpos_cart_item_update_from_quote_before', eventData);
                            data = eventData.data;

                            var added = self.getAddedItem({item_id: itemData.offline_item_id}) || self.getAddedItem({item_id: itemId});
                            if(added === false){
                                data[ItemInterface.OPTIONS_LABEL] = itemData.options_label;
                                data[ItemInterface.ITEM_ID] = itemId;
                                self.addItem(data);
                            }else{
                                data[ItemInterface.ITEM_ID] = itemId;
                                self.setItemData(elementItemId, data);
                                self.setItemData(itemId, data);
                                var options_label = self.getItemData(elementItemId, 'options_label');
                                if(!options_label && itemData.options_label){
                                    self.setItemData(elementItemId, 'options_label', itemData.options_label);
                                }
                                var options_label = self.getItemData(itemId, 'options_label');
                                if(!options_label && itemData.options_label){
                                    self.setItemData(itemId, 'options_label', itemData.options_label);
                                }
                            }
                        }
                    });
                }
            }
        };
        return Items.initialize();
    }
);