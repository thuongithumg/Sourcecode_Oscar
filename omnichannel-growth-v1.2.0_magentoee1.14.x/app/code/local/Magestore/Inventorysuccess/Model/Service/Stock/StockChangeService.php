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
class Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
{
    /**
     * Define qty actions
     */
    CONST QTY_CHANGE_ACTION = 'change';
    CONST QTY_UPDATE_ACTION = 'update';
    CONST QTY_INCREASE_ACTION = 'increase';
    CONST QTY_DECREASE_ACTION = 'decrease';
    CONST QTY_FORCE_EDIT_ACTION = 'force_edit';
    /*
     * Define stock activity
     */
    CONST ISSUE_STOCK = 'issue_stock';
    CONST RECEIVE_STOCK = 'receive_stock';
    CONST ADJUST_STOCK = 'adjust_stock';
    
    /**
     * Query process name
     */
    const QUERY_PROCESS = 'stock_change';

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
     * change qty of product in warehouse
     * 
     * @param int $warehouseId
     * @param int $productId
     * @param float $qty
     * @param bool $updateCatalog
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    public function change($warehouseId, $productId, $qty, $updateCatalog = true)
    {
        $qtys = array($productId => $qty);
        return $this->massChange($warehouseId, $qtys, $updateCatalog);      
    }
    
    /**
     * update qty of product in warehouse
     * 
     * @param int $warehouseId
     * @param int $productId
     * @param float $qty
     * @param bool $updateCatalog
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    public function update($warehouseId, $productId, $qty, $updateCatalog = true)
    {
        $qtys = array($productId => $qty);
        return $this->massUpdate($warehouseId, $qtys, $updateCatalog);              
    }    

    /**
     * decrease qty of product in warehouse
     * 
     * @param int $warehouseId
     * @param int $productId
     * @param float $qty
     * @param bool $updateCatalog
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    public function decrease($warehouseId, $productId, $qty, $updateCatalog = true)
    {
        $qtys = array($productId => -abs($qty));
        return $this->massChange($warehouseId, $qtys, $updateCatalog);         
    }

    /**
     * increase qty of product in warehouse
     * 
     * @param int $warehouseId
     * @param int $productId
     * @param float $qty
     * @param bool $updateCatalog
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    public function increase($warehouseId, $productId, $qty, $updateCatalog = true)
    {       
        $qtys = array($productId => abs($qty));
        return $this->massChange($warehouseId, $qtys, $updateCatalog);
    }

    /**
     * Mass change stocks
     * 
     * @param int $warehouseId
     * @param array $qtys
     * @param bool $updateCatalog
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    public function massChange($warehouseId, $qtys, $updateCatalog = true)
    {
        return $this->massUpdate($warehouseId, $qtys, $updateCatalog, self::QTY_CHANGE_ACTION);
    }

    /**
     * @param $pId
     * @param $stockItemData
     * @param $type
     * @param bool|true $updateCatalog
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    public function forceEdit($pId, $stockItemData , $type , $updateCatalog = true){

        $warehouseId  = $stockItemData['warehouse_id'];
        $qtys = array($pId=> $stockItemData['available_qty']);
        if($type == self::QTY_FORCE_EDIT_ACTION){
            return $this->massUpdate($warehouseId, $qtys, $updateCatalog, self::QTY_FORCE_EDIT_ACTION);
        }

    }

    /**
     * Mass update stocks
     * 
     * @param int $warehouseId
     * @param array $qtys
     * @param bool $updateCatalog
     * @param string|null $actiontype
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    public function massUpdate($warehouseId, $qtys, $updateCatalog = true, $actiontype = null)
    {

        /* add by Kai */
        if($actiontype == self::QTY_FORCE_EDIT_ACTION){
            $actiontype = self::QTY_FORCE_EDIT_ACTION;
        }else{
            $actiontype = ($actiontype == self::QTY_CHANGE_ACTION) ? $actiontype : self::QTY_UPDATE_ACTION;
        }
        /* end by Kai */

        
        /* start queries processing */
        $this->queryProcessorService->start(self::QUERY_PROCESS);
        
        /* prepare queries to update stocks in warehouse, then add queries to Processor */
        $prepareData = $this->getResource()->prepareUpdateWarehouseStocks($warehouseId, $qtys, $actiontype);
        $this->queryProcessorService->addQueries($prepareData['queries'], self::QUERY_PROCESS);
        $changeQtys = $prepareData['change_qtys'];
        
