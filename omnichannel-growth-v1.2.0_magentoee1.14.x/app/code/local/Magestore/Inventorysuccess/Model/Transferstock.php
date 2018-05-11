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
class Magestore_Inventorysuccess_Model_Transferstock
    extends Mage_Core_Model_Abstract
    implements Magestore_Inventorysuccess_Model_Service_ProductSelection_StockActivityInterface
{
    const STATUS_CANCELED       = "canceled";
    const STATUS_PENDING       = "pending";
    const STATUS_PROCESSING    = "processing";
    const STATUS_COMPLETED     = "completed";
    const TYPE_REQUEST         = "request";
    const TYPE_SEND            = "send";
    const TYPE_TO_EXTERNAL     = "to_external";
    const TYPE_FROM_EXTERNAL   = "from_external";
    const TRANSFER_CODE_PREFIX = "TRA";

    const STOCK_MOVEMENT_ACTION_CODE = 'transferstock';
    const STOCK_MOVEMENT_ACTION_LABEL = 'Transfer Stock';

    const TRANSFERSTOCK_ID      = 'transferstock_id';
    const TRANSFERSTOCK_CODE    = 'transferstock_code';
    const SOURCE_WAREHOUSE_ID   = 'source_warehouse_id';
    const SOURCE_WAREHOUSE_CODE = 'source_warehouse_code';
    const DES_WAREHOUSE_ID      = 'des_warehouse_id';
    const DES_WAREHOUSE_CODE    = 'des_warehouse_code';
    const REASON                = 'reason';
    const CREATED_BY            = 'created_by';
    const CREATED_AT            = 'created_at';
    const EXTERNAL_LOCATION     = 'external_location';
    const NOTIFIER_EMAILS       = 'notifier_emails';
    const STATUS                = 'status';
    const SHIPPING_INFO         = 'shipping_info';
    const TYPE                  = 'type';
    const QTY                   = 'qty';
    const QTY_DELIVERED         = 'qty_delivered';
    const QTY_RECEIVED          = 'qty_received';
    const QTY_RETURNED          = 'qty_returned';

    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/transferstock');
    }

    /**
     * get stockactivity-product model
     *
     * @return Magestore_Inventorysuccess_Model_Service_ProductSelection_StockActivityProductInterface
     */
    public function getSelectionProductModel()
    {
        return Mage::getModel('inventorysuccess/transferstock_product');
    }


    /**
     * @return string
     */
    public function getTransferstockId()
    {
        return $this->getData(self::TRANSFERSTOCK_ID);
    }

    /**
     * @return string
     */
    public function getTransferstockCode()
    {
        return $this->getData(self::TRANSFERSTOCK_CODE);
    }

    /**
     * @return string
     */
    public function getSourceWarehouseId()
    {
        return $this->getData(self::SOURCE_WAREHOUSE_ID);
    }

    /**
     * @return string
     */
    public function getSourceWarehouseCode()
    {
        return $this->getData(self::SOURCE_WAREHOUSE_CODE);
    }

    /**
     * @return string
     */
    public function getDesWarehouseId()
    {
        return $this->getData(self::DES_WAREHOUSE_ID);
    }

    /**
     * @return string
     */
    public function getDesWarehouseCode()
    {
        return $this->getData(self::DES_WAREHOUSE_CODE);
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->getData(self::REASON);
    }

    /**
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->getData(self::CREATED_BY);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @return string
     */
    public function getExternalLocation()
    {
        return $this->getData(self::EXTERNAL_LOCATION);
    }

    /**
     * @return string
     */
    public function getNotifierEmails()
    {
        return $this->getData(self::NOTIFIER_EMAILS);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @return string
     */
    public function getShippingInfo()
    {
        return $this->getData(self::SHIPPING_INFO);
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
     * @param $value
     * @return Varien_Object
     */
    public function setTransferstockId($value)
    {
        return $this->setData(self::TRANSFERSTOCK_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setTransferstockCode($value)
    {
        return $this->setData(self::TRANSFERSTOCK_CODE, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setSourceWarehouseId($value)
    {
        return $this->setData(self::SOURCE_WAREHOUSE_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setSourceWarehouseCode($value)
    {
        return $this->setData(self::SOURCE_WAREHOUSE_CODE, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setDesWarehouseId($value)
    {
        return $this->setData(self::DES_WAREHOUSE_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setDesWarehouseCode($value)
    {
        return $this->setData(self::DES_WAREHOUSE_CODE, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setReason($value)
    {
        return $this->setData(self::REASON, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setCreatedBy($value)
    {
        return $this->setData(self::CREATED_BY, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setExternalLocation($value)
    {
        return $this->setData(self::EXTERNAL_LOCATION, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setNotifierEmails($value)
    {
        return $this->setData(self::NOTIFIER_EMAILS, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setShippingInfo($value)
    {
        return $this->setData(self::SHIPPING_INFO, $value);
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
     * @return int
     */
    public function hasReturn(){
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Activity_Collection $transferStock */
        $transferStock = Mage::getModel('inventorysuccess/transferstock_activity')->getCollection()
            ->addFieldToFilter(Magestore_Inventorysuccess_Model_Transferstock_Activity::TRANSFERSTOCK_ID,$this->getId())
            ->addFieldToFilter(Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE,'returning');
        return $transferStock->getSize();
    }

}