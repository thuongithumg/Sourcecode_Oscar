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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Model_Observer_Inventorysuccess_CreateCreditmemoWarehouse extends Magestore_Webpos_Model_Observer_Abstract
{
    /**
     * Load linked Warehouse from Location of WebPOS Order
     * 
     * @param $observer
     * @return $this
     */
    public function execute($observer)
    {
        $warehouse = $observer->getEvent()->getWarehouse();
        $creditmemoItem = $observer->getEvent()->getItem();
        /* check warehouse_id from post data */
        $creditmemoData =  Mage::app()->getRequest()->getParam('creditmemo');
        $paramOrderItemId = $creditmemoItem->getOrderItemId();;
        if($parentItem = $creditmemoItem->getOrderItem()->getParentItem()) {
           if($parentItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
               $paramOrderItemId = $parentItem->getItemId();
           }
        }          
        $returnWarehouseId = null;
        if(isset($creditmemoData['items'][$paramOrderItemId]['warehouse'])) {
            $returnWarehouseId = $creditmemoData['items'][$paramOrderItemId]['warehouse'];
        }      
        if($returnWarehouseId) {
            return $this;
        }

        /* if there is no posted warehouse_id, then get warehouse_id from location_id */
            /* get current location */
        $locationId = Mage::helper('webpos/permission')->getCurrentLocation();
            /* if $location is null, get location_id from Order */    
        if(!$locationId) {
            $order = $creditmemoItem->getOrderItem()->getOrder();
            $locationId = $order->getData('location_id');
        }
        if(!$locationId) {
            return $this;
        }

        /* get warehouse which is linked to current location */
        $locationMapping =  $locationMapping = Magestore_Coresuccess_Model_Service::locationService();
        $warehouseId = $locationMapping->getWarehouseIdByLocationId($locationId);
        $orderItemWarehouseId = $creditmemoItem->getOrderItem()->getWarehouseId();
        $warehouseId = ($orderItemWarehouseId)?$orderItemWarehouseId:$warehouseId;
        if($warehouseId) {
            $warehouse->load($warehouseId);
        }
    }    

}