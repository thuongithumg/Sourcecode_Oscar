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
class Magestore_Suppliersuccess_Model_Service_Supplier_ImportService 
{
    
    const SUPPLIER_PRODUCT_IMPORT_DATA = 'suppliersuccess_supplier_product_import_data';
    
    /**
     * Get content of csv sample file
     * 
     * @return string
     */
    public function getSampleCSV()
    {
        $csv = '';
        $data = array();
        $columns = array('PRODUCT_SKU', 'COST', 'TAX', 'PRODUCT_SUPPLIER_SKU');
        /* prepare data */
        $products = Mage::getModel('catalog/product')->getCollection()
                                ->addFieldToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
                                ->addAttributeToSelect('cost')
                                ->setPageSize(10)
                                ->setCurPage(1);
        if($products->getSize()) {
            foreach($products as $product) {
                $data[] = array(
                    'sku' => $product->getSku(),
                    'cost' => $product->getCost() ? $product->getCost() : rand(19, 29),
                    'tax' => 10,
                    'product_supplier_sku' => $product->getSku(),
                );
            }
        } else {
            $data = array(
                array(
                    'sku' => 'msj000',
                    'cost' => 23,
                    'tax' => 10,
                    'product_supplier_sku' => 'msj000'
                ),
                array(
                    'sku' => 'msj001',
                    'cost' => 21,
                    'tax' => 10,
                    'product_supplier_sku' => 'msj001'
                ),
                array(
                    'sku' => 'msj002',
                    'cost' => 19,
                    'tax' => 10,
                    'product_supplier_sku' => 'msj002'
                ),
            );
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
     * @param string $fileName
     */
    public function validateCSV($fileName)
    {
        if(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) != 'csv') {
            throw new Exception(Mage::helper('suppliersuccess')->__('Invalid CSV file uploaded.'));
        }
        return $this;
    }
    
    /**
     * 
     * @param string $csvFile
     * @param string $fileName
     * @return array
     */
    public function parseCSVfile($csvFile, $fileName='')
    {
        $this->validateCSV($fileName);
        $csvAdapter =  new Varien_File_Csv();
        $data = $csvAdapter->getData($csvFile);
        $productData = array();
        if(count($data)) {
            $fields = array();
            foreach ($data as $col => $row) {
                $supplierProduct = array();
                if ($col == 0) {
                    if (count($row)) {
                        foreach ($row as $index => $cell)
                            $fields[$index] = (string) $cell;
                    }
                }elseif ($col > 0) {
                    if (count($row))
                        foreach ($row as $index => $cell) {
                            if (isset($fields[$index])) {
                                $field = strtolower($fields[$index]);
                                $supplierProduct[$field] = $cell;
                            }
                        }
                    $productData[] = $supplierProduct;
                }
            }
        }
        if(!count($productData)) {
            throw new Exception(Mage::helper('suppliersuccess')->__('Invalid file structure or empty file uploaded.'));
        }
        
        return $productData;
    }
    
    /**
     * process import products
     * 
     * @param string $csvFile
     * @param string $fileName
     */
    public function importFromCsvFile($csvFile, $fileName='')
    {
        $productData = $this->parseCSVfile($csvFile, $fileName);
        /* check & load product_id */
        if(count($productData)) {
            $skus = array();
            foreach($productData as $row){
                if(isset($row['product_sku'])) {
                    $skus[] = $row['product_sku'];
                }
            }
            $products = Mage::getModel('catalog/product')->getCollection()
                            ->addFieldToFilter('sku', array('in' => $skus));
            $skuMap = array();
            if($products->getSize()) {
                foreach($products as $product) {
                    $skuMap[$product->getSku()] = $product->getId();
                }
            }
            foreach($productData as &$row){
                if(isset($row['product_sku']) && isset($skuMap[$row['product_sku']])) {
                    $row['id'] = $skuMap[$row['product_sku']];
                }
            }
        }

        $this->setImportProducts($productData);
        return $this;
    }
    
    /**
     * 
     * @param array $products
     * @return Magestore_Suppliersuccess_Model_Service_Supplier_ImportService
     */
    public function setImportProducts($products)
    {
        Mage::getSingleton('adminhtml/session')->setData(self::SUPPLIER_PRODUCT_IMPORT_DATA, $products);
        return $this;
    }
    
    /**
     * 
     * @return Magestore_Suppliersuccess_Model_Service_Supplier_ImportService
     */
    public function resetImportProducts()
    {
        Mage::getSingleton('adminhtml/session')->unsetData(self::SUPPLIER_PRODUCT_IMPORT_DATA);
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public function getImportProducts()
    {
        return Mage::getSingleton('adminhtml/session')->getData(self::SUPPLIER_PRODUCT_IMPORT_DATA);
    }
}