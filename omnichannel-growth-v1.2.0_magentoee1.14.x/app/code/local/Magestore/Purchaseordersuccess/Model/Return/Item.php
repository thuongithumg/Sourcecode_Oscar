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
 * Purchaseorder item Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Return_Item extends Mage_Core_Model_Abstract
    implements Magestore_Coresuccess_Model_Service_ProductSelection_SelectionProductInterface
{
    const RETURN_ITEM_ID = 'return_item_id';

    const RETURN_ID = 'return_id';

    const PRODUCT_ID = 'product_id';

    const PRODUCT_SKU = 'product_sku';

    const PRODUCT_NAME = 'product_name';

    const PRODUCT_SUPPLIER_SKU = 'product_supplier_sku';

    const QTY_TRANSFERRED = 'qty_transferred';

    const QTY_RETURNED = 'qty_returned';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_return_item';

    /**
     * Initialization
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/return_item');
    }

    /**
     * Get return order item id
     *
     * @return int
     */
    public function getReturnItemId(){
        return $this->_getData(self::RETURN_ITEM_ID);
    }

    /**
     * Set return order item id
     *
     * @param int $returnItemId
     * @return $this
     */
    public function setReturnItemId($returnItemId){
        return $this->setData(self::RETURN_ITEM_ID, $returnItemId);
    }

    /**
     * Get return order id
     *
     * @return int
     */
    public function getReturnId(){
        return $this->_getData(self::RETURN_ID);
    }

    /**
     * Set return order id
     *
     * @param int $returnId
     * @return $this
     */
    public function setReturnId($returnId){
        return $this->setData(self::RETURN_ID, $returnId);
    }

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId(){
        return $this->_getData(self::PRODUCT_ID);
    }

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId){
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get product sku
     *
     * @return string
     */
    public function getProductSku(){
        return $this->_getData(self::PRODUCT_SKU);
    }

    /**
     * Set product sku
     *
     * @param string $productSku
     * @return $this
     */
    public function setProductSku($productSku){
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }

    /**
     * Get product name
     *
     * @return string
     */
    public function getProductName(){
        return $this->_getData(self::PRODUCT_NAME);
    }

    /**
     * Set product name
     *
     * @param string $productName
     * @return $this
     */
    public function setProductName($productName){
        return $this->setData(self::PRODUCT_NAME, $productName);
    }

    /**
     * Get product supplier sku
     *
     * @return string
     */
    public function getProductSupplierSku(){
        return $this->_getData(self::PRODUCT_SUPPLIER_SKU);
    }

    /**
     * Set product supplier sku
     *
     * @param string $productSupplierSku
     * @return $this
     */
    public function setProductSupplierSku($productSupplierSku){
        return $this->setData(self::PRODUCT_SUPPLIER_SKU, $productSupplierSku);
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
     * Get qty returned
     *
     * @return float
     */
    public function getQtyReturned(){
        return $this->_getData(self::QTY_RETURNED);
    }

    /**
     * Set qty returned
     *
     * @param float $qtyReturned
     * @return $this
     */
    public function setQtyReturned($qtyReturned){
        return $this->setData(self::QTY_RETURNED, $qtyReturned);
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
}