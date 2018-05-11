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

/**
 * Transferstock Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Transferstock_Product
    extends
    Mage_Core_Model_Abstract
    implements
    Magestore_Inventorysuccess_Model_Service_ProductSelection_StockActivityProductInterface
{
    const TRANSFERSTOCK_PRODUCT_ID = 'transferstock_product_id';
    const TRANSFERSTOCK_ID = 'transferstock_id';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_NAME = 'product_name';
    const PRODUCT_SKU = 'product_sku';
    const QTY = 'qty';
    const QTY_DELIVERED = 'qty_delivered';
    const QTY_RECEIVED = 'qty_received';
    const QTY_RETURNED = 'qty_returned';
    const TRANSFER_TYPE = 'transfer_type';

    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/transferstock_product');
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setTranferstockProductId($value)
    {
        return $this->setData(self::TRANSFERSTOCK_PRODUCT_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setTranferstockId($value)
    {
        return $this->setData(self::TRANSFERSTOCK_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setProductId($value)
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setProductName($value)
    {
        return $this->setData(self::PRODUCT_NAME, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setProductSku($value)
    {
        return $this->setData(self::PRODUCT_SKU, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setQty($value)
    {
        return $this->setData(self::QTY, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setQtyDelivered($value)
    {
        return $this->setData(self::QTY_DELIVERED, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setQtyReceived($value)
    {
        return $this->setData(self::QTY_RECEIVED, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setQtyReturned($value)
    {
        return $this->setData(self::QTY_RETURNED, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setTransferType($value)
    {
        return $this->setData(self::TRANSFER_TYPE, $value);
    }


    /**
     * @return string
     */
    public function getTranferstockId()
    {
        return $this->getData(self::TRANSFERSTOCK_ID);
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
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
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
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * @return string
     */
    public function getQtyDelivered()
    {
        return $this->getData(self::QTY_DELIVERED);
    }

    /**
     * @return string
     */
    public function getQtyReceived()
    {
        return $this->getData(self::QTY_RECEIVED);
    }

    /**
     * @return string
     */
    public function getQtyReturned()
    {
        return $this->getData(self::QTY_RETURNED);
    }

    /**
     * @return string
     */
    public function getTransferType()
    {
        return $this->getData(self::TRANSFER_TYPE);
    }

}