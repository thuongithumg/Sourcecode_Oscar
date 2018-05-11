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
 * Giftvoucher Displayincart Model
 * 
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */
class Magestore_Giftvoucher_Model_Displayincart
{

    /**
     * Get model option as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $positions = array(
            'amount' => Mage::helper('giftvoucher')->__('Gift Card value'),
            'giftcard_template_id' => Mage::helper('giftvoucher')->__('Gift Card template'),
            'customer_name' => Mage::helper('giftvoucher')->__('Sender name'),
            'recipient_name' => Mage::helper('giftvoucher')->__('Recipient name'),
            'recipient_email' => Mage::helper('giftvoucher')->__('Recipient email address'),
            'recipient_ship' => Mage::helper('giftvoucher')->__('Ship to recipient'),
            'recipient_address' => Mage::helper('giftvoucher')->__('Recipient address'),
            'message' => Mage::helper('giftvoucher')->__('Custom message'),
            'day_to_send' => Mage::helper('giftvoucher')->__('Day to send'),
            'timezone_to_send' => Mage::helper('giftvoucher')->__('Time zone'),
        );
        $options = array();

        foreach ($positions as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }
        return $options;
    }

}
