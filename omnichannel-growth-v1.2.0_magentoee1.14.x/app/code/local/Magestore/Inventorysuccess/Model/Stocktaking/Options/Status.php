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
 * Class Magestore_Inventorysuccess_Model_Stocktaking_Options_Status
 */
class Magestore_Inventorysuccess_Model_Stocktaking_Options_Status
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $option = array();

        $option[] = array(
            'label' => Mage::helper('inventorysuccess')->__('Pending'),
            'value' => Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING
        );
        $option[] = array(
            'label' => Mage::helper('inventorysuccess')->__('Completed'),
            'value' => Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED
        );
        $option[] = array(
            'label' => Mage::helper('inventorysuccess')->__('Processing'),
            'value' => Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING
        );
        $option[] = array(
            'label' => Mage::helper('inventorysuccess')->__('Verified'),
            'value' => Magestore_Inventorysuccess_Model_Stocktaking::STATUS_VERIFIED
        );
        $option[] = array(
            'label' => Mage::helper('inventorysuccess')->__('Canceled'),
            'value' => Magestore_Inventorysuccess_Model_Stocktaking::STATUS_CANCELED
        );

        return $option;
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        $option = array(
            Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING => Mage::helper('inventorysuccess')->__('Pending'),
            Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED => Mage::helper('inventorysuccess')->__('Complete'),
            Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING => Mage::helper('inventorysuccess')->__('Processing'),
            Magestore_Inventorysuccess_Model_Stocktaking::STATUS_VERIFIED => Mage::helper('inventorysuccess')->__('Verified'),
            Magestore_Inventorysuccess_Model_Stocktaking::STATUS_CANCELED => Mage::helper('inventorysuccess')->__('Canceled')
        );

        return $option;
    }
}
