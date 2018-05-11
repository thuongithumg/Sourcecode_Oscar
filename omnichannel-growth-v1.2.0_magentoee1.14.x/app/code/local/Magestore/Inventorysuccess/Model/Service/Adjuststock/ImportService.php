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
class Magestore_Inventorysuccess_Model_Service_Adjuststock_ImportService
{
    /**
     * @param $file
     * @throws \Exception
     * @throws Mage::throwException
     */
    public function importFromCsvFile(
        $file,
        $adjustStockId = false
    ) {
        if ( !isset($file['tmp_name']) ) {
            Mage::throwException(Mage::helper('inventorysuccess')->__('Invalid file upload attempt.'));
        }
        $csvObject            = new Varien_File_Csv();
        $importProductRawData = $csvObject->getData($file['tmp_name']);
        $fileFields           = $importProductRawData[0];
        $validFields          = $this->_filterFileFields($fileFields);
        $invalidFields        = array_diff_key($fileFields, $validFields);
        $importProductData    = $this->_filterImportProductData($importProductRawData, $invalidFields, $validFields);
        $adjustStock          = Mage::getModel('inventorysuccess/adjuststock');
        $adjustStock          = $adjustStock->load($adjustStockId);
        $adjustData           = array();
        $invalidData          = array();
        $qtyKey               = 'adjust_qty';
        if ( Mage::helper('inventorysuccess')->getAdjustStockChange() ) {
            $qtyKey = 'change_qty';
        }

        $isBarcode = strtoupper($importProductData[0][0]) == "BARCODE";
        foreach ( $importProductData as $rowIndex => $dataRow ) {
            if ( $rowIndex == 0 ) {
                continue;
            }
            if ( $isBarcode ) {
                $productSku = Magestore_Coresuccess_Model_Service::barcodeService()->getProductSkuByBarcode($dataRow[0]);
            } else {
                $productSku = $dataRow[0];
            }
            $productModel = Mage::getModel('catalog/product')->getCollection()
                                ->addAttributeToSelect('name')
                                ->addFieldToFilter('sku', $productSku)
                                ->setPageSize(1)->setCurPage(1)->getFirstItem();
            if ( $productModel->getId() && isset($dataRow[1]) &&
                 is_numeric($dataRow[1])
            ) {
                $productNewQty                                  = floatval($dataRow[1]);
                $adjustData['products'][$productModel->getId()] = array(
                    "product_sku"  => $productSku,
                    $qtyKey        => $productNewQty,
                    "product_name" => $productModel->getName(),
                );
            } else {
                $invalidData[] = $dataRow;
            }
        }
        if ( $adjustStock->getId() ) {
            $adjustData['warehouse_id']     = $adjustStock->getData('warehouse_id');
            $adjustData['adjuststock_code'] = $adjustStock->getData('adjuststock_code');
            $adjustData['warehouse_code']   = $adjustStock->getData('warehouse_code');
            $adjustData['warehouse_name']   = $adjustStock->getData('warehouse_name');
            $adjustData['reason']           = $adjustStock->getData('reason');
            $adjustData['created_at']       = $adjustStock->getData('created_at');
            $adjustData['created_by']       = $adjustStock->getData('created_by');
        }

        if ( count($invalidData) ) {
            $this->createInvalidAdjustedFile($invalidData);
        }
        $adjustStockService = Magestore_Coresuccess_Model_Service::adjustStockService();
        $adjustStockService->createAdjustment($adjustStock, $adjustData);
    }

    /**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $fileFields
     * @return string[] filtered fields
     */
    protected function _filterFileFields( array $fileFields )
    {
        $filteredFields    = $this->getRequiredCsvFields();
        $requiredFieldsNum = count($this->getRequiredCsvFields());
        $fileFieldsNum     = count($fileFields);
        for ( $index = $requiredFieldsNum; $index < $fileFieldsNum; $index++ ) {
            $titleFieldName         = $fileFields[$index];
            $filteredFields[$index] = $titleFieldName;
        }
        return $filteredFields;
    }

    /**
     * create adjusted invalid file
     *
     * @param array
     * @return
     */
    protected function createInvalidAdjustedFile( $invalidData )
    {
        Mage::getSingleton('adminhtml/session')->setData('import_type',
                                                         Magestore_Inventorysuccess_Model_ImportType::TYPE_ADJUST_STOCK);
        Mage::getSingleton('adminhtml/session')->setData('error_import', true);
        Mage::getSingleton('adminhtml/session')->setData('sku_invalid', count($invalidData));
        $data     = array(
            $this->getRequiredCsvFields(),
        );
        $data     = array_merge($data, $invalidData);
        $uploader = new Varien_File_Csv();
        $fileName = Magestore_Inventorysuccess_Model_ImportType::INVALID_ADJUST_STOCK;
        $fileDir  = Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . $fileName;
        $uploader->saveData($fileDir, $data);
    }

    /**
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return array(
            0 => Mage::helper('inventorysuccess')->__('SKU'),
            1 => Mage::helper('inventorysuccess')->__('QTY'),
        );
    }

    /**
     * @param array $productRawData
     * @param array $invalidFields
     * @param array $validFields
     * @return array
     * @throws Mage::throwException
     */
    protected function _filterImportProductData(
        array $productRawData,
        array $invalidFields,
        array $validFields
    ) {
        $validFieldsNum = count($validFields);
        foreach ( $productRawData as $rowIndex => $dataRow ) {
            // skip empty rows
            if ( count($dataRow) <= 1 ) {
                unset($productRawData[$rowIndex]);
                continue;
            }
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
}
