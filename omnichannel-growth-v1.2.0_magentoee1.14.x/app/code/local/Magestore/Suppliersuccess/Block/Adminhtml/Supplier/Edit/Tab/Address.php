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
class Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Edit_Tab_Address extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Edit_Tab_Address
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
            'legend'=>Mage::helper('suppliersuccess')->__('Mailing Address')
        ));

        $fieldset->addField('telephone', 'text', array(
            'name' => 'telephone',
            'label' => $this->__('Telephone'),
            'title' => $this->__('Telephone'),
                
        ));
        
        $fieldset->addField('fax', 'text', array(
            'name' => 'fax',
            'label' => $this->__('Fax'),
            'title' => $this->__('Fax'),
                
        ));        
        
        $fieldset->addField('street', 'text', array(
            'name' => 'street',
            'label' => $this->__('Street Address'),
            'title' => $this->__('Street Address'),

        ));
        
        $fieldset->addField('city', 'text', array(
            'name' => 'city',
            'label' => $this->__('City'),
            'title' => $this->__('City'),
        ));
        
        $fieldset->addField('country_id', 'select', array(
            'name' => 'country_id',
            'label' => $this->__('Country'),
            'values' => Magestore_Coresuccess_Model_Service::supplierCountryService()->getCountryListHash(),
        ));
        
        $fieldset->addField('regionEl', 'note', array(
            'name' => 'regionEl',
            'label' => $this->__('State/Province'),
            'text' => $this->getLayout()
                    ->createBlock('suppliersuccess/adminhtml_supplier_edit_tab_renderer_region')
                    ->setTemplate('suppliersuccess/supplier/region.phtml')
                    ->toHtml(),
        ));
        
        $fieldset->addField('postcode', 'text', array(
            'name' => 'postcode',
            'label' => $this->__('Zip/Postal Code'),
            'title' => $this->__('Zip/Postal Code'),
        ));


        $form->setValues($data);
        return parent::_prepareForm();
    }
}