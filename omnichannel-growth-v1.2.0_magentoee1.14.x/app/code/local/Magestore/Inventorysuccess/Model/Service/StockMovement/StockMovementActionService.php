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
 * Inventorysuccess Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_StockMovement_StockMovementActionService
{
    /**
     * @var Magestore_Coresuccess_Model_Service_QueryProcessorService
     */
    protected  $queryProcessorService;

    /**
     * Magestore_Inventorysuccess_Model_Service_StockMovement_StockMovementActionService constructor.
     */
    public function __construct()
    {
        $this->queryProcessorService = Magestore_Coresuccess_Model_Service::queryProcessorService();
    }
    
    /**
     * add a row into table os_stock_movement
     *
     * @param array $data
     * @return Magestore_Inventorysuccess_Model_StockMovement
     */
    public function addStockMovementAction($data = array()){
        $this->queryProcessorService->start();
        if(count($data)>0){
            $this->_prepareAddStockMovement($data);
        }
        $this->queryProcessorService->process();
        return $this;
    }

    /**
     * Prepare to add new Stock Movement
     *
     * @param array $data Stock Movement Data
     * @return Magestore_Inventorysuccess_Model_Mysql4_Stock_StockChange
     */
    protected function _prepareAddStockMovement($data)
    {
        /* add query to the processor */
        $this->queryProcessorService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Service_QueryProcessorService::QUERY_TYPE_INSERT,
            'values' => $data,
            'table' => Mage::getResourceSingleton('inventorysuccess/stockMovement')->getMainTable(),
        ));
        return $this;
    }
}
