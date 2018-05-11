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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Marketingautomation Edit Form Content Tab Block
 *
 * @category Magestore
 * @package Magestore_Webpos
 * @author Magestore Developer
 */
class Magestore_Webpos_Block_Adminhtml_Pos_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_Marketingautomation_Block_Adminhtml_Contact_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form ();
        $this->setForm($form);
        $data = array();
        if (Mage::registry('pos_data')) {
            $data = Mage::registry('pos_data')->getData();
        }
        if(!isset($data['pos_id'])){
            $data['pin'] = '0000';
        }
        $fieldset = $form->addFieldset('Pos_form', array(
            'legend' => Mage::helper('webpos')->__('Pos Information')
        ));
        $fieldset->addField('pos_name', 'text', array(
            'label' => Mage::helper('webpos')->__('Pos Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'pos_name'
        ));
//        if (!Mage::app()->isSingleStoreMode()) {
//            $field = $fieldset->addField('store_id', 'multiselect', array(
//                'name'      => 'stores[]',
//                'label'     => Mage::helper('checkout')->__('Store View'),
//                'title'     => Mage::helper('checkout')->__('Store View'),
//                'required'  => true,
//                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
//            ));
//            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
//            $field->setRenderer($renderer);
//        }
//        else {
//            $fieldset->addField('store_id', 'hidden', array(
//                'name'      => 'stores[]',
//                'value'     => Mage::app()->getStore(true)->getId()
//            ));
//        }
        $fieldset->addField('location_id', 'select', array(
            'label' => Mage::helper('webpos')->__('Location'),
            'required' => true,
            'name' => 'location_id',
            'values' => Mage::getSingleton('webpos/userlocation')->toOptionArray(),
        ));
        $posId = $this->getRequest()->getParam('id');
        $fieldset->addField('user_id', 'select', array(
            'label' => Mage::helper('webpos')->__('Current User'),
            'name' => 'user_id',
            'values' => Mage::getSingleton('webpos/pos')->getAvailableStaff($posId),
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('webpos')->__('Status'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'status',
            'values' => Mage::getSingleton('webpos/status')->getOptionArray()
        ));
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
