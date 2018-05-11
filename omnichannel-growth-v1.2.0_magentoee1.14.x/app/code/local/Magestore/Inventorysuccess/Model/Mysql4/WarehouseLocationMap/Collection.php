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
class Magestore_Inventorysuccess_Model_Mysql4_WarehouseLocationMap_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    /**
     * @var Magestore_Webpos_Model_Userlocation
     */
    protected $_location;

    /**
     * Init select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->setOrder('location_id', 'ASC');
        return $this;
    }

    /**
     * @return array
     */
    public function getAllWarehouseIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('main_table.warehouse_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * @return array
     */
    public function getAllLocationIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('main_table.location_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * @return $this
     */
    public function joinLocationCollection()
    {
        if (!Mage::helper('core')->isModuleEnabled('Magestore_Webpos')) {
            return $this;
        }
        $this->getSelect()->joinLeft(
            array('webpos_user_location' => $this->getTable('webpos_user_location')),
            'main_table.location_id = webpos_user_location.location_id', array('display_name')
        )
            ->columns(array('id' => 'main_table.location_id'));
        return $this;
    }

    /**
     * @return boolean
     */
    public function getLocationCollection()
    {
        if ($this->_location) {
            return $this->_location;
        }
        if (Mage::helper('core')->isModuleEnabled('Magestore_Webpos')) {
            try {
                if (!$this->_location) {
                    $this->_location = Mage::getModel('webpos/userlocation')->getCollection();
                }
            } catch (\Exception $ex) {
                return false;
            }
        } else {
            return false;
        }
        $this->_location->getSelect()->joinLeft(
            array('warehouse_location_map' => $this->_mainTable),
            'main_table.location_id = warehouse_location_map.location_id', array('warehouse_id')
        )
            ->columns(array('position' => 'main_table.location_id'));
        return $this->_location;
    }

    /**
     * @param $collection
     * @param $warehouseId
     * @return mixed
     */
    public function setStockItemIntoCollectionByLocation($collection, $warehouseId)
    {
        $collection->getSelect()->joinLeft(
            array('os_warehouse_product' => $this->getTable('os_warehouse_product')),
            'e.entity_id = os_warehouse_product.product_id AND os_warehouse_product.' . Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . ' = ' . $warehouseId,
            array('total_qty', 'qty')
        )//->columns(['qty' => new Zend_Db_Expr('total_qty - qty_to_ship')])
        ;
        return $collection;
    }
}