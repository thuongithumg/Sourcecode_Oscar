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
class Magestore_Inventorysuccess_Model_Service_OrderProcess_ShipmentViewService
{
    /**
     * get Warehouse from shipment_id
     * 
     * @param int $shipmentId
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function getShipWarehouse($shipmentId)
    {
        $warehouseId = Magestore_Coresuccess_Model_Service::shipmentItemService()->getWarehouseIdByShipmentId($shipmentId);
        return Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
    }
}