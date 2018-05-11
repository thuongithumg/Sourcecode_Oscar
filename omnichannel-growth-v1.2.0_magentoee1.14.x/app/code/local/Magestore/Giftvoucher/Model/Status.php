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
 * Giftvoucher Status Model
 * 
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */
class Magestore_Giftvoucher_Model_Status extends Varien_Object
{

    const STATUS_PENDING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DISABLED = 3;
    const STATUS_USED = 4;
    const STATUS_EXPIRED = 5;
    const STATUS_DELETED = 6;
    const STATUS_NOT_SEND = 0;
    const STATUS_SENT_EMAIL = 1;
    const STATUS_SENT_OFFICE = 2;

    /**
     * Get the gift code's status options as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::STATUS_PENDING => Mage::helper('giftvoucher')->__('Pending'),
            self::STATUS_ACTIVE => Mage::helper('giftvoucher')->__('Active'),
            self::STATUS_DISABLED => Mage::helper('giftvoucher')->__('Disabled'),
            self::STATUS_USED => Mage::helper('giftvoucher')->__('Used'),
            self::STATUS_EXPIRED => Mage::helper('giftvoucher')->__('Expired'),
        );
    }

    /**
     * Get the email's status options as array 
     *
     * @return array
     */
    static public function getOptionEmail()
    {
        return array(
            self::STATUS_NOT_SEND => Mage::helper('giftvoucher')->__('Not Send'),
            self::STATUS_SENT_EMAIL => Mage::helper('giftvoucher')->__('Sent via Email'),
            self::STATUS_SENT_OFFICE => Mage::helper('giftvoucher')->__('Send via Post Office'),
        );
    }

    /**
     * Get the gift code's status options  
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

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return self::getOptions();
    }

}
