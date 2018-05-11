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
 * Rewardpointsloyaltylevel Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Block_Rewardpointsloyaltylevel extends Mage_Core_Block_Template {

    /**
     * prepare block's layout
     *
     * @return Magestore_RewardPointsLoyaltyLevel_Block_Rewardpointsloyaltylevel
     */
    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getCollection() {
        $collection = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->getCollection()
                ->addFieldToFilter('status', 1)
                ->setOrder('condition_value', 'asc');
        return $collection;
    }

    public function getEarningPoints($levelId) {
        $level = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($levelId);
        $customer_group_id = $level->getCustomerGroupId();
        $website_id = Mage::app()->getStore()->getWebsiteId();
        $rules = Mage::helper('rewardpointsloyaltylevel')->getAllEarnRuleResponsive($customer_group_id, $website_id);
        $message = array();
        foreach ($rules as $rule) {
            if ($rule->getType() == 'rate')
                $message[] = '<strong style="color: #3182be">' . $rule->getDescriptionFrontend() . '</strong>';
            else
                $message[] = '<strong style="color: #3182be">' . $rule->getTitle() . '</strong><span>' . $rule->getDescriptionFrontend() . '</span>';
        }
        if (count($message) > 0)
            return $message;
        return null;
    }

    public function getSpendingPoints($levelId) {
        $level = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($levelId);
        $customer_group_id = $level->getCustomerGroupId();
        $website_id = Mage::app()->getStore()->getWebsiteId();
        $rules = Mage::helper('rewardpointsloyaltylevel')->getAllSpendingRuleResponsive($customer_group_id, $website_id);
        $message = array();
        foreach ($rules as $rule) {
            if ($rule->getType() == 'rate')
                $message[] = '<strong style="color: #3182be">' . $rule->getDescriptionFrontend() . '</strong>';
            else
                $message[] = '<strong style="color: #3182be">' . $rule->getTitle() . '</strong><span>' . $rule->getDescriptionFrontend() . '</span>';
        }
        if (count($message) > 0)
            return $message;
        return null;
    }

    public function getAllPromoSpendingRuleResponsive($levelId) {
        $level = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($levelId);
        $customer_group_id = $level->getCustomerGroupId();
        $website_id = Mage::app()->getStore()->getWebsiteId();
        $rules = Mage::helper('rewardpointsloyaltylevel')->getAllPromoSpendingRuleResponsive($customer_group_id, $website_id);
        $message = array();
        foreach ($rules as $rule) {
            $mess = '<strong style="color: #3182be">' . $rule->getTitle() . '</strong>';
            if ($rule->getDescription())
                $mess .= ': <span>' . $rule->getDescription() . '</span>';
            $message[] = $mess;
        }
        return $message;
    }

    /**
     * @author Hiepdd-Magestore
     * Check if customer can join level 
     * @param type $levelId
     * @return type
     */
    public function enableJoin($levelId) {
        $rewardAccount = Mage::helper('rewardpointsloyaltylevel')->getCustomer();
        if (!$rewardAccount->getId())
            return 0;
        $loytalyLevel = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($levelId);
        if ($loytalyLevel->getConditionType() == Magestore_RewardPointsLoyaltyLevel_Model_Loyaltylevel::CONDITION_TYPE_SALES) {
            //condition type : sales
            return $rewardAccount->getTotalSales() >= $loytalyLevel->getConditionValue();
        } else {
            // condition type : point
            return $rewardAccount->getAccumulatedPoints() >= $loytalyLevel->getConditionValue();
        }
    }

    public function getPointMinimum($demerit, $minimum) {
        if ($minimum < $demerit) {
            return Mage::helper('rewardpoints/point')->format($demerit);
        } else {
            return Mage::helper('rewardpoints/point')->format($minimum);
        }
    }

    public function getLoyaltyLevel() {
        $levelId = Mage::helper('rewardpointsloyaltylevel')->getCustomerLevel();
        if ($levelId == NULL)
            return null;
        $level = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($levelId);
        if ($level->getId())
            return $level;
        return null;
    }

    public function formatDay($day) {
        if ($day == null || $day == 0) {
            return $this->__('Permanent');
        } else if ($day == 1) {
            return $day . ' ' . $this->__('day');
        } else {
            return $day . ' ' . $this->__('days');
        }
    }

    public function getTimeLeft() {
        $customerReward = Mage::helper('rewardpointsloyaltylevel')->getCustomer();
        if ($customerReward->getId()) {
            if ($customerReward->getLoyaltyExpire() != NULL) {
                $day = $this->time_duration($customerReward->getLoyaltyExpire(), 'dh');
                return $day;
            }
        }
        return null;
    }

    public function getLoyaltyNotification() {
        $customerReward = Mage::helper('rewardpointsloyaltylevel')->getCustomer();
        if ($customerReward != null)
            return $customerReward->getLoyaltyNotification();
        return null;
    }

    function time_duration($time_end, $use = null, $zeros = false) {
//        $seconds = strtotime(date('Y-m-d', strtotime("$time_end + 1 day"))) - strtotime(date('Y-m-d', Mage::getModel('core/date')->timestamp(time())));
        $seconds = strtotime($time_end) - Mage::getModel('core/date')->timestamp(time());
        if ($seconds <= 0)
            return NULL;
        // Define time periods
        $periods = array(
            'years' => 31556926,
            'months' => 2629743,
            'weeks' => 604800,
            'days' => 86400,
            'hours' => 3600,
            'minutes' => 60,
            'seconds' => 1
        );

        // Break into periods
        $seconds = (float) $seconds;
        $segments = array();
        foreach ($periods as $period => $value) {
            if ($use && strpos($use, $period[0]) === false) {
                continue;
            }
            $count = floor($seconds / $value);
            if ($count == 0 && !$zeros) {
                continue;
            }
            $segments[strtolower($period)] = $count;
            $seconds = $seconds % $value;
        }

        // Build the string
        $string = array();
        foreach ($segments as $key => $value) {
            if ($key == 'days') {
                if ($value != 1)
                    $segment = $this->__('%s days', $value);
                else
                    $segment = $this->__('%s day', $value);
            }else {
                if ($value != 1)
                    $segment = $this->__('%s hours', $value);
                else
                    $segment = $this->__('%s hour', $value);
            }
            $string[] = $segment;
        }

        return implode(', ', $string);
    }

}
