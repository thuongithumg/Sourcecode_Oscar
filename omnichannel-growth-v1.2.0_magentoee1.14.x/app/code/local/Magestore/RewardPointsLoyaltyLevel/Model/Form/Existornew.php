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
class Magestore_RewardPointsLoyaltyLevel_Model_Form_Existornew extends Varien_Object
{
    const FORM_EXIST    = 1;
    const FORM_NEW    = 2;
    
    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::FORM_NEW   => Mage::helper('rewardpointsloyaltylevel')->__('New Group'),
            self::FORM_EXIST    => Mage::helper('rewardpointsloyaltylevel')->__('Existed Group')
        );
    }
    
    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash()
    {
        $optionArray = self::getOptionArray();
        $groupId = Mage::getModel('rewardpointsloyaltylevel/form_exist')->getOptionHash();
        if(count($groupId) == 0) array_pop ($optionArray);
        $options = array();
        foreach ($optionArray as $value => $label) {
            $options[] = array(
                'value'    => $value,
                'label'    => $label
            );
        }
        return $options;
    }
}