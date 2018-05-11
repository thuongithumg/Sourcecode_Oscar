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
 * Rewardpointsloyaltylevel Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Model_Loyaltylevel extends Mage_Core_Model_Abstract {

    const CONDITION_TYPE_POINT = 0;
    const CONDITION_TYPE_SALES = 1;
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    public function _construct() {
        parent::_construct();
        $this->_init('rewardpointsloyaltylevel/loyaltylevel');
    }

    public function loadByGroup($group) {
        return $this->load($group, 'customer_group_id');
    }

    public function getRequirement() {
        $helper = Mage::helper('rewardpointsloyaltylevel');
        $store = Mage::app()->getStore();
        $pointHelper = Mage::helper('rewardpoints/point');
        $coreHelper = Mage::helper('core');
        $conditionType = $this->getConditionType();

        if (!$this->getConditionValue()) {
            return $helper->__("No Requirement");
        }
        if ($conditionType == self::CONDITION_TYPE_POINT) {
            return $pointHelper->format($this->getConditionValue());
        }
        if ($conditionType == self::CONDITION_TYPE_SALES) {
            return $helper->__('Total Sales ') . $store->formatPrice($this->getConditionValue());
        }
    }

    public function loadByRewardCustomer($rewardCustomer) {
        $customer = Mage::getModel('customer/customer')->load($rewardCustomer->getCustomerId());
        if (!$customer->getId())
            return null;
        $customerGroup = $customer->getGroupId();
        return $this->loadByGroup($customerGroup);
    }

    /**
     * @author Hiepdd - Magestore
     * @param type $rewardCustomer
     * @return \Magestore_RewardPointsLoyaltyLevel_Model_Loyaltylevel
     */
    public function setLevelForRewardCustomer($rewardCustomer) {
        $customerGroup = $this->getCustomerGroupId();
        $customer = $rewardCustomer->getCustomer();
        //deduct points
        if ($this->getDemeritPoints() > 0) {
            Mage::helper('rewardpoints/action')->addTransaction('loyalty', $customer, $this, array());
        }
        // set expired time and change group
        $period = $this->getRetentionPeriod();
        if ($period && is_numeric($period))
            $string_time = date('Y-m-d H:i:s', time() + $period * 24 * 3600);
        else
            $string_time = null;

        $resource = Mage::getSingleton('core/resource');
        $write = $resource->getConnection('core/write');
        $customerId = $customer->getId();
        if ($string_time) {
            $write->query("UPDATE " . $resource->getTableName('rewardpoints/customer') . " SET `loyalty_expire` = '$string_time' WHERE `customer_id` = '$customerId'");
        }
        $write->query("UPDATE " . $resource->getTableName('customer/entity') . " SET `group_id` = '$customerGroup' WHERE `entity_id` = '$customerId'");

        return $this;
    }

}
