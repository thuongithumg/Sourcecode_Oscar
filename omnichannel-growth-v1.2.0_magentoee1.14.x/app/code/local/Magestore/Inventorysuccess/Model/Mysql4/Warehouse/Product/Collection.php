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
 * Warehouse Product Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $MAPPING_FIELD = array(
        'warehouse_id' => 'main_table.stock_id',
        'product_id' => 'main_table.product_id',
        'total_qty' => 'main_table.total_qty',
        'sum_total_qty' => 'SUM(main_table.total_qty)',
        'qty_to_ship' => 'main_table.total_qty - main_table.qty',
        'sum_qty_to_ship' => 'SUM(main_table.total_qty - main_table.qty)',
        'available_qty' => 'SUM(main_table.qty)',
        'total_qty_shipped' => 'SUM(warehouse_shipment_item.qty_shipped)',
        'warehouse' => 'CONCAT(warehouse.warehouse_name, " (",warehouse.warehouse_code,")")',
        'price' => 'catalog_product_entity_decimal.value',
        'name' => 'catalog_product_entity_varchar.value',
        'status' => 'catalog_product_entity_int.value'
    );

    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/warehouse_product');
    }
    
    /**
     * Init collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID, 
                array('neq' => Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID)
        );
        return $this;
    }    
    
    /**
     * select data from all stocks (include global stock)
     * 
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection
     */
    public function selectAllStocks()
    {
        $this->getSelect()->reset(Zend_Db_Select::WHERE);
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public static function getMappingFields()
    {
        return array(
            'warehouse_id' => 'main_table.' . Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID,
            'product_id' => 'main_table.product_id',
            'total_qty' => 'main_table.total_qty',
            'sum_total_qty' => 'SUM(main_table.total_qty)',
            'qty_to_ship' => 'main_table.total_qty - main_table.qty',
            'sum_qty_to_ship' => 'SUM(main_table.total_qty - main_table.qty)',
            'available_qty' => 'SUM(main_table.qty)',
            'total_qty_shipped' => 'SUM(warehouse_shipment_item.qty_shipped)',
            'warehouse' => 'CONCAT(warehouse.warehouse_name, " (",warehouse.warehouse_code,")")',
            'price' => 'catalog_product_entity_decimal.value',
            'name' => 'catalog_product_entity_varchar.value',
            'status' => 'catalog_product_entity_int.value'
        );
    }

    /**
     * Join with product entity table and product attribute value tables to get product information
     * 
     * @return $this
     */
    public function joinProductCollection(){
        $productNameAttributeId = Mage::getModel('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name')
            ->getId();
        $productPriceAttributeId = Mage::getModel('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'price')
            ->getId();
        $productStatusAttributeId = Mage::getModel('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'status')
            ->getId();
        $this->getSelect()->joinLeft(
                array('product_entity' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')),
                $this->MAPPING_FIELD['product_id'].' = product_entity.entity_id',
                array('entity_id', 'sku')
            )->joinLeft(
                array('catalog_product_entity_varchar' => Mage::getSingleton('core/resource')
                    ->getTableName('catalog_product_entity_varchar')),
                "catalog_product_entity_varchar.entity_id = product_entity.entity_id && 
                    catalog_product_entity_varchar.attribute_id = $productNameAttributeId && catalog_product_entity_varchar.store_id = 0",
                array('')
            )->columns(array('name' => $this->MAPPING_FIELD['name']))
            ->joinLeft(
                array('catalog_product_entity_decimal' => Mage::getSingleton('core/resource')
                    ->getTableName('catalog_product_entity_decimal')),
                "catalog_product_entity_decimal.entity_id = product_entity.entity_id && 
                    catalog_product_entity_decimal.attribute_id = $productPriceAttributeId &&  catalog_product_entity_decimal.store_id = 0",
                array('')
            )->columns(array('price' => $this->MAPPING_FIELD['price']))
            ->joinLeft(
                array('catalog_product_entity_int' => Mage::getSingleton('core/resource')
                    ->getTableName('catalog_product_entity_int')),
                "catalog_product_entity_int.entity_id = product_entity.entity_id && 
                    catalog_product_entity_int.attribute_id = $productStatusAttributeId &&  catalog_product_entity_int.store_id = 0",
                array('')
            )->columns(array('status' => $this->MAPPING_FIELD['status']));
        return $this;
    }

    /**
     * Calculate qty in warehouse, qty to ship and available qty
     *
     * @return $this
     */
    public function calculateQtys(){
        $this->getSelect()->group($this->MAPPING_FIELD['product_id']);
        $this->getSelect()->columns(array(
            'sum_total_qty' => new Zend_Db_Expr($this->MAPPING_FIELD['sum_total_qty']),
            'sum_qty_to_ship' => new Zend_Db_Expr($this->MAPPING_FIELD['sum_qty_to_ship']),
            'available_qty' => new Zend_Db_Expr($this->MAPPING_FIELD['available_qty']),
        ));
        return $this;
    }

    /**
     * Filter warehouse product collection by warehouse id
     *
     * @param $warehouseId
     * @return $this
     */
    public function addWarehouseToFilter($warehouseId)
    {
        $this->getSelect()->where($this->MAPPING_FIELD['warehouse_id'] . ' = ?', $warehouseId);
        return $this;
    }

    /**
     * Filter warehouse product collection by product ids
     *
     * @param array $productIds
     * @return $this
     */
    public function addProductIdsToFilter($productIds = array())
    {
        $this->getSelect()->where($this->MAPPING_FIELD['product_id'] . ' IN (?)', $productIds);
        return $this;
    }

    /**
     * @param int $warehouseId
     * @return Mage_Core_Model_Abstract
     */
    public function getTotalQtysFromWarehouse($warehouseId)
    {
        $this->addWarehouseToFilter($warehouseId);
        $this->getSelect()->columns(array(
            'sum_total_qty' => new Zend_Db_Expr($this->MAPPING_FIELD['sum_total_qty']),
            'sum_qty_to_ship' => new Zend_Db_Expr($this->MAPPING_FIELD['sum_qty_to_ship']),
            'available_qty' => new Zend_Db_Expr($this->MAPPING_FIELD['available_qty'])
        ))
            ->reset(Zend_Db_Select::GROUP)
            ->group($this->MAPPING_FIELD['warehouse_id']);
        return $this->getFirstItem();
    }

    /**
     * Retrieve Warehouse Stocks by productId
     *
     * @param int $productId
     * @return $this
     */
    public function retrieveWarehouseStocks($productId)
    {
        $this->addFieldToFilter('main_table.product_id', $productId);
        $this->getSelect()->joinLeft(
            array('warehouse' => Mage::getSingleton('core/resource')->getTableName('os_warehouse')),
            $this->MAPPING_FIELD['warehouse_id'] . ' = warehouse.warehouse_id',
            array()
        );
        $this->getSelect()->columns(array(
            'warehouse' => new Zend_Db_Expr($this->MAPPING_FIELD['warehouse']),
            'available_qty' => new Zend_Db_Expr($this->MAPPING_FIELD['available_qty']),
            'qty_to_ship' => new Zend_Db_Expr($this->MAPPING_FIELD['qty_to_ship'])
        ))->group($this->MAPPING_FIELD['warehouse_id']);
        return $this;
    }

    /**
     * Get list products can be deleted in warehouse
     *
     * @return $this
     */
    public function getCanDeleteProducts()
    {
        $this->getSelect()->having($this->MAPPING_FIELD['sum_total_qty'] . ' = ?', 0)
            ->having($this->MAPPING_FIELD['sum_qty_to_ship'] . ' = ?', 0);
        return $this;
    }

    /**
     * Get list warehouse products are in stock
     *
     * @return $this
     */
    public function getInStockProduct()
    {
        $this->getSelect()->having($this->MAPPING_FIELD['sum_total_qty'] . ' > ?', 0);
        return $this;
    }

    /**
     * Get list warehouse products are out of stock
     *
     * @return $this
     */
    public function getOutStockProduct()
    {
        $this->getSelect()->having($this->MAPPING_FIELD['sum_total_qty'] . ' <= ?', 0);
        return $this;
    }

    /**
     * Get best seller product from warehouse id
     * 
     * @param $numberProduct
     * @param null $warehouseId
     * @return $this
     */
    public function getBestSellerProducts($numberProduct, $warehouseId = null){
        if($warehouseId) {
            $this->addWarehouseToFilter($warehouseId);
        }
        $this->getSelect()->joinLeft(
            array('warehouse_shipment_item' => Mage::getSingleton('core/resource')->getTableName('os_warehouse_shipment_item')),
            $this->MAPPING_FIELD['product_id'] . ' = warehouse_shipment_item.product_id AND ' .
            $this->MAPPING_FIELD['warehouse_id'] . ' = warehouse_shipment_item.warehouse_id',
            '*'
        );
        $this->getSelect()->columns(array(
            'total_qty_shipped' => new Zend_Db_Expr($this->MAPPING_FIELD['total_qty_shipped'])
        ));
        $this->getSelect()->order(new Zend_Db_Expr($this->MAPPING_FIELD['total_qty_shipped'] . ' DESC'));
        return $this->setPageSize($numberProduct)->setCurPage(1);
    }

    /**
     * Get highest qty products from warehouse id
     *
     * @param int $numberProduct
     * @param null $warehouseId
     * @return $this
     */
    public function getHighestQtyProducts($numberProduct, $warehouseId = null){
        if($warehouseId) {
            $this->addWarehouseToFilter($warehouseId);
        }
        $this->getSelect()->order(new Zend_Db_Expr($this->MAPPING_FIELD['sum_total_qty'] . ' DESC'));
        return $this->setPageSize($numberProduct)->setCurPage(1);
    }

    /**
     * @param string $columnName
     * @param array $filterValue
     * @return $this
     */
    public function addQtyToFilter($columnName, $filterValue)
    {
        if (isset($filterValue['from'])) {
            $this->getSelect()->having($this->MAPPING_FIELD[$columnName] . ' >= ?', $filterValue['from']);
        }
        if (isset($filterValue['to'])) {
            $this->getSelect()->having($this->MAPPING_FIELD[$columnName] . ' <= ?', $filterValue['to']);
        }
        return $this;
    }

    /**
     * @param string $columnName
     * @param array $filterValue
     * @return $this
     */
    public function addSheldLocationToFilter($columnName, $filterValue)
    {
        $this->getSelect()->having($columnName . ' LIKE ?', '%' . $filterValue . '%');
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @param   string|array $field
     * @param   null|string|array $condition
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        foreach ($this->MAPPING_FIELD as $alias => $realField) {
            if ($alias == $field) {
                $field = new Zend_Db_Expr($this->MAPPING_FIELD[$alias]);
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection_Db
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        foreach ($this->MAPPING_FIELD as $alias => $realField) {
            if ($alias == $field) {
                $field = new Zend_Db_Expr($this->MAPPING_FIELD[$alias]);
            }
        }
        return parent::setOrder($field, $direction);
    }

    /**
     * self::setOrder() alias
     *
     * @param string $field
     * @param string $direction
     * @return Varien_Data_Collection_Db
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        foreach ($this->MAPPING_FIELD as $alias => $realField) {
            if ($alias == $field) {
                $field = new Zend_Db_Expr($this->MAPPING_FIELD[$alias]);
            }
        }
        return parent::addOrder($field, $direction);
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $sql = $this->getSelect();
            $this->_totalRecords = count($this->getConnection()->fetchAll($sql, $this->_bindParams));
        }
        return intval($this->_totalRecords);
    }

    /**
     * add barcode to products into a collection
     *
     * @param
     * @return $this
     */
    public function addBarcodeToSelect(){
        $this->getSelect()->join(
            array('barcode_product' => $this->getTable('os_barcode')),
            'warehouse_product.product_id = barcode_product.product_id',
            array('barcode')
        );
        return $this;
    }

    /**
     * add barcode to products into a collection
     *
     * @param
     * @return $this
     */
    public function addBarcodeToFilter($barcode){
        $this->getSelect()->where('barcode_product.barcode = ?', $barcode);
        return $this;
    }

    /**
     * Get count sql
     *
     * @return Zend_DB_Select
     */
    public function getSelectCountSql() {
        $this->_renderFilters();
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns('COUNT(DISTINCT main_table.product_id)');
        $countSelect = clone $this->getSelect();
        $countSelect->reset()->from(array('main_table'=> new Zend_Db_Expr('('.$select->__toString(). ')')))
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('COUNT(*)');
        return $countSelect;
    }
}