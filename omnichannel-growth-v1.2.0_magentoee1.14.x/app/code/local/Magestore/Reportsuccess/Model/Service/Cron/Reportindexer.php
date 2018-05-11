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
 * Reportsuccess Magestore_Reportsuccess_Model_Service_Cron_Reportindexer
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

class Magestore_Reportsuccess_Model_Service_Cron_Reportindexer
{
    const CACHE_TYPE_SALES_REPORT = "sales";
    const CACHE_TYPE_HISTORICS_REPORT = "historics";

    const SALES_REPORT_CACHE_ID = "reportsuccess_sale_report";
    const HISTORICS_REPORT_CACHE_ID = "reportsuccess_historics_report";

    const CACHE_TIMEOUT = 180;
    const CACHE_TIMEOUT_HISTORICS = 120;
    const _LIMIT_ITEMS = 500;
    /**
     * starting run cron job to sync data for reportsuccess
     * @return bool
     */
    public function execute()
    {
         //Mage::log('cron run '.__LINE__.' '.__METHOD__,null,"log.log");
         $this->syncSalesData();
         $this->syncHistoricsData();
         return true;
    }

    /**
     * sync sales data into reportsuccess
     */
    public function syncSalesData(){
        if(Mage::getStoreConfig("reportsuccess/sales_report/use_cron") != 1){
            return;
        }
        if($this->isRunning(self::CACHE_TYPE_SALES_REPORT)){
            return;
        }
        $this->start(self::CACHE_TYPE_SALES_REPORT);
        //Mage::dispatchEvent(Magestore_Reportsuccess_Helper_Data::EVENT_SYNC_SALES_REPORT);
        Mage::helper('reportsuccess')->service()->reindexDataSalesReport(self::_LIMIT_ITEMS);
        //$this->stop(self::CACHE_TYPE_SALES_REPORT);
        //$service = Mage::getSingleton('reportsuccess/service_cron_reportindexer')->stop(Magestore_Reportsuccess_Model_Service_Cron_Reportindexer::CACHE_TYPE_SALES_REPORT);

    }

    /**
     * save historics stock data into reportsuccess
     */
    public function syncHistoricsData(){
        if(Mage::getStoreConfig("reportsuccess/general/use_cron") != 1){
            return;
        }
        if($this->isRunning(self::CACHE_TYPE_HISTORICS_REPORT)){
            return;
        }
        $this->start(self::CACHE_TYPE_HISTORICS_REPORT);
        Mage::helper('reportsuccess')->service()->prepareDataHistorics(true);
        //$this->stop(self::CACHE_TYPE_HISTORICS_REPORT);
    }

    /**Check if have another cron task is running,
     * prevent parallel running to keep data consistency
     * @return bool
     */
    public function isRunning($type){
        $cacheId = ($type == self::CACHE_TYPE_SALES_REPORT)?(self::SALES_REPORT_CACHE_ID):(self::HISTORICS_REPORT_CACHE_ID);
        $cache = Mage::app()->getCache();
        return  $cache->load($cacheId);
    }

    /**
     * Mark it is have a cron task is running
     */
    private function start($type){
        $cacheId = ($type == self::CACHE_TYPE_SALES_REPORT)?(self::SALES_REPORT_CACHE_ID):(self::HISTORICS_REPORT_CACHE_ID);
        $cache = Mage::app()->getCache();
        $timeout = ($type == self::CACHE_TYPE_SALES_REPORT)?(self::CACHE_TIMEOUT):(self::CACHE_TIMEOUT_HISTORICS);
        $cache->save('1', $cacheId, array($cacheId), $timeout);
    }

    /**
     * Mark it is already finish
     */
    public function stop($type){
        $cacheId = ($type == self::CACHE_TYPE_SALES_REPORT)?(self::SALES_REPORT_CACHE_ID):(self::HISTORICS_REPORT_CACHE_ID);
        $cache = Mage::app()->getCache();
        $cache->remove($cacheId);
    }
}