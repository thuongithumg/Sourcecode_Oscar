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
 * Barcodesuccess Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Model_Mysql4_Barcode extends
    Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('barcodesuccess/barcode', Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID);
    }

    /**
     * @param $barcodes
     * @return int
     */
    public function saveBarcodes($barcodes)
    {
        if(!count($barcodes)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('barcodesuccess')->__('There is no barcode data to update')
            );            
            return 0;
        }
        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->beginTransaction();
        try {
            $count = $writeAdapter->insertOnDuplicate($this->getMainTable(), $barcodes);
            $writeAdapter->commit();
        } catch (Exception $e ) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('barcodesuccess')->__($e->getMessage())
            );
            $count = 0;
            $writeAdapter->rollBack();
        }
        return $count;
    }

    /**
     * delete all barcodes has $sku
     * @param $productSku
     * @return int
     */
    public function deleteBarcodesWithSku( $productSku )
    {
        $count        = 0;
        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->beginTransaction();
        try {
            $count = $writeAdapter->delete($this->getMainTable(), Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU . '=' . "'$productSku'");
            $writeAdapter->commit();
        } catch ( \Exception $e ) {
            $writeAdapter->rollBack();
        }
        return $count;
    }
}