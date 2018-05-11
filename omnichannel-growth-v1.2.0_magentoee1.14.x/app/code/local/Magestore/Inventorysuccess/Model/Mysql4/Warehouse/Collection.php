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
 * Warehouse Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $MAPPING_FIELDS = array(
        'warehouse' => 'CONCAT(warehouse_name, " (",warehouse_code,")")',
        'total_sku' => 'COUNT(wh_product.item_id)',
        'total_qty' => 'SUM(IFNULL(wh_product.total_qty,0))'
    );
    
    //MAPPING_FIELDS

    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/warehouse');
        $this->_setIdFieldName('warehouse_id');
        $this->_map['fields']['store'] = 'store_table.store_id';        
    }

    /**
     * Init collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getMainTable()))
            ->columns(array('warehouse' => new Zend_Db_Expr('CONCAT(warehouse_name, " (",warehouse_code,")")')));
        return $this;
    }

    /**
     * Add Total SKU and total Qty to Warehouse Collection
     *
     * @return $this
     */
    public function getTotalSkuAndQtyCollection()
    {
        $this->getSelect()->joinLeft(
            array('wh_product' => $this->getTable('inventorysuccess/warehouse_product')),
            'main_table.warehouse_id = wh_product.stock_id',
            array()
        )->columns(array(
            'total_sku' => new Zend_Db_Expr($this->MAPPING_FIELDS['total_sku']),
            'total_qty' => new Zend_Db_Expr($this->MAPPING_FIELDS['total_qty'])
        ))->group('main_table.warehouse_id');
        return $this;
    }

    public function joinPermissionByUserId($userId)
    {
        $warehouseIds = Magestore_Coresuccess_Model_Service::permissionService()->getListPermissionsByObject(
            Mage::getModel($this->getModelName()),
            $userId
        )->getColumnValues('object_id');
        if ($warehouseIds)
            $this->addFieldToFilter('main_table.warehouse_id', array('nin' => $warehouseIds));
        return $this;
    }

    /**
     * Add field filter to collection
     *
     *
     * @param   string|array $field
     * @param   null|string|array $condition
     *
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        foreach ($this->MAPPING_FIELDS as $alias => $column) {
            if ($field == $alias) {
                $field = new Zend_Db_Expr($column);
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  $this
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        foreach ($this->MAPPING_FIELDS as $alias => $column) {
            if ($field == $alias) {
                $field = new Zend_Db_Expr($column);
            }
        }
        return parent::setOrder($field, $direction);
    }

    /**
     * @param string $columnName
     * @param array $filterValue
     * @return $this
     */
    public function addQtyToFilter($columnName, $filterValue)
    {
        if (isset($filterValue['from']))
            $this->getSelect()->having($this->MAPPING_FIELDS[$columnName] . ' >= ?', $filterValue['from']);
        if (isset($filterValue['to']))
            $this->getSelect()->having($this->MAPPING_FIELDS[$columnName] . ' <= ?', $filterValue['to']);
        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        if (!count($this->getSelect()->getPart(Zend_Db_Select::GROUP))) {
            $countSelect->columns(new Zend_Db_Expr('COUNT(*)'));
            return $countSelect;
        }
        //$countSelect->reset(Zend_Db_Select::HAVING);
       // $countSelect->reset(Zend_Db_Select::GROUP);
        $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
        $countSelect->columns(new Zend_Db_Expr(("COUNT(DISTINCT " . implode(", ", $group) . ")")));
        return $countSelect;
    }

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('warehouse_id', 'warehouse');
    }

    /**
     *
     * @return array
     */
    public function getOptionArray()
    {
        $data = array();
        foreach ($this as $item) {
            $data[$item->getId()] = $item->getWarehouseName() .'('.$item->getWarehouseCode() .')';
        }
        return $data;
    }
    
    /**
     * Add filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @param bool $withAdmin
     * @return Mage_Cms_Model_Resource_Block_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        if (!is_array($store)) {
            $store = array($store);
        }

        if ($withAdmin) {
            $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
        }

        $this->addFilter('store', array('in' => $store), 'public');

        return $this;
    }  
    
    /**
     * Join store relation table if there is store filter
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('inventorysuccess/warehouse_store')),
                'main_table.warehouse_id = store_table.warehouse_id',
                array()
            )->group('main_table.warehouse_id');

            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }    
}