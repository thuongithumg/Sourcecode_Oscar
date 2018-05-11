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
 * Inventorysuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Notification_Email extends Mage_Adminhtml_Block_Template
{
    /**
     * get down load url
     * @param $id
     * @return string
     */
    public function getDownloadUrl($id)
    {
        $storeId = Mage::app()->getDefaultStoreView()->getStoreId();
        return Mage::app()->getStore($storeId)->getUrl('inventorysuccess/lowstocknotification_notification/download', array('notification_id' =>$id));
    }

    /**
     * get warehouse information by warehouse ids
     * @param $warehouseIds
     * @return array
     */
    public function getWarehouseInformation($warehouseIds) {
        $warehouses = array();
        /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouseModel */
        $warehouseModel = Mage::getModel('inventorysuccess/warehouse');
        foreach ($warehouseIds as $key => $id) {
            $warehouseId = $key;
            $warehouseModel->load($warehouseId);
            $warehouseInfo = array();
            if ($warehouseModel->getId()) {
                $warehouseInfo['warehouse_id'] = $warehouseId;
                $warehouseInfo['warehouse_name'] = $warehouseModel->getWarehouseName();
                $warehouseInfo['notification_id'] = $id;
                $warehouses[] = $warehouseInfo;
            }
        }
        return $warehouses;
    }
}