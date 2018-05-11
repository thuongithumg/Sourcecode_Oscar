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
 * Warehouse Producr Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Warehouse_Store extends Mage_Core_Model_Abstract
{
    const ID = 'id';
    const WAREHOUSE_ID = 'warehouse_id';
    const STORE_ID = 'store_id';
    
    /**
     * 
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/warehouse_store');
    }
    
    /**
     * @return int|null
     */
    public function getWarehouseId()
    {
        return $this->getData(self::WAREHOUSE_ID);
    }
    
    /**
     * @param int $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }        
    
    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }
    
    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }     
}