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
class Magestore_Suppliersuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Field_Supplier
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    public function __construct()
    {
        $this->addColumn('supplier', array(
            'label' => Mage::helper('suppliersuccess')->__('Supplier'),
            'style' => 'width:180px',
            'renderer' => Mage::getBlockSingleton('suppliersuccess/adminhtml_catalog_product_edit_tab_renderer_supplier'),
        ));      

        $this->addColumn('product_supplier_sku', array(
            'label' => Mage::helper('suppliersuccess')->__('Supplier product sku'),
//            'class' => 'required-entry',
            'style' => 'width:150px',
        ));
        
        $this->addColumn('cost', array(
            'label' => Mage::helper('suppliersuccess')->__('Purchase price'),
            'class' => 'validate-zero-or-greater',
            'style' => 'width:150px',
        ));

        $this->addColumn('tax', array(
            'label' => Mage::helper('suppliersuccess')->__('Tax (%)'),
            'class' => 'validate-zero-or-greater',
            'style' => 'width:150px',
        ));      
        
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('suppliersuccess')->__('Add New Supplier');

        parent::__construct();
        
        $this->setTemplate('coresuccess/system/field/array.phtml');
    }
    
    /**
     * 
     * @return string
     */
    public function disableRemoveButton()
    {
        return '0';
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

}