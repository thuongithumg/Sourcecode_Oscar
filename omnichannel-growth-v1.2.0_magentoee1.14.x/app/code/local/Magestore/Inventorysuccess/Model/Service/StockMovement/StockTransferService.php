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
 * Stock Transfer Service
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */

use Magestore_Inventorysuccess_Model_StockMovement_StockTransfer as StockTransfer;
use Magestore_Inventorysuccess_Model_StockMovement as StockMovement;

class Magestore_Inventorysuccess_Model_Service_StockMovement_StockTransferService
{
    /**
     * @var Magestore_Coresuccess_Model_Service_QueryProcessorService
     */
    protected $queryProcessorService;

    /**
     * Magestore_Inventorysuccess_Model_Service_StockMovement_StockMovementActionService constructor.
     */
    public function __construct()
    {
        $this->queryProcessorService = Magestore_Coresuccess_Model_Service::queryProcessorService();
    }

    /**
     * Add all stock movement records to transfer
     *
     * @return $this|Magestore_Inventorysuccess_Model_Service_StockMovement_StockTransferService
     */
    public function addAllStockMovement()
    {
        $movementCollection = $this->getStockMovementNotSync();
        $movementCollection->getSelect()
            ->group(array('warehouse_id', 'action_code', 'action_id', 'created_at'))
            ->columns(array(
                'total_qty' => new Zend_Db_Expr('SUM(qty)'),
                'total_sku' => new Zend_Db_Expr('COUNT(DISTINCT product_sku)')
            ));
        $movementCollection->setOrder('created_at', 'ASC');
        if (!$movementCollection->count()) {
            return $this;
        }
        return $this->createStockTransfer($movementCollection);
    }

    /**
     * Get stock movement
     *
     * @return Magestore_Inventorysuccess_Model_Mysql4_StockMovement_Collection
     */
    public function getStockMovementNotSync()
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_StockMovement_Collection $collection */
        $collection = Mage::getResourceModel('inventorysuccess/stockMovement_collection')
            ->addFieldToFilter('main_table.' . StockMovement::STOCK_TRANSFER_ID, array('null' => true));
        return $collection;
    }

    /**
     * Add stock transfer from stock movement collection
     *
     * @param Magestore_Inventorysuccess_Model_Mysql4_StockMovement_Collection $movementCollection
     * @return $this
     */
    public function createStockTransfer($movementCollection)
    {
        $data = $this->prepareStockTransferData($movementCollection);
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $data,
            'table' => Mage::getResourceModel('inventorysuccess/stockMovement_stockTransfer')->getMainTable()
        );
        $this->queryProcessorService->start();
        $this->queryProcessorService->addQuery($query);
        $this->queryProcessorService->process();
        $this->addStockTransferToMovement();
        return $this;
    }

    /**
     * Prepare stock transfer data from stock movement collection
     *
     * @param Magestore_Inventorysuccess_Model_Mysql4_StockMovement_Collection $movementCollection
     * @return array
     */
    public function prepareStockTransferData($movementCollection)
    {
        $data = array();
        /** @var  Magestore_Inventorysuccess_Model_StockMovement $stockMovement */
        foreach ($movementCollection as $stockMovement) {
            $data[] = array(
                StockTransfer::TRANSFER_CODE => Magestore_Coresuccess_Model_Service::incrementIdService()
                    ->getNextCode(Magestore_Inventorysuccess_Model_StockMovement_StockTransfer::PREFIX_CODE),
                StockTransfer::QTY => $stockMovement->getTotalQty(),
                StockTransfer::TOTAL_SKU => $stockMovement->getTotalSku(),
                StockTransfer::ACTION_CODE => $stockMovement->getActionCode(),
                StockTransfer::ACTION_ID => $stockMovement->getActionId(),
                StockTransfer::ACTION_NUMBER => $stockMovement->getActionNumber(),
                StockTransfer::WAREHOUSE_ID => $stockMovement->getWarehouseId(),
                StockTransfer::CREATED_AT => $stockMovement->getCreatedAt(),
            );
        }
        return $data;
    }

    public function addStockTransferToMovement()
    {
        $data = $this->prepareStockMovementData();
        if (!count($data)) {
            return $this;
        }
        $updateValues = array(
            StockMovement::STOCK_TRANSFER_ID => Mage::getSingleton('core/resource')
                ->getConnection('core_read')
                ->getCaseSql(StockMovement::STOCK_MOVEMENT_ID, $data, null)
        );
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $updateValues,
            'condition' => array(StockMovement::STOCK_MOVEMENT_ID . ' IN (?)' => array_keys($data)),
            'table' => Mage::getResourceModel('inventorysuccess/stockMovement')->getMainTable()
        );
        $this->queryProcessorService->start();
        $this->queryProcessorService->addQuery($query);
        $this->queryProcessorService->process();
        return $this;
    }

    /**
     * Prepare stock movement data
     *
     * @return array
     */
    public function prepareStockMovementData()
    {
        $data = array();
        $movementCollection = $this->getStockMovementNotSync();
        $movementCollection->getSelect()->joinLeft(
            array('transfer' => Mage::getResourceModel('inventorysuccess/stockMovement_stockTransfer')->getMainTable()),
            'main_table.warehouse_id = transfer.warehouse_id AND 
            main_table.action_code = transfer.action_code AND 
            main_table.action_id = transfer.action_id AND 
            main_table.created_at = transfer.created_at'
        )->columns(
            array('main_table.stock_movement_id', 'transfer_id' => 'transfer.stock_transfer_id')
        )->group('main_table.stock_movement_id');
//        \Zend_Debug::dump($movementCollection->getSelect()->__toString());die;
        if (!$movementCollection->count()) {
            return $data;
        }
        /** @var  Magestore_Inventorysuccess_Model_StockMovement $stockMovement */
        foreach ($movementCollection as $stockMovement) {
            $data[$stockMovement->getStockMovementId()] = $stockMovement->getTransferId();
        }
        return $data;
    }
}
