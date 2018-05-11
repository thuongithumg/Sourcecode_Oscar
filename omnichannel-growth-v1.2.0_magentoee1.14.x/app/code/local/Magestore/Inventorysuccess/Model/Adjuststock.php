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
 * Class Magestore_Inventorysuccess_Model_Adjuststock
 */
class Magestore_Inventorysuccess_Model_Adjuststock extends Mage_Core_Model_Abstract
    implements Magestore_Inventorysuccess_Model_Service_ProductSelection_StockActivityInterface
{
    const ADJUSTSTOCK_ID = 'adjuststock_id';
    const ADJUSTSTOCK_CODE = 'adjuststock_code';
    const CONFIRMED_AT = 'confirmed_at';
    const CONFIRMED_BY = 'confirmed_by';
    const CREATED_AT = 'created_at';
    const CREATED_BY = 'created_by';
    const REASON = 'reason';
    const STATUS = 'status';
    const WAREHOUSE_ID = 'warehouse_id';
    const WAREHOUSE_CODE = 'warehouse_code';
    const WAREHOUSE_NAME = 'warehouse_name';
    const KEY_PRODUCTS = 'products';

    /**
     * Constants defined Statuses
     */
    const STATUS_PENDING = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_CANCELED = 2;

    /**
     * Prefix code (using for generate the adjustment code)
     */
    const PREFIX_CODE = 'ADJ';

    /**
     * Magestore_Inventorysuccess_Model_Adjuststock constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/adjuststock');
    }

    /**
     * @return mixed
     */
    public function getAdjustStockId()
    {
        return $this->getData(self::ADJUSTSTOCK_ID);
    }

    /**
     * 
     * @return string
     */
    public function getAdjustStockCode()
    {
        return $this->getData(self::ADJUSTSTOCK_CODE);
    }
    
    /**
     * 
     * @return string
     */
    public function getConfirmedAt()
    {
        return $this->getData(self::CONFIRMED_AT);
    }
    
    /**
     * 
     * @return string
     */
    public function getConfirmedBy()
    {
        return $this->getData(self::CONFIRMED_BY);
    }

    /**
     * 
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * 
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->getData(self::CREATED_BY);
    }

    /**
     * 
     * @return string
     */
    public function getReason()
    {
        return $this->getData(self::REASON);
    }

    /**
     * 
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * 
     * @return int
     */
    public function getWarehouseId()
    {
        return $this->getData(self::WAREHOUSE_ID);
    }

    /**
     * 
     * @return string
     */
    public function getWarehouseCode()
    {
        return $this->getData(self::WAREHOUSE_CODE);
    }    

    /**
     * 
     * @return string
     */
    public function getWarehouseName()
    {
        return $this->getData(self::WAREHOUSE_NAME);
    }

    /**
     *
     * @param string $adjustStockId
     * @return $this
     */
    public function setAdjustStockId($adjustStockId)
    {
        return $this->setData(self::ADJUSTSTOCK_ID, $adjustStockId);
    }
    
    /**
     * 
     * @param string $adjustStockCode
     * @return $this
     */
    public function setAdjustStockCode($adjustStockCode)
    {
        return $this->setData(self::ADJUSTSTOCK_CODE, $adjustStockCode);
    }

    /**
     * 
     * @param string $confirmedAt
     * @return $this
     */
    public function setConfirmedAt($confirmedAt)
    {
        return $this->setData(self::CONFIRMED_AT, $confirmedAt);
    }

    /**
     * 
     * @param string $confirmedBy
     * @return $this
     */
    public function setConfirmedBy($confirmedBy)
    {
        return $this->setData(self::CONFIRMED_BY, $confirmedBy);
    }

    /**
     * 
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * 
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        return $this->setData(self::CREATED_BY, $createdBy);
    }

    /**
     * 
     * @param string $reason
     * @return $this
     */
    public function setReason($reason)
    {
        return $this->setData(self::REASON, $reason);
    }

    /**
     * 
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * 
     * @param int $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }

    /**
     * 
     * @param string $warehouseName
     * @return int 
     */
    public function setWarehouseName($warehouseName)
    {
        return $this->setData(self::WAREHOUSE_NAME, $warehouseName);
    }

    /**
     * 
     * @param string $warehouseCode
     * @return $this
     */
    public function setWarehouseCode($warehouseCode)
    {
        return $this->setData(self::WAREHOUSE_CODE, $warehouseCode);
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_ProductSelection_StockActivityProductInterface
     */
    public function getSelectionProductModel()
    {
        return Mage::getModel('inventorysuccess/adjuststock_product');
    }
}
