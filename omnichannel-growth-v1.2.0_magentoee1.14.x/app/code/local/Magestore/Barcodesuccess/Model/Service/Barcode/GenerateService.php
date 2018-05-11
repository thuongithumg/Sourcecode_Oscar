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
 *   @category    Magestore
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Class Magestore_Barcodesuccess_Model_Service_Barcode_GenerateService
 */
class Magestore_Barcodesuccess_Model_Service_Barcode_GenerateService
{
    /**
     * @param $barcodes
     * @param $historyId
     * @param $generateNew
     * @param $removeOld
     * @return array
     */
    public function generateTypeItem(
        $barcodes,
        $historyId,
        $generateNew = true,
        $removeOld = false
    ) {
        $generateNew = $removeOld ? true : $generateNew;
        $result  = array();
        $unsaved = array();
        foreach ( $barcodes as $key => $data ) {
            if ( $data['qty'] > 1 ) {
                $maxQty      = floatval($data['qty']);
                $data['qty'] = 1;
                for ( $i = 0; $i < $maxQty; $i++ ) {
                    $data['history_id'] = $historyId;
                    $data['barcode']    = $this->generateBarcode();
                    if ( $data['barcode'] == false ) {
                        $result['fail'][] = $data['product_sku'];
                    } else {
                        $result['success'][] = $data['product_sku'];
                        $unsaved[]           = $data;
                    }
                }
            } else {
                $data['history_id'] = $historyId;
                $data['barcode']    = $this->generateBarcode();
                if ( $data['barcode'] == false ) {
                    $result['fail'][] = $data['product_sku'];
                } else {
                    $result['success'][] = $data['product_sku'];
                    $unsaved[]           = $data;
                }
            }
        }
        if ( count($unsaved) > 0 ) {
            $result['success']['count'] = $this->_barcodeService()->saveBarcodes($unsaved, $historyId, $generateNew, $removeOld);
            $this->_getSession()->unsetData('generated_barcodes');
        }
        return $result;
    }

    /**
     * @param $barcodes
     * @param $historyId
     * @param bool $generateNew
     * @param bool $removeOld
     * @return array
     */
    public function generateTypePurchase(
        $barcodes,
        $historyId,
        $generateNew = true,
        $removeOld = false
    ) {
        $result  = array();
        $unsaved = array();
        foreach ( $barcodes as $data ) {
            $data['history_id'] = $historyId;
            $data['barcode']    = $this->generateBarcode();
            if ( $data['barcode'] == false ) {
                $result['fail'][] = $data['product_sku'];
            } else {
                $result['success'][] = $data['product_sku'];
                $unsaved[]           = $data;
            }
        }
        if ( count($unsaved) > 0 ) {
            $result['success']['count'] = $this->_barcodeService()->saveBarcodes($unsaved, $historyId, $generateNew, $removeOld);
            $this->_getSession()->unsetData('generated_barcodes');
        }
        return $result;
    }

    /**
     * generate new barcode
     * @return bool|mixed
     */
    public function generateBarcode()
    {
        $pattern = Mage::helper('barcodesuccess')->getBarcodePattern();
        $pattern = strtoupper($pattern);
        $barcode = preg_replace_callback('#\[([AN]{1,2})\.([0-9]+)\]#', array($this, 'convertExpression'), $pattern);

        $barcodeCollection = Mage::getResourceModel('barcodesuccess/barcode_collection');
        $barcodeCollection->addFieldToFilter('barcode', $barcode);
        $generated = $this->_getSession()->getData('generated_barcodes');
        $generated = (isset($generated)) ? $generated : array();
        if ( $barcodeCollection->getSize() > 0
             || (is_array($generated) && in_array($barcode, $generated))
        ) {
            $count = $this->_getSession()->getData('barcode_existing_count');
            $count = (isset($count)) ? $count + 1 : 1;
            $this->_getSession()->setData('barcode_existing_count', $count);
            if ( $count == 5 ) {
                $barcode = false;
                $this->_getSession()->unsetData('barcode_existing_count');
            } else {
                $barcode = $this->generateBarcode();
            }
        } else {
            $generated[] = $barcode;
            $this->_getSession()->unsetData('barcode_existing_count');
            $this->_getSession()->setData('generated_barcodes', $generated);
        }
        return $barcode;
    }

    /**
     * @param $param
     * @return mixed
     */
    public function convertExpression( $param )
    {
        $alphabet = (strpos($param[1], 'A')) === false ? '' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet .= (strpos($param[1], 'N')) === false ? '' : '0123456789';
        return Mage::helper('core')->getRandomString($param[2], $alphabet);
    }

    /**
     * @return Mage_Adminhtml_Model_Session
     */
    public function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * @return Magestore_Barcodesuccess_Model_Service_Barcode_BarcodeService
     */
    public function _barcodeService()
    {
        return Magestore_Coresuccess_Model_Service::barcodeService();
    }
}