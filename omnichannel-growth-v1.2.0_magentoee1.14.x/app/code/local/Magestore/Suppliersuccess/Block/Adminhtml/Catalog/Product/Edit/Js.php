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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adjuststock Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Block_Adminhtml_Catalog_Product_Edit_Js
    extends Mage_Adminhtml_Block_Template
{
    /**
     * 
     */
    protected function _construct()
    {
        //$this->setTemplate('suppliersuccess/catalog/product/edit/js.phtml');
        parent::_construct();
    }
    
    /**
     * 
     * @return bool
     */
    public function isManageStockDefault()
    {
        return Mage::getStoreConfigFlag(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
    }
    
    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }
    
    /**
     * Retrieve Catalog Inventory  Stock Item Model
     *
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function getStockItem()
    {
        return $this->getProduct()->getStockItem();
    }      
    
    /**
     * 
     * @return boolean
     */
    public function isManageStock()
    {
        if($this->getStockItem() && $this->getStockItem()->getManageStock()) {
            return true;
        } 
        if(!$this->getStockItem() && $this->isManageStockDefault()) {
            return true;
        }
        return false;        
    } 
    
    /**
     * 
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array(
        );
        return Zend_Json::encode($config);
    }
    
}