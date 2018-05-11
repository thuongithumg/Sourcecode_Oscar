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

class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Notification_Edit_Tab_Detail extends Mage_Core_Block_Template
{

    /**
     * Get current lowstocknotification
     *
     */
    public function getCurrentNotification()
    {
        $notificationService = Magestore_Coresuccess_Model_Service::notificationService();
        return $notificationService->getCurrentNotification();
    }

    /**
     * get update type
     * @return array
     */
    public function getUpdateType()
    {
        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Notification_UpdateType $sourceUpdateType */
        $sourceUpdateType = Mage::getModel('inventorysuccess/lowStockNotification_source_notification_updateType');
        return $sourceUpdateType->toOptionArray();
    }


    /**
     * get low stock threshold type
     * @return array
     */
    public function getLowStockThresholdType()
    {
        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Notification_LowStockThresholdType $sourceLowStockThresholdType */
        $sourceLowStockThresholdType = Mage::getModel('inventorysuccess/lowStockNotification_source_rule_lowStockThresholdType');
        return $sourceLowStockThresholdType->toOptionArray();
    }
}
