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
 * Reportsuccess Magestore_Reportsuccess_Model_Service_Inventoryreport_InventoryService
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

class Magestore_Reportsuccess_Model_Service_Inventoryreport_InventoryService
extends Magestore_Reportsuccess_Model_Service_Inventoryreport_Modifigrids_Modifigrids
{
    /**
     * Return warehouse product collection with product information and total qtys
     *
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection
     */
    public function getAllStocksWithProductInformation()
    {
        return Mage::getResourceModel('reportsuccess/costofgood_collection')->getAllStocksWithProductInformation();
    }

    /**
     * @param $collection
     * @param $columnName
     * @param $filterValue
     * @return $collection
     */
    public function filterInventoryCallback($collection,$columnName,$filterValue){
        return Mage::getResourceModel('reportsuccess/costofgood_collection')->filterInventoryCallback($collection,$columnName,$filterValue);
    }

    /**
     * @param $grid
     * @param $type
     */
    public function modifiColumns($grid,$type){
       return $this->Columns($grid,$type);
    }

    /**
     * @param $grid
     * @param $type
     * @return Varien_Object
     */
    public function modifiTotals($grid,$type){
        return $this->Totals($grid,$type);

    }

    /**
     * @param $ids
     * @param $type
     * @return bool
     */
    public function getWarehouse($ids,$type){
        return $this->Warehouse($ids,$type);

    }

    /**
     * @param $date
     * @param $type
     * @return mixed
     */
    public function getSelectDate($date,$type){
        return $this->SelectDate($date,$type);
    }


    /**
     * @param $ids
     * @param $type
     * @return mixed
     */
    public function getType($ids,$type){
        return $this->Type($ids,$type);
    }

    /**
     * @param $warehouse
     * @param $collection
     * @param $type
     * @return mixed
     */
    public function getCollection($warehouse,$collection,$type){
       return Mage::getSingleton('reportsuccess/service_inventoryreport_modifigrids_modificollection')->getCollection($warehouse,$collection,$type);
    }

    /**
     * @return mixed
     */
    public function prepareDataHistorics($cron = null){
        return Mage::getSingleton('reportsuccess/mysql4_historics')->createDB($cron);
    }

    /**
     * @return mixed
     */
    public function getDataHistorics(){
        return Mage::getSingleton('reportsuccess/service_inventoryreport_historics_historicsService')->getAllfilesName();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function updateMacService($data){
        return Mage::getSingleton('reportsuccess/service_inventoryreport_mac_macService')->updateMacValues($data);
    }

    /**
     * @return mixed
     */
    public function reindexDataSalesReport($cron = null){
        return Mage::getSingleton('reportsuccess/service_salesreport_salesReportService')->salesReportProcess($cron);
    }

    /**
     * @return mixed
     */
    public function sqlService(){
        return Mage::getSingleton('reportsuccess/service_salesreport_sqlService');
    }

    /**
     * @return mixed
     */
    public function getSalesReportCollection()
    {
        return Mage::getResourceModel('reportsuccess/salesreport_collection');
    }

    /**
     * @return mixed
     */
    public function salesReportSelectDate($date_from,$date_to,$type){
        return $this->reportSelectDate($date_from,$date_to,$type);
    }

    /**
     * @param $type
     * @param $action
     * @return mixed
     */
    public function updateDementionAndMetrics($type,$action){
        return Mage::getResourceModel('reportsuccess/editcolumns_collection')->updateDementionAndMetrics($type,$action);
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function attributeMapping($collection){
        return  Mage::getSingleton('reportsuccess/service_mapping_attributeMapping')->attributeMapping($collection);
    }

    /**
     * @param $collection
     * @param $column
     * @param $value
     * @return mixed
     */
    public function fillterAttributeMapping($collection,$column,$value){
        return Mage::getSingleton('reportsuccess/service_mapping_attributeMapping')->attributeFillter($collection,$column,$value);
    }

    /**
     * @param $collection
     * @param $type
     * @return mixed
     */
    public function dimensionsMapping($collection,$type){
        return  Mage::getSingleton('reportsuccess/service_mapping_dimensionsMapping')->dimensionsMapping($collection,$type);
    }


}