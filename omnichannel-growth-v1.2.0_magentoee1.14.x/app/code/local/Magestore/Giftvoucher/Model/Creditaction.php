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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Giftvoucher Creditaction Model
 * 
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */

class Magestore_Giftvoucher_Model_Creditaction extends Varien_Object
{

    const ACTIONS_REDEEM = 'Redeem';
    const ACTIONS_APIREDEEM = 'Api_re';
    const ACTIONS_APIUPDATE = 'Apiupdate';
    const ACTIONS_ADMINUPDATE = 'Adminupdate';
    const ACTIONS_SPEND = 'Spend';
    const ACTIONS_REFUND = 'Refund';

    /**
     * Get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::ACTIONS_REDEEM => Mage::helper('giftvoucher')->__('Customer Redemption'),
            self::ACTIONS_APIREDEEM => Mage::helper('giftvoucher')->__('API User Redemption'),
            self::ACTIONS_APIUPDATE => Mage::helper('giftvoucher')->__('API User Update'),
            self::ACTIONS_ADMINUPDATE => Mage::helper('giftvoucher')->__('Admin Update'),
            self::ACTIONS_SPEND => Mage::helper('giftvoucher')->__('Customer Spend'),
            self::ACTIONS_REFUND => Mage::helper('giftvoucher')->__('Admin Refund'),
        );
    }

    /**
     * Get all options
     *
     * @return array
     */
    static public function getOptions()
    {
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
