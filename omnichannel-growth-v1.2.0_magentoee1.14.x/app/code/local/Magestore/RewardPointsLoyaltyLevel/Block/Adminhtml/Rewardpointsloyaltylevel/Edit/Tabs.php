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
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointsloyaltylevel Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('rewardpointsloyaltylevel_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('rewardpointsloyaltylevel')->__('Loyalty Level Information'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel_Edit_Tabs
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('General Information'),
            'title' => Mage::helper('rewardpointsloyaltylevel')->__('General Information'),
            'content' => $this->getLayout()
                    ->createBlock('rewardpointsloyaltylevel/adminhtml_rewardpointsloyaltylevel_edit_tab_form')
                    ->toHtml(),
//            'url'       => $this->getUrl('*/*/*', array('tab' => 'form_section')),
        ));
        $this->addTab('benefit_section', array(
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Benefits'),
            'title' => Mage::helper('rewardpointsloyaltylevel')->__('Benefits'),
            'content' => $this->getLayout()
                    ->createBlock('rewardpointsloyaltylevel/adminhtml_rewardpointsloyaltylevel_edit_tab_benefit')
                    ->toHtml(),
        ));
        if ($this->_getExistCustomer()) {
            $this->addTab('customer_section', array(
                'label' => Mage::helper('rewardpointsloyaltylevel')->__('Members'),
                'title' => Mage::helper('rewardpointsloyaltylevel')->__('Members'),
                'content' => $this->getLayout()->createBlock('rewardpointsloyaltylevel/adminhtml_rewardpointsloyaltylevel_edit_tab_customer')->toHtml(),
            ));
        }
        $this->_updateActiveTab();
        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab() {
        $tabId = $this->getRequest()->getParam('tab');
        if ($tabId) {
            if ($tabId == 'form') {
                $this->setActiveTab('form_section');
            } else
                $this->setActiveTab('benefit_section');
        }
    }

    protected function _getExistCustomer() {
        $customerReward = Mage::getModel('customer/customer')->getCollection()
                ->addAttributeToFilter('group_id',$this->getRequest()->getParam('id'),'group_id');
        if (count($customerReward)) {
            return true;
        }
        return false;
    }

}
