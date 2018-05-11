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
class Magestore_Inventorysuccess_Model_Service_Installation_ConvertDataService
{
    const PROCESS = 'installation_convert_data';
    const SIZE = 100;
    
    /**
     * @var Magestore_Coresuccess_Model_Service_QueryProcessorService 
     */
    protected $queryProcessorService;
    
    /**
     * 
     */
    public function __construct()
    {
        $this->queryProcessorService = Magestore_Coresuccess_Model_Service::queryProcessorService();
    }

    /**
     * 
     * @param int $start
     * @return int
     */
    public function convertWarehouses($start=0)
    {
        $convertData = $this->getResource()->prepareConvertWarehouses($start, self::SIZE);

        if(isset($convertData['query'])) {
            $this->queryProcessorService->start(self::PROCESS);
            $this->queryProcessorService->addQuery($convertData['query'], self::PROCESS);
            $this->queryProcessorService->process(self::PROCESS);
            $this->generateStocksFromWarehouses();
        }
        return isset($convertData['total']) ? $convertData['total'] : 0;
    }
    
    /**
     * 
     * @param int $start
     * @return int
     */    
    public function convertWarehouseStocks($start=0)
    {
        $convertData = $this->getResource()->prepareConvertWarehouseStocks($start, self::SIZE);
        if(isset($convertData['query'])) {
            $this->queryProcessorService->start(self::PROCESS);
            $this->queryProcessorService->addQuery($convertData['query'], self::PROCESS);
            $this->queryProcessorService->process(self::PROCESS);
        }
        return isset($convertData['total']) ? $convertData['total'] : 0;       
    }
   
    /**
     * 
     * @return Magestore_Inventorysuccess_Model_Service_Installation_ConvertDataService
     */
    public function generateStocksFromWarehouses()
    {
        $query = $this->getResource()->prepareGenerateStocksFromWarehouses();
        if($query) {
            $this->queryProcessorService->start(self::PROCESS);
            $this->queryProcessorService->addQuery($query, self::PROCESS);
            $this->queryProcessorService->process(self::PROCESS);       
        }
        return $this;
    }
    
    /**
     * 
     * @param int $warehouseId
     * @return string
     */
    public function generateWarehouseCode($warehouseId)
    {
        return 'WH'. $warehouseId;
    }    
    
    /**
     * 
     * @return bool
     */
    public function needConvert()
    {
        return Mage::helper('core')->isModuleEnabled('Magestore_Inventoryplus');
    }
    
    
    /**
     * 
     * @return Magestore_Inventorysuccess_Model_Mysql4_Installation_ConvertData
     */
    public function getResource()
    {
        return Mage::getResourceModel('inventorysuccess/installation_convertData');
    }    
}