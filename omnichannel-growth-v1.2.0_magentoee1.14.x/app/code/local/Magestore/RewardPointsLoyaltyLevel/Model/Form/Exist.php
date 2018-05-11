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
 * RewardPointsLoyaltyLevel Status Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Model_Form_Exist extends Varien_Object
{
    
    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        $levelGroup = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->getCollection();
        $levelGroupId = array('0', '1');
        foreach($levelGroup as $level){
            $levelGroupId[] = $level->getCustomerGroupId();
        }
        $groups = Mage::getModel('customer/group')->getCollection()
                ->addFieldToFilter('customer_group_id', array('nin' => $levelGroupId));
        $groupsArray = array();
        foreach ($groups as $group){
            $groupsArray[$group->getId()] = $group->getCustomerGroupCode();
        }
        return $groupsArray;
    }
    
    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value'    => $value,
                'label'    => $label
            );
        }
        return $options;
    }
}