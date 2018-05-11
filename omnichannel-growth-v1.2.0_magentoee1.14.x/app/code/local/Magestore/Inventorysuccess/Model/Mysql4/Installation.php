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
 * Adjuststock Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Installation extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * @var
     */
    protected $_mainTableQuery;
    protected $_joinTableQuery;
    protected $_conditionQuery;
    protected $_primaryField;

    /**
     *
     */
    protected function _construct()
    {
        //todo
        $this->_init('inventorysuccess/installation', 'id');
        $this->_mainTableQuery = array(
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER_ITEM =>
                Mage::getSingleton('core/resource')->getTableName('os_warehouse_order_item'),
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_SHIPMENT_ITEM =>
                Mage::getSingleton('core/resource')->getTableName('os_warehouse_shipment_item'),
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_CREDITMEMO_ITEM =>
                Mage::getSingleton('core/resource')->getTableName('os_warehouse_creditmemo_item'),
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER =>
                Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item'),
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER_GRID =>
                Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item'),
        );
        $this->_joinTableQuery = array(
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER_ITEM =>
                $this->getTable('sales/order_item'),
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_SHIPMENT_ITEM =>
                $this->getTable('sales/shipment_item'),
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_CREDITMEMO_ITEM =>
                $this->getTable('sales/creditmemo_item'),
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER =>
                Mage::getSingleton('core/resource')->getTableName('sales_flat_order'),
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER_GRID =>
                Mage::getSingleton('core/resource')->getTableName('sales_flat_order_grid'),
        );
        $this->_conditionQuery = array(
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER_ITEM =>
                'main_table.order_id = order.order_id and main_table.item_id = order.item_id and main_table.product_id = order.product_id',
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_SHIPMENT_ITEM =>
                'main_table.shipment_id = order.parent_id and main_table.order_item_id = order.order_item_id and main_table.product_id = order.product_id',
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_CREDITMEMO_ITEM =>
                'main_table.creditmemo_id = order.parent_id and main_table.order_item_id = order.order_item_id and main_table.product_id = order.product_id',
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER =>
                'main_table.order_id = order.entity_id AND main_table.warehouse_id != 0',
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER_GRID =>
                'main_table.order_id = order.entity_id AND main_table.warehouse_id != 0',
        );
        $this->_primaryField = array(
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER_ITEM => 'item_id',
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_SHIPMENT_ITEM => 'entity_id',
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_CREDITMEMO_ITEM => 'entity_id',
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER => 'entity_id',
            Magestore_Inventorysuccess_Model_Service_InstallationService::STEP_CONVERT_ORDER_GRID => 'entity_id',
        );
    }

    /**
     * Prepare to transfer products to default warehouse
     *
     * @param int $warehouseId
     * @param int $start
     * @param int $size
     * @return array
     */
    public function prepareTransferProductsToDefaultWarehouse2($warehouseId, $start = 0, $size = 0)
    {
        $connection = $this->_getConnection('read');
        /* load all ids of simple products */
        $select = $connection->select()
            ->from($this->getTable('catalog/product'), array('entity_id'))
            ->where('type_id = ?', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);

        $total = $this->getTotalItems($select);

        if ($size) {
            $select->limit($size, $start);
        }
        $query = $connection->query($select);

        $productIds = array();
        while ($row = $query->fetch()) {
            $productIds[] = $row['entity_id'];
        }

        if (!count($productIds)) {
            return array();
        }
        /* load all stock items of simple products */
        $select = $connection->select()
            ->from($this->getTable('cataloginventory/stock_item'), array('product_id', 'qty'))
            ->where("product_id IN ('" . implode("','", $productIds) . "')");

        $query = $connection->query($select);
        $values = array();
        while ($row = $query->fetch()) {
            $qty = $row['qty'] ? $row['qty'] : 0;
            $values[] = array('warehouse_id' => $warehouseId, 'product_id' => $row['product_id'], 'total_qty' => $qty);
        }

        /* add query to Processor */
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $values,
            'table' => $this->getTable('inventorysuccess/warehouse_product')
        );
        return array('query' => $query, 'total' => $total);
    }

    /**
     * Prepare to transfer products to default warehouse
     *
     * @param int $warehouseId
     * @param int $start
     * @param int $size
     * @return array
     */
    public function prepareTransferProductsToDefaultWarehouse($warehouseId, $start = 0, $size = 0)
    {
        $connection = $this->_getConnection('read');
        /* load all ids of simple products */
        $select = $connection->select()
            ->from($this->getTable('catalog/product'), array('entity_id'))//->where('type_id = ?', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
        ;

        if (Magestore_Coresuccess_Model_Service::installationConvertDataService()->needConvert()) {
            /* add composite products to primary warehouse after converted data from inventorypluss */
            $select->where('type_id != ?', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
        }

        $total = $this->getTotalItems($select);

        if ($size) {
            $select->limit($size, $start);
        }
        $query = $connection->query($select);

        $productIds = array();
        while ($row = $query->fetch()) {
            $productIds[] = $row['entity_id'];
        }

        if (!count($productIds)) {
            return array();
        }
        /* load all stock items of simple products */
        $select = $connection->select()
            ->from($this->getTable('cataloginventory/stock_item'), array('*'))
            ->where("stock_id=?", Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID)
            ->where("product_id IN ('" . implode("','", $productIds) . "')");

        $query = $connection->query($select);
        $values = array();
        while ($row = $query->fetch()) {
            unset($row['item_id']);
            $row['total_qty'] = $row['qty'] ? $row['qty'] : 0;
            $row['stock_id'] = $warehouseId;
            $values[] = $row;

        }   
	
        if(!count($values)) {
                return array();
        }

        /* add query to Processor */
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $values,
            'table' => $this->getTable('inventorysuccess/warehouse_product')
        );
        return array('query' => $query, 'total' => $total);
    }

    /**
     * Scan configurable items in orders to update qty_to_ship of child products
     *
     * @param array $items
     * @param array $products
     * @return array
     */
    public function scanOrderConfigurableItems($items, $products)
    {
        $parentItemIds = array();
        foreach ($items as $item) {
            if (isset($item['parent_item_id']) && $item['parent_item_id']) {
                $parentItemIds[] = $item['parent_item_id'];
            }
        }
        if (!count($parentItemIds)) {
            return $products;
        }
        $connection = $this->_getConnection('read');

        /* Get order items */
        $select = $connection->select()->from(array('main_table' => $this->getTable('sales/order_item')), array(
            'item_id',
            'order_id',
            'product_id',
            'qty_ordered',
            'qty_canceled',
            'subtotal' => 'base_row_total',
            'qty_to_ship' => "IF(qty_ordered-qty_shipped-qty_refunded-qty_canceled > '0', qty_ordered-qty_shipped-qty_refunded-qty_canceled, 0)",
        ))
            ->where('product_type = ?', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            ->where('item_id IN(\'' . implode("','", $parentItemIds) . '\')');
        $query = $connection->query($select);
        $parentQtyToShips = array();

        while ($row = $query->fetch()) {
            $parentProductId = $row['product_id'];
            $parentItemId = $row['item_id'];
            $productId = null;
            $qtyToShip = 0;
            foreach ($items as $item) {
                if ($item['parent_item_id'] == $parentItemId) {
                    $productId = $item['product_id'];
                    $qtyToShip = $item['qty_to_ship'];
                }
            }
            if (!$productId) {
                continue;
            }
            if (!isset($parentQtyToShips[$productId])) {
                $parentQtyToShips[$productId] = ($row['qty_to_ship'] - $qtyToShip);
            } else {
                $parentQtyToShips[$productId] += ($row['qty_to_ship'] - $qtyToShip);
            }
        }

        if (!count($parentQtyToShips)) {
            return $products;
        }

        foreach ($products as &$product) {
            if (isset($parentQtyToShips[$product['product_id']])) {
                $product['qty_to_ship'] += $parentQtyToShips[$product['product_id']];
            }
        }
        return $products;
    }

    /**
     * Prepare calculating qty-to-ship
     *
     * @param int $warehouseId
     * @param int $start
     * @param int $size
     * @return array ['query' => $query, 'qtys_to_ship' => $products, 'total' => $total]
     */
    public function scanOrderItems($warehouseId, $start = 0, $size = 0)
    {
        $items = array();
        $products = array();
        $connection = $this->_getConnection('read');
        /* Get order items */
        $select = $connection->select()->from(array('main_table' => $this->getTable('sales/order_item')), array(
            'item_id',
            'parent_item_id',
            'product_id',
            'qty_to_ship' => "IF(qty_ordered-qty_shipped-qty_refunded-qty_canceled > '0', qty_ordered-qty_shipped-qty_refunded-qty_canceled, 0)",
        ))
            ->where('product_type = ?', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
        $total = $this->getTotalItems($select);
        if ($size) {
            $select->limit($size, $start);
        }
        $query = $connection->query($select);
        $ids = array();
        while ($row = $query->fetch()) {
            /* item_id to update in sales_flat_order_item table */
            $ids[] = $row['item_id'];

            /* prepare qty_to_ship data of product in warehouse */
            $productId = $row['product_id'];
            $qtyToShip = $row['qty_to_ship'];
            if (isset($products[$productId])) {
                $qtyToShip += $products[$productId]['qty_to_ship'];
            }
            $products[$productId] = array('product_id' => $productId, 'qty_to_ship' => $qtyToShip);

            $items[$row['item_id']] = $row;
        }
        if (!count($ids)) {
            return array('query' => array(), 'qtys_to_ship' => array());
        }

        /* prepare query to update in sales_flat_order_item table */
        $updateValues = array(
            'warehouse_id' => $warehouseId,
        );
        $field = 'item_id';
        $where = $connection->quoteInto($this->getTable('sales/order_item') . '.' . $field . ' IN (?) ', $ids);
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $updateValues,
            'condition' => $where,
            'table' => $this->getTable('sales/order_item')
        );

        /* scan qty-to-ship of configurable item */
        $products = $this->scanOrderConfigurableItems($items, $products);
        return array('query' => $query, 'total' => $total, 'qtys_to_ship' => $products);
    }


    public function convertItems($type, $warehouseId, $start = 0, $size = 0)
    {
        $connection = $this->_getConnection('read');
        $select = $connection->select()->from(array('main_table' => $this->_mainTableQuery[$type]), array(
            'warehouse_id',
        ))
            ->joinLeft(array('order' => $this->_joinTableQuery[$type]),
                $this->_conditionQuery[$type], array(
                    'item_id_convert' => 'order.' . $this->_primaryField[$type]
                )
            );
        $total = $this->getTotalItems($select);
        if ($size) {
            $select->limit($size, $start);
        }
        $query = $connection->query($select);
        $conditions = array();
        $itemIdsArray = array();
        while ($row = $query->fetch()) {
            if (!isset($row['item_id_convert']) || !$row['item_id_convert']) {
                continue;
            }
            $itemIdsArray[] = $row['item_id_convert'];
            $case = $connection->quoteInto('?', $row['item_id_convert']);
            $warehouse_id = $row['warehouse_id'];
            $conditions['convert'][$case] = $warehouse_id;
        }
        if (!$conditions) {
            return array();
        }
        $values = array(
            'warehouse_id' => $connection->getCaseSql($this->_primaryField[$type], $conditions['convert'], $warehouseId),
        );
        $where = array($this->_primaryField[$type] . ' IN (?)' => $itemIdsArray);
        /* query to update warehouse_id */
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $values,
            'condition' => $where,
            'table' => $this->_joinTableQuery[$type]
        );
        return $data = array(
            'query' => $query,
            'total' => $total
        );
    }

    /**
     * prepare query to add shipment_items to warehouse
     * array('query', 'total')
     *
     * @param int $warehouseId
     * @param int $start
     * @param int $size
     * @return array
     */
    public function scanShipmentItems($warehouseId, $start = 0, $size = 0)
    {
        $items = array();
        $connection = $this->_getConnection('read');
        /* Get shipment items */
        $select = $connection->select()->from(array('main_table' => $this->getTable('sales/shipment_item')), array(
            'item_id' => 'entity_id',
        ));
        $total = $this->getTotalItems($select);
        if ($size) {
            $select->limit($size, $start);
        }
        $query = $connection->query($select);
        $ids = array();
        while ($row = $query->fetch()) {
            $ids[] = $row['item_id'];
        }
        if (!count($ids)) {
            return array();
        }
        $updateValues = array(
            'warehouse_id' => $warehouseId,
        );
        $field = 'entity_id';
        $where = $connection->quoteInto($this->getTable('sales/shipment_item') . '.' . $field . ' IN (?) ', $ids);

        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $updateValues,
            'condition' => $where,
            'table' => $this->getTable('sales/shipment_item')
        );
        return array('query' => $query, 'total' => $total);
    }

    /**
     * prepare query to add creditmemo_items to warehouse
     * return array('query', 'total')
     *
     * @param int $warehouseId
     * @param int $start
     * @param int $size
     * @return array
     */
    public function scanCreditmemoItems($warehouseId, $start = 0, $size = 0)
    {
        $items = array();
        $connection = $this->_getConnection('read');
        /* Get shipment items */
        $select = $connection->select()->from(array('main_table' => $this->getTable('sales/creditmemo_item')), array(
            'item_id' => 'entity_id',
        ));
        $total = $this->getTotalItems($select);
        if ($size) {
            $select->limit($size, $start);
        }
        $query = $connection->query($select);
        $ids = array();
        while ($row = $query->fetch()) {
            $ids[] = $row['item_id'];
        }
        if (!count($ids)) {
            return array();
        }
        $updateValues = array(
            'warehouse_id' => $warehouseId,
        );
        $field = 'entity_id';
        $where = $connection->quoteInto($this->getTable('sales/creditmemo_item') . '.' . $field . ' IN (?) ', $ids);
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $updateValues,
            'condition' => $where,
            'table' => $this->getTable('sales/creditmemo_item')
        );
        return array('query' => $query, 'total' => $total);
    }

    /**
     * Prepare update qty-to-ship to warehouse
     *
     * @param int $warehouseId
     * @param array $qtys
     * @return array
     */
    public function prepareQtyToShipWarehouse($warehouseId, $qtys)
    {
        $connection = $this->_getConnection('read');
        if (!count($qtys)) {
            return array();
        }
        $conditions = array();
        foreach ($qtys as $productId => $item) {
            $case = $connection->quoteInto('?', $productId);
            $totalQtyResult = $connection->quoteInto('total_qty+?', $item['qty_to_ship']);
            $conditions['total_qty'][$case] = $totalQtyResult;
        }
        $values = array(
            'total_qty' => $connection->getCaseSql('product_id', $conditions['total_qty'], 'total_qty'),
        );
        $where = array('product_id IN (?)' => array_keys($qtys), 'stock_id = ?' => $warehouseId);

        /* query to update qty-to-ship of products in warehouse */
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $values,
            'condition' => $where,
            'table' => $this->getTable('inventorysuccess/warehouse_product')
        );

        return $query;
    }

    /**
     * Prepare update qty-to-ship to warehouse
     *
     * @param int $warehouseId
     * @param array $qtys
     * @return array
     */
    public function prepareQtyToShipWarehouse2($warehouseId, $qtys)
    {
        $connection = $this->_getConnection('read');
        if (!count($qtys)) {
            return array();
        }
        $conditions = array();
        foreach ($qtys as $productId => $item) {
            $case = $connection->quoteInto('?', $productId);
            $qtyToShipResult = $connection->quoteInto('qty_to_ship+?', $item['qty_to_ship']);
            $totalQtyResult = $connection->quoteInto('total_qty+?', $item['qty_to_ship']);
            $conditions['qty_to_ship'][$case] = $qtyToShipResult;
            $conditions['total_qty'][$case] = $totalQtyResult;
        }
        $values = array(
            'qty_to_ship' => $connection->getCaseSql('product_id', $conditions['qty_to_ship'], 'qty_to_ship'),
            'total_qty' => $connection->getCaseSql('product_id', $conditions['total_qty'], 'total_qty'),
        );
        $where = array('product_id IN (?)' => array_keys($qtys), 'warehouse_id = ?' => $warehouseId);

        /* query to update qty-to-ship of products in warehouse */
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $values,
            'condition' => $where,
            'table' => $this->getTable('inventorysuccess/warehouse_product')
        );

        return $query;
    }

    /**
     *
     * @return array
     */
    public function getNeedToShipOrderIds()
    {
        $orderIds = array();
        $connection = $this->_getConnection('read');
        $condition = $connection->prepareSqlCondition('status', array('nin' => array('complete', 'closed', 'canceled')));
        $select = $connection->select()
            ->from($this->getTable('sales/order'), array('entity_id'))
            ->where($condition);

        $query = $connection->query($select);
        while ($row = $query->fetch()) {
            $orderIds[] = $row['entity_id'];
        }
        return $orderIds;
    }

    /**
     * Get total items in select
     *
     * @param Zend_Db_Select $select
     * @return int
     */
    public function getTotalItems($select)
    {
        $countSelect = clone $select;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns(array('total' => 'COUNT(*)'));
        $connection = $this->_getConnection('read');
        $query = $connection->query($countSelect);
        $row = $query->fetch();
        return isset($row['total']) ? intval($row['total']) : 0;
    }

    /**
     * @param $select
     */
    public function viewData($select)
    {
        $cr = Mage::getSingleton('core/resource');
        $connection = $cr->getConnection('core_write');
        $query = $connection->fetchAll($select);
    }

    /**
     *
     * re-run the setup script
     */
    public function reapplySetupScript()
    {
        $installer = new Mage_Core_Model_Resource_Setup(Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);

        /* mysql4-upgrade-1.0.0-1.0.1 */
        if (!$installer->getConnection()->isTableExists($installer->getTable('os_warehouse_store'))) {
            /**
             * create os_warehouse_store table
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('os_warehouse_store'))
                ->addColumn(
                    'id',
                    Varien_Db_Ddl_Table::TYPE_INTEGER,
                    null,
                    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
                    'Id'
                )
                ->addColumn(
                    'store_id',
                    Varien_Db_Ddl_Table::TYPE_INTEGER,
                    6,
                    array('unsigned' => true, 'nullable' => false),
                    'Store Id'
                )
                ->addColumn(
                    'warehouse_id',
                    Varien_Db_Ddl_Table::TYPE_INTEGER,
                    11,
                    array('unsigned' => true, 'nullable' => false),
                    'Warehouse Id'
                )->addIndex(
                    $installer->getIdxName('os_warehouse_store', array('warehouse_id')),
                    array('warehouse_id')
                )->addForeignKey(
                    $installer->getFkName(
                        'os_warehouse_store',
                        'warehouse_id',
                        'os_warehouse',
                        'warehouse_id'
                    ),
                    'warehouse_id',
                    $installer->getTable('os_warehouse'),
                    'warehouse_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE
                )->addIndex(
                    $installer->getIdxName('os_warehouse_store', array('store_id')),
                    array('store_id')
                )->addForeignKey(
                    $installer->getFkName(
                        'os_warehouse_store',
                        'store_id',
                        'core_store',
                        'store_id'
                    ),
                    'store_id',
                    $installer->getTable('core_store'),
                    'store_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE
                )->addIndex(
                    $installer->getIdxName(
                        'os_warehouse_store',
                        array('warehouse_id', 'store_id'),
                        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                    ),
                    array('warehouse_id', 'store_id'),
                    array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
                );

            $installer->getConnection()->createTable($table);
        }
        /* remove store_id from os_warehouse */
        $connection = $installer->getConnection();
        if ($connection->tableColumnExists($installer->getTable('os_warehouse'), 'store_id')) {
            Magestore_Coresuccess_Model_Service::warehouseStoreService()->transferStoreIdFromWarehouseTable();
            $connection->dropColumn(
                $installer->getTable('os_warehouse'),
                'store_id'
            );
        }

        /* mysql4-upgrade-1.0.1-1.2.0 */
        $connection = $installer->getConnection();
        /* add warehouse_id to sales_flat_order_item */
        if (!$connection->tableColumnExists($installer->getTable('sales/order_item'), 'warehouse_id')) {
            $connection->addColumn($installer->getTable('sales/order_item'), 'warehouse_id', array(
                'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable' => false,
                'length' => 11,
                'comment' => 'Warehouse Id'
            ));
        }
        /* add warehouse_id to sales_flat_shipment_item */
        if (!$connection->tableColumnExists($installer->getTable('sales/shipment_item'), 'warehouse_id')) {
            $connection->addColumn($installer->getTable('sales/shipment_item'), 'warehouse_id', array(
                'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable' => false,
                'length' => 11,
                'comment' => 'Warehouse Id'
            ));
        }
        /* add warehouse_id to sales_flat_creditmemo_item */
        if (!$connection->tableColumnExists($installer->getTable('sales/creditmemo_item'), 'warehouse_id')) {
            $connection->addColumn($installer->getTable('sales/creditmemo_item'), 'warehouse_id', array(
                'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable' => false,
                'length' => 11,
                'comment' => 'Warehouse Id'
            ));
        }
    }

}
