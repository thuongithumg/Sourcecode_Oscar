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
class Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_Item_ReceivedService
    extends Magestore_Purchaseordersuccess_Model_Service_AbstractService
{
    /**
     * @param array $params
     * @return array
     */
    public function processReceivedData($params = array())
    {
        $result = array();
        foreach ($params as $productId => $itemData) {
            if ($itemData['receive_qty'] > 0)
                $result[$productId] = $itemData['receive_qty'];
        }
        return $result;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $purchaseItem
     * @param float|null $receivedQty
     * @return float
     */
    public function getQtyReceived($purchaseItem, $receivedQty = null)
    {
        $qty = $purchaseItem->getQtyOrderred() - $purchaseItem->getQtyReceived();
        if (!$receivedQty || $receivedQty > $qty)
            $receivedQty = $qty;
        return $receivedQty;
    }

    /**
     * Prepare received item
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseItem
     * @param int|null $receivedQty
     * @param string|null $receivedTime
     * @param string|null $createdBy
     * @return Magestore_Purchaseordersuccess_Model_Purchaseorder_Item_Received
     */
    public function prepareItemReceived(
        $purchaseItem, $receivedQty = null, $receivedTime = null, $createdBy = null
    )
    {
        $receivedQty = $this->getQtyReceived($purchaseItem, $receivedQty);
        return Mage::getModel('purchaseordersuccess/purchaseorder_item_received')
            ->setPurchaseOrderItemId($purchaseItem->getPurchaseOrderItemId())
            ->setQtyReceived($receivedQty)
            ->setReceivedAt($receivedTime)
            ->setCreatedBy($createdBy)
            ->setId(null);
    }

    /**
     * Receive an purchase item by purchase item and qty
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $purchaseItem
     * @param float|null $receivedQty
     * @param string|null $receivedTime
     * @param string|null $createdBy
     * @return bool
     */
    public function receiveItem(
        $purchaseOrder, $purchaseItem, $receivedQty = null, $receivedTime = null, $createdBy = null, $updateStock = false
    )
    {
        $receivedQty = $this->getQtyReceived($purchaseItem, $receivedQty);
        if ($receivedQty == 0)
            return true;
        $itemReceived = $this->prepareItemReceived($purchaseItem, $receivedQty, $receivedTime, $createdBy);
        try {
            $itemReceived->save();
            $purchaseItem->setQtyReceived($purchaseItem->getQtyReceived() + $receivedQty);
            $purchaseItem->save();
            $purchaseOrder->setTotalQtyReceived($purchaseOrder->getTotalQtyReceived() + $receivedQty);
            if ($updateStock) {
                /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($purchaseItem->getProductId());
                if ($stockItem->getId() > 0 && $stockItem->getManageStock()) {
                    $stockItem->setQty($stockItem->getQty() + $receivedQty);
                    $stockItem->setIsInStock((int)($stockItem->getQty() > 0));
                    $stockItem->save();
                }
            }

            // add product not exist to supplier
            $this->addProductToSupplier($purchaseItem, $purchaseOrder->getSupplierId());
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $purchaseItem
     * @param string $supplierId
     */
    protected function addProductToSupplier($purchaseItem, $supplierId) {
        /** @var Magestore_Suppliersuccess_Model_Service_Supplier_SupplierService $supplierService */
        $supplierService = Mage::getSingleton('suppliersuccess/service_supplier_supplierService');

        /** @var Magestore_Suppliersuccess_Model_Mysql4_Supplier_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel('suppliersuccess/supplier_product_collection')
            ->addFieldToFilter('supplier_id', $supplierId)
            ->addFieldToFilter('product_id', $purchaseItem->getProductId());

        if(!$productCollection->getSize()) {
            $data[$purchaseItem->getProductId()] = [
                "cost" => "0.0000",
                "tax" => "0",
                "product_supplier_sku" => ""
            ];
            /** @var Magestore_Suppliersuccess_Model_Supplier $supplier */
            $supplier = Mage::getModel('suppliersuccess/supplier')->load($supplierId);
            $supplierService->addProductsToSupplier($supplier, $data);
        }
    }

    /*
     * validate imported product to receive
     * @param $product
     * @param $purchase_id
     * @return array
     * */
    public function validateReceivedProductImported($products, $purchase_id){
        $message = 'No item received<br/>';
        $total_received_item = 0;
        $success = false;
        $importableProducts = array();
        foreach ($products as $product){
            if(isset($product['product_sku']) && isset($product['received_qty'])){
                $sku = $product['product_sku'];
                /* import qty must greater than 0 */
                if((int)$product['received_qty'] > 0){
                    $purchaseorder_item = Mage::getModel('purchaseordersuccess/purchaseorder_item')
                        ->getCollection()
                        ->addFieldToFilter('purchase_order_id', $purchase_id)
                        ->addFieldToFilter('product_sku', $sku)
                    ;
                    /* check product orderred */
                    $purchaseorder_item->getSelect()->where('qty_orderred > qty_received');

                    if($purchaseorder_item->getData()){
                        $item = $purchaseorder_item->getData()[0];
                        $qty_orderred =  $item['qty_orderred'];
                        $qty_received = $item['qty_received'];
                        $product_id = $item['product_id'];
                        $curr_qty_receive = (int)$product['received_qty'];

                        /* calculate available receive item */
                        if($qty_orderred > $qty_received){
                            $importableProducts[$product_id] = array('receive_qty' => $curr_qty_receive);
                            $importNumber = ($qty_received + $curr_qty_receive > $qty_orderred) ?
                                $qty_orderred - $qty_received :
                                $curr_qty_receive;
                            $total_received_item += $importNumber;
                        }
                        $success = true;
                    }
                }else{
                    $message .= "Invalid received_qty of <b>". $product['product_sku'] ."</b><br/>";
                }
            }else{
                $message = 'Invalid file upload attempt.';
                break;
            }
        }
        $response = array('success' => $success, 'message' => $message,
                        'total_received_item' => $total_received_item, 'selected_items' => $importableProducts);
        return $response;
    }
}