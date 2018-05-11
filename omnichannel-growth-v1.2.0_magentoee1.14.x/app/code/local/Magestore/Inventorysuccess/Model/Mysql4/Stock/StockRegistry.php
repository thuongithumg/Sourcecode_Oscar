<?php
/**
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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorysuccess Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Stock_StockRegistry extends Magestore_Coresuccess_Model_Mysql4_Base
{

    /**
     * prepare to update shelf location in Warehouse
     * add queries to Processor
     *
     * @param int $warehouseId
     * @param array $locations
     * @return array
     */
    public function prepareUpdateLocation($warehouseId, $locations)
    {
        if (!count($locations)) {
            return $this;
        }
        $connection = $this->_getConnection('read');
        $conditions = array();
        foreach ($locations as $productId => $location) {
            $case = $connection->quoteInto('?', $productId);
            $conditions[$case] = $connection->quoteInto('?', $location);
        }
        $values = array('shelf_location' => $connection->getCaseSql('product_id', $conditions, 'shelf_location'));
        $where = array(
            'product_id IN (?)' => array_keys($locations),
            Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . ' = ?' => $warehouseId,
        );

        return array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $values,
            'condition' => $where,
            'table' => $this->getTable('inventorysuccess/warehouse_product')
        );
    }

    /**
     * Prepare query to add product to warehouse
     * Do not update global stock
     * Do not commit query
     *
     * @param int $warehouseId
     * @param int $productId
     * @param array $stockData
     * @retrun array
     */
    public function prepareAddProductToWarehouse($warehouseId, $productId, $stockData)
    {
        $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::TOTAL_QTY] = isset($stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::TOTAL_QTY])
            ? ($stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::TOTAL_QTY]) : 0;
        $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::QTY_TO_SHIP] = isset($stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::QTY_TO_SHIP])
            ? ($stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::QTY_TO_SHIP]) : 0;
        $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::SHELF_LOCATION] = isset($stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::SHELF_LOCATION])
            ? ($stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::SHELF_LOCATION]) : '';
        $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID] = $warehouseId;
        $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::PRODUCT_ID] = $productId;
        /* validate total_qty & qty_to_ship */
        $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::TOTAL_QTY] = max(0, $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::TOTAL_QTY]);
        $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::QTY_TO_SHIP] = max(0, $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::QTY_TO_SHIP]);
        /* calculate available qty */
        $availableQty = $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::TOTAL_QTY]
            - $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::QTY_TO_SHIP];
        $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY] = isset($stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY])
            ? $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY] : $availableQty;
        unset($stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::QTY_TO_SHIP]);

        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => array($stockData),
            'table' => $this->getTable('inventorysuccess/warehouse_product')
        );
        return $query;
    }

    /**
     * Prepare query to change total_qty, qty_to_ship of product in warehouse
     * Do not update global stock
     * Do not commit query
     *
     * @param int $warehouseId
     * @param int $productId
     * @param array $changeQtys
     */
    public function prepareChangeProductQty($warehouseId, $productId, $changeQtys)
    {
        if (!count($changeQtys)) {
            return array();
        }
        $values = array();
        foreach ($changeQtys as $field => $qtyChange) {
            if ($field == Magestore_Inventorysuccess_Model_Warehouse_Product::QTY_TO_SHIP) {
                $field = Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY;
                $operation = $qtyChange > 0 ? '-' : '+';
            } else {
                $operation = $qtyChange > 0 ? '+' : '-';
            }
            $values[$field] = new Zend_Db_Expr($field . $operation . abs($qtyChange));
        }
        $where = array(
            Magestore_Inventorysuccess_Model_Warehouse_Product::PRODUCT_ID . '=?' => $productId,
            Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . '=?' => $warehouseId
        );

        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $values,
            'condition' => $where,
            'table' => $this->getTable('inventorysuccess/warehouse_product')
        );
        return $query;
    }

    /**
     * clone stock item data to many warehouses
     *
     * @param int $productId
     * @param array $stockItemData
     * @param array $warehouses
     * @param array $ignoreWarehouses
     */
    public function prepareCloneStockItemData($productId, $stockItemData, $warehouses = array(), $ignoreWarehouses = array())
    {
        $conditions = array(
            Magestore_Inventorysuccess_Model_Warehouse_Product::PRODUCT_ID . '=?' => $productId
        );
        if (count($ignoreWarehouses)) {
            $conditions[] = Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . ' NOT IN (' . implode($ignoreWarehouses) . ')';
        }
        if (count($warehouses)) {
            $conditions[] = Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . ' IN (' . implode($warehouses) . ')';
        }

        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $stockItemData,
            'condition' => $conditions,
            'table' => $this->getTable('inventorysuccess/warehouse_product')
        );
        return $query;
    }

    /**
     * clone stock status to many warehouses
     *
     * @param int $productId
     * @param array $stockStatusData
     * @param array $warehouses
     * @param array $ignoreWarehouses
     */
    public function prepareCloneStockStatus($productId, $stockStatusData, $warehouses = array(), $ignoreWarehouses = array())
    {
        $conditions = array(
            Magestore_Inventorysuccess_Model_Warehouse_Product::PRODUCT_ID . '=?' => $productId
        );
        if (count($ignoreWarehouses)) {
            $conditions[] = Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . ' NOT IN (' . implode($ignoreWarehouses) . ')';
        }
        if (count($warehouses)) {
            $conditions[] = Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . ' IN (' . implode($warehouses) . ')';
        }
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $stockStatusData,
            'condition' => $conditions,
            'table' => $this->getTable('cataloginventory/stock_status')
        );
        return $query;
    }
}