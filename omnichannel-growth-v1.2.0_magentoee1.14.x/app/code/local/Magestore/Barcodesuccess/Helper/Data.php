<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Barcodesuccess Helper
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Helper_Data extends
    Mage_Core_Helper_Abstract
{
    const ONE_BARCODE_PER_SKU_PATH = 'one_barcode_per_sku';
    const BARCODE_PATTERN_PATH     = 'barcode_pattern';

    /**
     * @param $id
     * @param null $store
     * @return mixed
     */
    public function getGeneralConfig(
        $id,
        $store = null
    ) {
        return Mage::getStoreConfig('barcodesuccess/general/' . $id, $store);
    }

    /**
     * @return bool
     */
    public function isOneBarcodePerSku()
    {
        return $this->getGeneralConfig(self::ONE_BARCODE_PER_SKU_PATH);
    }

    /**
     * @return string
     */
    public function getBarcodePattern()
    {
        return $this->getGeneralConfig(self::BARCODE_PATTERN_PATH);
    }

    /**
     * check if inventory success is installed.
     * @return bool
     */
    public function inventoryInstalled()
    {
        /** @var Mage_Core_Helper_Abstract $coreHelper */
        $coreHelper = Mage::helper('core');
        if ( $coreHelper->isModuleEnabled('Magestore_Inventorysuccess')
             && $coreHelper->isModuleOutputEnabled('Magestore_Inventorysuccess')
        ) {
            return true;
        }
        return false;
    }
}