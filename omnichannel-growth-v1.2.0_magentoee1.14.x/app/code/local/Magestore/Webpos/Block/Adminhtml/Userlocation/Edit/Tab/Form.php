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
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 07/07/2015
 * Time: 9:57 SA
 */

class Magestore_Webpos_Block_Adminhtml_Userlocation_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('userlocation_form', array(
            'legend'=>Mage::helper('webpos')->__('Location information')
        ));

        $fieldset->addField('display_name', 'text', array(
            'label'        => Mage::helper('webpos')->__('Display Name'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'display_name',
        ));

        $fieldset->addField('address', 'textarea', array(
            'label'        => Mage::helper('webpos')->__('Address'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'address',
        ));

        $fieldset->addField('description', 'textarea', array(
            'label'        => Mage::helper('webpos')->__('Description'),
            'name'        => 'description',
        ));

        $fieldset->addField('location_store_id', 'select', array(
            'label'       => Mage::helper('webpos')->__('Store View'),
            'name'        => 'location_store_id',
            'class'        => 'required-entry',
            'required'    => true,
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false)
        ));

        if (Mage::getSingleton('adminhtml/session')->getUserlocationData()) {
            $model = Mage::getSingleton('adminhtml/session');
            $data = Mage::getSingleton('adminhtml/session')->getUserlocationData();
            Mage::getSingleton('adminhtml/session')->getUserlocationData(null);
        } elseif (Mage::registry('userlocation_data')) {
            $model = Mage::registry('userlocation_data');
            $data = $model->getData();
        }

        Mage::dispatchEvent('webpos_location_edit_form',
                                array(
                                    'form' => $form,
                                    'field_set' => $fieldset,
                                    'model_data'=> $model,
                                ));

        if (Mage::getSingleton('adminhtml/session')->getUserlocationData()) {
            $data = $model->getUserlocationData();
            Mage::getSingleton('adminhtml/session')->getUserlocationData(null);
        } elseif (Mage::registry('userlocation_data')) {
            $data = $model->getData();
        }
        $form->setValues($data);
        return parent::_prepareForm();
    }
}