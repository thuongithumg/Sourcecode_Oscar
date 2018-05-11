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

class Magestore_Inventorysuccess_Model_Stocktaking extends Mage_Core_Model_Abstract
    implements Magestore_Inventorysuccess_Model_Service_ProductSelection_StockActivityInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const STOCKTAKING_ID = 'stocktaking_id';
    const STOCKTAKING_CODE = 'stocktaking_code';
    const CONFIRMED_AT = 'confirmed_at';
    const CONFIRMED_BY = 'confirmed_by';
    const VERIFIED_BY = 'verified_by';
    const VERIFIED_AT = 'verified_at';
    const CREATED_AT = 'created_at';
    const CREATED_BY = 'created_by';
    const STOCKTAKE_AT = 'stocktake_at';
    const REASON = 'reason';
    const PARTICIPANTS = 'participants';
    const STATUS = 'status';
    const WAREHOUSE_ID = 'warehouse_id';
    const WAREHOUSE_CODE = 'warehouse_code';
    const WAREHOUSE_NAME = 'warehouse_name';

    /**
     * Constants defined Statuses
     */
    const STATUS_PENDING = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_VERIFIED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_NEW = 5;

    /**
     * Prefix code (using for generate the stocktaking code)
     */
    const PREFIX_CODE = 'STA';

    /**
     * construct
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/stocktaking');
    }

    /**
     *
     * @return int
     */
    public function getStocktakingId()
    {
        return $this->getData(self::STOCKTAKING_ID);
    }

    /**
     *
     * @return string
     */
    public function getStocktakingCode()
    {
        return $this->getData(self::STOCKTAKING_CODE);
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
     * @return string
     */
    public function getVerifiedBy()
    {
        return $this->getData(self::VERIFIED_BY);
    }

    /**
     * @return string
     */
    public function getVerifiedAt()
    {
        return $this->getData(self::VERIFIED_AT);
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
     * Get Participants
     *
     * @return string|null
     */
    public function getParticipants()
    {
        return $this->getData(self::PARTICIPANTS);
    }

    /**
     *
     * @return string
     */
    public function getStocktakeAt()
    {
        return $this->getData(self::STOCKTAKE_AT);
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
     * @param int $stocktakingId
     * @return $this
     */
    public function setStocktakingId($stocktakingId)
    {
        return $this->setData(self::STOCKTAKING_ID, $stocktakingId);
    }

    /**
     *
     * @param string $stocktakingCode
     * @return $this
     */
    public function setStocktakingCode($stocktakingCode)
    {
        return $this->setData(self::STOCKTAKING_CODE, $stocktakingCode);
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
     * @param string $verifiedBy
     * @return $this
     */
    public function setVerifiedBy($verifiedBy)
    {
        return $this->setData(self::VERIFIED_BY, $verifiedBy);
    }

    /**
     *
     * @param string $verifiedat
     * @return $this
     */
    public function setVerifiedAt($verifiedAt)
    {
        return $this->setData(self::VERIFIED_AT, $verifiedAt);
    }

    /**
     *
     * @param string $participants
     * @return $this
     */
    public function setParticipants($participants)
    {
        return $this->setData(self::PARTICIPANTS, $participants);
    }

    /**
     *
     * @param string $stocktakeAt
     * @return $this
     */
    public function setStocktakeAt($stocktakeAt)
    {
        return $this->setData(self::STOCKTAKE_AT, $stocktakeAt);
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
     * @return $this
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
        return Mage::getModel('inventorysuccess/stocktaking_product');
    }

}
