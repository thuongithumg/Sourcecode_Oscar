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
class Magestore_Inventorysuccess_Model_Service_LowStockNotification_NotificationService
{

    protected $currentNotification;

    /**
     * get product notification by system
     * @param $rule
     * @param $productIds
     * @return mixed
     */
    public function getProductNotificationBySystem($rule, $productIds)
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Notification $notificationResource */
        $notificationResource = Mage::getResourceModel('inventorysuccess/lowStockNotification_notification');
        return $notificationResource->getProductNotificationBySystem($rule, $productIds);
    }

    /**
     * get product notification by warehouse
     * @param $rule
     * @param $productIds
     * @param $warehouseIds
     * @return mixed
     */
    public function getProductNotificationByWarehouse($rule, $productIds, $warehouseIds)
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Notification $notificationResource */
        $notificationResource = Mage::getResourceModel('inventorysuccess/lowStockNotification_notification');
        return $notificationResource->getProductNotificationByWarehouse($rule, $productIds, $warehouseIds);
    }

    /**
     * Get current lowstocknotification
     *
     * @return mixed
     */
    public function getCurrentNotification()
    {
        if (!$this->currentNotification)
            $this->currentNotification = Mage::registry('lowstocknotification_notification_data');
        if (!$this->currentNotification) {
            if ($id = Mage::app()->getRequest()->getParam('id')) {
                $this->currentNotification = Mage::getResourceModel('inventorysuccess/lowStockNotification_notification_collection')
                    ->addFieldToFilter('notification_id', $id)
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem();
            }
        }
        return $this->currentNotification;
    }

    /**
     * prepare data to download
     * @param Magestore_Inventorysuccess_Model_LowStockNotification_Notification $notification
     * @return array
     * @throws Exception
     */
    public function getPrepareDataToDownload(Magestore_Inventorysuccess_Model_LowStockNotification_Notification $notification)
    {
        $notificationId = $notification->getId();
        $heading = array();
        /** availability date */
        if ($notification->getLowstockThresholdType() == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_SALE_DAY) {
            $heading = array(
                Mage::helper('inventorysuccess')->__('Id'),
                Mage::helper('inventorysuccess')->__('SKU'),
                Mage::helper('inventorysuccess')->__('Name'),
                Mage::helper('inventorysuccess')->__('Current Qty'),
                Mage::helper('inventorysuccess')->__('Qty. Sold/day'),
                Mage::helper('inventorysuccess')->__('Total Sold'),
                Mage::helper('inventorysuccess')->__('Availability Days'),
                Mage::helper('inventorysuccess')->__('Available Date'),
                Mage::helper('inventorysuccess')->__('Qty Needed')
            );
        }

        /** availability qty */
        if ($notification->getLowstockThresholdType() == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_PRODUCT_QTY) {
            $heading = array(
                Mage::helper('inventorysuccess')->__('Id'),
                Mage::helper('inventorysuccess')->__('SKU'),
                Mage::helper('inventorysuccess')->__('Name'),
                Mage::helper('inventorysuccess')->__('Current Qty'),
                Mage::helper('inventorysuccess')->__('Qty Needed')
            );
        }
        /** @var Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Notification_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel('inventorysuccess/lowStockNotification_notification_product_collection')
            ->addFieldToFilter('notification_id', $notificationId);
        $path = Mage::getBaseDir('var') . DS . 'lowstocknotification' . DS . 'download' . DS;
        $outputFile = "LowStockNotification_". date('Ymd_His').".csv";
        $filename = $path.$outputFile;

        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($filename, 'w+');
        $io->streamLock(true);
        $io->streamWriteCsv($heading);

        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Notification_Product $product */
        foreach ($productCollection as $product) {
            if ($notification->getData('lowstock_threshold_type') == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_SALE_DAY) {
                $row = array(
                    $product->getProductId(),
                    $product->getProductSku(),
                    $product->getProductName(),
                    $product->getCurrentQty(),
                    $product->getSoldPerDay(),
                    $product->getTotalSold(),
                    $product->getAvailabilityDays(),
                    $product->getAvailabilityDate(),
                    ceil($notification->getLowstockThreshold() * $product->getSoldPerDay() - $product->getCurrentQty())
                );
            }
            if ($notification->getData('lowstock_threshold_type') == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_PRODUCT_QTY) {
                $row = array(
                    $product->getProductId(),
                    $product->getProductSku(),
                    $product->getProductName(),
                    $product->getCurrentQty(),
                    ceil($notification->getLowstockThresholdQty() - $product->getCurrentQty())
                );
            }
            $io->streamWriteCsv($row);
        }
        $io->streamUnlock();
        $io->streamClose();
        return array(
            'type'  => 'filename',
            'value' => $filename,
            'rm'    => true // can delete file after use
        );
    }
}