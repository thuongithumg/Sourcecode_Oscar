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
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_LowStockNotification_InboxService
{
    const NOTIFICATIONS_NUMBER = 3;

    /**
     * Retrieve the list of latest unread notifications
     * @return Mage_AdminNotification_Model_Mysql4_Inbox_Collection
     */
    public function getLatestUnreadNotifications()
    {
        /** @var Mage_AdminNotification_Model_Mysql4_Inbox_Collection $adminNotification */
        $adminNotification = Mage::getResourceModel('adminnotification/inbox_collection');
        $adminNotification->addFieldToFilter('is_read', 0);
        $adminNotification->addFieldToFilter('is_remove', 0);
        $adminNotification->setOrder('date_added');

        $adminNotification->setPageSize(self::NOTIFICATIONS_NUMBER);

        return $adminNotification;
    }
}