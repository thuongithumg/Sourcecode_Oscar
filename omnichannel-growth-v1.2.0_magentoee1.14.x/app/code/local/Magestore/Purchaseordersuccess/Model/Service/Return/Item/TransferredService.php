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

class Magestore_Purchaseordersuccess_Model_Service_Return_Item_TransferredService
    extends Magestore_Purchaseordersuccess_Model_Service_AbstractService
{
    /**
     * @var Magestore_Inventorysuccess_Model_Service_Transfer_TransferService
     */
    protected $transferStockService;

    public function __construct()
    {
        $this->transferStockService = Magestore_Coresuccess_Model_Service::transferStockService();
    }

    /**
     * @param array $params
     * @return array
     */
    public function processTransferredData($params = array())
    {
        $result = array();
        $productIds = array_keys($params);
        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $products */
        $products = Mage::getResourceModel('catalog/product_collection')
            ->addFieldToFilter('entity_id', $productIds)
            ->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner');
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($products as $product){
            $productId = $product->getId();
            if(in_array($productId, $productIds)){
                if(isset($params[$productId]['transfer_qty']) &&  $params[$productId]['transfer_qty'] > 0) {
                    $params[$productId]['product_id'] = $product->getId();
                    $params[$productId]['qty'] = $params[$productId]['transfer_qty'];
                    $params[$productId]['transfer_qty'] = $params[$productId]['transfer_qty'];
                    $params[$productId]['product_sku'] = $product->getSku();
                    $params[$productId]['product_name'] = $product->getName();
                    $result[$productId] = $params[$productId];
                }
            }
        }
        return $result;
    }

    /**
     * Create an empty transfer stock
     *
     * @param array $param
     * @param string $userName
     * @return Magestore_Inventorysuccess_Model_Transferstock
     */
    public function createTransferStock($param = array(), $userName)
    {
        $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($param['warehouse_id']);
        /** @var Magestore_Purchaseordersuccess_Model_Return $returnRequest */
        $returnRequest = Mage::getModel('purchaseordersuccess/return')->load($param['id']);
        $purchaseCode = $returnRequest->getReturnCode();
        /** @var Magestore_Suppliersuccess_Model_Supplier $supplier */
        $supplier = Mage::getModel('suppliersuccess/supplier')->load($returnRequest->getSupplierId());
        return Mage::getModel('inventorysuccess/transferstock')
            ->setData('transferstock_code', $this->transferStockService->generateCode())
            ->setData('external_location', Mage::helper('purchaseordersuccess')->__('Return to supplier #%s (%s)', $supplier->getSupplierName(), $supplier->getSupplierCode()))
            ->setData('source_warehouse_id', $warehouse->getWarehouseId())
            ->setData('source_warehouse_code', $warehouse->getWarehouseCode())
            ->setData('reason', Mage::helper('purchaseordersuccess')->__('Transfer stock from return request #%s', $purchaseCode))
            ->setData('notifier_emails', '')
            ->setData('status', 'pending')
            ->setData('type', 'to_external')
            ->setData('created_by', $userName)
            ->setData('created_at', $param['transferred_at'])
            ->setId(null)
            ->save();
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transferStock
     * @param array $transferredData
     * @return Magestore_Inventorysuccess_Model_Transferstock
     */
    public function saveTransferStockData($transferStock, $transferredData = array()
    )
    {
        $data = $this->reformatPostData($transferStock, $transferredData);
        $this->transferStockService->saveTransferStockProduct($transferStock, $data);
        $this->transferStockService->updateStock($transferStock);
        $transferStock->setData('status', 'completed');
        return $transferStock->save();
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transferStock
     * @param array $transferredData
     * @return array
     */
    public function reformatPostData($transferStock, $transferredData = array())
    {
        $id = $transferStock->getId();
        $newData = array();
        foreach ($transferredData as $data) {
            $item = array();
            $item['transferstock_id'] = $id;
            $item['product_id'] = $data['product_id'];
            $item['product_name'] = $data['product_name'];
            $item['product_sku'] = $data['product_sku'];
            $item['qty'] = $data['qty'];
            $newData[$data['product_id']] = $item;
        }
        return $newData;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Return_Item $returnItem
     * @param null $returnedQty
     * @return array
     */
    public function setQtyTransferred($returnItem, $transferData = array())
    {
        $qty = $returnItem->getQtyReturned() - $returnItem->getQtyTransferred();
        if (!isset($transferData['transfer_qty']) || $transferData['transfer_qty'] > $qty){
            $transferData['transfer_qty'] = $qty;
            $transferData['qty'] = $qty;
        }
        return $transferData;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Return_Item $returnItem
     * @param array $transferData
     * @param array $params
     * @param null $createdBy
     * @return Magestore_Purchaseordersuccess_Model_Return_Item_Transferred
     */
    public function prepareItemTransferred(
        $returnItem, $transferData = array(), $params = array(), $createdBy = null
    )
    {
        $transferData = $this->setQtyTransferred($returnItem, $transferData);
        return Mage::getModel('purchaseordersuccess/return_item_transferred')
            ->setReturnItemId($returnItem->getReturnItemId())
            ->setQtyTransferred($transferData['transfer_qty'])
            ->setWarehouseId($params['warehouse_id'])
            ->setTransferredAt($params['transferred_at'])
            ->setCreatedBy($createdBy)
            ->setId(null);
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Return $returnOrder
     * @param Magestore_Purchaseordersuccess_Model_Return_Item $returnItem
     * @param null $transferData
     * @param array $params
     * @param null $createdBy
     * @return bool|array
     */
    public function transferItem(
        $returnOrder, $returnItem, $transferData = null, $params = array(), $createdBy = null
    )
    {
        $transferData = $this->setQtyTransferred($returnItem, $transferData);
        $itemTransferred = $this->prepareItemTransferred($returnItem, $transferData, $params, $createdBy);
        try {
            $itemTransferred->save();
            $returnItem->setQtyTransferred($returnItem->getQtyTransferred() + $itemTransferred->getQtyTransferred());
            $returnItem->save();
            $returnOrder->setTotalQtyTransferred($returnOrder->getTotalQtyTransferred() + $itemTransferred->getQtyTransferred());
        } catch (\Exception $e) {
            return false;
        }
        /* add by Kai - update Mac value for sale report */
//        $transferData['cost'] = $returnItem->getCost();
//        $transferData['purchase_order_id'] = $returnItem->getPurchaseOrderId();
        /* end by Kai */
        return $transferData;
    }

    /*
     * validate imported product to transfer
     * @param $product
     * @param $return_id
     * @return array
     * */
    public function validateTransferProductImported($products, $return_id){
        $message = 'No item transferred<br/>';
        $total_transferred_item = 0;
        $success = false;
        $importableProducts = array();
        foreach ($products as $product){
            if(isset($product['product_sku']) && isset($product['transferred_qty'])){
                $sku = $product['product_sku'];
                /* import qty must greater than 0 */
                if((int)$product['transferred_qty'] > 0){
                    $purchaseorder_item = Mage::getModel('purchaseordersuccess/return_item')
                        ->getCollection()
                        ->addFieldToFilter('return_id', $return_id)
                        ->addFieldToFilter('product_sku', $sku)
                    ;
                    /* check product transfer */
                    $purchaseorder_item->getSelect()->where('qty_returned > qty_transferred');
                    if($purchaseorder_item->getData()){
                        $item = $purchaseorder_item->getData()[0];
                        $qty_returned =  $item['qty_returned'];
                        $qty_transferred = $item['qty_transferred'];
                        $product_id = $item['product_id'];
                        $curr_qty_transfer = (int)$product['transferred_qty'];

                        /* calculate available transfer item */
                        if($qty_returned > $qty_transferred){
                            $importableProducts[$product_id] = array('transfer_qty' => $curr_qty_transfer);
                            $importNumber = ($qty_transferred + $curr_qty_transfer > $qty_returned) ?
                                $qty_returned - $qty_transferred :
                                $curr_qty_transfer;
                            $total_transferred_item += $importNumber;
                        }
                        $success = true;
                    }
                }else{
                    $message .= "Invalid transferred_qty of <b>". $product['product_sku'] ."</b><br/>";
                }
            }else{
                $message = 'Invalid file upload attempt.';
                break;
            }
        }
        $response = array('success' => $success, 'message' => $message,
            'total_transferred_item' => $total_transferred_item, 'selected_items' => $importableProducts);
        return $response;
    }
}