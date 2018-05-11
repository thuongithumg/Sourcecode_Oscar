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
 * Purchaseorder Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */

class Magestore_Purchaseordersuccess_Model_Return extends Mage_Core_Model_Abstract
    implements Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const RETURN_ORDER_ID = 'return_id';

    const RETURN_CODE = 'return_code';

    const WAREHOUSE_ID = 'warehouse_id';

    const SUPPLIER_ID = 'supplier_id';

    const TYPE = 'type';

    const STATUS = 'status';

    const REASON = 'reason';

    const USER_ID = 'user_id';

    const CREATED_BY = 'created_by';

    const TOTAL_QTY_TRANSFERRED = 'total_qty_transferred';

    const TOTAL_QTY_RETURNED = 'total_qty_returned';

    const RETURNED_AT = 'returned_at';

    const CANCELED_AT = 'canceled_at';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const ITEMS = 'items';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_return';

    /**
     * Initialization
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/return');
    }

    /**
     * Get return order id
     *
     * @return int
     */
    public function getReturnOrderId(){
        return $this->_getData(self::RETURN_ORDER_ID);
    }

    /**
     * Set return order id
     *
     * @param int $returnOrderId
     * @return $this
     */
    public function setReturnOrderId($returnOrderId){
        return $this->setData(self::RETURN_ORDER_ID, $returnOrderId);
    }

    /**
     * Get return code
     *
     * @return string|null
     */
    public function getReturnCode(){
        return $this->_getData(self::RETURN_CODE);
    }

    /**
     * Set return code
     *
     * @param string $returnCode
     * @return $this
     */
    public function setReturnCode($returnCode){
        return $this->setData(self::RETURN_CODE, $returnCode);
    }

    /**
     * Get supplier id
     *
     * @return int
     */
    public function getSupplierId(){
        return $this->_getData(self::SUPPLIER_ID);
    }

    /**
     * Set supplier id
     *
     * @param int $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId){
        return $this->setData(self::SUPPLIER_ID, $supplierId);
    }

    /**
     * Get supplier id
     *
     * @return int
     */
    public function getWarehouseId(){
        return $this->_getData(self::WAREHOUSE_ID);
    }

    /**
     * Set warehouse id
     *
     * @param int $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId){
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType(){
        return $this->_getData(self::TYPE);
    }

    /**
     * Set type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type){
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus(){
        return $this->_getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason(){
        return $this->_getData(self::REASON);
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return $this
     */
    public function setReason($reason){
        return $this->setData(self::REASON, $reason);
    }

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId(){
        return $this->_getData(self::USER_ID);
    }

    /**
     * Set user id
     *
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId){
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Get created by
     *
     * @return string
     */
    public function getCreatedBy(){
        return $this->_getData(self::CREATED_BY);
    }

    /**
     * Set created by
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy){
        return $this->setData(self::CREATED_BY, $createdBy);
    }

    /**
     * Get total qty transferred
     *
     * @return float
     */
    public function getTotalQtyTransferred(){
        return $this->_getData(self::TOTAL_QTY_TRANSFERRED);
    }

    /**
     * Set total qty transferred
     *
     * @param float $totalQtyTransferred
     * @return $this
     */
    public function setTotalQtyTransferred($totalQtyTransferred){
        return $this->setData(self::TOTAL_QTY_TRANSFERRED, $totalQtyTransferred);
    }

    /**
     * Get total qty returned
     *
     * @return float
     */
    public function getTotalQtyReturned(){
        return $this->_getData(self::TOTAL_QTY_RETURNED);
    }

    /**
     * Set total qty returned
     *
     * @param float $totalQtyReturned
     * @return $this
     */
    public function setTotalQtyReturned($totalQtyReturned){
        return $this->setData(self::TOTAL_QTY_RETURNED, $totalQtyReturned);
    }

    /**
     * Get returnd at
     *
     * @return string
     */
    public function getReturnedAt() {
        return $this->_getData(self::RETURNED_AT);
    }

    /**
     * Set returned at
     *
     * @param string $returnedAt
     * @return $this
     */
    public function setReturnedAt($returnedAt) {
        return $this->setData(self::RETURNED_AT, $returnedAt);
    }

    /**
     * Get canceled at
     *
     * @return string
     */
    public function getCanceledAt(){
        return $this->_getData(self::CANCELED_AT);
    }

    /**
     * Set canceled at
     *
     * @param string $canceledAt
     * @return $this
     */
    public function setCanceledAt($canceledAt){
        return $this->setData(self::CANCELED_AT, $canceledAt);
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

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt(){
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt){
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    public function canSendEmail(){
        $status = $this->getStatus();
        if(!$status || !$this->getId())
            return false;
        if($status == Magestore_Purchaseordersuccess_Model_Return_Options_Status::STATUS_CANCELED)
            return false;
        return true;
    }

    /**
     * Get purchase order item
     *
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Return_Item_Collection
     */
    public function getItems($returnId = null, $productIds = array())
    {
        $collection = Mage::getResourceModel('purchaseordersuccess/return_item_collection');
        if ($this->getReturnOrderId())
            $collection->addFieldToFilter('return_id', $this->getReturnOrderId());
        else
            $collection->addFieldToFilter('return_id', $returnId);
        if (!empty($productIds))
            $collection->addFieldToFilter('product_id', array('in' => $productIds));
        return $collection;
    }

    public function canAddProduct()
    {
        return $this->getStatus() == Magestore_Purchaseordersuccess_Model_Return_Options_Status::STATUS_PENDING;
    }

    public function canTransferItem()
    {
        return $this->getStatus() != Magestore_Purchaseordersuccess_Model_Return_Options_Status::STATUS_CANCELED &&
            $this->getStatus() != Magestore_Purchaseordersuccess_Model_Return_Options_Status::STATUS_PENDING &&
            $this->getTotalQtyReturned() > $this->getTotalQtyTransferred();
    }

    /**
     * @return
     */
    public function getSelectionProductModel()
    {
        return Mage::getModel('purchaseordersuccess/return_item');
    }
}