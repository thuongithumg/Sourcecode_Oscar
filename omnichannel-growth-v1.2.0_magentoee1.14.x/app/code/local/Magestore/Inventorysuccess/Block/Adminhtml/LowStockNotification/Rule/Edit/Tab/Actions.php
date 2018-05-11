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
 * LowStockNotification Rule Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Edit_Tab_Actions extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getLowstocknotificationRuleData()) {
            $data = Mage::getSingleton('adminhtml/session')->getLowstocknotificationRuleData();
            Mage::getSingleton('adminhtml/session')->setLowstocknotificationRuleData(null);
        } elseif (Mage::registry('lowstocknotification_rule_data')) {
            $data = Mage::registry('lowstocknotification_rule_data')->getData();
        }
        $fieldset = $form->addFieldset('lowstocknotification_rule_action', array(
            'legend' => Mage::helper('inventorysuccess')->__('Action')
        ));

        $fieldset->addField('notifier_emails',
            'editor',
            array(
                'name'        => 'notifier_emails',
                'label'        => Mage::helper('inventorysuccess')->__('Notification recipient list'),
                'title'        => Mage::helper('inventorysuccess')->__('Notification recipient list'),
                'style'        => 'width:500px; height:100px;',
                'wysiwyg'    => false,
                'note' => Mage::helper('inventorysuccess')->__('Emails are separated with commas. For example, johndoe@domain.com, johnsmith@domain.com.')
        ));

        $fieldset->addField('warning_message',
            'editor',
            array(
                'name'        => 'warning_message',
                'label'        => Mage::helper('inventorysuccess')->__('Warning Message'),
                'title'        => Mage::helper('inventorysuccess')->__('Warning Message'),
                'style'        => 'width:500px; height:100px;',
                'wysiwyg'    => false
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}