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
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Suppliersuccess Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getSupplierData()) {
            $data = Mage::getSingleton('adminhtml/session')->getSupplierData();
            Mage::getSingleton('adminhtml/session')->setSupplierData(null);
        } elseif (Mage::registry('supplier_data')) {
            $data = Mage::registry('supplier_data')->getData();
        }
        $fieldset = $form->addFieldset('suppliersuccess_form', array(
            'legend'=>Mage::helper('suppliersuccess')->__('Supplier information')
        ));

        $fieldset->addField('supplier_name', 'text', array(
            'label'        => Mage::helper('suppliersuccess')->__('Supplier Name'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'supplier_name',
        ));
        
        $fieldset->addField('supplier_code', 'text', array(
            'label'        => Mage::helper('suppliersuccess')->__('Supplier Code'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'supplier_code',
            'note' =>   Mage::helper('suppliersuccess')->__('It can be Commercial And Government Entity (CAGE) code of supplier, or supplier number, or supplier code, etc.'),
        ));  
        
        $fieldset->addField('contact_name', 'text', array(
            'label'        => Mage::helper('suppliersuccess')->__('Contact Person'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'contact_name',
        ));        
        
        $fieldset->addField('contact_email', 'text', array(
            'label'        => Mage::helper('suppliersuccess')->__('Email'),
            'class'        => 'required-entry validate-email',
            'required'    => true,
            'name'        => 'contact_email',
        ));  
        
        $fieldset->addField('status', 'select', array(
            'label'        => Mage::helper('suppliersuccess')->__('Status'),
            'name'        => 'status',
            'values'    => Mage::getSingleton('suppliersuccess/status')->getOptionHash(),
        ));
        
        $fieldset->addField('description', 'textarea', array(
            'label'        => Mage::helper('suppliersuccess')->__('Description'),
            'name'        => 'description',
        ));        

        $form->setValues($data);
        return parent::_prepareForm();
    }
}