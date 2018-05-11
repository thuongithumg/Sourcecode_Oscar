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
 * Rewardpointsloyaltylevel Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel_Edit_Tab_Benefit extends Mage_Core_Block_Template {

    public function _construct() {
        parent::_construct();
        $this->setTemplate('loyaltylevel/benefit.phtml');
    }

    public function getAllEarningRuleHasLoyalty() {
        return Mage::helper('rewardpointsloyaltylevel')->getAllEarnRuleResponsive($this->getRequest()->getParam('id',-1));
    }
    public function getAllSpendingRuleHasLoyalty() {
       
        return Mage::helper('rewardpointsloyaltylevel')->getAllSpendingRuleResponsive($this->getRequest()->getParam('id',-1));
    }
     public function getAllPromoSpendingRuleResponsive() {
       
        return Mage::helper('rewardpointsloyaltylevel')->getAllPromoSpendingRuleResponsive($this->getRequest()->getParam('id',-1));
    }

}
