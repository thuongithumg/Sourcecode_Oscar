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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseorder Service
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Item as PurchaseorderItem;

class Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_ItemService
    extends Magestore_Purchaseordersuccess_Model_Service_AbstractService
{
    /**
     * @var array
     */
    protected $updateFields = array(
        PurchaseOrderItemInterface::COST,
        PurchaseOrderItemInterface::TAX,
        PurchaseOrderItemInterface::DISCOUNT,
        PurchaseOrderItemInterface::QTY_ORDERRED
    );

    /**
     * Get purchase order product collection from purchase order id and product ids
     *
     * @param int $purchaseId
     * @param array $productIds
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection
     */
    public function getProductsByPurchaseOrderId($purchaseId, $productIds = array())
    {
        $collection = Mage::getResourceModel('purchaseordersuccess/purchaseorder_item_collection');
        $collection->addFieldToFilter('purchase_order_id', $purchaseId);
        if (!empty($productIds)) {
            $collection->addFieldToFilter('product_id', array('in' => $productIds));
        }
        return $collection;
    }

    /**
     * @param array $params
     * @return array
     */
    public function processIdsProductModal($params = array())
    {
        if (isset($params['selected'])) {
            return $params['selected'];
        }
        if (isset($params['excluded'])) {
            $supplierProductIds = Magestore_Coresuccess_Model_Service::supplierProductService()
                ->getProductsBySupplierId($params['supplier_id'])
                ->getColumnValues('product_id');
            if ($params['excluded'] == 'false') {
                return $supplierProductIds;
            }
            if (is_array($params['excluded'])) {
                return array_diff($supplierProductIds, $params['excluded']);
            }
        }
        return array();
    }

    /**
     * Process param to save product data
     *
     * @param array $params
     * @return array
     */
    public function processUpdateProductParams($params = array())
    {
        $result = array();
        if (isset($params['selected_products']) && is_string($params['selected_products'])) {
            $selectedProduct = json_decode($params['selected_products'], true);
            foreach ($selectedProduct as $productId => $productData) {
                $result = $this->processProductData($result, $productId, $productData);
            }
        }
        return $result;
    }

    /**
     * Process product data
     *
     * @param int $productId
     * @param array|null $productData
     */
    public function processProductData($result, $productId, $productData)
    {
        if (is_string($productData))
            $productData = json_decode($productData, true);
        foreach ($this->updateFields as $field) {
            if ($productData[$field] != $productData[$field . '_old']) {
                $result[$productId] = $productData;
                return $result;
            }
        }
        return $result;
    }

    /**
     * Add product to purchase order
     *
     * @param string $purchaseId
     * @param array $productData
     * @return bool
     */
    public function addProductToPurchaseOrder($purchaseId, $productsData = array())
    {
        $productIds = array_column($productsData, 'product_id');
        $purchaseProductIds = $this->itemCollectioFactory->create()
            ->addFieldToFilter(PurchaseorderItem::PURCHASE_ORDER_ID, $purchaseId)
            ->addFieldToFilter(PurchaseorderItem::PRODUCT_ID, array('in' => $productIds))
            ->getColumnValues(PurchaseorderItem::PRODUCT_ID);
        $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($purchaseId);
        $purchaseProductsData = $this->prepareProductDataToPurchaseOrder(
            $purchaseId, $productsData, $purchaseProductIds, array(), $purchaseOrder->getCurrencyRate()
        );
        return $this->purchaseOrderItemRepository->addProductsToPurchaseOrder($purchaseProductsData);
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param int $supplierId
     * @param array $productData
     * @return $this
     */
    public function updateProductDataToPurchaseOrder($purchaseOrder, $productsData = array())
    {
        $purchaseId = $purchaseOrder->getPurchaseOrderId();
        if (empty($productsData))
            return $this;
        $purchaseProductData = $this->getProductsByPurchaseOrderId($purchaseId, array_keys($productsData))
            ->getData();
        $productsData = $this->prepareProductDataToPurchaseOrder($purchaseId, $purchaseProductData, array(), $productsData);
        $productsData = $this->updateDefaultProductData($purchaseOrder->getSupplierId(), $productsData);
        foreach ($productsData as $productId => $productData) {
            Mage::getModel('purchaseordersuccess/purchaseorder_item')->create()
                ->addData($productData)
                ->setId($productData[PurchaseorderItem::PURCHASE_ORDER_ITEM_ID])
                ->save();
        }
        return $this;
    }

    /**
     * Prepare data add product to purchase order
     *
     * @param int $purchaseId
     * @param array $productsData
     * @param array $purchaseProductIds
     * @return array
     */
    public function prepareProductDataToPurchaseOrder(
        $purchaseId, $productsData = array(), $purchaseProductIds = array(), $updateData = array(), $rate = 1
    )
    {
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($purchaseId);
        $purchaseProductsData = array();
//        $defaultTax = Magestore_Purchaseordersuccess_Model_Service_Config_TaxAndShipping::getDefaultTax();
        foreach ($productsData as $productData) {
            if (in_array($productData['product_id'], $purchaseProductIds))
                continue;
            $productId = $productData['product_id'];
            $cost = $productData['cost'] * $rate;
            $purchaseProductsData[$productId] = array(
                PurchaseorderItem::PURCHASE_ORDER_ID => $purchaseId,
                PurchaseorderItem::PRODUCT_ID => $productData['product_id'],
                PurchaseorderItem::PRODUCT_SKU => $productData['product_sku'],
                PurchaseorderItem::PRODUCT_NAME => $productData['product_name'],
                PurchaseorderItem::PRODUCT_SUPPLIER_SKU => $productData['product_supplier_sku'],
                PurchaseorderItem::ORIGINAL_COST => $cost,
                PurchaseorderItem::COST => $cost,
                PurchaseorderItem::TAX => $productData['tax'],
//                PurchaseorderItem::TAX => $defaultTax ? $defaultTax : $productData['tax'],
            );
            if (isset($updateData[$productId])) {
                $purchaseProductsData[$productId] = array_merge(
                    $purchaseProductsData[$productId],
                    $updateData[$productId],
                    array(PurchaseorderItem::PURCHASE_ORDER_ITEM_ID => $productData[PurchaseorderItem::PURCHASE_ORDER_ITEM_ID])
                );
            }
        }
        return $purchaseProductsData;
    }

    /**
     * Set default value for product data
     *
     * @param int $supplierId
     * @param array $productData
     * @return array
     */
    public function updateDefaultProductData($supplierId, $productData = array())
    {
//        $defaultTax = Magestore_Purchaseordersuccess_Model_Service_Config_TaxAndShipping::getDefaultTax();
        foreach ($productData as $key => $data) {
//            if ($data[PurchaseorderItem::TAX] == '') {
//                $data[PurchaseorderItem::TAX] = $defaultTax;
//            }
            if ($data[PurchaseorderItem::COST] == '') {
                $cost = Magestore_Coresuccess_Model_Service::supplierProductService()
                    ->getProductsBySupplierId($supplierId, array($data['product_id']))
                    ->getFirstItem()->getCost();
                $data[PurchaseorderItem::COST] = $cost;
            }
            $productData[$key] = $data;
        }
        return $productData;
    }
}