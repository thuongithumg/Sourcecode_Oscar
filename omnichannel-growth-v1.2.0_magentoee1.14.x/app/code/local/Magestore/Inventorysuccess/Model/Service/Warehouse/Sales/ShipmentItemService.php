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
class Magestore_Inventorysuccess_Model_Service_Warehouse_Sales_ShipmentItemService
{
    
    /**
     * get warehouse id from order item id
     * 
     * @param int $itemId
     * @return int
     */
    public function getWarehouseIdByItemId($itemId)
    {
        $item = Mage::getResourceModel('sales/order_shipment_item_collection')
                        ->addFieldToFilter('order_item_id', $itemId)
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();
        return $item->getWarehouseId();
    }    
    
    /**
     * get warehouse id from shipment item id
     * 
     * @param int $shipmentItemId
     * @return int
     */
    public function getWarehouseIdByShipmentItemId($shipmentItemId)
    {
        $item = Mage::getResourceModel('sales/order_shipment_item_collection')
                        ->addFieldToFilter('entity_id', $shipmentItemId)
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();
        return $item->getWarehouseId();
    }        
    
    /**
     * get warehouse id from shipment id
     * 
     * @param int $shipmentId
     * @return int
     */
    public function getWarehouseIdByShipmentId($shipmentId)
    {
        $item = Mage::getResourceModel('sales/order_shipment_item_collection')
                        ->addFieldToFilter('parent_id', $shipmentId)
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();
        return $item->getWarehouseId();
    }        
}