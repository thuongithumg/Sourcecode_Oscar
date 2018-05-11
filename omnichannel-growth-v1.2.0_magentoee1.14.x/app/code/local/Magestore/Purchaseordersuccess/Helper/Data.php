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
 * Purchaseordersuccess Helper
 * 
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Helper_Data extends Mage_Core_Helper_Abstract
{
    const DEFAULT_PRODUCT_SOURCE = 'purchaseordersuccess/product_source/product_source';

    public function isProductFromSupplier()  {
        return (bool)(Mage::getStoreConfig(self::DEFAULT_PRODUCT_SOURCE) == Magestore_Purchaseordersuccess_Model_System_Config_Source_Product::TYPE_SUPPLIER);
    }
}