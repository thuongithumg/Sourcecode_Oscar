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
class Magestore_RewardPointsLoyaltyLevel_Model_System_Config_Source_Conditiontype extends Varien_Object {

    const CONDITION_TYPE_POINT = Magestore_RewardPointsLoyaltyLevel_Model_Loyaltylevel::CONDITION_TYPE_POINT;
    const CONDITION_TYPE_SALES = Magestore_RewardPointsLoyaltyLevel_Model_Loyaltylevel::CONDITION_TYPE_SALES;

    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray() {
        return array(
            self::CONDITION_TYPE_POINT => Mage::helper('rewardpointsloyaltylevel')->__('Accumulated Points'),
            self::CONDITION_TYPE_SALES => Mage::helper('rewardpointsloyaltylevel')->__('Total Sales')
        );
    }

    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash() {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }

}
