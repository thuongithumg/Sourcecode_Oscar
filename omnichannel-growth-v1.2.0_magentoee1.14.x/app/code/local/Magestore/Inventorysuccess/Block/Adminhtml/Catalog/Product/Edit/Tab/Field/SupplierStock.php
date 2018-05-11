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
class Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Field_SupplierStock
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    public function __construct()
    {
        $this->addColumn('supplier', array(
            'label' => Mage::helper('inventorysuccess')->__('Supplier'),
            'style' => 'width:250px',
            'renderer' => Mage::getBlockSingleton('inventorysuccess/adminhtml_catalog_product_edit_tab_renderer_supplier'),
        ));      
        
        if($this->_isShowData()) {
            $this->addColumn('product_supplier_sku', array(
                'label' => Mage::helper('inventorysuccess')->__('Supplier Product SKU'),
                'style' => 'width:120px',
            ));


            $this->addColumn('cost', array(
                'label' => Mage::helper('inventorysuccess')->__('COST'),
                'style' => 'width:120px',
            ));

            $this->addColumn('tax', array(
                'label' => Mage::helper('inventorysuccess')->__('TAX(%)'),
                'class' => 'validate-zero-or-greater',
                'style' => 'width:120px',
            ));
        }

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('inventorysuccess')->__('Register to Suppliers');

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
    protected function _isShowData()
    {
        if(!$this->getProduct()->isComposite()) {
            return true;
        }
        return false;
    }
    

}