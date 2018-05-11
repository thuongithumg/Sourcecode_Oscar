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
class Magestore_Inventorysuccess_Model_Service_Transfer_TransferService
    extends
    Magestore_Inventorysuccess_Model_Service_ProductSelection_ProductSelectionService
{
    /**
     * validate transfer stock general information form input
     * @param $data
     * @return array
     */
    public function validateTranferGeneralForm($data)
    {
        $is_validate = true;
        $errors = array();
        if (isset($data["source_warehouse_id"]) && isset($data["des_warehouse_id"])) {
            if ($data["source_warehouse_id"] == $data["des_warehouse_id"]) {
                $is_validate = false;
                $errors[] = "Destination Warehouse must be different from Source Warehouse";
            }
        }

        if (!isset($data['transferstock_id'])) {
            if (!$this->validateTransferstockCode($data['transferstock_code'])) {
                $is_validate = false;
                $errors[] = "Transfer Stock Code #" . $data['transferstock_code'] . " is aready exits!";
            }
        }

        return array("is_validate" => $is_validate, "errors" => $errors);
    }

    /**
     * check if a transferstock_code is valid or not
     * @param $transferstock_code
     * @return bool
     */
    public function validateTransferstockCode($transferstock_code)
    {
        $transferstock = Mage::getModel('inventorysuccess/transferstock')->load($transferstock_code,
            "transferstock_code");
        if ($transferstock->getId() != null) {
            return false;
        }
        return true;
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transfer
     * @param $data
     */
    public function initTransfer(
        $transfer,
        $data
    )
    {
        $transfer->setData($data);
        if (array_key_exists('source_warehouse_id', $data) && $data['source_warehouse_id']) {
            $transfer->setSourceWarehouseCode(
                Mage::getModel('inventorysuccess/warehouse')->load($data['source_warehouse_id'])->getWarehouseCode()
            );
        }
        if (array_key_exists('des_warehouse_id', $data) && $data['des_warehouse_id']) {
            $transfer->setDesWarehouseCode(
                Mage::getModel('inventorysuccess/warehouse')->load($data['des_warehouse_id'])->getWarehouseCode()
            );
        }
        $transfer->setCreatedAt(date('Y-m-d H:i:s'));
        $transfer->setStatus(Magestore_Inventorysuccess_Model_Transferstock::STATUS_PENDING);
        $transfer->setCreatedBy($this->getCurrentBackendUsername());
    }


    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transfer
     * @param array $sendProducts
     * @param bool $updateStock
     * @param bool $directTransfer
     * @return bool
     */
    public function saveTransferStockProduct(
        $transfer,
        $sendProducts,
        $updateStock = false,
        $directTransfer = false
    )
    {
        if (($transfer->getType() == Magestore_Inventorysuccess_Model_Transferstock::TYPE_SEND
                || $transfer->getType() == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL)
            && $this->validateStockDelivery($transfer, $sendProducts) == false
        ) {
            $this->_getSession()->setData("send_products", $sendProducts);
            return false;
        }
        $this->setProducts($transfer, $sendProducts);
        try {
            $transfer->setData("qty", $this->_getTotalQty($sendProducts))->save();
        } catch (\Exception $e) {
        }
        if ($directTransfer) {
//            $this->updateStock($transfer);
            if ($transfer->getType() == Magestore_Inventorysuccess_Model_Transferstock::TYPE_SEND) {
                $this->saveTransferActivityProduct($transfer, $sendProducts,
                    Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_DELIVERY);
            }
            $this->saveTransferActivityProduct($transfer, $sendProducts,
                Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_RECEIVING);
            /** send email */
            Magestore_Coresuccess_Model_Service::transferEmailService()->notifyCreateDirectTransfer($transfer);
            return true;
        } elseif ($updateStock) {
            if($transfer->getType() != Magestore_Inventorysuccess_Model_Transferstock::TYPE_SEND){
                $this->updateStock($transfer);
            }
            /** send email */
            Magestore_Coresuccess_Model_Service::transferEmailService()->notifyCreateNewTransfer($transfer);
            return true;
        }
        return true;
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transfer
     * @param array $products
     * @param string $type
     */
    public function saveTransferActivityProduct(
        $transfer,
        $products,
        $type,
        $updateStock = true
    )
    {
        $transferActivity = $this->createTransferActivity($transfer, $products, $type);
        if ($transferActivity->getActivityId()) {
            $this->getTransferActivityService()->setProducts($transferActivity, $products);
            if($updateStock) {
                $this->getTransferActivityService()->updateStock($transferActivity);
            }
            $this->getTransferActivityService()->updateTransferstockProductQtySummary($transfer, $products, $type);
        }
        if ($type == Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_DELIVERY) {
            Magestore_Coresuccess_Model_Service::transferEmailService()->notifyCreateDelivery($transfer);
        }
        elseif ($type == Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_RETURNING) {
            Magestore_Coresuccess_Model_Service::transferEmailService()->notifyReturn($transfer);
        }else {
            Magestore_Coresuccess_Model_Service::transferEmailService()->notifyCreateReceiving($transfer);
        }
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transfer
     * @param array $products
     * @param string $type
     * @return Magestore_Inventorysuccess_Model_Transferstock_Activity
     */
    public function createTransferActivity(
        $transfer,
        $products,
        $type
    )
    {
        $data = array();
        $data['activity_type'] = $type;
        $data['created_by'] = $this->getCurrentBackendUsername();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['transferstock_id'] = $transfer->getId();
        $data['total_qty'] = $this->_getTotalQty($products);
        if($type == Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_RETURNING) {
            $data['note'] = Mage::helper('inventorysuccess')->__('Return by new transferstock');
        }
        $newTransferNote = Mage::registry('new_transferstock_code');
        if($newTransferNote){
            $data['note'] = Mage::helper('inventorysuccess')->__('Return by new transferstock %s',$newTransferNote);
        }
        $transferActivity = Mage::getModel('inventorysuccess/transferstock_activity');
        $transferActivity->setData($data)->setId(null);
        try {
            $transferActivity->save();
        } catch (\Exception $e) {
        }
        return $transferActivity;
    }


    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transfer
     */
    public function updateStock(
        $transfer
    )
    {
        $products = $this->getProducts($transfer);
        $productData = array();
        if ($products->getSize()) {
            foreach ($products as $product) {
                $productData[$product->getProductId()] = $product->getQty();
            }
            switch ($transfer->getType()) {
                case Magestore_Inventorysuccess_Model_Transferstock::TYPE_SEND:
                    $warehouseId = $transfer->getSourceWarehouseId();
                    $this->_getStockChangeService()->issue($warehouseId, $productData,
                        Magestore_Inventorysuccess_Model_Transferstock::STOCK_MOVEMENT_ACTION_CODE,
                        $transfer->getTransferstockId());
                    break;
                case Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL:
                    $warehouseId = $transfer->getSourceWarehouseId();
                    $this->_getStockChangeService()->issue($warehouseId, $productData,
                        Magestore_Inventorysuccess_Model_Transferstock::STOCK_MOVEMENT_ACTION_CODE,
                        $transfer->getTransferstockId());
                    break;
                case Magestore_Inventorysuccess_Model_Transferstock::TYPE_FROM_EXTERNAL:
                    $warehouseId = $transfer->getDesWarehouseId();
                    $this->_getStockChangeService()->receive($warehouseId, $productData,
                        Magestore_Inventorysuccess_Model_Transferstock::STOCK_MOVEMENT_ACTION_CODE,
                        $transfer->getTransferstockId());
                    break;
            }
        }
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transfer
     * @param array $deliveryProducts
     * @return bool
     */
    public function validateStockDelivery(
        $transfer,
        $deliveryProducts
    )
    {
        $warehouseId = $transfer->getSourceWarehouseId();
        $productStocks = array();
        foreach ($deliveryProducts as $index => $item) {
            $productStocks[$item['product_id']] = $item['qty'];
        }
        $isValid = $this->validateStock($warehouseId, $productStocks);
        if (!$isValid && !Mage::registry('new_transferstock_code')) {
            $this->_getSession()->addError(Mage::helper('inventorysuccess')->__('Qty sent must be less than or equal available qty!'));
        }
        return $isValid;
    }

    /**
     * check if a the qty of a product is less than available qty in a warehouse or not.
     * @param $productStock ([product_id => qty]
     * @param $warehouseId
     * @return bool
     */
    public function validateStock(
        $warehouseId,
        $productStock
    )
    {
        $warehouseService = Magestore_Coresuccess_Model_Service::warehouseStockService();
        $products = $warehouseService->getStocks($warehouseId, array_keys($productStock));
        foreach ($products as $product) {
            $availableQty = $product->getTotalQty() - $product->getQtyToShip();
            if ((int)$productStock[$product->getProductId()] && $availableQty < (int)$productStock[$product->getProductId()]) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function getCurrentBackendUsername()
    {
        return Mage::getSingleton('admin/session')->getUser()->getUsername();
    }


    /**
     * @return Magestore_Inventorysuccess_Model_Service_Transfer_TransferActivityService
     */
    public function getTransferActivityService()
    {
        return Magestore_Coresuccess_Model_Service::transferActivityService();
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    protected function _getStockChangeService()
    {
        return Magestore_Coresuccess_Model_Service::stockChangeService();
    }

    /**
     * @return string
     */
    public function generateCode()
    {
        return $this->generateUniqueCode(Magestore_Inventorysuccess_Model_Transferstock::TRANSFER_CODE_PREFIX);
    }

    /**
     * @return mixed
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * @param array $products
     * @return int
     */
    protected function _getTotalQty(
        $products
    )
    {
        $totalQty = 0;
        foreach ($products as $item) {
            $totalQty += $item["qty"];
        }
        return $totalQty;
    }

    /**
     * @return array
     */
    public function getAvailableWarehousesArray($resourceId = null)
    {
        $optionArray = array();
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $collection */
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_collection')->getTotalSkuAndQtyCollection();

        $collection = Magestore_Coresuccess_Model_Service::permissionService()->filterPermission(
            $collection,
            $resourceId
        );

        //$collection->getSelect()->where('total_sku > ?', 0);
        $items = $collection->toArray(array(
            Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID,
            Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_NAME,
            Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE,
        ));

        if (isset($items['items']) && count($items['items'])) {
            foreach ($items['items'] as $item) {
                $optionArray[$item[Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID]]
                    = Magestore_Coresuccess_Model_Service::warehouseOptionService()->_getWarehouseLabel($item);
            }
        }
        return $optionArray;
    }


    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transferStock
     * @param array $barcodes
     * @return Magestore_Coresuccess_Model_Service_ProductSelection_ProductSelectionService
     */
    public function addProductFromBarcode(
        $transferStock,
        $barcodes
    )
    {
        if (!is_object($transferStock)) {
            $transferStock = Mage::getModel('inventorysuccess/transferstock')->load($transferStock);
        }
        $transferProducts = $this->getProducts($transferStock);
        $data = array();
        foreach ($transferProducts as $transferProduct) {
            $data[$transferProduct['product_id']] = $transferProduct->getData();
        }
        foreach ($barcodes as $barcode) {
            if (array_key_exists($barcode['product_id'], $data)) {
                $data[$barcode['product_id']]['qty'] += $barcode['qty'];
            } else {
                $data[$barcode['product_id']] = array(
                    'product_id' => $barcode['product_id'],
                    'product_name' => $barcode['product_name'],
                    'product_sku' => $barcode['product_sku'],
                    'qty' => $barcode['qty'],
                );
            }
        }
        return $this->setProducts($transferStock, $data);
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transferStock
     * @param array $barcodes
     * @param string $type
     */
    public function createTransferActivityFromBarcode(
        $transferStock,
        $barcodes,
        $type
    )
    {
        $products = array();
        foreach ($barcodes as $barcode) {
            if (array_key_exists($barcode['product_id'], $products)) {
                $products[$barcode['product_id']]['qty'] += $barcode['qty'];
            } else {
                $products[$barcode['product_id']] = array(
                    'product_id' => $barcode['product_id'],
                    'product_name' => $barcode['product_name'],
                    'product_sku' => $barcode['product_sku'],
                    'qty' => $barcode['qty'],
                );
            }
        }
        return $this->saveTransferActivityProduct($transferStock, $products, $type);
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transferStock
     * @param array $itemData
     */
    public function returnItems($transferStock, $itemData = array())
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection $transferProducts */
        $transferProducts = $this->getProducts($transferStock, array_keys($itemData));
        $transferProducts->getRemainingItems();
        $returnData = $this->getReturnData($transferProducts, $itemData);
        $prepareAdjustData = $this->prepareReturnAdjustmentData($transferStock, $returnData);
        if (count($prepareAdjustData['products']) > 0) {
            $this->createAdjustment($prepareAdjustData);
            $this->createReturnTransferActivity($transferStock, $prepareAdjustData['products']);
        }
        return $this;
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transferStock
     * @param array $itemData
     */
    public function returnItemsNotAdjustStock($transferStock, $itemData = array())
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection $transferProducts */
        $transferProducts = $this->getProducts($transferStock, array_keys($itemData));
        $transferProducts->getRemainingItems();
        $returnData = $this->getReturnData($transferProducts, $itemData);
        $prepareAdjustData = $this->prepareReturnAdjustmentData($transferStock, $returnData);
        if (count($prepareAdjustData['products']) > 0) {
//            $this->createAdjustment($prepareAdjustData);
            $this->createReturnTransferActivity($transferStock, $prepareAdjustData['products']);
        }
        return $this;
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection $transferProducts
     * @param array $itemData
     */
    public function getReturnData($transferProducts, $itemData = array())
    {
        $returnData = array();
        $returnAll = empty($itemData) ? true : false;
        /** @var Magestore_Inventorysuccess_Model_Transferstock_Product $transferProduct */
        foreach ($transferProducts as $transferProduct) {
            if ($returnAll)
                $returnData[$transferProduct->getProductId()] = $transferProduct->getQtyRemaining();
            else {
                if (!isset($itemData[$transferProduct->getProductId()]))
                    continue;
                $returnData[$transferProduct->getProductId()] = min($itemData[$transferProduct->getProductId()], $transferProduct->getQtyRemaining());
            }
        }
        return $returnData;
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transferStock
     * @param array $returnData
     */
    public function prepareReturnAdjustmentData($transferStock, $returnData = array())
    {
        $adjustData = array(
            Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID => $transferStock->getSourceWarehouseId(),
            Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME => null,
            Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_CODE => null,
            Magestore_Inventorysuccess_Model_Adjuststock::REASON =>
                Mage::helper('inventorysuccess')->__('Return items from transfer Stock #%s', $transferStock->getTransferstockCode()),
            'products' => array()
        );
        $warehouseStocks = Magestore_Coresuccess_Model_Service::stockRegistryService()
            ->getStocks($transferStock->getSourceWarehouseId(), array_keys($returnData))
            ->joinProductCollection();
        /** @var Magestore_Inventorysuccess_Model_Warehouse_Product $warehouseStock */
        foreach ($warehouseStocks as $warehouseStock) {
            if (isset($returnData[$warehouseStock->getProductId()])) {
                $adjustData['products'][$warehouseStock->getProductId()] = array(
                    'product_id' => $warehouseStock->getProductId(),
                    'adjust_qty' => $warehouseStock->getTotalQty() + $returnData[$warehouseStock->getProductId()],
                    'old_qty' => $warehouseStock->getTotalQty(),
                    'product_sku' => $warehouseStock->getSku(),
                    'product_name' => $warehouseStock->getName()
                );
            }
        }
        return $adjustData;
    }


    /**
     * Create stock adjustment, $adjustData['products' => array(), 'warehouse_id' => $warehouseId,... ]
     *
     * @param array $adjustData
     * @return Magestore_Inventorysuccess_Model_AdjustStock
     */
    public function createAdjustment($adjustData, $updateCatalog = true)
    {
        $adjustStock = Mage::getModel('inventorysuccess/adjustStock');;

        /* create stock adjustment, require products, require qty changed */
        Magestore_Coresuccess_Model_Service::adjustStockService()->createAdjustment($adjustStock, $adjustData);

        /* created adjuststock or not */
        if ($adjustStock->getId()) {
            /* complete stock adjustment */
            Magestore_Coresuccess_Model_Service::adjustStockService()->complete($adjustStock, $updateCatalog);
        }
        return $adjustStock;
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transferStock
     * @param array $products
     */
    public function createReturnTransferActivity($transferStock, $products)
    {
        foreach ($products as &$product) {
            $product['qty'] = $product['adjust_qty'] - $product['old_qty'];
            unset($product['adjust_qty']);
            unset($product['old_qty']);
        }
        $this->saveTransferActivityProduct(
            $transferStock,
            $products,
            Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_RETURNING
        );
    }
}