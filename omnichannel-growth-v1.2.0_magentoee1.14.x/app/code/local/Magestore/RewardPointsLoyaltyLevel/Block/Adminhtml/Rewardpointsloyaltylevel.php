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
 * Rewardpointsloyaltylevel Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_rewardpointsloyaltylevel';
        $this->_blockGroup = 'rewardpointsloyaltylevel';
        $this->_headerText = Mage::helper('rewardpointsloyaltylevel')->__('Loyalty Level Manager');
        $this->_addButtonLabel = Mage::helper('rewardpointsloyaltylevel')->__('Add New Customer Group');
        parent::__construct();
    }
}