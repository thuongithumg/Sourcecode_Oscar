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
class Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Field_WarehouseStock
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    public function __construct()
    {
        $this->addColumn('warehouse', array(
            'label' => Mage::helper('inventorysuccess')->__('Warehouse'),
            'style' => 'width:250px',
            'renderer' => Mage::getBlockSingleton('inventorysuccess/adminhtml_catalog_product_edit_tab_renderer_warehouse'),
        ));      
        
        if($this->_isShowAvailableQty()) {
            $this->addColumn('available_qty', array(
                'label' => Mage::helper('inventorysuccess')->__('Available Qty'),
                'class' => 'disable',
                'style' => 'width:50px',
                'renderer' => Mage::getBlockSingleton('inventorysuccess/adminhtml_catalog_product_edit_tab_renderer_textfield'),
            ));
            $this->addColumn('force_edit', array(
                'label' => Mage::helper('inventorysuccess')->__('Force Edit'),
                'class' => 'disable',
                'style' => 'width:50px',
                'renderer' => Mage::getBlockSingleton('inventorysuccess/adminhtml_catalog_product_edit_tab_renderer_checkbox'),
            ));
        }
        
        if($this->_isShowQtyToShip()) {
            $this->addColumn('qty_to_ship', array(
                'label' => Mage::helper('inventorysuccess')->__('Qty to ship'),
                'class' => 'disable',
                'style' => 'width:50px',
                'renderer' => Mage::getBlockSingleton('inventorysuccess/adminhtml_catalog_product_edit_tab_renderer_textfield'),
            ));         
        }
        if($this->_isShowTotalQty()) {
            $this->addColumn('total_qty', array(
                'label' => Mage::helper('inventorysuccess')->__('Qty in Warehouse'),
                'class' => 'validate-zero-or-greater',
                'style' => 'width:180px',
            ));
        }
        if($this->_isShowShelfLocation()) {
            $this->addColumn('shelf_location', array(
                'label' => Mage::helper('inventorysuccess')->__('Shelf Location'),
                'style' => 'width:180px',
            ));   
        }
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('inventorysuccess')->__('Register to Warehouse');

        parent::__construct();
        
        $this->setTemplate('inventorysuccess/system/field/array.phtml');
    }
    
    /**
     * 
     * @return string
     */
    public function disableRemoveButton()
    {
        return '1';
    }
    
    /**
     * 
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }
    
    /**
     * 
     * @return bool
     */
    protected function isEditMode()
    {
        if($this->getProduct()->getId()) {
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return bool
     */
    protected function _isShowAvailableQty()
    {
        if($this->isEditMode() && !$this->getProduct()->isComposite()) {
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return bool
     */
    protected function _isShowQtyToShip()
    {
        if($this->isEditMode() && !$this->getProduct()->isComposite()) {
            return true;
        }
        return false;
    }    
    
    /**
     * 
     * @return bool
     */    
    protected function _isShowShelfLocation()
    {
        if(!$this->getProduct()->isComposite()) {
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return bool
     */    
    protected function _isShowTotalQty()
    {
        if(!$this->getProduct()->isComposite()) {
            return true;
        }
        return false;
    }
}