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
 * Class Magestore_Barcodesuccess_Model_Barcode
 */
class Magestore_Barcodesuccess_Model_Barcode extends
    Mage_Core_Model_Abstract
{

    const BARCODE_ID     = 'barcode_id';
    const BARCODE        = 'barcode';
    const QTY            = 'qty';
    const PRODUCT_ID     = 'product_id';
    const PRODUCT_SKU    = 'product_sku';
    const SUPPLIER_ID    = 'supplier_id';
    const SUPPLIER_CODE  = 'supplier_code';
    const PURCHASED_ID   = 'purchased_id';
    const PURCHASED_TIME = 'purchased_time';
    const HISTORY_ID     = 'history_id';
    const CREATED_AT     = 'created_at';

    public function _construct()
    {
        parent::_construct();
        $this->_init('barcodesuccess/barcode');
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setBarcodeId( $value )
    {
        return $this->setData(self::BARCODE_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setBarcode( $value )
    {
        return $this->setData(self::BARCODE, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setQty( $value )
    {
        return $this->setData(self::QTY, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setProductId( $value )
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setProductSku( $value )
    {
        return $this->setData(self::PRODUCT_SKU, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setSupplierId( $value )
    {
        return $this->setData(self::SUPPLIER_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setSupplierCode( $value )
    {
        return $this->setData(self::SUPPLIER_CODE, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setPurchasedId( $value )
    {
        return $this->setData(self::PURCHASED_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setPurchasedTime( $value )
    {
        return $this->setData(self::PURCHASED_TIME, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setHistoryId( $value )
    {
        return $this->setData(self::HISTORY_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setCreatedAt( $value )
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @return string
     */
    public function getBarcodeId()
    {
        return $this->getData(self::BARCODE_ID);
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->getData(self::BARCODE);
    }

    /**
     * @return string
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @return string
     */
    public function getProductSku()
    {
        return $this->getData(self::PRODUCT_SKU);
    }

    /**
     * @return string
     */
    public function getSupplierId()
    {
        return $this->getData(self::SUPPLIER_ID);
    }

    /**
     * @return string
     */
    public function getSupplierCode()
    {
        return $this->getData(self::SUPPLIER_CODE);
    }

    /**
     * @return string
     */
    public function getPurchasedId()
    {
        return $this->getData(self::PURCHASED_ID);
    }

    /**
     * @return string
     */
    public function getPurchasedTime()
    {
        return $this->getData(self::PURCHASED_TIME);
    }

    /**
     * @return string
     */
    public function getHistoryId()
    {
        return $this->getData(self::HISTORY_ID);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }
}