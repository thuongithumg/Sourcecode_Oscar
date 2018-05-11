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
 * Class Magestore_Barcodesuccess_Model_History
 */
class Magestore_Barcodesuccess_Model_History extends
    Mage_Core_Model_Abstract
{
    const  TYPE_GENERATED = 1;
    const  TYPE_IMPORTED  = 2;

    const HISTORY_ID = 'history_id';
    const CREATED_AT = 'created_at';
    const CREATED_BY = 'created_by';
    const REASON     = 'reason';
    const TOTAL_QTY  = 'total_qty';
    const TYPE       = 'type';

    public function _construct()
    {
        parent::_construct();
        $this->_init('barcodesuccess/history');
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
     * @param $value
     * @return Varien_Object
     */
    public function setCreatedBy( $value )
    {
        return $this->setData(self::CREATED_BY, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setReason( $value )
    {
        return $this->setData(self::REASON, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setTotalQty( $value )
    {
        return $this->setData(self::TOTAL_QTY, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setType( $value )
    {
        return $this->setData(self::TYPE, $value);
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
    public function getReason()
    {
        return $this->getData(self::REASON);
    }

    /**
     * @return string
     */
    public function getTotalQty()
    {
        return $this->getData(self::TOTAL_QTY);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }
}