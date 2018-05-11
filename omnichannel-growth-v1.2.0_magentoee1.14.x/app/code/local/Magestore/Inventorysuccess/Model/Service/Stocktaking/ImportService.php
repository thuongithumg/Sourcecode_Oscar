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
class Magestore_Inventorysuccess_Model_Service_Stocktaking_ImportService
{
    /**
     * import csv file
     *
     * @param $file
     * @param string $stocktakingId
     * @param string $status
     *
     * @return mixed
     * @throws Mage::throwException
     */
    public function importFromCsvFile(
        $file,
        $stocktakingId = '',
        $status = '0'
    ) {
        if ( !isset($file['tmp_name']) ) {
            Mage::throwException(Mage::helper('inventorysuccess')->__('Invalid file upload attempt.'));
        }
        $csvObject            = new Varien_File_Csv();
        $importProductRawData = $csvObject->getData($file['tmp_name']);
        $fileFields           = $importProductRawData[0];
        $validFields          = $this->_filterFileFields($fileFields, $status);
        $invalidFields        = array_diff_key($fileFields, $validFields);
        $importProductData    = $this->_filterImportProductData($importProductRawData, $invalidFields, $validFields);
        $stocktaking          = Mage::getModel('inventorysuccess/stocktaking');
        $stocktaking          = $stocktaking->load($stocktakingId);
        $stocktakingData      = array();
        if ( $stocktaking->getId() ) {
            $stocktakingData                     = $this->getDataFromFile($importProductData, $status, $stocktaking->getData('warehouse_id'));
            $stocktakingData['warehouse_id']     = $stocktaking->getData('warehouse_id');
            $stocktakingData['stocktaking_code'] = $stocktaking->getData('stocktaking_code');
            $stocktakingData['warehouse_code']   = $stocktaking->getData('warehouse_code');
            $stocktakingData['warehouse_name']   = $stocktaking->getData('warehouse_name');
            $stocktakingData['reason']           = $stocktaking->getData('reason');
            $stocktakingData['created_at']       = $stocktaking->getData('created_at');
            $stocktakingData['created_by']       = $stocktaking->getData('created_by');
            $stocktakingData['participants']     = $stocktaking->getData('participants');
            $stocktakingData['stocktake_at']     = $stocktaking->getData('stocktake_at');
        }
        $stocktakingService = Magestore_Coresuccess_Model_Service::stocktakingService();
        $stocktakingService->createStocktaking($stocktaking, $stocktakingData);
    }

    /**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $importProductData
     * @param int $status
     * @param int $wareHouseId
     *
     * @return array
     */
    protected function getDataFromFile(
        $importProductData,
        $status,
        $wareHouseId
    ) {
        $stocktakingData = array();
        $invalidData     = array();
        $isBarcode       = false;
        $isBarcode       = strtoupper($importProductData[0][0]) == "BARCODE";
        if ( $status == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING ) {
            foreach ( $importProductData as $rowIndex => $dataRow ) {
                if ( $rowIndex == 0 ) {
                    continue;
                }
                $productCode           = $dataRow[0];
                $warehouseStockService = Magestore_Coresuccess_Model_Service::warehouseStockService();
                $productCollection     = $warehouseStockService->getStocks($wareHouseId);
                if ( $isBarcode ) {
                    $productCollection->addBarcodeToSelect();
                    $productCollection->addBarcodeToFilter($productCode);
                }
                if ( !$isBarcode ) {
                    $productCollection->addFieldToFilter('sku', $productCode);
                }

                if ( $productCollection->getSize() ) {
                    $productModel                                               = $productCollection->setPageSize(1)->setCurPage(1)->getFirstItem();
                    $productData                                                = array();
                    $productData['product_sku']                                 = $productModel->getSku();
                    $productData['product_name']                                = $productModel->getName();
                    $productData['stocktaking_qty']                             = 0;
                    $stocktakingData['products'][$productModel->getProductId()] = $productData;
                } else {
                    $invalidData[] = array($dataRow[0]);
                }
            }
        }
        if ( $status == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING ) {
            foreach ( $importProductData as $rowIndex => $dataRow ) {
                if ( $rowIndex == 0 ) {
                    if ( strtoupper($dataRow[0]) == 'BARCODE' ) {
                        $isBarcode = true;
                    }
                    continue;
                }
                $productCode           = $dataRow[0];
                $warehouseStockService = Magestore_Coresuccess_Model_Service::warehouseStockService();
                $productCollection     = $warehouseStockService->getStocks($wareHouseId);
                if ( $isBarcode ) {
                    $productCollection->addBarcodeToSelect();
                    $productCollection->addBarcodeToFilter($productCode);
                }
                if ( !$isBarcode ) {
                    $productCollection->addFieldToFilter('sku', $productCode);
                }
                if ( $productCollection->getSize() ) {
                    $productModel                = $productCollection->setPageSize(1)->setCurPage(1)->getFirstItem();
                    $coutedProduct               = floatval($dataRow[1]);
                    $productData                 = array();
                    $productData['product_sku']  = $productModel->getSku();
                    $productData['product_name'] = $productModel->getName();
                    //if ( $coutedProduct ) {
                    $productData['stocktaking_qty'] = $coutedProduct;
                    //}
                    $stocktakingData['products'][$productModel->getProductId()] = $productData;
                } else {
                    $invalidData[] = $dataRow;
                }
            }
        }
        if ( count($invalidData, $isBarcode) ) {
            $this->createInvalidStocktakingFile($invalidData, $status);
        }
        return $stocktakingData;
    }

