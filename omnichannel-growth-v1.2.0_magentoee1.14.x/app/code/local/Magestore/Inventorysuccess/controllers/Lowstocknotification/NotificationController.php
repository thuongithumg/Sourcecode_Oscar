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
 * Adjuststock Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Lowstocknotification_NotificationController extends Mage_Core_Controller_Front_Action
{
    /**
     * download notification file in email
     */
    public function downloadAction()
    {
        $notificationId = $this->getRequest()->getParam('notification_id');
        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Notification $notification */
        $notification = Mage::getResourceModel('inventorysuccess/lowStockNotification_notification_collection')
            ->addFieldToFilter('notification_id', $notificationId)
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();

        if ($notification->getId()) {
            $notificationService = Magestore_Coresuccess_Model_Service::notificationService();
            $prepareData = $notificationService->getPrepareDataToDownload($notification);
            $outputFile = "LowStockNotification_". date('Ymd_His').".csv";
            $this->_prepareDownloadResponse(
                $outputFile,
                $prepareData
            );
        }
    }
}