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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status
    extends Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_AbstractOption
{
    /**
     * Purchase order status value
     */
    const STATUS_NEW = 0;

    const STATUS_PENDING = 1;

    const STATUS_COMFIRMED = 2;

    const STATUS_PROCESSING = 3;

    const STATUS_COMPLETED = 4;

    const STATUS_CANCELED = 5;

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionHash()
    {
        return array(
            self::STATUS_PENDING => Mage::helper('purchaseordersuccess')->__('Pending'),
            self::STATUS_COMFIRMED => Mage::helper('purchaseordersuccess')->__('Confirmed'),
            self::STATUS_PROCESSING => Mage::helper('purchaseordersuccess')->__('Processing'),
            self::STATUS_COMPLETED => Mage::helper('purchaseordersuccess')->__('Completed'),
            self::STATUS_CANCELED => Mage::helper('purchaseordersuccess')->__('Canceled')
        );
    }
}