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

class Magestore_Webpos_Block_Adminhtml_Cashdenomination_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getDenominationData()) {
            $data = Mage::getSingleton('adminhtml/session')->getDenominationData();
            Mage::getSingleton('adminhtml/session')->getDenominationData(null);
        } elseif (Mage::registry('denomination_data')) {
            $data = Mage::registry('denomination_data')->getData();
        }
        $fieldset = $form->addFieldset('denomination_form', array(
            'legend'=>Mage::helper('webpos')->__('Denomination Information')
        ));

        $fieldset->addField('denomination_name', 'text', array(
            'label'        => Mage::helper('webpos')->__('Denomination Name'),
            'name'        => 'denomination_name',
            'class' => 'required-entry',
            'required' => true,
        ));
        $fieldset->addField('denomination_value', 'text', array(
            'label'     => __('Denomination Value'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'denomination_value',
            'disabled' => false,
        ));
        $fieldset->addField('sort_order', 'text', array(
            'label'     => __('Sort Order'),
            'required'  => true,
            'name'      => 'sort_order',
            'disabled' => false,
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}