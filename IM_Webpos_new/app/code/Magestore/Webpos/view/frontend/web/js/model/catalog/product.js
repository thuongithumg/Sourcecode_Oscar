/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'Magestore_Webpos/js/model/catalog/product/type/configurable',
    'Magestore_Webpos/js/model/catalog/product/type/bundle',
    'Magestore_Webpos/js/model/catalog/product/type/grouped',
    'Magestore_Webpos/js/model/catalog/product/type/downloadable',
    'Magestore_Webpos/js/model/catalog/product/type/storecredit',
    'Magestore_Webpos/js/model/catalog/product/type/simple',
    ]);
    
define(
        [
            'jquery',
            'ko',
            'Magestore_Webpos/js/model/abstract',
            'Magestore_Webpos/js/model/resource-model/magento-rest/catalog/product',
            'Magestore_Webpos/js/model/resource-model/indexed-db/catalog/product',
            'Magestore_Webpos/js/model/collection/catalog/product',
            'Magestore_Webpos/js/model/inventory/stock-item-factory',
            'Magestore_Webpos/js/model/catalog/product/type/configurable-factory',
            'Magestore_Webpos/js/model/catalog/product/type/bundle-factory',
            'Magestore_Webpos/js/model/catalog/product/type/grouped-factory',
            'Magestore_Webpos/js/model/catalog/product/type/downloadable-factory',
            'Magestore_Webpos/js/model/catalog/product/type/storecredit-factory',
            'Magestore_Webpos/js/model/catalog/product/type/simple-factory',            
            'Magestore_Webpos/js/helper/alert',
            'mage/translate',
            'Magestore_Webpos/js/action/notification/add-notification',
            'Magestore_Webpos/js/helper/general',
            'Magestore_Webpos/js/model/giftvoucher/giftvoucher',
            'Magestore_Webpos/js/helper/price'
        ],
        function ($, ko, modelAbstract, restResource, indexedDbResource, collection, StockItemFactory,
                ConfigurableFactory, BundleFactory, GroupedFactory, DownloadableFactory, StoreCreditFactory, SimpleFactory,
                Alert, $t, AddNoti, Helper, giftvoucherModel, priceHelper) {
            "use strict";
            return modelAbstract.extend({
                event_prefix: 'catalog_product',
                sync_id:'product',
                initialize: function () {
                    this._super();
                    this.setResource(restResource(), indexedDbResource());
                    this.setResourceCollection(collection());
                },
                /**
                 * Get isalable information
                 * 
                 * @param {int} childId;
                 * @returns {bool}
                 */
                isSalable: function (childId) {
                    if(Helper.isStockOnline()){
                        return this.data['is_salable'];
                    }
                    return this.getTypeInstance().isSalable(childId);
                },
                /**
                 * Can buy product
                 * 
                 * @param {int|float} requestQty;
                 * @param {int} childId;
                 * @param {int} customerGroup;
                 * @returns {}
                 */
                canBuy: function (requestQty, childId, customerGroup) {
                    // if(Helper.isStockOnline()){
                    //     return {stock_error:false, can_buy:this.data['is_salable'], message:'This product is currently out of stock'};
                    // }
                    return this.getTypeInstance().canBuy(requestQty, childId, customerGroup);
                },  
                /**
                 * Update product qty (qty = qty + changeQty)
                 * Do not update qty if not manage stock
                 * 
                 * @param {int|float} changeQty
                 * @param {int} childId
                 */
                updateStock: function(changeQty, childId) {
                    /* update stock qty */
                    StockItemFactory.create().updateQty(childId, changeQty);
                },
                /**
                 * Get stock-item model
                 * 
                 */
                getStockItem: function() {
                    return StockItemFactory.get().setProduct(this);
                },
                /**
                 * Get product qty
                 * 
                 * @param {int} childId;
                 * @returns {Float|Int}
                 */
                getQty: function (childId) {
                    if(Helper.isStockOnline()){
                        return this.data['qty']
                    }
                    return this.getTypeInstance().getQty(childId);
                },
                /**
                 * Get min_sale_qty
                 * 
                 * @param {int} childId
                 * @param {int} customerGroup
                 * @returns {Float|Int}
                 */
                getMinSaleQty: function (childId, customerGroup) {
                    if(Helper.isStockOnline()){
                        return (this.data['minimum_qty'])?parseFloat(this.data['minimum_qty']):1;
                    }
                    return this.getTypeInstance().getMinSaleQty(childId, customerGroup);
                },  
                /**
                 * Get max_sale_qty
                 * 
                 * @param {int} childId
                 * @returns {Float|Int}
                 */
                getMaxSaleQty: function (childId) {
                    if(Helper.isStockOnline()){
                        return (this.data['maximum_qty'])?parseFloat(this.data['maximum_qty']):0;
                    }
                    return this.getTypeInstance().getMaxSaleQty(childId);
                },                  
                /**
                 * Mange stock of product or not
                 * 
                 * @param {int} childId;
                 * @return {Boolean};
                 */                
                isManageStock: function(childId) {
                    return this.getTypeInstance().isManageStock(childId);
                },
                /**
                 * Allow to backorder or not
                 * 
                 * @param {int} childId;
                 * @return {Boolean};
                 */                
                isBackorder: function(childId) {
                    return this.getTypeInstance().isBackorder(childId);
                },
                /**
                 * get product type instance
                 * 
                 * @returns {object}
                 */
                getTypeInstance: function () {
                   // if (!this.typeInstance) {
                        if (this.data['type_id']) {
                            switch (this.data['type_id']) {
                                case 'configurable':
                                    this.typeInstance = ConfigurableFactory.get();
                                    break;
                                case 'bundle':
                                    this.typeInstance = BundleFactory.get();
                                    break;
                                case 'grouped':
                                    this.typeInstance = GroupedFactory.get();
                                    break;
                                case 'downloadable':
                                    this.typeInstance = DownloadableFactory.get();
                                    break;       
                                case 'customercredit':
                                    this.typeInstance = StoreCreditFactory.get();
                                    break;
                                default:
                                    this.typeInstance = SimpleFactory.get();
                            }
                            this.typeInstance.setProduct(this);
                            this.typeInstance.refreshStockData();
                        }
                   // }
                    return this.typeInstance;
                },

                giftVoucherField: [
                    'amount',
                    'customer_name',
                    'giftcard_template_id',
                    'giftcard_template_image',
                    'send_friend',
                    'recipient_name',
                    'recipient_email',
                    'message',
                    'day_to_send',
                    'timezone_to_send',
                    'recipient_address',
                    'notify_success',
                    'recipient_ship',
                ],
                /**
                 * get product info buy request to add to cart
                 * @param {object} product
                 * @returns {json object}
                 */
                getInfoBuyRequest: function(customer_group){
                    var product = this.data;
                    if(!product.options && (product.type_id == "configurable" || product.type_id == "grouped" || product.type_id == "bundle" || product.type_id == "customercredit")){
                        product.options = 1;
                    }
                    var infoBuyRequest = {
                        id:product.id,
                        product_id:product.id,
                        child_id:(product.child_id)?product.child_id:product.id,
                        child_product:(product.child_product)?product.child_product:product.id,
                        product_name:product.name,
                        custom_sale_description: product.custom_sale_description,
                        unit_price:this.getFinalPrice(),
                        product_type:product.type_id,
                        type_id:product.type_id,
                        sku:product.sku,
                        is_out_of_stock:false,
                        tier_prices:product.tier_prices,
                        tax_class_id:product.tax_class_id,
                        stock:product.stock,
                        stocks:product.stocks,
                        hasOption:(product.options == 1)?true:false,
                        qty_increment:product.qty_increment,
                        is_qty_decimal:(product.is_qty_decimal == 1)?true:false,
                        qty:(product.qty_increment > 0)?product.qty_increment:1,
                        is_virtual:product.is_virtual,
                        is_salable:product.is_salable
                    };
                    if(product.final_price && (product.final_price < product.price)){
                        infoBuyRequest.base_original_price = product.price;
                        infoBuyRequest.applied_catalog_rules = true;
                    }
                    if(product.image){
                        infoBuyRequest.image_url = product.image;
                    }
                    if(product.selected_options){
                        infoBuyRequest.options = this.getOptionsInfoRequest(product.selected_options);
                    }
                    if(product.super_attribute){
                        infoBuyRequest.super_attribute = this.getOptionsInfoRequest(product.super_attribute);
                    }
                    if(product.super_group){
                        infoBuyRequest.super_group = this.getOptionsInfoRequest(product.super_group);
                    }
                    if(product.bundle_option){
                        infoBuyRequest.bundle_option = this.getOptionsInfoRequest(product.bundle_option);
                    }
                    if(product.bundle_option_qty){
                        infoBuyRequest.bundle_option_qty = this.getOptionsInfoRequest(product.bundle_option_qty);
                    }
                    if(product.bundle_childs_qty){
                        infoBuyRequest.bundle_childs_qty = this.getOptionsInfoRequest(product.bundle_childs_qty);
                    }
                    if(product.options_label){
                        infoBuyRequest.options_label = this.getOptionsLabelInfoRequest(product.options_label);
                    }
                    if(product.custom_options_label){
                        infoBuyRequest.options_label = (infoBuyRequest.options_label)?infoBuyRequest.options_label:'';
                        infoBuyRequest.options_label += this.getOptionsLabelInfoRequest(product.custom_options_label);
                    }
                    if(typeof product.qty != "undefined"){
                        var qtyForCart = parseFloat(product.qty);
                        var increment = parseFloat(product.qty_increment);
                        if((increment > 0) && qtyForCart%increment > 0){
                            qtyForCart -= parseFloat(qtyForCart%increment);
                            qtyForCart = (qtyForCart > 0)?qtyForCart:increment;
                        }
                        infoBuyRequest.qty = parseFloat(qtyForCart);
                    }
                    if(typeof product.unit_price != "undefined"){
                        infoBuyRequest.unit_price = product.unit_price;
                    }
                    if(product.stocks){
                        var child_id = (infoBuyRequest.child_id)?infoBuyRequest.child_id:infoBuyRequest.product_id;
                        infoBuyRequest.maximum_qty = this.getMaxSaleQty(child_id,customer_group);
                        infoBuyRequest.minimum_qty = this.getMinSaleQty(child_id,customer_group);
                    }else{
                        if(Helper.isStockOnline()) {
                            infoBuyRequest.maximum_qty = this.getMaxSaleQty();
                            infoBuyRequest.minimum_qty = this.getMinSaleQty();
                        }
                    }
                    if(product.credit_price_amount){
                        infoBuyRequest.credit_price_amount = product.credit_price_amount;
                    }else{
                        if(product.storecredit_type == 3){
                            var values = product.customercredit_value.split(',');
                            var rate = product.storecredit_rate;
                            infoBuyRequest.credit_price_amount = parseFloat(values[0]) * parseFloat(rate);
                        }else{
                            infoBuyRequest.credit_price_amount = product.customercredit_value;
                        }
                    }
                    if(product.amount){
                        infoBuyRequest.amount = product.amount;
                    }else{
                        infoBuyRequest.amount = product.final_price;
                    }

                    if (product.type_id == "giftvoucher") {

                        $.each(this.giftVoucherField, function (index, value) {
                            if (typeof product[value] !== 'undefined') {
                                infoBuyRequest[value] = product[value];
                            } else {
                                infoBuyRequest[value] = '';
                            }
                        });

                        var giftCardValue = parseFloat(giftvoucherModel.giftCardValue());
                        var giftCardProduct;
                        if (giftvoucherModel.selectPriceType() === giftvoucherModel.SELECT_PRICE_TYPE_DROPDOWN ||
                            giftvoucherModel.selectPriceType()  === giftvoucherModel.SELECT_PRICE_TYPE_RANGE) {
                            if (giftvoucherModel.priceType() === giftvoucherModel.PRICE_TYPE_PERCENT) {
                                var percent = giftvoucherModel.giftCardPrice();
                                giftCardProduct = giftCardValue * percent / 100;
                            } else if (giftvoucherModel.priceType() === giftvoucherModel.PRICE_TYPE_FIXED) {
                                giftCardProduct = parseFloat(giftvoucherModel.giftCardPrice());
                            } else {
                                giftCardProduct = giftCardValue;
                            }
                        }
                        else {
                            giftCardProduct = giftvoucherModel.giftCardPrice();
                        }

                        infoBuyRequest.unit_price = giftCardProduct;
                        infoBuyRequest.options_label = priceHelper.formatPrice(giftCardValue);

                    }
                    infoBuyRequest.productObject = this.data;
                    return infoBuyRequest;
                },
                /**
                 * prepare option data for api params
                 * @param {object} data
                 * @returns {Array}
                 */
                getOptionsInfoRequest: function(data){
                    var options = [];
                    if(data){
                        ko.utils.arrayForEach(data, function (option) {
                            options.push({
                                code: option.id,
                                value: ($.isArray(option.value) && option.value.length == 1)?option.value[0]:option.value
                            });
                        });
                    }
                    return options;
                },
                /**
                 * 
                 * @param {Array} data
                 * @returns {String}
                 */
                getOptionsLabelInfoRequest: function(data){
                    var labels = "";
                    if(data){
                        if($.isArray(data)){
                            ko.utils.arrayForEach(data, function (label,index) {
                                if(label && label.value){
                                    labels += (index == data.length - 1)?label.value:label.value+", ";
                                }
                            });
                        }else{
                            labels = data;
                        }
                    }
                    return labels;
                },
                /**
                 * remove temporary data
                 * @param {object} product
                 */
                resetTempAddData: function(){
                    this.data.qty = 1;
                    this.data.unit_price = this.getFinalPrice();
                    this.data.selected_options = "";
                    this.data.super_attribute = "";
                    this.data.super_group = "";
                    this.data.bundle_option = "";
                    this.data.bundle_option_qty = "";
                    this.data.bundle_childs_qty = "";
                    this.data.options_label = "";
                    this.data.not_enough_qty = "";
                },
                /**
                 * Get final_price of product
                 * @return float
                 */
                getFinalPrice: function(){
                    return this.data.final_price ? this.data.final_price : this.data.price;
                },
                /**
                 * 
                 * @param {Object} data
                 * @returns {Boolean}
                 */
                validateQtyInCart: function(data){
                    this.setData(data);
                    var canBuy = this.canBuy(data.qty,parseInt(data.child_id),data.customer_group);
                    if(canBuy.stock_error && canBuy.stock_error == true){
                        var message = $t('Stock item cannot be loaded, please reload the stock item data from Synchronization page');
                        AddNoti(message, true, 'danger', $t('Error'));
                        return false;
                    }else{
                        if(!canBuy.can_buy){
                            Alert({
                                priority:'warning',
                                title: $t('Warning'),
                                message: canBuy.message
                            });
                            return false;
                        }
                    }
                    return true;
                },
                /**
                 * 
                 * @param {Object} data
                 * @returns {Boolean}
                 */
                checkStockItemsInCart: function(data){
                    this.setData(data);
                    var canBuy = this.canBuy(data.qty,data.child_id,data.customer_group);
                    if(!canBuy.can_buy){
                        return canBuy.message;
                    }
                    return true;
                }
            });
        }
);