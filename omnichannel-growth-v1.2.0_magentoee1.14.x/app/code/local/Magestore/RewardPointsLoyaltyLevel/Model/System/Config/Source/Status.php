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
class Magestore_RewardPointsLoyaltyLevel_Model_System_Config_Source_Status extends Varien_Object {

    const STATUS_ENABLED = Magestore_RewardPointsLoyaltyLevel_Model_Loyaltylevel::STATUS_ENABLED;
    const STATUS_DISABLED = Magestore_RewardPointsLoyaltyLevel_Model_Loyaltylevel::STATUS_DISABLED;

    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray() {
        return array(
            self::STATUS_ENABLED => Mage::helper('rewardpointsloyaltylevel')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('rewardpointsloyaltylevel')->__('Disabled')
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
