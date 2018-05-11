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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adjuststock Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_SpecificMonth extends Magestore_Inventorysuccess_Model_LowStockNotification_Source_AbstractSource
{

    /**
     * Get options
     *
     * @return array
     */

    public function toOptionArray()
    {
        $months = array(
            '1' => Mage::helper('inventorysuccess')->__('January'),
            '2' => Mage::helper('inventorysuccess')->__('February'),
            '3' => Mage::helper('inventorysuccess')->__('March'),
            '4' => Mage::helper('inventorysuccess')->__('April'),
            '5' => Mage::helper('inventorysuccess')->__('May'),
            '6' => Mage::helper('inventorysuccess')->__('June'),
            '7' => Mage::helper('inventorysuccess')->__('July'),
            '8' => Mage::helper('inventorysuccess')->__('August'),
            '9' => Mage::helper('inventorysuccess')->__('September'),
            '10' => Mage::helper('inventorysuccess')->__('October'),
            '11' => Mage::helper('inventorysuccess')->__('November'),
            '12' => Mage::helper('inventorysuccess')->__('December')
        );

        return $months;
    }
}
