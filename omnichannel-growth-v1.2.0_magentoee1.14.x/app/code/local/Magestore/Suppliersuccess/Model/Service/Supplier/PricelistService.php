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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Suppliersuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Model_Service_Supplier_PricelistService
{
    const QUERY_PROCESS            = 'import_pricelist';
    const QUERY_MASSDELETE_PROCESS = 'massdelete_pricelist';
    const QUERY_MASSUPDATE_PROCESS = 'massupdate_pricelist';

    /**
     * @var Magestore_Suppliersuccess_Model_Service_Supplier_ImportService
     */
    protected $importService;

    /**
     * @var Magestore_Coresuccess_Model_Service_QueryProcessorService
     */
    protected $queryProcessorService;

    /**
     *
     */
    public function __construct()
    {
        $this->importService         = Magestore_Coresuccess_Model_Service::supplierImportService();
        $this->queryProcessorService = Magestore_Coresuccess_Model_Service::queryProcessorService();
    }

    /**
     * Get content of csv sample file
     *
     * @return string
     */
    public function getSampleCSV()
    {
        $csv     = '';
        $data    = array();
        $columns = array(
            'SUPPLIER_CODE',
            'PRODUCT_SKU',
            'PRODUCT_SUPPLIER_SKU',
            'MINIMAL_QTY',
            'COST',
            'START_DATE',
            'END_DATE',
        );
        /* prepare data */
        $supplier = Mage::getModel('suppliersuccess/supplier')
                        ->getCollection()
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();
        if ( !$supplier->getId() ) {
            throw new Exception(Mage::helper('suppliersuccess')->__('There is no supplier.'));
        }
        $supplierCode = $supplier->getSupplierCode();
        $products     = Mage::getModel('catalog/product')->getCollection()
                            ->addFieldToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
                            ->addAttributeToSelect('cost')
                            ->setPageSize(10)
                            ->setCurPage(1);
        if ( $products->getSize() ) {
            $qtyRanges = array(10, 100, 200);
            foreach ( $products as $product ) {
                foreach ( $qtyRanges as $qty ) {
                    $data[] = array(
                        'supplier_code'        => $supplierCode,
                        'sku'                  => $product->getSku(),
                        'product_supplier_sku' => $product->getSku(),
                        'minimal_qty'          => $qty,
                        'cost'                 => ceil(($product->getCost() ? $product->getCost() : 29) * (1 - $qty / 1000)) + 0.99,
                        'start_date'           => date('Y-m-d', strtotime('-1 day')),
                        'end_date'             => date('Y-m-d', strtotime('+30 day')),
                    );
                }
            }
        } else {
            $data = array(
                array(
                    'supplier_code'        => $supplierCode,
                    'sku'                  => 'msj000',
                    'product_supplier_sku' => 'msj000',
                    'minimal_qty'          => 10,
                    'cost'                 => 29.99,
                    'start_date'           => date('Y-m-d', strtotime('-1 day')),
                    'end_date'             => date('Y-m-d', strtotime('+30 day')),
                ),
                array(
                    'supplier_code'        => $supplierCode,
                    'sku'                  => 'msj000',
                    'product_supplier_sku' => 'msj000',
                    'minimal_qty'          => 100,
                    'cost'                 => 27.99,
                    'start_date'           => date('Y-m-d', strtotime('-1 day')),
                    'end_date'             => date('Y-m-d', strtotime('+30 day')),
                ),
                array(
                    'supplier_code'        => $supplierCode,
                    'sku'                  => 'msj000',
                    'product_supplier_sku' => 'msj000',
                    'minimal_qty'          => 200,
                    'cost'                 => 24.99,
                    'start_date'           => date('Y-m-d', strtotime('-1 day')),
                    'end_date'             => date('Y-m-d', strtotime('+30 day')),
                ),
            );
        }

        /* bind data to $csv */
        $csv .= implode(',', $columns) . "\n";
        foreach ( $data as $row ) {
            $csv .= implode(',', $row) . "\n";
        }
        return $csv;
    }

    /**
     *
     * @param array $pricelist
     * @return array
     */
    public function prepareImportData( $pricelist )
    {
        $invalidData = array();
        /* check & load product_id, product_name, supplier_name */
        if ( count($pricelist) ) {
            $skus          = array();
            $supplierCodes = array();
            foreach ( $pricelist as $row ) {
                if ( isset($row['product_sku']) ) {
                    $skus[$row['product_sku']] = $row['product_sku'];
                }
                if ( isset($row['supplier_code']) ) {
                    $supplierCodes[$row['supplier_code']] = $row['supplier_code'];
                }
            }
            $products = Mage::getModel('catalog/product')->getCollection()
                            ->addAttributeToSelect('name')
                            ->addFieldToFilter('sku', array('in' => $skus));
            $skuMap   = array();
            if ( $products->getSize() ) {
                foreach ( $products as $product ) {
                    $skuMap[$product->getSku()] = array(
                        'product_id'   => $product->getId(),
                        'product_name' => $product->getName(),
                    );
                }
            }

            $suppliers   = Mage::getModel('suppliersuccess/supplier')->getCollection()
                               ->addFieldToFilter('supplier_code', array('in' => $supplierCodes));
            $supplierMap = array();
            if ( $suppliers->getSize() ) {
                foreach ( $suppliers as $supplier ) {
                    $supplierMap[$supplier->getSupplierCode()] = array(
                        'supplier_id' => $supplier->getId(),
                    );
                }
            }
            foreach ( $pricelist as $index => &$row ) {
                if ( isset($row['product_sku']) && isset($skuMap[$row['product_sku']]) ) {
                    $row['product_id']   = $skuMap[$row['product_sku']]['product_id'];
                    $row['product_name'] = $skuMap[$row['product_sku']]['product_name'];
                } else {
                    $invalidData[] = $pricelist[$index];
                    unset($pricelist[$index]);
                }
                if ( isset($row['supplier_code']) && isset($supplierMap[$row['supplier_code']]) ) {
                    $row['supplier_id'] = $supplierMap[$row['supplier_code']]['supplier_id'];
                }
                $row['updated_at'] = now();
                $row['start_date'] = (isset($row['start_date']) && $row['start_date'])  ? $row['start_date'] : now();
                $row['end_date'] = (isset($row['end_date']) && $row['end_date'])? $row['end_date'] : date('Y-m-d', strtotime('+1 year'));
                $row['start_date'] = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $row['start_date']);
                $row['end_date']   = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $row['end_date']);
                unset($row['supplier_code']);
                if ((!isset($row['supplier_id']) || !$row['supplier_id']) && isset($pricelist[$index])) {
                    $invalidData[] = $pricelist[$index];
                    unset($pricelist[$index]);
                }
            }
        }
        if(count($invalidData)){
            Mage::getSingleton('adminhtml/session')->setData('error_import',true);
            Mage::getSingleton('adminhtml/session')->setData('sku_invalid',count($invalidData));
            Mage::getSingleton('adminhtml/session')->setData('invalid_pricelist',$invalidData);
        } else {
            if(!count($pricelist)) {
                throw new Exception(Mage::helper('suppliersuccess')->__('Invalid file type or empty file uploaded.'));
            }            
        }
        return $pricelist;
    }

    /**
     * @param string $csvFile
     * @param string $fileName
     * @return Magestore_Suppliersuccess_Model_Service_Supplier_PricelistService $this
     * @throws Exception
     */
    public function importFromCsvFile($csvFile , $fileName='')
    {
        /* get data from csv file */
        $pricelist = $this->importService->parseCSVfile($csvFile, $fileName);
        /* add more data to price list */
        $pricelist = $this->prepareImportData($pricelist);
        if ( !count($pricelist) ) {
            throw new Exception(Mage::helper('suppliersuccess')->__('There is no record imported.'));
        }

        /* prepare product data to add to suppliers */
        $supplierProducts = $this->prepareProductDataToAddToSupplier($pricelist);

        /* add query to the processor */
        $this->queryProcessorService->start(self::QUERY_PROCESS);

        /* insert price list records */
        $query = $this->getResource()->prepareMultipleInsertQuery($pricelist);
        $this->queryProcessorService->addQuery($query, self::QUERY_PROCESS);

        /* add products to suppliers */
        $query = $this->getResource()->prepareInsertProductToSupplier($supplierProducts);
        $this->queryProcessorService->addQuery($query, self::QUERY_PROCESS);

        $this->queryProcessorService->process(self::QUERY_PROCESS);
        return $this;
    }

    /**
     *
     * @param array $pricelistIds
     * @return Magestore_Suppliersuccess_Model_Service_Supplier_PricelistService
     */
    public function massRemove( $pricelistIds )
    {
        $query = $this->getResource()->prepareMassDeleteQuery($pricelistIds);
        $this->queryProcessorService->start(self::QUERY_MASSDELETE_PROCESS);
        $this->queryProcessorService->addQuery($query, self::QUERY_MASSDELETE_PROCESS);
        $this->queryProcessorService->process(self::QUERY_MASSDELETE_PROCESS);
        return $this;
    }

    /**
     *
     * @param array $pricelistIds
     * @return Magestore_Suppliersuccess_Model_Service_Supplier_PricelistService
     */
    public function massUpdate( $pricelist )
    {
        $query = $this->getResource()->prepareMassUpdateQuery($pricelist);
        $this->queryProcessorService->start(self::QUERY_MASSUPDATE_PROCESS);
        $this->queryProcessorService->addQuery($query, self::QUERY_MASSUPDATE_PROCESS);
        $this->queryProcessorService->process(self::QUERY_MASSUPDATE_PROCESS);
        return $this;
    }


    /**
     *
     * @param array $pricelist
     * @return array
     */
    public function prepareProductDataToAddToSupplier( $pricelist )
    {
        $supplierProducts = array();
        $products         = array();
        foreach ( $pricelist as $row ) {
            $supplierId         = isset($row['supplier_id']) ? $row['supplier_id'] : null;
            $productId          = isset($row['product_id']) ? $row['product_id'] : null;
            $productSku         = isset($row['product_sku']) ? $row['product_sku'] : null;
            $productName        = isset($row['product_name']) ? $row['product_name'] : null;
            $productSupplierSku = isset($row['product_supplier_sku']) ? $row['product_supplier_sku'] : null;
            $cost               = isset($row['cost']) ? $row['cost'] : 0;
            if ( !$supplierId || !$productId || !$productSku || !$productName || !$productSupplierSku ) {
                continue;
            }
            if ( !isset($supplierProducts[$supplierId][$productId]) ) {
                $supplierProducts[$supplierId][$productId] = array(
                    'supplier_id'          => $supplierId,
                    'product_id'           => $productId,
                    'product_sku'          => $productSku,
                    'product_name'         => $productName,
                    'product_supplier_sku' => $productSupplierSku,
                    'cost'                 => $cost,
                    'updated_at'           => now(),
                );
            } else {
                $supplierProducts[$supplierId][$productId]['cost'] = max($supplierProducts[$supplierId][$productId]['cost'], $cost);
            }
        }
        if ( count($supplierProducts) ) {
            foreach ( $supplierProducts as $supplierId => $supplierProduct ) {
                foreach ( $supplierProduct as $row ) {
                    $row['supplier_id'] = $supplierId;
                    $products[]         = $row;
                }
            }
        }
        return $products;
    }

    /**
     *
     * @return Magestore_Suppliersuccess_Model_Mysql4_Supplier_Pricelist
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('suppliersuccess/supplier_pricelist');
    }
}