    /**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $fileFields
     * @return string[] filtered fields
     */
    protected function _filterFileFields(
        array $fileFields,
        $status
    ) {
        $filteredFields    = $this->getRequiredCsvFields($status);
        $requiredFieldsNum = count($this->getRequiredCsvFields($status));
        $fileFieldsNum     = count($fileFields);
        for ( $index = $requiredFieldsNum; $index < $fileFieldsNum; $index++ ) {
            $titleFieldName         = $fileFields[$index];
            $filteredFields[$index] = $titleFieldName;
        }
        return $filteredFields;
    }

    /**
     * create stocktaking invalid file
     *
     * @param array
     * @return mixed
     */
    protected function createInvalidStocktakingFile(
        $invalidStocktakingData,
        $status
    ) {
        Mage::getSingleton('adminhtml/session')->setData('import_type',
                                                         Magestore_Inventorysuccess_Model_ImportType::TYPE_STOCKTAKING);
        Mage::getSingleton('adminhtml/session')->setData('error_import', true);
        Mage::getSingleton('adminhtml/session')->setData('sku_invalid', count($invalidStocktakingData));
        $data     = array(
            $this->getRequiredCsvFields($status),
        );
        $data     = array_merge($data, $invalidStocktakingData);
        $uploader = new Varien_File_Csv();
        $fileName = Magestore_Inventorysuccess_Model_ImportType::INVALID_STOCKTAKING;
        $fileDir  = Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . $fileName;
        $uploader->saveData($fileDir, $data);
    }

    /**
     * prepare fields
     *
     * @return array
     */
    public function getRequiredCsvFields( $status )
    {
        if ( $status == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING ) {
            return array(
                0 => Mage::helper('inventorysuccess')->__('SKU'),
            );
        }
        if ( $status == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING ) {
            return array(
                0 => Mage::helper('inventorysuccess')->__('SKU'),
                1 => Mage::helper('inventorysuccess')->__('QTY'),
            );
        }
    }

    /**
     * filter file import
     *
     * @param array $productRawData
     * @param array $invalidFields
     * @param array $validFields
     * @return array
     * @throws Exception
     */
    protected function _filterImportProductData(
        array $productRawData,
        array $invalidFields,
        array $validFields
    ) {
        $validFieldsNum = count($validFields);
        foreach ( $productRawData as $rowIndex => $dataRow ) {
            // unset invalid fields from data row
            foreach ( $dataRow as $fieldIndex => $fieldValue ) {
                if ( isset($invalidFields[$fieldIndex]) ) {
                    unset($productRawData[$rowIndex][$fieldIndex]);
                }
            }
            // check if number of fields in row match with number of valid fields
            if ( count($productRawData[$rowIndex]) != $validFieldsNum ) {
                Mage::throwException(Mage::helper('inventorysuccess')->__('Invalid file format.'));
            }
        }
        return $productRawData;
    }

    /**
     * get base media dir
     *
     * @return string
     */
    public function getBaseDirMedia()
    {
        return Mage::getBaseDir('media');
    }
}
