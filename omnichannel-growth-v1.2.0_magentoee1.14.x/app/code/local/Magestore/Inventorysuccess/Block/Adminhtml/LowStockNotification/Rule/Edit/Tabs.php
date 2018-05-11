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
 * LowStockNotification Rule Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('lowstocknotification_rule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inventorysuccess')->__('Rule Information'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        /** information form */
        $this->addTab('form_section', array(
            'label'     => Mage::helper('inventorysuccess')->__('Rule Information'),
            'title'     => Mage::helper('inventorysuccess')->__('Rule Information'),
            'content'   => $this->getLayout()
                                ->createBlock('inventorysuccess/adminhtml_lowStockNotification_rule_edit_tab_form')
                                ->toHtml(),
        ));

        /** conditions form */
        $this->addTab('conditions_section', array(
            'label'     => Mage::helper('inventorysuccess')->__('Conditions'),
            'title'     => Mage::helper('inventorysuccess')->__('Conditions'),
            'content'   => $this->getLayout()
                                ->createBlock('inventorysuccess/adminhtml_lowStockNotification_rule_edit_tab_conditions')
                                ->toHtml(),
        ));

        /** action form */
        $this->addTab('action_section', array(
            'label'     => Mage::helper('inventorysuccess')->__('Action'),
            'title'     => Mage::helper('inventorysuccess')->__('Action'),
            'content'   => $this->getLayout()
                                ->createBlock('inventorysuccess/adminhtml_lowStockNotification_rule_edit_tab_actions')
                                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}