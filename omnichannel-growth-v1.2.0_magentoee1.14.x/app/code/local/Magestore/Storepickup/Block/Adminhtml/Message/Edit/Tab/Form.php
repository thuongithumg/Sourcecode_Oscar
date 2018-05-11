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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Block_Adminhtml_Message_Edit_Tab_Form
 */
class Magestore_Storepickup_Block_Adminhtml_Message_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return mixed
     */
    protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('store_form', array('legend'=>Mage::helper('storepickup')->__('Customer Message')));     	       
      $fieldset->addField('name', 'label', array(
			'label'		=> Mage::helper('storepickup')->__('Name'),
			'class'		=> 'required-entry',			
			'name'		=> 'name',
		));
      $fieldset->addField('email', 'label', array(
			'label'		=> Mage::helper('storepickup')->__('Email'),
			'required'	=> false,
			'name'		=> 'email',                       
		));
      $fieldset->addField('message', 'label', array(
			'label'		=> Mage::helper('storepickup')->__('Message'),
			'name'		=> 'message',			
		));
      $fieldset->addField('date_sent', 'label', array(
			'label'		=> Mage::helper('storepickup')->__('Contact At'),
			'name'		=> 'date_sent',			
		));
      if ( Mage::getSingleton('adminhtml/session')->getMessageData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMessageData());
          Mage::getSingleton('adminhtml/session')->getMessageData(null);
      } elseif ( Mage::registry('store_data') ) {
          $form->setValues(Mage::registry('store_data')->getData());
      }
      return parent::_prepareForm();
  }
}