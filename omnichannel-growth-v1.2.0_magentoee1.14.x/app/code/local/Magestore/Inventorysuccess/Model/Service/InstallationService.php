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
class Magestore_Inventorysuccess_Model_Service_InstallationService
    implements Magestore_Coresuccess_Model_Service_Process_ProcessServiceInterface
{
    const SIZE = 100;
    const PROCESS = 'installation';

    const STEP_CONVERT_WAREHOUSE_DATA = 'convert_warehouse_data';
    const STEP_CONVERT_STOCK_DATA = 'convert_stock_data';
    const STEP_SCAN_PRODUCT = 'scan_product';
    const STEP_SCAN_ORDER_ITEM = 'scan_order_item';
    const STEP_SCAN_SHIPMENT_ITEM = 'scan_shipment_item';
    const STEP_SCAN_CREDITMEMO_ITEM = 'scan_creditmemo_item';

    /* convert data from os_warehouse_order_item to sale_flat_order_item*/
    const STEP_CONVERT_ORDER_ITEM = 'convert_order_item';
    /* convert data from os_warehouse_shipment_item to sale_flat_shipment_item*/
    const STEP_CONVERT_SHIPMENT_ITEM = 'convert_shipment_item';
    /* convert data from os_warehouse_creditmemo_item to sale_flat_creditmemo_item*/
    const STEP_CONVERT_CREDITMEMO_ITEM = 'convert_creditmemo_item';
    /* convert data from sale_flat_order_item to sales_flat_order*/
    const STEP_CONVERT_ORDER = 'convert_order';
    /* convert data from sale_flat_order_item to sales_flat_order_grid*/
    const STEP_CONVERT_ORDER_GRID = 'convert_order_grid';

    /**
     * @var Magestore_Coresuccess_Model_Service_QueryProcessorService
     */
    protected $queryProcessorService;

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Installation_ConvertDataService
     */
    protected $convertDataService;

    protected $steps = array(
        self::STEP_CONVERT_WAREHOUSE_DATA => array(
            'method' => 'convertInventoryplusWarehouseData',
            'title' => 'Checking & converting warehouse data from old Inventory Management system',
        ),
        self::STEP_CONVERT_STOCK_DATA => array(
            'method' => 'convertInventoryplusStockData',
            'title' => 'Checking & converting warehouse stocks from old Inventory Management system',
        ),
        self::STEP_SCAN_PRODUCT => array(
            'method' => 'scanProducts',
            'title' => 'Scan Products',
        ),
        self::STEP_SCAN_ORDER_ITEM => array(
            'method' => 'scanOrderItems',
            'title' => 'Scan Order Items',
        ),
        self::STEP_SCAN_SHIPMENT_ITEM => array(
            'method' => 'scanShipmentItems',
            'title' => 'Scan Shipment Items',
        ),
        self::STEP_SCAN_CREDITMEMO_ITEM => array(
            'method' => 'scanCreditmemoItems',
            'title' => 'Scan Creditmemo Items',
        ),

        /* convert steps */
        self::STEP_CONVERT_ORDER_ITEM => array(
            'method' => 'convertOrderItems',
            'title' => 'Convert Order Items',
        ),
        self::STEP_CONVERT_SHIPMENT_ITEM => array(
            'method' => 'convertShipmentItems',
            'title' => 'Convert Shipment Items',
        ),
        self::STEP_CONVERT_CREDITMEMO_ITEM => array(
            'method' => 'convertCreditmemoItems',
            'title' => 'Convert Creditmemo Items',
        ),
        self::STEP_CONVERT_ORDER => array(
            'method' => 'convertOrders',
            'title' => 'Convert Orders',
        ),
        self::STEP_CONVERT_ORDER_GRID => array(
            'method' => 'convertOrdersGrid',
            'title' => 'Convert Orders Grid',
        ),
        /* end convert steps */

    );

    /**
     *
     */
    public function __construct()
    {
        $this->queryProcessorService = Magestore_Coresuccess_Model_Service::queryProcessorService();
        $this->convertDataService = Magestore_Coresuccess_Model_Service::installationConvertDataService();
        if ($this->convertDataService->needConvert()) {
            /* do not scan products if convert data from old inventory management system */
            //unset($this->steps[self::STEP_SCAN_PRODUCT]);
        } else {
            unset($this->steps[self::STEP_CONVERT_WAREHOUSE_DATA]);
            unset($this->steps[self::STEP_CONVERT_STOCK_DATA]);
        }
    }

    /**
     * create primary warehouse
     *
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function createDefaultWarehouse()
    {
        return Magestore_Coresuccess_Model_Service::warehouseService()->createPrimaryWarehouse();
    }

    /**
     * transfer magento data to primary warehouse by step
     *
     * @return Magestore_Inventorysuccess_Model_Installation|null
     */
    public function processByStep()
    {
        $currentStep = $this->getCurrentStep();
        if (!$currentStep) {
            return null;
        }
        $method = $this->getProcessMethod($currentStep->getStep());
        if ($method && $this->canRun($currentStep)) {
            if ($currentStep->getStep() == self::STEP_CONVERT_WAREHOUSE_DATA
                || $currentStep->getStep() == self::STEP_CONVERT_STOCK_DATA
            ) {
                $warehouseId = 0;
            } else {
                $warehouseId = $this->createDefaultWarehouse()->getWarehouseId();
            }
            $this->_beforeRun($currentStep);
            try {
                $result = $this->$method($warehouseId, $currentStep->getCurrentIndex());
                $this->_afterRun($currentStep, $result);
            } catch (Exception $e) {
                $this->_rollback($currentStep);
            }
        }
        return $currentStep;
    }

    /**
     * check the installation process done or not
     *
     * @return boolean
     */
    public function isProcessedInstallation()
    {
        if (!$this->getCurrentStep()) {
            return true;
        }
        return false;
    }

    /**
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * Can run installation?
     *
     * @param Magestore_Inventorysuccess_Model_Installation $step
     * @return boolean
     */
    public function canRun($step)
    {
        if ($step->getStatus() == Magestore_Inventorysuccess_Model_Installation::STATUS_PENDING) {
            return true;
        }
        return true;
    }

    /**
     *
     * @param Magestore_Inventorysuccess_Model_Installation $currentStep
     * @return Magestore_Inventorysuccess_Model_Installation
     */
    protected function _beforeRun($currentStep)
    {
        //$currentStep->setStatus(Magestore_Inventorysuccess_Model_Installation::STATUS_PROCESSING);
        //$currentStep->save();
        return $currentStep;
    }

    /**
     *
     * @param Magestore_Inventorysuccess_Model_Installation $currentStep
     * @return Magestore_Inventorysuccess_Model_Installation
     */
    protected function _rollback($currentStep)
    {
        $currentStep->setStatus(Magestore_Inventorysuccess_Model_Installation::STATUS_PENDING);
        $currentStep->save();
        return $currentStep;
    }

    /**
     *
     * @param Magestore_Inventorysuccess_Model_Installation $currentStep
     * @param mixed $result
     */
    protected function _afterRun($currentStep, $result)
    {
        if ($result) {
            $currentStep->setCurrentIndex($currentStep->getCurrentIndex() + self::SIZE);
            $currentStep->setTotal($result);
            $currentStep->setStatus(Magestore_Inventorysuccess_Model_Installation::STATUS_PENDING);
        } else {
            $currentStep->setStatus(Magestore_Inventorysuccess_Model_Installation::STATUS_COMPLETED);
        }
        $currentStep->save();
        return $currentStep;
    }

    /**
     * get current step of installation
     *
     * @return Magestore_Inventorysuccess_Model_Installation|null
     */
    public function getCurrentStep()
    {
        foreach ($this->steps as $stepId => $step) {
            $currentStep = Mage::getModel('inventorysuccess/installation')
                ->getCollection()
                ->addFieldToFilter(Magestore_Inventorysuccess_Model_Installation::STEP, $stepId)
                //->addFieldToFilter(Magestore_Inventorysuccess_Model_Installation::STATUS, array('neq' => Magestore_Inventorysuccess_Model_Installation::STATUS_COMPLETED))
                ->setOrder(Magestore_Inventorysuccess_Model_Installation::ID, 'ASC')
                ->setPageSize(1)->setCurPage(1)
                ->getFirstItem();
            if (!$currentStep->getId()) {
                $currentStep->setData(array(
                    Magestore_Inventorysuccess_Model_Installation::STEP => $stepId,
                    Magestore_Inventorysuccess_Model_Installation::CURRENT_INDEX => 0,
                    Magestore_Inventorysuccess_Model_Installation::STATUS => Magestore_Inventorysuccess_Model_Installation::STATUS_PENDING
                ));
                $currentStep->save();
                return $currentStep;
            } else {
                if ($currentStep->getStatus() == Magestore_Inventorysuccess_Model_Installation::STATUS_COMPLETED) {
                    continue;
                }
                return $currentStep;
            }
        }
        return null;
    }

    /**
     *
     * @param Magestore_Inventorysuccess_Model_Installation $step
     * @return Magestore_Inventorysuccess_Model_Service_InstallationService
     */
    public function completeStep($step)
    {
        $step->setStatus(Magestore_Inventorysuccess_Model_Installation::STATUS_COMPLETED)
            ->save();
        return $this;
    }

    /**
     *
     * @param string $step
     * @return string|null
     */
    public function getProcessMethod($step)
    {
        if (isset($this->steps[$step]['method'])) {
            return $this->steps[$step]['method'];
        }
        return null;
    }

    /**
     *
     * @param string $step
     * @return string|null
     */
    public function getProcessTitle($step)
    {
        if (isset($this->steps[$step]['title'])) {
            return $this->steps[$step]['title'];
        }
        return null;
    }

    /**
     *
     * @param int $warehouseId
     * @param int $start
     * @return int
     */
    public function scanProducts($warehouseId, $start = 0)
    {
        $this->queryProcessorService->start(self::PROCESS);

        /* prepare query to transfer products to default warehouse */
        $data = $this->getResource()->prepareTransferProductsToDefaultWarehouse($warehouseId, $start, self::SIZE);
        if (count($data)) {
            $this->queryProcessorService->addQuery($data['query'], self::PROCESS);
            $this->queryProcessorService->process(self::PROCESS);
            return isset($data['total']) ? $data['total'] : 0;
        }
        return 0;
    }

    /**
     *
     * @param int $warehouseId
     * @param int $start
     * @return int
     */
    public function scanOrderItems($warehouseId, $start = 0)
    {
        $this->queryProcessorService->start(self::PROCESS);

        /* scan order items then add to warehouse */
        $data = $this->getResource()->scanOrderItems($warehouseId, $start, self::SIZE);
        if (count($data)) {
            $this->queryProcessorService->addQuery($data['query'], self::PROCESS);
            /* prepare query to update qty-to-ship of products in warehouse */
            $queryData = $this->getResource()->prepareQtyToShipWarehouse($warehouseId, $data['qtys_to_ship']);
            $this->queryProcessorService->addQuery($queryData, self::PROCESS);
            $this->queryProcessorService->process(self::PROCESS);
            return isset($data['total']) ? $data['total'] : 0;
        }
        return 0;
    }

    /**
     * Add warehouse id to sales_flat_order_item table
     *
     * @param $warehouseId
     * @param int $start
     * @return int
     */
    public function convertOrderItems($warehouseId, $start = 0)
    {
        $type = self::STEP_CONVERT_ORDER_ITEM;
        return $this->convertItems($type, $warehouseId, $start);
    }

    /**
     * Add warehouse id to sales_flat_shipment_item table
     *
     * @param $warehouseId
     * @param int $start
     * @return int
     */
    public function convertShipmentItems($warehouseId, $start = 0)
    {
        $type = self::STEP_CONVERT_SHIPMENT_ITEM;
        return $this->convertItems($type, $warehouseId, $start);
    }

    /**
     * Add warehouse id to sales_flat_creditmemo_item
     *
     * @param $warehouseId
     * @param int $start
     * @return int
     */
    public function convertCreditmemoItems($warehouseId, $start = 0)
    {
        $type = self::STEP_CONVERT_CREDITMEMO_ITEM;
        return $this->convertItems($type, $warehouseId, $start);
    }

    /**
     * Add warehouse id to sales_flat_order table
     *
     * @param $warehouseId
     * @param int $start
     * @return int
     */
    public function convertOrders($warehouseId, $start = 0)
    {
        $type = self::STEP_CONVERT_ORDER;
        return $this->convertItems($type, $warehouseId, $start);
    }

    /**
     * Add warehouse id to sales_flat_order_grid table
     *
     * @param $warehouseId
     * @param int $start
     * @return int
     */
    public function convertOrdersGrid($warehouseId, $start = 0)
    {
        $type = self::STEP_CONVERT_ORDER_GRID;
        return $this->convertItems($type, $warehouseId, $start);
    }

    /**
     * Convert items from warehouse table to sales tables
     *
     * @param $type
     * @param $warehouseId
     * @param int $start
     */
    public function convertItems($type, $warehouseId, $start = 0)
    {
        $this->queryProcessorService->start(self::PROCESS);
        $queryData = $this->getResource()->convertItems($type, $warehouseId, $start, self::SIZE);
        if (count($queryData)) {
            $this->queryProcessorService->addQuery($queryData['query'], self::PROCESS);
            $this->queryProcessorService->process(self::PROCESS);
            return isset($queryData['total']) ? $queryData['total'] : 0;
        }
        return 0;
    }

    /**
     *
     * @param int $warehouseId
     * @param int $start
     * @return int
     */
    public function scanShipmentItems($warehouseId, $start = 0)
    {
        $this->queryProcessorService->start(self::PROCESS);

        /* scan shipment items then add to warehouse_shipment_item */
        $data = $this->getResource()->scanShipmentItems($warehouseId, $start, self::SIZE);
        if (count($data)) {
            $this->queryProcessorService->addQuery($data['query'], self::PROCESS);
            $this->queryProcessorService->process(self::PROCESS);
            return isset($data['total']) ? $data['total'] : 0;
        }
        return 0;
    }

    /**
     *
     * @param int $warehouseId
     * @param int $start
     * @return int
     */
    public function scanCreditmemoItems($warehouseId, $start = 0)
    {
        $this->queryProcessorService->start(self::PROCESS);

        /* scan creditmemo items then add to warehouse_creditmemo_item */
        $data = $this->getResource()->scanCreditmemoItems($warehouseId, $start, self::SIZE);
        if (count($data)) {
            $this->queryProcessorService->addQuery($data['query'], self::PROCESS);
            $this->queryProcessorService->process(self::PROCESS);
            return isset($data['total']) ? $data['total'] : 0;
        }
        return 0;
    }

    /**
     * transfer products to primary warehouse
     *
     * @return Magestore_Inventorysuccess_Model_Service_InstallationService
     */
    public function transferMagentoDataToDefaultWarehouse()
    {
        $warehouseId = $this->createDefaultWarehouse()->getId();

        /* start query process */
        $this->queryProcessorService->start(self::PROCESS);

        /* prepare query to transfer products to default warehouse */
        $data = $this->getResource()->prepareTransferProductsToDefaultWarehouse($warehouseId);
        $this->queryProcessorService->addQuery($data['query'], self::PROCESS);

        /* scan order items then add to warehouse */
        $scannedOrderItemData = $this->getResource()->scanOrderItems($warehouseId);
        $this->queryProcessorService->addQuery($scannedOrderItemData['query'], self::PROCESS);

        /* prepare query to update qty-to-ship of products in warehouse */
        $queryData = $this->getResource()->prepareQtyToShipWarehouse($warehouseId, $scannedOrderItemData['qtys_to_ship']);
        $this->queryProcessorService->addQuery($queryData, self::PROCESS);

        /* scan shipment items then add to warehouse_shipment_item */
        $data = $this->getResource()->scanShipmentItems($warehouseId);
        $this->queryProcessorService->addQuery($data['query'], self::PROCESS);

        /* scan creditmemo items then add to warehouse_creditmemo_item */
        $data = $this->getResource()->scanCreditmemoItems($warehouseId);
        $this->queryProcessorService->addQuery($data['query'], self::PROCESS);

        /* process queries in Processor */
        $this->queryProcessorService->process(self::PROCESS);

        return $this;
    }

    /**
     * Create default low-stock notification rules
     *
     * @return $this
     */
    public function createDefaultLowStockNotificationRule()
    {
        return Magestore_Coresuccess_Model_Service::ruleService()->createDefaultNotificationRule();
    }

    /**
     * remove the warehouse which has the same ID with default Stock (1)
     *
     * @return Magestore_Inventorysuccess_Model_Service_InstallationService
     */
    public function removeFirstWarehouseId()
    {
        Magestore_Coresuccess_Model_Service::warehouseService()->removeFirstWarehouseId();
        return $this;
    }

    /**
     *
     * @param int $warehouseId
     * @param int $start
     * @return int
     */
    public function convertInventoryplusWarehouseData($warehouseId, $start = 0)
    {
        return $this->convertDataService->convertWarehouses($start);
    }

    /**
     *
     * @param int $warehouseId
     * @param int $start
     * @return int
     */
    public function convertInventoryplusStockData($warehouseId, $start = 0)
    {
        return $this->convertDataService->convertWarehouseStocks($start);
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Mysql4_Installation
     */
    public function getResource()
    {
        return Mage::getResourceModel('inventorysuccess/installation');
    }

    /**
     *
     */
    public function reapplySetupScript()
    {
        $this->getResource()->reapplySetupScript();
    }
}