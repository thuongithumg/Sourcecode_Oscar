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
        'model/abstract',
        'model/resource-model/magento-rest/catalog/product',
        'model/resource-model/indexed-db/catalog/product',
        'model/collection/catalog/product',
        'model/inventory/stock-item-factory'
    ],
    function ($,ko, modelAbstract, restResource, indexedDbResource, collection, StockItemFactory) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'product',
            initialize: function () {
                this._super();
                this.setResource(restResource(), indexedDbResource());
                this.setResourceCollection(collection());
            },
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
                var productId = (product.entity_id)?product.entity_id:product.id;
                var infoBuyRequest = {
                    id:productId,
                    product_id:productId,
                    child_id:(product.child_id)?product.child_id:productId,
                    child_data:product,
                    product_name:product.name,
                    unit_price:(product.final_price)?product.final_price:product.price,
                    product_type:product.type_id,
                    type_id:product.type_id,
                    sku:product.sku,
                    is_out_of_stock:false,
                    group_prices:product.group_price,
                    tier_prices:product.tier_price,
                    tax_class_id:product.tax_class_id,
                    maximum_qty: product.maximum_qty,
                    minimum_qty: product.minimum_qty,
                    qty_increment: product.qty_increment,
                    hasOption:(product.options == 1)?true:false,
                    qty:(product.qty_increment)?product.qty_increment:((product.minimum_qty)?product.minimum_qty:1),
                    is_virtual:product.is_virtual,
                    options_data: {}
                };

                if(product.image){
                    infoBuyRequest.image_url = product.image;
                }

                if(product.options){
                    infoBuyRequest.options = this.getOptionsInfoRequest(product.options);
                }

                if(product.selected_options){
                    infoBuyRequest.options = this.getOptionsInfoRequest(product.selected_options);
                }

                if(product.super_attribute){
                    infoBuyRequest.super_attribute = this.getOptionsInfoRequest(product.super_attribute);
                    infoBuyRequest.options_data.attributes_info = product.attributes_info;
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
                    var validQty = parseFloat(product.qty);
                    var increment = parseFloat(product.qty_increment);
                    if(increment){
                        if(validQty%increment != 0){
                            validQty = increment * Math.ceil(validQty/increment);
                        }
                    }
                    infoBuyRequest.qty = validQty;
                }
                if(
                    typeof product.unit_price != "undefined"
                    &&
                    (
                        (product.type_id == 'simple' && product.options == 1)
                        || product.type_id == 'configurable'
                        || product.type_id == 'grouped'
                        || product.type_id == 'virtual'
                        || product.type_id == 'bundle'
                        || product.type_id =='customercredit'
                        || product.type_id =='giftvoucher'
                    )
                ){
                    infoBuyRequest.unit_price = product.unit_price;
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

                if (product.type_id =='giftvoucher') {
                    infoBuyRequest.unit_price = product.unit_price;
                    let giftcardTemplateIds = product.gift_template_ids.split(',');

                    infoBuyRequest.amount = infoBuyRequest.unit_price;
                    infoBuyRequest.giftcard_template_id = giftcardTemplateIds[0];
                    infoBuyRequest.recipient_ship = product.recipient_ship;
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
                var product = this.data;
                product.qty = 1;
                product.unit_price = product.price;
                product.selected_options = "";
                product.super_attribute = "";
                product.super_group = "";
                product.bundle_option = "";
                product.bundle_option_qty = "";
                product.bundle_childs_qty = "";
                product.options_label = "";
                product.not_enough_qty = "";
            },

            /**
             * Get final_price of product
             * @return float
             */
            getFinalPrice: function(){
                var finalPrice = this.data.final_price ? this.data.final_price : this.data.price;
                return finalPrice;
            },

            isSalable: function (childId) {
                return true;
                return this.getTypeInstance().isSalable(childId);
            },
            /**
             *
             * @param {Object} data
             * @returns {Boolean}
             */
            validateQtyInCart: function(data){
                this.setData(data);
                // var canBuy = this.canBuy(data.qty,parseInt(data.child_id),data.customer_group);
                // if(canBuy.stock_error && canBuy.stock_error == true){
                //     var message = $t('Stock item cannot be loaded, please reload the stock item data from Synchronization page');
                //     AddNoti(message, true, 'danger', $t('Error'));
                //     return false;
                // }else{
                //     if(!canBuy.can_buy){
                //         Alert({
                //             priority:'warning',
                //             title: $t('Warning'),
                //             message: canBuy.message
                //         });
                //         return false;
                //     }
                // }
                return true;
            },
            /**
             *
             * @param {Object} data
             * @returns {Boolean}
             */
            checkStockItemsInCart: function(data){
                this.setData(data);
                // var canBuy = this.canBuy(data.qty,data.child_id,data.customer_group);
                // if(!canBuy.can_buy){
                //     return canBuy.message;
                // }
                return true;
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
                StockItemFactory.get().updateQty(childId, changeQty);
            }
        });
    }
);