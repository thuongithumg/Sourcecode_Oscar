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
 * @package     RewardpointsTransfer
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardpointsTransfer Renderer for Customer
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTranfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel_Renderer_Level extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render customer info to grid column html
     * 
     * @param Varien_Object $row
     */
    public function render(Varien_Object $row) {
        $actionName = $this->getRequest()->getActionName();
        $level_id = $row->getLevelId();
        if (strpos($actionName, 'export') === 0) {
            return Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($level_id)->getRetentionPeriod();
        }
        $retention = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($level_id)->getRetentionPeriod();
        if($retention == 0 || $retention == null) return Mage::helper('rewardpointsloyaltylevel')->__('Permanent');
        else return Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($level_id)->getRetentionPeriod() . Mage::helper('rewardpointsloyaltylevel')->__(' days');
    }

}
