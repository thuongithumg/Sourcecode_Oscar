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

/*global define*/
define(
    [
        'jquery',
        'ko',
        'helper/price',
        'model/checkout/cart',
        'model/catalog/product-factory',
        'model/resource-model/magento-rest/abstract',
        'model/checkout/cart/items/item/interface'
    ],
    function($, ko, PriceHelper, CartModel, ProductFactory, restAbstract, ItemInterface) {
        'use strict';
        return function (itemData) {
            if(itemData && itemData.info_buy) {
                var Product = ProductFactory.get();
                var itemsId = [];
                var numberItem = 0;
                itemsId.push(PriceHelper.toNumber(itemData.info_buy.id));
                var deferred = $.Deferred();
                restAbstract().setPush(true).callRestApi(
                    'webpos/product/list',
                    'post',
                    {},
                    {itemsId: itemsId},
                    deferred
                );
                deferred.done(function (response) {
                    if (response.total_count > 0 && response.items) {
                        ko.utils.arrayForEach(response.items, function (product, index) {
                            if (itemData.info_buy.id == product.entity_id) {
                                $.each(itemData.info_buy, function (key, value) {
                                    product[key] = value;
                                });
                                Product.setData(product);
                                var infoBuy = Product.getInfoBuyRequest(CartModel.customerGroup());
                                if (itemData.info_buy.base_original_price && (itemData.info_buy.base_original_price != itemData.info_buy.base_unit_price)) {
                                    infoBuy.unit_price = itemData.info_buy.base_original_price;
                                    infoBuy.has_custom_price = true;
                                    infoBuy.show_original_price = true;
                                    infoBuy.custom_type = ItemInterface.CUSTOM_PRICE_CODE;
                                    infoBuy.custom_price_type = ItemInterface.FIXED_AMOUNT_CODE;
                                    infoBuy.custom_price_amount = itemData.info_buy.unit_price;
                                } else {
                                    infoBuy.unit_price = itemData.info_buy.base_unit_price;
                                    if (infoBuy.product_type == "customercredit")
                                        infoBuy.options_label = priceHelper.convertAndFormat(itemData.info_buy.amount);
                                }
                                CartModel.addProduct(infoBuy);
                                Product.resetTempAddData();
                            }
                        });
                    }
                });
            }
        }
    }
);
