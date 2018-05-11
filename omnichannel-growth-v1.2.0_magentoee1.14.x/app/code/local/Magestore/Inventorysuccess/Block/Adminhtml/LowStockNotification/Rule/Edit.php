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
 * Adjuststock Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_lowStockNotification_rule';
        $this->_blockGroup = 'inventorysuccess';

        if ($this->getRequest()->getParam('id')) {
            $this->_addButton('run_now', array(
                'class' => 'btn-next-step',
                'label' => Mage::helper('inventorysuccess')->__('Run Now'),
                'onclick' => 'runNow()',
            ));
            $this->_addButton('duplicate', array(
                'class' => 'add',
                'label' => Mage::helper('inventorysuccess')->__('Duplicate'),
                'onclick' => 'duplicate()',
            ));
        }
        $this->_addButton('save_and_apply', array(
            'class'   => 'save',
            'label'   => Mage::helper('inventorysuccess')->__('Save and Apply'),
            'onclick' => 'saveAndApply()',
        ));
        $this->_updateButton('save', 'label', Mage::helper('inventorysuccess')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('inventorysuccess')->__('Delete Rule'));

        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('inventorysuccess')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        $this->removeButton('reset');
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventorysuccess_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'inventorysuccess_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventorysuccess_content');
            }

            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }

            function saveAndApply() {
                editForm.submit($('edit_form').action+'back/edit/auto_apply/1');
            }

            function duplicate() {
                deleteConfirm(
                    '".Mage::helper('inventorysuccess')->__('Are you sure you want to duplicate this rule?')."',
                    '".$this->getUrl('adminhtml/inventorysuccess_lowstocknotification_rule/duplicate', array('id' => $this->getRequest()->getParam('id', null)))."'
                );
            }

            function runNow() {
                deleteConfirm(
                    '".Mage::helper('inventorysuccess')->__('Are you sure you want to run this rule now?')."',
                    '".$this->getUrl('adminhtml/inventorysuccess_lowstocknotification_rule/run', array('id' => $this->getRequest()->getParam('id', null)))."'
                );
            }
        ";
    }
    
    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('lowstocknotification_rule_data')
            && Mage::registry('lowstocknotification_rule_data')->getId()
        ) {
            return Mage::helper('inventorysuccess')->__("Edit Rule '%s'",
                                                $this->escapeHtml(Mage::registry('lowstocknotification_rule_data')->getRuleName())
            );
        }
        return Mage::helper('inventorysuccess')->__('Add New Rule');
    }
}