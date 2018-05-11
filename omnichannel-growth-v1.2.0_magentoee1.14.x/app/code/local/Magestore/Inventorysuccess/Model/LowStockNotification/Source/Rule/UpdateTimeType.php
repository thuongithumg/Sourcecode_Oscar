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
class Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_UpdateTimeType extends Magestore_Inventorysuccess_Model_LowStockNotification_Source_AbstractSource
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel */
        $ruleModel = Mage::getSingleton('inventorysuccess/lowStockNotification_rule');
        $availableOptions = $ruleModel->getAvailableUpdateTimeType();
        return $availableOptions;
    }
}
