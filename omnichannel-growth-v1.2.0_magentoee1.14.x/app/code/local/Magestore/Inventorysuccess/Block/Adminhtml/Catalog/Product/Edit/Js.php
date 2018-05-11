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
 * Adjuststock Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Js
    extends Mage_Adminhtml_Block_Template
{
    /**
     * 
     */
    protected function _construct()
    {
        $this->setTemplate('inventorysuccess/catalog/product/edit/js.phtml');
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
            'manage_stock_default' => $this->isManageStockDefault(),
            'manage_stock' => $this->isManageStock(),
            'notice_edit_qty' => $this->__('Cannot edit here. Please edit qty in the Warehouse Stocks section below.'),
            'force_edit_permission' => $this->permission(),
            'force_edit_label' => $this->__('Force Edit'),
        );
        return Zend_Json::encode($config);
    }

    public function permission(){
        return Mage::getSingleton('admin/session')->isAllowed('inventorysuccess/catalog_force_edit');
    }
    
}