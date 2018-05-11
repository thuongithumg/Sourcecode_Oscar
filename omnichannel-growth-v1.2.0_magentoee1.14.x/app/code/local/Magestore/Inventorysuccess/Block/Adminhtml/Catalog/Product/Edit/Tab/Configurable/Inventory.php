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
class Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Configurable_Inventory
    extends Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Inventory
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * 
     */
    protected function _prepareForm()
    {
        if(!$this->_isShow()) {
            return;
        }
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('fields', array('legend'=>Mage::helper('catalog')->__('Warehouse Stocks')));
        $form->setDataObject(Mage::getModel('catalog/product'));
        $form->setName('warehouse_stock');
        
        $fieldset->addField('simple_product_warehouse_stock', 'text', array(
            'label' => '',
            'name'  => 'simple_product_warehouse_stock',
        ));
        
        $form->getElement('simple_product_warehouse_stock')->setRenderer(
            $this->getLayout()->createBlock('inventorysuccess/adminhtml_catalog_product_edit_tab_field_simpleProductWarehouseStock')
        );          

        $this->setForm($form);
    }
      
    
    /**
     * @return bool
     */
    protected function _isShow()
    {
        if($this->getProduct() && $this->getProduct()->isConfigurable()) {
            return true;
        }
        return false;
    }

    
    /**
     * 
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * 
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Configurable Product Inventory');
    }

    /**
     * 
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Configurable Product Inventory');
    }

    /**
     * 
     * @return boolean
     */
    public function isHidden()
    {
        return true;
    }

}