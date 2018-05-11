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
class Magestore_Inventorysuccess_Model_Service_Transfer_TransferActivityService
    extends Magestore_Inventorysuccess_Model_Service_ProductSelection_ProductSelectionService
{

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock_Activity $transferActivity
     */
    public function updateStock($transferActivity)
    {
        $products      = $this->getProducts($transferActivity);
        $transferStock = Mage::getModel('inventorysuccess/transferstock')->load($transferActivity->getTransferstockId());
        $productData   = array();
        if ($products->getSize()) {
            foreach ($products as $product) {
                $productData[$product->getProductId()] = $product->getQty();
            }
        }
        if ($transferActivity->getActivityType() == Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_DELIVERY) {
            $warehouseId = $transferStock->getSourceWarehouseId();
            $this->_getStockChangeService()->issue(
                $warehouseId,
                $productData,
                Magestore_Inventorysuccess_Model_Service_StockMovement_Activity_TransferService::STOCK_MOVEMENT_ACTION_CODE,
                $transferStock->getTransferstockId()
            );
        } else if ($transferActivity->getActivityType() == Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_RECEIVING) {
            $warehouseId = $transferStock->getDesWarehouseId();
            $this->_getStockChangeService()->receive(
                $warehouseId,
                $productData,
                Magestore_Inventorysuccess_Model_Service_StockMovement_Activity_TransferService::STOCK_MOVEMENT_ACTION_CODE,
                $transferStock->getTransferstockId()
            );
        }
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transfer
     * @param array $products
     * @param string $type
     */
    public function updateTransferstockProductQtySummary($transfer, $products, $type)
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product $transferStockProductResoure */
        $transferStockProductResoure = Mage::getResourceModel('inventorysuccess/transferstock_product');
        $qtys                        = array();
        $qtyChanged                  = 0;

        foreach ($products as $index => $product) {
            $qtys[$product['product_id']] = array($product['product_id'] => $product['qty']);
            $qtyChanged += $product['qty'];
        }
        if ($type == Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_RECEIVING) {
            $field = Magestore_Inventorysuccess_Model_Transferstock::QTY_RECEIVED;
        } else if ($type == Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_DELIVERY){
            $field = Magestore_Inventorysuccess_Model_Transferstock::QTY_DELIVERED;
        } else {
            $field = Magestore_Inventorysuccess_Model_Transferstock::QTY_RETURNED;
        }
        $transfer->setData($field, $transfer->getData($field) + $qtyChanged);
        $transfer->save();
        $transferStockProductResoure->updateQty($transfer->getId(), $qtys, $field);
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    protected function _getStockChangeService()
    {
        return Magestore_Coresuccess_Model_Service::stockChangeService();
    }
}