        /* prepare to update global stocks, then add queries to Processor */        
        if($updateCatalog) {
            $queries = $this->getResource()->prepareUpateGlobalStocks($changeQtys);
            $this->queryProcessorService->addQueries($queries, self::QUERY_PROCESS);
        }
        
        /* process queries in Processor */
        $this->queryProcessorService->process(self::QUERY_PROCESS);
            
        /* reindex stock data */
        $this->getResource()->reindexStockData(array_keys($changeQtys));      
        
        return $this;
    }
    
    /**
     * update global stock (stock_id = 1)
     * 
     * @param array $qtys
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    public function prepareUpdateGlobalStock($qtys)
    {
        return $this->getResource()->prepareUpateGlobalStocks($changeQtys); 
    }
    
    /**
     * 
     * @return Magestore_Inventorysuccess_Model_Mysql4_Stock_StockChange
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('inventorysuccess/stock_stockChange');
    }
    
    /**
     * Adjust stocks in the warehouse
     * 
     * @param int $warehouseId
     * @param array $products
     * @param string $actionType
     * @param int $actionId
     * @param bool $updateCatalog
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    public function adjust($warehouseId, $products, $actionType, $actionId, $updateCatalog = true)
    {
        /* format qty before updating */
        $changeQtys = $this->_prepareProductQtys($products, self::ADJUST_STOCK);
        /* update stocks in warehouse & global stocks */
        $this->massUpdate($warehouseId, $changeQtys, $updateCatalog);
        
        Mage::dispatchEvent('stockchange_adjust_stock_after', array(
            'warehouse_id' => $warehouseId,
            'products' => $products,
            'action_type' => $actionType,
            'action_id' => $actionId,
        ));
        return $this;
    }

    /**
     * Issue stocks from the warehouse
     * 
     * @param int $warehouseId
     * @param array $products
     * @param string $actionType
     * @param int $actionId
     * @param bool $updateCatalog
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */    
    public function issue($warehouseId, $products, $actionType, $actionId, $updateCatalog = true)
    {
        /* format qty before updating */
        $changeQtys = $this->_prepareProductQtys($products, self::ISSUE_STOCK);
        /* update stocks in warehouse & global stocks */
        $this->massChange($warehouseId, $changeQtys, $updateCatalog);
        
        Mage::dispatchEvent('stockchange_issue_stock_after', array(
            'warehouse_id' => $warehouseId,
            'products' => $changeQtys,
            'action_type' => $actionType,
            'action_id' => $actionId,
        ));
        return $this;        
    }

    /**
     * Receive stocks to the warehouse
     * 
     * @param int $warehouseId
     * @param array $products
     * @param string $actionType
     * @param int $actionId
     * @param bool $updateCatalog
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */    
    public function receive($warehouseId, $products, $actionType, $actionId, $updateCatalog = true)
    {
        /* format qty before updating */
        $changeQtys = $this->_prepareProductQtys($products, self::RECEIVE_STOCK);
        /* update stocks in warehouse & global stocks */
        $this->massChange($warehouseId, $changeQtys, $updateCatalog);
        
        Mage::dispatchEvent('stockchange_receive_stock_after', array(
            'warehouse_id' => $warehouseId,
            'products' => $changeQtys,
            'action_type' => $actionType,
            'action_id' => $actionId,
        ));
        return $this;         
    }
    
    /**
     * Format product qty
     * 
     * @param array $products
     * @return array 
     */
    protected function _prepareProductQtys($products, $stockAction)
    {
        $prepareProducts = array();
        if(!count($products)) {
            return array();
        }
        
        foreach($products as $productId => $qty) {
            $formatQty = $qty;
            switch ($stockAction) {
                case self::ADJUST_STOCK:
                    $formatQty = floatval($qty['adjust_qty']);
                    break;
                case self::ISSUE_STOCK:
                    $formatQty = -abs(floatval($qty));
                    break;
                case self::RECEIVE_STOCK:
                    $formatQty = abs(floatval($qty));
                    break;
            }
            $prepareProducts[$productId] = $formatQty;
        }
        
        return $prepareProducts;
    }
    
}