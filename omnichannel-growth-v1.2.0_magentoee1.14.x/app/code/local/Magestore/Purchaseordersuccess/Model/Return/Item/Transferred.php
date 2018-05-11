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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseorder Item Transferred Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Return_Item_Transferred extends Mage_Core_Model_Abstract
{
    /**#@+
     * Constants defined for keys of  data array
     */

    const RETURN_ITEM_TRANSFERRED_ID = 'return_item_transferred_id';

    const RETURN_ITEM_ID = 'return_item_id';

    const QTY_TRANSFERRED = 'qty_transferred';

    const WAREHOUSE_ID = 'warehouse_id';

    const TRANSFERRED_AT = 'transferred_at';

    const CREATED_AT = 'created_at';

    const CREATED_BY = 'created_by';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_return_item_transferred';

    /**
     * Initialization
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/return_item_transferred');
    }

    /**
     * Get return order item transferred id
     *
     * @return int
     */
    public function getReturnItemTransferredId() {
        return $this->_getData(self::RETURN_ITEM_TRANSFERRED_ID);
    }

    /**
     * Set return order item transferred id
     *
     * @param int $returnItemTransferredId
     * @return $this
     */
    public function setReturnItemTransferredId($returnItemTransferredId) {
        return $this->setData(self::RETURN_ITEM_TRANSFERRED_ID, $returnItemTransferredId);
    }

    /**
     * Get return order item id
     *
     * @return int
     */
    public function getReturnItemId() {
        return $this->_getData(self::RETURN_ITEM_ID);
    }

    /**
     * Set return order item id
     *
     * @param int $returnItemId
     * @return $this
     */
    public function setReturnItemId($returnItemId) {
        return $this->setData(self::RETURN_ITEM_ID, $returnItemId);
    }

    /**
     * Get created by
     *
     * @return string
     */
    public function getCreatedBy() {
        return $this->_getData(self::CREATED_BY);
    }

    /**
     * Set created by
     *
     * @param string|null $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy) {
        return $this->setData(self::CREATED_BY, $createdBy);
    }

    /**
     * Get qty transferred
     *
     * @return float
     */
    public function getQtyTransferred(){
        return $this->_getData(self::QTY_TRANSFERRED);
    }

    /**
     * Set qty transferred
     *
     * @param float $qtyTransferred
     * @return $this
     */
    public function setQtyTransferred($qtyTransferred){
        return $this->setData(self::QTY_TRANSFERRED, $qtyTransferred);
    }

    /**
     * Get warehouse id
     *
     * @return int
     */
    public function getWarehouseId(){
        return $this->_getData(self::WAREHOUSE_ID);
    }

    /**
     * Set warehouse id
     *
     * @param float $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId){
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }

    /**
     * Get transferred at
     *
     * @return string
     */
    public function getTransferredAt(){
        return $this->_getData(self::TRANSFERRED_AT);
    }

    /**
     * Set transferred at
     *
     * @param string $transferredAt
     * @return $this
     */
    public function setTransferredAt($transferredAt){
        return $this->setData(self::TRANSFERRED_AT, $transferredAt);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}