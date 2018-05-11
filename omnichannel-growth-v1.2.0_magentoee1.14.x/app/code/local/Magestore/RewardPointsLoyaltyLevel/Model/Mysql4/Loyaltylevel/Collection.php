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
 * Rewardpointsloyaltylevel Resource Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Model_Mysql4_Loyaltylevel_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpointsloyaltylevel/loyaltylevel');
    }
    /**
     * Retreive option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->addFieldToFilter('status', array('eq'=>Magestore_RewardPointsLoyaltyLevel_Model_System_Config_Source_Status::STATUS_ENABLED));
        return parent::_toOptionArray('level_id', 'level_name');
    }
}