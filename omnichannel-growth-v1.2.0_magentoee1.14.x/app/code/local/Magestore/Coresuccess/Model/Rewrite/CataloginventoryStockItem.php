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
 * @package     Magestore_Coresuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @author      Magestore Developer
 */

class Magestore_Coresuccess_Model_Rewrite_CataloginventoryStockItem
    extends Mage_CatalogInventory_Model_Stock_Item
{
    /**
     * Retrieve stock identifier
     *
     * @todo multi stock
     * @return int
     */
    public function getStockId()
    {
        if($this->isModuleEnable('Magestore_Inventorysuccess')) {
            return Magestore_Coresuccess_Model_Service::stockService()->getStockId();
        }
        return parent::getStockId();
    }    
    
    /**
     * Add join for catalog in stock field to product collection
     *
     * @param Mage_Catalog_Model_Entity_Product_Collection $productCollection
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function addCatalogInventoryToProductCollection($productCollection)
    {
        parent::addCatalogInventoryToProductCollection($productCollection);
        $productCollection->getSelect()->where('cisi.stock_id=?', $this->getStockId());
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getBackorders() 
    {
        if($this->isModuleEnable('Magestore_Webpos')) {
            $isWebposApi = Mage::helper('webpos/permission')->getCurrentSession();
            $storeId = Mage::app()->getStore()->getId();
            if ($isWebposApi 
                    && strpos(Mage::app()->getRequest()->getPathInfo(), 'webpos') !== false 
                    && Mage::getStoreConfig('webpos/general/ignore_checkout', $storeId)) {
                return Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY;
            }
        }
        return parent::getBackorders();
    }

    /**
     * @return bool
     */
    public function getIsInStock() 
    {
        if($this->isModuleEnable('Magestore_Webpos')) {
            $isWebposApi = Mage::helper('webpos/permission')->getCurrentSession();
            $storeId = Mage::app()->getStore()->getId();
            if ($isWebposApi 
                    && strpos(Mage::app()->getRequest()->getPathInfo(), 'webpos') !== false 
                    && Mage::getStoreConfig('webpos/general/ignore_checkout', $storeId)) {
                return true;
            }
        }
        return parent::getIsInStock();
    }  
    
    /**
     * Check quantity
     *
     * @param   decimal $qty
     * @exception Mage_Core_Exception
     * @return  bool
     */
    public function checkQty($qty)
    {
        if($this->isModuleEnable('Magestore_Webpos')) {
            $isWebposApi = Mage::helper('webpos/permission')->getCurrentSession();
            $storeId = Mage::app()->getStore()->getId();
            if ($isWebposApi 
                    && strpos(Mage::app()->getRequest()->getPathInfo(), 'webpos') !== false ) {
                /* request from Webpos */
                if(!$this->getManageStock() || Mage::getStoreConfig('webpos/general/ignore_checkout', $storeId)) {
                    return true;
                }
                if ($this->getQty() - $this->getMinQty() - $qty < 0) {
                    switch ($this->getBackorders()) {
                        case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY:
                        case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY:
                            break;
                        default:
                            return false;
                            break;
                    }
                }
                return true;                
            }
        }
        
        return parent::checkQty($qty);
    }    
    
    /**
     * 
     * @param string $module
     * @return bool
     */
    public function isModuleEnable($module)
    {
        return Mage::helper('core')->isModuleEnabled($module);
    }
  
}

