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
 * Class Magestore_Inventorysuccess_Model_Adjuststock_Options_Status
 */
class Magestore_Inventorysuccess_Model_Adjuststock_Options_Status
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $option = array();

        $option[] = array(
            'label' => Mage::helper('inventorysuccess')->__('Pending'),
            'value' => Magestore_Inventorysuccess_Model_Adjuststock::STATUS_PENDING
        );
        $option[] = array(
            'label' => Mage::helper('inventorysuccess')->__('Completed'),
            'value' => Magestore_Inventorysuccess_Model_Adjuststock::STATUS_COMPLETED
        );

        return $option;
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        $option = array(
            Magestore_Inventorysuccess_Model_Adjuststock::STATUS_PENDING => Mage::helper('inventorysuccess')->__('Pending'),
            Magestore_Inventorysuccess_Model_Adjuststock::STATUS_COMPLETED => Mage::helper('inventorysuccess')->__('Complete'),
        );

        return $option;
    }
}
