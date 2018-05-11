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


class Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_Item_ImportService
    extends Magestore_Purchaseordersuccess_Model_Service_AbstractService
{
    /**
     * Get content of csv sample file
     *
     * @var int $supplierId
     * @return string
     */
    public function getSampleCSV($supplierId)
    {
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::registry('current_purchase_order');
        $csv = '';
        $data = array();
        $columns = array('PRODUCT_SKU', 'COST', "TAX", 'DISCOUNT', 'QTY_ORDERRED');
        /* prepare data */
        $products = Mage::getModel('suppliersuccess/supplier_product')->getCollection()
            ->addFieldToFilter('supplier_id', $supplierId)
            ->setPageSize(10)
            ->setCurPage(1);
        if($products->getSize()) {
            /** @var Magestore_Suppliersuccess_Model_Supplier_Product $product */
            foreach($products as $product) {
                $data[] = array(
                    $product->getProductSku(), 
                    $product->getCost() * $purchaseOrder->getCurrencyRate(), 
                    $product->getTax(), 
                    0, 
                    1
                );
            }
        }

        /* bind data to $csv */
        $csv.= implode(',', $columns)."\n";
        foreach($data as $row) {
            $csv.= implode(',', $row)."\n";
        }
        return $csv;
    }

    /**
     * Get content of csv sample file
     *
     * @var int $purchaseId
     * @return string
     */
    public function getSampleReceivedImportCSV($purchaseId)
    {
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::registry('current_purchase_order');
        $csv = '';
        $data = array();
        $columns = array('PRODUCT_SKU', 'RECEIVED_QTY');
        /* prepare data */
        $products = Mage::getModel('purchaseordersuccess/purchaseorder_item')->getCollection()
            ->addFieldToFilter('purchase_order_id', $purchaseId)
            ->setPageSize(5)
            ->setCurPage(1);
        if($products->getSize()) {
            /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $product */
            foreach($products as $product) {
                $data[] = array(
                    $product->getProductSku(),
                    rand(1,5)
                );
            }
        }

        /* bind data to $csv */
        $csv.= implode(',', $columns)."\n";
        foreach($data as $row) {
            $csv.= implode(',', $row)."\n";
        }
        return $csv;
    }

    /**
     * Get content of csv sample file
     *
     * @var int $purchaseId
     * @return string
     */
    public function getSampleTransferredImportCSV($purchaseId)
    {
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::registry('current_purchase_order');
        $csv = '';
        $data = array();
        $columns = array('PRODUCT_SKU', 'TRANSFERRED_QTY');
        /* prepare data */
        $products = Mage::getModel('purchaseordersuccess/purchaseorder_item')->getCollection()
            ->addFieldToFilter('purchase_order_id', $purchaseId)
            ->setPageSize(5)
            ->setCurPage(1);
        if($products->getSize()) {
            /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $product */
            foreach($products as $product) {
                $data[] = array(
                    $product->getProductSku(),
                    (int)$product->getQtyReceived()
                );
            }
        }

        /* bind data to $csv */
        $csv.= implode(',', $columns)."\n";
        foreach($data as $row) {
            $csv.= implode(',', $row)."\n";
        }
        return $csv;
    }

    /**
     * Get content of csv sample file
     *
     * @var int $purchaseId
     * @return string
     */
    public function getSampleInvoiceImportCSV($purchaseId)
    {
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::registry('current_purchase_order');
        $csv = '';
        $data = array();
        $columns = array('PRODUCT_SKU', 'BILL_QTY', 'UNIT_PRICE', 'TAX', 'DISCOUNT');
        /* prepare data */
        $products = Mage::getModel('purchaseordersuccess/purchaseorder_item')->getCollection()
            ->addFieldToFilter('purchase_order_id', $purchaseId)
            ->setPageSize(5)
            ->setCurPage(1);
        if($products->getSize()) {
            /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $product */
            foreach($products as $product) {
                $data[] = array(
                    $product->getProductSku(),
                    (int)$product->getQtyReceived(),
                    rand(1,10),
                    rand(1,10),
                    rand(1,10)
                );
            }
        }
        /* bind data to $csv */
        $csv.= implode(',', $columns)."\n";
        foreach($data as $row) {
            $csv.= implode(',', $row)."\n";
        }
        return $csv;
    }

    /**
     * Get content of csv sample file
     *
     * @var int $purchaseId
     * @return string
     */
    public function getSampleReturnedImportCSV($purchaseId)
    {
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::registry('current_purchase_order');
        $csv = '';
        $data = array();
        $columns = array('PRODUCT_SKU', 'RETURN_QTY');
        /* prepare data */
        $products = Mage::getModel('purchaseordersuccess/purchaseorder_item')->getCollection()
            ->addFieldToFilter('purchase_order_id', $purchaseId)
            ->setPageSize(5)
            ->setCurPage(1);
        if($products->getSize()) {
            /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $product */
            foreach($products as $product) {
                $data[] = array(
                    $product->getProductSku(),
                    (int)$product->getQtyReceived()
                );
            }
        }
        /* bind data to $csv */
        $csv.= implode(',', $columns)."\n";
        foreach($data as $row) {
            $csv.= implode(',', $row)."\n";
        }
        return $csv;
    }

    /**
     *
     * @param string $csvFile
     * @return array
     */
    public function parseCSVfile($csvFile)
    {
        $csvAdapter =  new Varien_File_Csv();
        $data = $csvAdapter->getData($csvFile);
        $productData = array();
        if(count($data)) {
            $fields = array();
            foreach ($data as $col => $row) {
                $purchaseProduct = array();
                if ($col == 0) {
                    if (count($row)) {
                        foreach ($row as $index => $cell)
                            $fields[$index] = (string) $cell;
                    }
                }elseif ($col > 0) {
                    if (count($row)) {
                        foreach ($row as $index => $cell) {
                            if (isset($fields[$index])) {
                                $field = strtolower($fields[$index]);
                                $purchaseProduct[$field] = $cell;
                            }
                        }
                        $productData[$row[0]] = $purchaseProduct;
                    }
                }
            }
        }
        return $productData;
    }

    /**
     * process import products
     *
     * @param string $csvFile
     * @param int $supplierId
     */
    public function importFromCsvFile($csvFile, $supplierId)
    {
        $productData = $this->parseCSVfile($csvFile);
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::registry('current_purchase_order');
        /* check & load product_id */
        $importData = array();
        if(count($productData)) {
            if(Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
                $products = Mage::getResourceModel('suppliersuccess/supplier_product_collection')
                    ->addFieldToFilter('product_sku', array('in' => array_keys($productData)))
                    ->addFieldToFilter('supplier_id', $supplierId);
            } else {
                /** @var Mage_Catalog_Model_Resource_Product_Collection $products */
                $products = Mage::getResourceModel('catalog/product_collection');
                $products->addAttributeToSelect('name');
                $products->addFieldToFilter('sku', array('in' => array_keys($productData)));
            }
            /** @var Magestore_Suppliersuccess_Model_Supplier_Product $product */
            foreach ($products as $product){
                if(Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
                    $productId = $product->getProductId();
                    $productSku = $product->getProductSku();
                    $productSupplierSku = $product->getProductSupplierSku();
                    $productName = $product->getProductName();
                    $productCost = $product->getCost();
                } else {
                    /** @var Mage_Catalog_Model_Product $product */
                    $productId = $product->getId();
                    $productSku = $product->getSku();
                    $productSupplierSku = '';
                    $productName = $product->getName();
                    $productCost = 0;
                }
                if(isset($productData[$productSku])){
                    $data = $productData[$productSku];
                    if($data[PurchaseorderItem::QTY_ORDERRED] < 0) {
                        $data[PurchaseorderItem::QTY_ORDERRED] = 0;
                    }
                    $data[PurchaseorderItem::PURCHASE_ORDER_ID] = $purchaseOrder->getPurchaseOrderId();
                    $data[PurchaseorderItem::PRODUCT_ID] = $productId;
                    $data[PurchaseorderItem::PRODUCT_NAME] = $productName;
                    $data[PurchaseorderItem::PRODUCT_SUPPLIER_SKU] = $productSupplierSku;
                    $data[PurchaseorderItem::ORIGINAL_COST] = $productCost * $purchaseOrder->getCurrencyRate();
                    $importData[$productId] = $data;
                }
            }
        }
        if(count($importData)){
            Magestore_Coresuccess_Model_Service::purchaseorderService()->addProducts($purchaseOrder, $importData);
        }
        return count($importData);
    }

    /**
     * process import products
     *
     * @param string $csvFile
     * @param int $supplierId
     */
    public function getCSVData($csvFile)
    {
        return $this->parseCSVfile($csvFile);
    }
}