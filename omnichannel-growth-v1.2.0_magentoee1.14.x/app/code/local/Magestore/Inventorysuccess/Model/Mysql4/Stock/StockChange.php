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
class Magestore_Inventorysuccess_Model_Mysql4_Stock_StockChange extends Mage_CatalogInventory_Model_Resource_Stock
{

    /**
     * prepare queries to update stocks in warehouse
     *
     * @param int $warehouseId
     * @param array $qtys
     * @param string $actionType
     * @return array ["change_qtys", "queries"]
     */
    public function prepareUpdateWarehouseStocks($warehouseId, $qtys, $actionType)
    {
        $queries = array();
        $warehouseProducts = Magestore_Coresuccess_Model_Service::stockRegistryService()->getStocks($warehouseId, array_keys($qtys));
        $connection = $this->_getConnection('core_write');
        $changeQtys = array();
        $newQtys = $qtys;

        /* update stocks in Warehouse */
        if ($warehouseProducts->getSize()) {
            /* update existed products in warehouse */
            $qtyConditions = array();
            $totalQtyConditions = array();
            foreach ($warehouseProducts as $warehouseProduct) {
                /* calculate changed qty */
                if($actionType == Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService::QTY_CHANGE_ACTION) {
                    $changeQty = $qtys[$warehouseProduct->getProductId()];
                } elseif($actionType == Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService::QTY_UPDATE_ACTION) {
                    $changeQty = $qtys[$warehouseProduct->getProductId()] - $warehouseProduct->getTotalQty() ;
                }

                /* add by Kai */
                elseif($actionType == Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService::QTY_FORCE_EDIT_ACTION) {
                    $changeQty = $qtys[$warehouseProduct->getProductId()];
                }
                /* end by Kai */

                $changeQtys[$warehouseProduct->getProductId()] = $changeQty;
                unset($newQtys[$warehouseProduct->getProductId()]);
                /* prepare update value */
                $case = $connection->quoteInto('?', $warehouseProduct->getProductId());
                $operator = $changeQty >= 0 ? '+' : '-';

                /* edit by Kai */
                if($actionType == Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService::QTY_FORCE_EDIT_ACTION){
                    $qtyResult = $connection->quoteInto("qty - qty{$operator}?", abs($changeQty));
                    $qtyConditions[$case] = $qtyResult;

                }else{
                    $qtyResult = $connection->quoteInto("qty{$operator}?", abs($changeQty));
                    $qtyConditions[$case] = $qtyResult;

                    $totalQtyResult = $connection->quoteInto("total_qty{$operator}?", abs($changeQty));
                    $totalQtyConditions[$case] = $totalQtyResult;
                }
                /* end by Kai */

            }

            /* edit by Kai */
            if($actionType == Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService::QTY_FORCE_EDIT_ACTION){
                $values = array(
                    'qty' => $connection->getCaseSql('product_id', $qtyConditions, 'qty'),
                );
            }else{
                $values = array(
                    'total_qty' => $connection->getCaseSql('product_id', $totalQtyConditions, 'total_qty'),
                    'qty' => $connection->getCaseSql('product_id', $qtyConditions, 'qty'),
                );
            }
            /* end by Kai */

            $where = array(
                Magestore_Inventorysuccess_Model_Warehouse_Product::PRODUCT_ID . ' IN (?)' => array_keys($changeQtys),
                Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . '=?' => $warehouseId
            );

            $queries[] = array(
                'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
                'values' =>  $values,
                'condition' => $where,
                'table' => $this->getTable('inventorysuccess/warehouse_product')
            );
        }

        /* add new products to warehouse */
        $queries[] = $this->_prepareAddProductsToWarehouse($warehouseId, $newQtys);

        /* collect all changed items */
        $changeQtys += $newQtys;

        /* prepare to update in-stock status of products in warehouse */
        $queries[] = $this->_prepareUpdateInStockStatus(array_keys($changeQtys), $warehouseId);

        /* prepare to update out-stock status of products in warehouse */
        $queries[] = $this->_prepareUpdateOutStockStatus(array_keys($changeQtys), $warehouseId);

        return array('change_qtys' => $changeQtys, 'queries' => $queries);
    }

