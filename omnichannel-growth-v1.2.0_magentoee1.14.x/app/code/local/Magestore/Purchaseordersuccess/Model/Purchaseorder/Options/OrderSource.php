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
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_OrderSource
    extends Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_AbstractOption
{
    /**
     * Purchase order status value
     */
    const SOURCE_NONE = '';
    
    const SOURCE_EMAIL = 1;
    
    const SOURCE_PHONE = 2;
    
    const SOURCE_FAX = 3;
    
    const SOURCE_VENDOR = 4;
    
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionHash()
    {
        return array(
            self::SOURCE_NONE => Mage::helper('purchaseordersuccess')->__('N/A'),
            self::SOURCE_EMAIL => Mage::helper('purchaseordersuccess')->__('Email'),
            self::SOURCE_PHONE => Mage::helper('purchaseordersuccess')->__('Phone'),
            self::SOURCE_FAX => Mage::helper('purchaseordersuccess')->__('Fax'),
            self::SOURCE_VENDOR => Mage::helper('purchaseordersuccess')->__('Vendor website')
        );
    }
}