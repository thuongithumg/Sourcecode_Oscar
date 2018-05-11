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
 * Warehouse Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Warehouse extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('inventorysuccess/warehouse', 'warehouse_id');
    }
    
    /**
     * Perform operations before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeSave($object);
        
        /* set updated time */
        if (!$object->getId() && !$object->getCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());  
        
        return $this;
    }
    
    /**
     * Perform operations after object save
     * 
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object) 
    {
        parent::_afterSave($object);
        
        /* update cataloginventory/stock 
         * do not update default Stock
         */
        if($object->getId() != Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID) {
            $stock = Mage::getModel('cataloginventory/stock');
            $stockData = array(
                'stock_id' => $object->getWarehouseId(),
                'stock_name' => $object->getWarehouseCode(),
            );
            $this->_getWriteAdapter()->insertOnDuplicate($stock->getResource()->getMainTable(), $stockData);
        }

        return $this;        
    }
    
    /**
     * Perform actions after object delete
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_afterDelete($object);
        
        /* delete linked stock_id from cataloginventory_stock 
         * do not delete default stock id
         */
        if($object->getId() != Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID) {
            $stock = Mage::getModel('cataloginventory/stock');
            $this->_getWriteAdapter()->delete(
                    $stock->getResource()->getMainTable(),
                    $this->_getWriteAdapter()->quoteInto($stock->getResource()->getIdFieldName(). '=?', $object->getId())
            );
        }
    }    
    
    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Block
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $storeIds = Magestore_Coresuccess_Model_Service::warehouseStoreService()
                        ->getStoreIdsFromWarehouseId($object->getId());
            $object->setData('store_id', $storeIds);
            $object->setData('stores', $storeIds);
        }

        return parent::_afterLoad($object);
    }    
    
}