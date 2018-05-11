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
 * Purchaseorder Service
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_ShippingMethod as ShippingMethodOption;

class Magestore_Purchaseordersuccess_Model_Service_Config_TaxAndShipping
    extends Magestore_Purchaseordersuccess_Model_Service_Config_AbstractConfig
{
    const DEFAULT_SHIPPING_TYPE_CONFIG_PATH = 'purchaseordersuccess/tax_and_shipping/shipping_price';

//    const DEFAULT_SHIPPING_COST_CONFIG_PATH = 'purchaseordersuccess/tax_and_shipping/default_shipping_cost';

    const DEFAULT_TAX_TYPE_CONFIG_PATH = 'purchaseordersuccess/tax_and_shipping/customer_tax';

//    const DEFAULT_TAX_CONFIG_PATH = 'purchaseordersuccess/tax_and_shipping/default_tax';

    /**
     * @return int
     */
    public static function getShippingType(){
        return Mage::getStoreConfig(self::DEFAULT_SHIPPING_TYPE_CONFIG_PATH);
    }

    /**
     * @return float
     */
//    public static function getDefaultShippingCost(){
//        return Mage::getStoreConfig(self::DEFAULT_SHIPPING_COST_CONFIG_PATH);
//    }

    /**
     * @return int
     */
    public static function getTaxType(){
        return Mage::getStoreConfig(self::DEFAULT_TAX_TYPE_CONFIG_PATH);
    }

    /**
     * Get Default Tax
     *
     * @return float
     */
//    public static function getDefaultTax(){
//        return Mage::getStoreConfig(self::DEFAULT_TAX_CONFIG_PATH);
//    }
}