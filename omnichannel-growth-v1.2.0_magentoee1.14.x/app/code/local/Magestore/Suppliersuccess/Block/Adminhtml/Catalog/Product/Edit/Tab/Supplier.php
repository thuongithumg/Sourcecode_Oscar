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
class Magestore_Suppliersuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Supplier
    extends Mage_Adminhtml_Block_Widget_Form
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
        $fieldset = $form->addFieldset('fields', array('legend'=>Mage::helper('catalog')->__('Suppliers')));
        $form->setDataObject(Mage::getModel('catalog/product'));
        $form->setName('product_supplier');       
        
        $fieldset->addField('product_supplier', 'text', array(
            'label' => '',
            'name'  => 'product_supplier',
        ));
        
        $form->getElement('product_supplier')->setRenderer(
            $this->getLayout()->createBlock('suppliersuccess/adminhtml_catalog_product_edit_tab_field_supplier')
        );  
                
        /* fill data to product_supplier form  */
        $this->_fillFormData($form);
        
        $this->setForm($form);
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
     * @return bool
     */
    protected function _isShow()
    {
        if($this->getProduct() && $this->getProduct()->isComposite()) {
            return false;
        }
        return true;
    }
    
    /**
     * fill data to form 
     * 
     * @param Varien_Data_Form $form
     */
    protected function _fillFormData($form)
    {
        $product = $this->getProduct();
        if($product->getId()) {        
            $productSuppliers = array();
            $supplierProducts = Magestore_Coresuccess_Model_Service::supplierProductService()
                                            ->getProductSuppliers($product->getId());
            if($supplierProducts->getSize()) {
                foreach($supplierProducts as $supplierProduct) {
                    $supplierId = $supplierProduct->getSupplierId();
                    $productSuppliers[] = array(
                        'supplier' => $supplierId,
                        'supplier_selected_'.$supplierId => 'selected',
                        'supplier_disabled' => 'disabled',                        
                        'product_supplier_sku' => $supplierProduct->getProductSupplierSku(),
                        'cost' => $supplierProduct->getCost(),
                        'tax' => $supplierProduct->getTax(),
                    );
                }
            }
            $form->getElement('product_supplier')->setValue($productSuppliers);
        }        
    }
    
    /**
     * 
     * @return string
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        return $html;
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
        return $this->__('Suppliers');
    }

    /**
     * 
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Suppliers');
    }

    /**
     * 
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

}