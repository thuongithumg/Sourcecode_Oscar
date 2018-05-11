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
class Magestore_Inventorysuccess_Model_Service_Transfer_ImportService
{
    /**
     * @param $file
     * @param $importType
     * @return array
     * @throws Exception
     */
    public function importFromCsvFile(
        $file,
        $importType
    ) {
        $helper = Mage::helper('inventorysuccess');
        if ( !isset($file['tmp_name']) ) {
            throw new Exception($helper->__('Invalid file upload attempt.'));
        }
        $csvObject            = new Varien_File_Csv();
        $importProductRawData = $csvObject->getData($file['tmp_name']);
        $fileFields           = $importProductRawData[0];
        $validFields          = $this->_filterFileFields($fileFields);
        $invalidFields        = array_diff_key($fileFields, $validFields);
        $importProductData    = $this->_filterImportProductData($importProductRawData, $invalidFields, $validFields);
        $dataToImport         = array();
        $invalidData          = array(array('SKU', 'QTY'));

        if ( !count($importProductData) ) {
            $invalidData = $importProductRawData;
        }
        $isBarcode = strtoupper($importProductData[0][0]) == "BARCODE";
        foreach ( $importProductData as $rowIndex => $dataRow ) {
            // skip headers
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
            if ( $productModel->getId() && isset($dataRow[1]) && is_numeric($dataRow[1]) && $dataRow[1] && $dataRow[1] > 0 ) {
                $productData                          = array(
                    'product_id'   => $productModel->getId(),
                    'product_name' => $productModel->getName(),
                    'product_sku'  => $productSku,
                    "qty"          => $dataRow[1],
                );
                $dataToImport[$productModel->getId()] = $productData;
            } else {
                $invalidData[] = $dataRow;
            }
        }

        if ( count($invalidData) > 1 ) {
            $this->createInvalidFile($invalidData, $importType);
        }
        return $dataToImport;
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
        // process title-related fields that are located right after required fields with store code as field name)
        for ( $index = $requiredFieldsNum; $index < count($fileFields); $index++ ) {
            $titleFieldName         = $fileFields[$index];
            $filteredFields[$index] = $titleFieldName;
        }
        return $filteredFields;
    }

    /**
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return array('SKU', 'QTY');
    }

    /**
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
                throw new Exception('Invalid file format.');
            }
        }
        return $productRawData;
    }

    /**
     * @param $invalidData
     * @param $importType
     * @return $this
     */
    protected function createInvalidFile(
        $invalidData,
        $importType
    ) {
        /** Create file */
        $uploader = new Varien_File_Csv();
        $fileDir  = Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . 'invalid.csv';
        switch ( $importType ) {
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_SEND:
                $fileDir = Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_SEND;
                break;
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_SEND_RECEIVING:
                $fileDir = Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_SEND_RECEIVING;
                break;
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_REQUEST:
                $fileDir = Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_REQUEST;
                break;
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_REQUEST_DELIVERY:
                $fileDir = Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_REQUEST_DELIVERY;
                break;
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_REQUEST_RECEIVING:
                $fileDir = Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_REQUEST_RECEIVING;
                break;
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_EXTERNAL_TO:
                $fileDir = Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_EXTERNAL_TO;
                break;
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_EXTERNAL_FROM:
                $fileDir = Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_EXTERNAL_FROM;
                break;
        }
        if ( !file_exists(Mage::getBaseDir('media') . DS . 'inventorysuccess') ) {
            mkdir(Mage::getBaseDir('media') . DS . 'inventorysuccess');
        }
        $uploader->saveData($fileDir, $invalidData);
        /** add Message */
        Mage::getSingleton('adminhtml/session')->setData('import_type', $importType);
        Mage::getSingleton('adminhtml/session')->setData('error_import', true);
        Mage::getSingleton('adminhtml/session')->setData('sku_invalid', count($invalidData) - 1);
        return $this;
    }
}