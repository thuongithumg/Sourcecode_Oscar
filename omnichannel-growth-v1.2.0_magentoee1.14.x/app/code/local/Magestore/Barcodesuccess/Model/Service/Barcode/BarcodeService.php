<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Class Magestore_Barcodesuccess_Model_Service_Barcode_BarcodeService
 */
class Magestore_Barcodesuccess_Model_Service_Barcode_BarcodeService
{
    /**
     * @param $barcodes
     * @param $historyId
     * @param bool $generateNew
     * @param bool $removeOld
     * @return int
     */
    public function saveBarcodes(
        $barcodes,
        $historyId = 0,
        $generateNew = true,
        $removeOld = false
    ) {
        foreach ( $barcodes as $index => $barcode ) {
            if ( !array_key_exists(Magestore_Barcodesuccess_Model_Barcode::HISTORY_ID, $barcode) ) {
                $barcodes[$index][Magestore_Barcodesuccess_Model_Barcode::HISTORY_ID] = $historyId;
            }
        }
        /** @var Magestore_Barcodesuccess_Model_Mysql4_Barcode $resoureModel */
        $resoureModel = Mage::getResourceModel('barcodesuccess/barcode');
        if ( !$generateNew && $generateNew !== null ) {
            foreach ( $barcodes as $index => $barcode ) {
                $model = Mage::getModel('barcodesuccess/barcode');
                $model->load($barcode[Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU], Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU);
                if ( $model->getId() ) {
                    unset($barcodes[$index]);
                }
            }
        }
        if ( $removeOld ) {
            foreach ( $barcodes as $index => $barcode ) {
                $resoureModel->deleteBarcodesWithSku($barcode[Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU]);
            }
        }
        $count = $resoureModel->saveBarcodes($barcodes);
        if ( !$count && $historyId ) {
            /** if there is no barcode saved. delete history */
            Mage::getModel('barcodesuccess/history')->load($historyId)->delete();
        }
        return $count;
    }

    /**
     * @param $barcodes
     * @param $template
     */
    public function getHtml(
        $barcodes,
        $template
    ) {

    }

    /**
     * @param string $barcode
     * @return string
     */
    public function getProductSkuByBarcode( $barcode )
    {
        /** @var Magestore_Barcodesuccess_Model_Barcode $barcodeModel */
        $barcodeModel = Mage::getModel('barcodesuccess/barcode')->load($barcode, Magestore_Barcodesuccess_Model_Barcode::BARCODE);
        if ( $barcodeModel->getId() ) {
            return $barcodeModel->getProductSku();
        }
        return '';
    }
}