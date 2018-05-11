<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Helper
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

use Magestore_Reportsuccess_Helper_Data as Data;
class Magestore_Reportsuccess_Helper_Variable
    extends
    Mage_Core_Helper_Abstract
{
    /**
     * types dimension
     */
    const _DIMENSIONS_PRO = 'product';
    const _DIMENSIONS_WH = 'warehouse';
    const _DIMENSIONS_SHIPPING = 'shipping_method';
    const _DIMENSIONS_ORDER = 'order_status';
    const _DIMENSIONS_CUSTOME = 'customer';
    const _DIMENSIONS_PAYTMENT = 'payment_method';

    /**
     * types metrics
     */
    const _METRICS_SKU = 'metrics_sku';
    const _METRICS_CUSTOMER = 'metrics_customer';
    const _METRICS_STATUS = 'metrics_status';
    const _METRICS_SHIPPING = 'metrics_shipping';
    const _METRICS_PAYMENT  = 'metrics_payment';
    /**
     *  variable
     */
    const _NULL = 'null';
    const _NAME = 'name';
    const _SKU = 'sku';
    const _WAREHOUSE_ID = 'warehouse_id';
    /**
     * shipping & payment
     */
    const _SHIPPING_METHOD = 'shipping_method';
    const _PAYMENT_METHOD = 'payment_method';
    /**
     * sold qty
     */
    const _REALIZED_SOLD_QTY = 'realized_sold_qty';
    const _POTENTIAL_SOLD_QTY = 'potential_sold_qty';
    /**
     * cogs
     */
    const _REALIZED_COGS = 'realized_cogs';
    const _POTENTIAL_COGS = 'potential_cogs';
    const _COGS = 'cogs';
    /**
     * profit
     */
    const _REALIZED_PROFIT = 'realized_profit';
    const _POTENTIAL_PROFIT = 'potential_profit';
    const _PROFIT = 'profit';
    /**
     * tax
     */
    const _REALIZED_TAX = 'realized_tax';
    const _POTENTIAL_TAX = 'potential_tax';
    const _TAX = 'tax';
    /**
     * discount
     */
    const _REALIZED_DISCOUNT = 'realized_discount';
    const _POTENTIAL_DISCOUNT = 'potential_discount';
    /**
     * unit
     */
    const _UNIT_COST = 'unit_cost';
    const _UNIT_PRICE = 'unit_price';
    const _UNIT_TAX = 'unit_tax';
    const _UNIT_DISCOUNT = 'unit_discount';
    const _UNIT_PROFIT = 'unit_profit';
    /**
     * customer
     */
    const _CUSTOMER_GROUP_ID = 'customer_group_id';
    const _CUSTOMER_EMAIL = 'customer_email';
    /**
     * date time
     */
    const _CREATED_AT = 'created_at';
    const _UPDATED_AT = 'updated_at';
    /**
     * total sales
     */
    const _TOTAL_SALE = 'total_sale';
    /**
     * order id & status
     */
    const _STATUS = 'status';
    const _ORDER_ID = 'order_id';
    /**
     * action
     */
    const _ACTION = 'action';

    /**
     * bool
     */
    const _TRUE = 1;
    const _FALSE = 0;


    /**
     * Columns name from Inventory report
     */
    const _SUM_TOTAL_QTY = 'sum_total_qty';
    const _MAC = 'mac';
    const _PRICE = 'price';
    const _TOTAL_INV_VALUE = 'total_inv_value';
    const _TOTAL_RETAIL_VALUE = 'total_retail_value';
    const _TOTAL_PROFIT = 'total_profit';
    const _TOTAL_PROFIT_MARGIN = 'total_profit_margin';
    const _SUPPLIER_NAME = 'supplier_name';
    const _SUM_QTY_TO_SHIP = 'sum_qty_to_ship';
    const _AVAILABLE_QTY = 'available_qty';
    const _QTY_IN_ORDER = 'qty_in_order';
    const _SHELF_LOCATION = 'shelf_location';

    /**
     * @param bool $type
     * @return string
     */
    public function _metrics($type = false){
        $metricts =  array(
            self::_NULL.':'.self::_FALSE,
            self::_NAME.':'.(($type && $type==self::_METRICS_SKU) ? self::_TRUE : self::_FALSE),
            self::_SKU.':'.(($type && $type==self::_METRICS_SKU) ? self::_TRUE : self::_FALSE),
            self::_SHIPPING_METHOD.':'.(($type && $type==self::_METRICS_SHIPPING) ? self::_TRUE : self::_FALSE),
            self::_PAYMENT_METHOD.':'.(($type && $type==self::_METRICS_PAYMENT) ? self::_TRUE : self::_FALSE),
            self::_REALIZED_COGS.':'.self::_FALSE,
            self::_POTENTIAL_COGS.':'.self::_FALSE,
            self::_REALIZED_PROFIT.':'.self::_FALSE,
            self::_POTENTIAL_PROFIT.':'.self::_FALSE,
            self::_REALIZED_TAX.':'.self::_FALSE,
            self::_POTENTIAL_TAX.':'.self::_FALSE,
            self::_REALIZED_DISCOUNT.':'.self::_FALSE,
            self::_POTENTIAL_DISCOUNT.':'.self::_FALSE,
            self::_UNIT_COST.':'.self::_FALSE,
            self::_UNIT_PRICE.':'.self::_FALSE,
            self::_UNIT_TAX.':'.self::_FALSE,
            self::_UNIT_DISCOUNT.':'.self::_FALSE,
            self::_UNIT_PROFIT.':'.self::_FALSE,
            self::_CUSTOMER_GROUP_ID.':'.(($type && $type==self::_METRICS_CUSTOMER) ? self::_TRUE : self::_FALSE),
            self::_CUSTOMER_EMAIL.':'.(($type && $type==self::_METRICS_CUSTOMER) ? self::_TRUE : self::_FALSE),
            self::_CREATED_AT.':'.self::_FALSE,
            self::_UPDATED_AT.':'.self::_FALSE,
            self::_STATUS.':'.(($type && $type==self::_METRICS_STATUS) ? self::_TRUE : self::_FALSE),
            self::_ORDER_ID.':'.self::_FALSE,
            /*default true */
            self::_REALIZED_SOLD_QTY.':'.self::_TRUE,
            self::_POTENTIAL_SOLD_QTY.':'.self::_TRUE,
            self::_COGS.':'.self::_TRUE,
            self::_PROFIT.':'.self::_TRUE,
            self::_TAX.':'.self::_TRUE,
            self::_TOTAL_SALE.':'.self::_TRUE,
            self::_ACTION.':'.self::_TRUE
        );
        return implode(',',$metricts);
    }

    /**
     * @param bool $type
     * @return string
     */
    public function _dimension($type = false){
        $dimension = array(
            self::_NULL.':'.self::_FALSE,
            self::_CUSTOMER_EMAIL.':'.(($type && $type==self::_DIMENSIONS_CUSTOME) ? self::_TRUE : self::_FALSE),
            self::_CUSTOMER_GROUP_ID.':'.self::_FALSE,
            self::_WAREHOUSE_ID.':'.(($type && $type==self::_DIMENSIONS_WH) ? self::_TRUE : self::_FALSE),
            self::_SHIPPING_METHOD.':'.(($type && $type==self::_DIMENSIONS_SHIPPING) ? self::_TRUE : self::_FALSE),
            self::_PAYMENT_METHOD.':'.(($type && $type==self::_DIMENSIONS_PAYTMENT) ? self::_TRUE : self::_FALSE),
            self::_STATUS.':'.(($type && $type==self::_DIMENSIONS_ORDER) ? self::_TRUE : self::_FALSE),
            self::_ORDER_ID.':'.self::_FALSE,
            self::_SKU.':'.(($type && $type==self::_DIMENSIONS_PRO) ? self::_TRUE : self::_FALSE),
        );
        return implode(',',$dimension);
    }


    /**
     * @return array
     */
    public function mappingFieldsName(){
        $array = array(
            /* Metrics */
            self::_NAME => $this->__("NAME"),
            self::_SKU => $this->__("SKU"),
            self::_WAREHOUSE_ID => $this->__("Warehouse"),
            self::_SHIPPING_METHOD => $this->__("Shipping Method"),
            self::_PAYMENT_METHOD => $this->__("Payment Method"),
            self::_REALIZED_SOLD_QTY => $this->__("Actual Sold Qty"),
            self::_POTENTIAL_SOLD_QTY => $this->__("Potential Sold Qty"),
            self::_REALIZED_COGS => $this->__("Actual COGS"),
            self::_POTENTIAL_COGS => $this->__("Potential COGS"),
            self::_COGS => $this->__("COGS"),
            self::_REALIZED_PROFIT => $this->__("Actual Profit"),
            self::_POTENTIAL_PROFIT => $this->__("Potential Profit"),
            self::_PROFIT => $this->__("Profit"),
            self::_REALIZED_TAX => $this->__("Actual Tax"),
            self::_POTENTIAL_TAX => $this->__("Potential Tax"),
            self::_TAX => $this->__("Tax"),
            self::_REALIZED_DISCOUNT => $this->__("Actual Discount"),
            self::_POTENTIAL_DISCOUNT => $this->__("Potential Discount"),
            self::_UNIT_COST => $this->__("Unit Cost"),
            self::_UNIT_PRICE => $this->__("Unit Price"),
            self::_UNIT_TAX => $this->__("Unit Tax"),
            self::_UNIT_DISCOUNT => $this->__("Unit discount"),
            self::_UNIT_PROFIT => $this->__("Unit Profit"),
            self::_REALIZED_PROFIT => $this->__("Actual Profit"),
            self::_POTENTIAL_PROFIT => $this->__("Potential Profit"),
            self::_PROFIT => $this->__("Profit"),
            self::_REALIZED_TAX => $this->__("Actual Tax"),
            self::_POTENTIAL_TAX => $this->__("Potential Tax"),
            self::_TAX => $this->__("Tax"),
            self::_CUSTOMER_GROUP_ID => $this->__("Customer Group"),
            self::_CUSTOMER_EMAIL => $this->__("Customer Email"),
            self::_CREATED_AT => $this->__("Created Time"),
            self::_UPDATED_AT => $this->__("Updated Time"),
            self::_TOTAL_SALE => $this->__("Total Sales"),
            self::_STATUS => $this->__("Status"),
            self::_ORDER_ID => $this->__("Order ID"),
            self::_ACTION => $this->__("View Action"),

            /* column name from inventory report */
            self::_SUM_TOTAL_QTY => $this->__("Qty in warehouse"),
            self::_MAC => $this->__("MAC"),
            self::_PRICE => $this->__("Selling Price"),
            self::_TOTAL_INV_VALUE => $this->__("Inventory Value"),
            self::_TOTAL_RETAIL_VALUE => $this->__("Potential Revenue"),
            self::_TOTAL_PROFIT => $this->__("Potential Profit"),
            self::_TOTAL_PROFIT_MARGIN => $this->__("Profit Margin"),
            self::_SUPPLIER_NAME => $this->__("Supplier"),
            self::_SUM_QTY_TO_SHIP => $this->__("Qty to ship"),
            self::_AVAILABLE_QTY => $this->__("Available Qty"),
            self::_QTY_IN_ORDER => $this->__("Qty On Purchase Order"),
            self::_SHELF_LOCATION => $this->__("Shelf Location"),
        );
        return $array;
    }
    /**
     * @return array
     */
    public function mappingDimentsionName(){
        $array = $this->mappingFieldsName();
        $array[self::_CUSTOMER_EMAIL] = $this->__("Customer");
        $array[self::_STATUS] = $this->__("Order Status");
        return $array;
    }
    public function inventoryReportMetrics($type = false){
        $metricts =  array(
            self::_NULL.':'.self::_FALSE,
            self::_SKU.':'.self::_TRUE,
            self::_NAME.':'.self::_TRUE,
            self::_SUM_TOTAL_QTY.':'.self::_TRUE,
            ($type == Data::STOCK_ON_HAND) ? self::_MAC.':'.self::_TRUE : self::_SUM_QTY_TO_SHIP.':'.self::_TRUE,
            ($type == Data::STOCK_ON_HAND) ? self::_PRICE.':'.self::_TRUE : self::_AVAILABLE_QTY.':'.self::_TRUE,
            ($type == Data::STOCK_ON_HAND) ? self::_TOTAL_INV_VALUE.':'.self::_TRUE : self::_SUPPLIER_NAME.':'.self::_TRUE,
            ($type == Data::STOCK_ON_HAND) ? self::_TOTAL_RETAIL_VALUE.':'.self::_TRUE : self::_QTY_IN_ORDER.':'.self::_TRUE,
            ($type == Data::STOCK_ON_HAND) ? self::_TOTAL_PROFIT.':'.self::_TRUE : self::_SHELF_LOCATION.':'.self::_TRUE,
            ($type == Data::STOCK_ON_HAND) ? self::_TOTAL_PROFIT_MARGIN.':'.self::_TRUE : self::_NULL.':'.self::_FALSE,
        );
        return implode(',',$metricts);
    }
}