    /**
     * Prepare query to add products to warehouse
     *
     * @param int $warehouseId
     * @param array $newQtys
     * @return array|null
     */
    protected function _prepareAddProductsToWarehouse($warehouseId, $newQtys)
    {
        if(!count($newQtys)) {
            return null;
        }
        /* add new products to warehouse */
        $insertData = array();
        foreach ($newQtys as $productId => $qty) {
            $insertData[] = array(
                Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID => $warehouseId,
                Magestore_Inventorysuccess_Model_Warehouse_Product::PRODUCT_ID => $productId,
                Magestore_Inventorysuccess_Model_Warehouse_Product::TOTAL_QTY => $qty,
                Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => $qty,
                'stock_status_changed_auto' => 1,
                'is_in_stock' => Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK,
            );
        }
        /* add query to the processor */
        return array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $insertData,
            'table' => $this->getTable('inventorysuccess/warehouse_product')
        );
    }

    /**
     * prepare queries to update global stocks
     *
     * @param array $changeQtys
     * @return array
     */
    public function prepareUpateGlobalStocks($changeQtys)
    {
        /* prepare to update qty of global stocks */
        $queries = $this->_prepareUpdateGlobalStockQty($changeQtys);

        /* prepare to update in-stock status of global stocks */
        $queries[] = $this->_prepareUpdateInStockStatus(array_keys($changeQtys));

        /* prepare to update out-stock status of global stocks */
        $queries[] = $this->_prepareUpdateOutStockStatus(array_keys($changeQtys));

        return $queries;
    }

    /**
     * Prepare query to update qty of global stocks
     *
     * @param array $changeQtys
     * @return array|null
     */
    protected function _prepareUpdateGlobalStockQty($changeQtys)
    {
        $queries = array();
        /* update global stocks */
        if (!count($changeQtys)) {
            return null;
        }
        $connection = $this->_getConnection('core_write');
        $qtyConditions = array();
        $nullQtyConditions = array();
        $totalQtyConditions = array();
        $nullTotalQtyConditions = array();
        foreach ($changeQtys as $productId => $qty) {
            $operator = $qty >= 0 ? '+' : '-';
            $case = $connection->quoteInto('?', $productId);
            $qtyConditions[$case] = $connection->quoteInto("qty{$operator}?", abs($qty));;
            $totalQtyConditions[$case] = $connection->quoteInto("total_qty{$operator}?", abs($qty));;
            /* in the case of qty is null */
            $nullQtyConditions[$case] = $connection->quoteInto('?', $qty);
            $nullTotalQtyConditions[$case] = $connection->quoteInto('?', $qty);
        }

        /* add query to the processor */
        /* in the case of qty is not null */
        $queries[] = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => array(
                'qty' => $connection->getCaseSql('product_id', $qtyConditions, 'qty'),
                'total_qty' => $connection->getCaseSql('product_id', $totalQtyConditions, 'total_qty'),
            ),
            'condition' => array(
                'product_id IN (?)' => array_keys($changeQtys),
                'stock_id=?' => Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID,
                'qty IS NOT NULL'
            ),
            'table' => $this->getTable('cataloginventory/stock_item')
        );

        /* in the case of qty is null */
        $queries[] = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => array(
                'qty' => $connection->getCaseSql('product_id', $nullQtyConditions, 'qty'),
                'total_qty' => $connection->getCaseSql('product_id', $nullTotalQtyConditions, 'total_qty'),
            ),
            'condition' => array(
                'product_id IN (?)' => array_keys($changeQtys),
                'stock_id=?' => Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID,
                'qty IS NULL'
            ),
            'table' => $this->getTable('cataloginventory/stock_item')
        );

        return $queries;
    }


    /**
     * Prepare query to update out-stock status of products
     *
     * @param array $productIds
     * @return array
     */
    protected function _prepareUpdateOutStockStatus($productIds, $stockId = null)
    {
        $stockId = $stockId ? $stockId : Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID;
        $this->_initConfig();
        $writeAdapter = $this->_getWriteAdapter();
        $updateValues  = array(
            'is_in_stock'                  => 0,
            'stock_status_changed_auto'    => 1
        );

        $select = $writeAdapter->select()
            ->from($this->getTable('catalog/product'), 'entity_id')
            ->where('type_id IN(?)', $this->_configTypeIds)
            ->where('entity_id IN(?)', $productIds);

        $condition = sprintf('stock_id = %1$d'
            . ' AND is_in_stock = 1'
            . ' AND ((use_config_manage_stock = 1 AND 1 = %2$d) OR (use_config_manage_stock = 0 AND manage_stock = 1))'
            . ' AND ((use_config_backorders = 1 AND %3$d = %4$d) OR (use_config_backorders = 0 AND backorders = %3$d))'
            . ' AND ((use_config_min_qty = 1 AND qty <= %5$d) OR (use_config_min_qty = 0 AND qty <= min_qty))'
            . ' AND product_id IN (%6$s)',
            $stockId,
            $this->_isConfigManageStock,
            Mage_CatalogInventory_Model_Stock::BACKORDERS_NO,
            $this->_isConfigBackorders,
            $this->_configMinQty,
            $select->assemble()
        );

        return array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $updateValues,
            'condition' => $condition,
            'table' => $this->getTable('cataloginventory/stock_item')
        );
    }

    /**
     * Prepare query to update in-stock status of products
     *
     * @param array $productIds
     * @return array
     */
    protected function _prepareUpdateInStockStatus($productIds, $stockId = null)
    {
        $stockId = $stockId ? $stockId : Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID;
        $this->_initConfig();
        $writeAdapter = $this->_getWriteAdapter();
        $updateValues  = array(
            'is_in_stock'   => 1,
        );

        $select = $writeAdapter->select()
            ->from($this->getTable('catalog/product'), 'entity_id')
            ->where('type_id IN(?)', $this->_configTypeIds)
            ->where('entity_id IN(?)', $productIds);

        $condition = sprintf('stock_id = %1$d'
            . ' AND is_in_stock = 0'
            . ' AND stock_status_changed_auto = 1'
            . ' AND ((use_config_manage_stock = 1 AND 1 = %2$d) OR (use_config_manage_stock = 0 AND manage_stock = 1))'
            . ' AND ((use_config_min_qty = 1 AND qty > %3$d) OR (use_config_min_qty = 0 AND qty > min_qty))'
            . ' AND product_id IN (%4$s)',
            $stockId,
            $this->_isConfigManageStock,
            $this->_configMinQty,
            $select->assemble()
        );

        return array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $updateValues,
            'condition' => $condition,
            'table' => $this->getTable('cataloginventory/stock_item')
        );
    }

    /**
     *
     * @param type $productIds
     * @return Magestore_Inventorysuccess_Model_Mysql4_Stock_StockChange
     */
    public function reindexStockData($productIds)
    {
        /* reindex stock data */
        if (count($productIds)) {
            Mage::getResourceSingleton('cataloginventory/indexer_stock')->reindexProducts($productIds);
        }
        return $this;
    }

}