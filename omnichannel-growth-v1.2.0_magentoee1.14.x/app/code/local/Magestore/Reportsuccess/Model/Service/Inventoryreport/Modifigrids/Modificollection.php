<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Magestore_Reportsuccess_Helper_Data
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

use Magestore_Reportsuccess_Helper_Data as Data;
class Magestore_Reportsuccess_Model_Service_Inventoryreport_Modifigrids_Modificollection
{
    /**
     * @param $warehouseId
     * @param $collection
     * @param $type
     * @return mixed
     */
   public function getCollection($warehouseId,$collection,$type){
       if($type == Data::LOCATIONS){
           return $collection = Mage::getResourceModel('reportsuccess/costofgood_collection')->getLocationreportCollection($collection);
       }
       if($type == Data::INCOMING_STOCK){
           return  $collection = Mage::getResourceModel('reportsuccess/costofgood_collection')->getDetailsreportCollection();
       }
       if($type == Data::STOCK_ON_HAND) {
           $collection = Mage::getResourceModel('reportsuccess/costofgood_collection')->getInventoryreportCollection($collection);
       }
       if($type == Data::DETAILS){
           $collection = Mage::getResourceModel('reportsuccess/costofgood_collection')->getDetailsreportCollection($collection);
       }
       if($type == Data::HISTORICS){
           return $collection = Mage::getResourceModel('reportsuccess/costofgood_collection')->getHistoricsreportCollection($warehouseId,$collection);
       }
       if ($warehouseId && ($warehouseId != Magestore_Reportsuccess_Helper_Data::ALL_WAREHOUSE)) {
           /* this function come from InventorySuccess extension */
           $collection->addWarehouseToFilter($warehouseId);
       }
       if(!$warehouseId || ($warehouseId == Magestore_Reportsuccess_Helper_Data::ALL_WAREHOUSE)){
           $warehouseId = Mage::getResourceModel('reportsuccess/costofgood_collection')->getWarehouseIds();
           $collection->addFieldToFilter('stock_id', array('in' => $warehouseId));
       }
       return $collection;
   }
}