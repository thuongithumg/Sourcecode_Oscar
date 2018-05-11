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
 * Class Magestore_Inventorysuccess_Model_Transferstock_Activity
 */
class Magestore_Inventorysuccess_Model_Transferstock_Activity extends
    Mage_Core_Model_Abstract implements
    Magestore_Inventorysuccess_Model_Service_ProductSelection_StockActivityInterface
{
    const ACTIVITY_TYPE_DELIVERY = "delivery";
    const ACTIVITY_TYPE_RECEIVING = "receiving";
    const ACTIVITY_TYPE_RETURNING = "returning";

    const ACTIVITY_ID = 'activity_id';
    const TRANSFERSTOCK_ID = 'transferstock_id';
    const NOTE = 'note';
    const CREATED_BY = 'created_by';
    const CREATED_AT = 'created_at';
    const ACTIVITY_TYPE = 'activity_type';
    const TOTAL_QTY = 'total_qty';


    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/transferstock_activity');
    }

    /**
     * get stockactivity-product model
     *
     * @return Magestore_Inventorysuccess_Model_Service_ProductSelection_StockActivityProductInterface
     */
    public function getSelectionProductModel()
    {
        return Mage::getModel('inventorysuccess/transferstock_activity_product');
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setActivityId($value)
    {
        return $this->setData(self::ACTIVITY_ID, $value);
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
    public function setNote($value)
    {
        return $this->setData(self::NOTE, $value);
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
    public function setCreatedBy($value)
    {
        return $this->setData(self::CREATED_BY, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setActivityType($value)
    {
        return $this->setData(self::ACTIVITY_TYPE, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setTotalQty($value)
    {
        return $this->setData(self::TOTAL_QTY, $value);
    }


    /**
     * @return string
     */
    public function getActivityId()
    {
        return $this->getData(self::ACTIVITY_ID);
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
    public function getNote()
    {
        return $this->getData(self::NOTE);
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
    public function getActivityType()
    {
        return $this->getData(self::ACTIVITY_TYPE);
    }

    /**
     * @return string
     */
    public function getTotalQty()
    {
        return $this->getData(self::TOTAL_QTY);
    }


}
