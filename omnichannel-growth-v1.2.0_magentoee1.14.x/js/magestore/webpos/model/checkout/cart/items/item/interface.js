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
    [],
    function () {
        "use strict";
        return {
            /* Fields key */
            ID: 'id',
            ITEM_ID: 'item_id',
            PRODUCT_ID: 'product_id',
            CHILD_ID: 'child_id',
            CHILD_DATA: 'child_data',
            TYPE_ID: 'type_id',
            IS_CUSTOM_SALE: 'is_custom_sale',
            TAX_CLASS_ID: 'tax_class_id',
            QTY: 'qty',
            QTY_INCREMENT: 'qty_increment',
            PRODUCT_NAME: 'product_name',
            IMAGE_URL: 'image_url',
            SKU: 'sku',
            PARENT_SKU: 'parent_sku',
            UNIT_PRICE: 'unit_price',
            TIER_PRICE: 'tier_price',
            GROUP_PRICE: 'group_price',
            TIER_PRICES: 'tier_prices',
            GROUP_PRICES: 'group_prices',
            MAXIMUM_QTY: 'maximum_qty',
            MINIMUM_QTY: 'minimum_qty',
            TAX_RATES: 'tax_rates',
            STORE_TAX_RATES: 'store_tax_rates',
            TAX_ORIGIN_RATE: 'tax_origin_rates',
            HAS_ERROR: 'has_error',
            HAS_CUSTOM_PRICE: 'has_custom_price',
            CUSTOM_TYPE: 'custom_type',
            CUSTOM_PRICE_TYPE: 'custom_price_type',
            CUSTOM_PRICE_AMOUNT: 'custom_price_amount',
            SUPER_ATTRIBUTE: 'super_attribute',
            SUPER_GROUP: 'super_group',
            OPTIONS_DATA: 'options_data',
            OPTIONS: 'options',
            BUNDLE_OPTION: 'bundle_option',
            BUNDLE_OPTION_QTY: 'bundle_option_qty',
            IS_OUT_OF_STOCK: 'is_out_of_stock',
            ROW_TOTAL: 'row_total',
            IS_VIRTUAL: 'is_virtual',
            QTY_TO_SHIP: 'qty_to_ship',
            TAX_AMOUNT: 'tax_amount',
            TAX_AMOUNT_BEFORE_DISCOUNT: 'tax_amount_before_discount',
            BASE_DISCOUNT_AMOUNT: 'base_discount_amount',
            ONLINE_BASE_TAX_AMOUNT: 'online_base_tax_amount',
            ONLINE_TAX_AMOUNT: 'online_tax_amount',
            DISCOUNT_AMOUNT: 'discount_amount',
            ITEM_BASE_DISCOUNT_AMOUNT: 'item_base_discount_amount',
            ITEM_DISCOUNT_AMOUNT: 'item_discount_amount',
            ITEM_BASE_CREDIT_AMOUNT: 'item_base_credit_amount',
            ITEM_CREDIT_AMOUNT: 'item_credit_amount',
            CREDIT_AMOUNT: 'amount',
            CREDIT_PRICE_AMOUNT: 'credit_price_amount',
            PRODUCT_TYPE: 'product_type',
            OPTIONS_LABEL: 'options_label',
            STOCKS: 'stocks',
            STOCK: 'stock',
            BUNDLE_CHILDS_QTY: 'bundle_childs_qty',
            SAVED_ONLINE_ITEM: 'saved_online_item',
            ITEM_POINT_EARN: 'item_point_earn',
            ITEM_POINT_SPENT: 'item_point_spent',
            ITEM_POINT_DISCOUNT: 'item_point_discount',
            ITEM_BASE_POINT_DISCOUNT: 'item_base_point_discount',
            ITEM_GIFTCARD_DISCOUNT: 'item_giftcard_discount',
            ITEM_GIFTCARD_AMOUNT: 'amount',
            ITEM_GIFTCARD_TEMPLATE_ID: 'giftcard_template_id',
            ITEM_GIFTCARD_CAN_SHIP: 'recipient_ship',
            ITEM_BASE_GIFTCARD_DISCOUNT: 'item_base_giftcard_discount',
            /* Const value */
            CUSTOM_PRICE_CODE: "price",
            CUSTOM_DISCOUNT_CODE: "discount",
            FIXED_AMOUNT_CODE: "$",
            PERCENTAGE_CODE: "%",
            APPLY_TAX_ON_CUSTOMPRICE: "0",
            APPLY_TAX_ON_ORIGINALPRICE: "1",
            WAREHOUSE_ID: "warehouse_id"
        };
    }
);