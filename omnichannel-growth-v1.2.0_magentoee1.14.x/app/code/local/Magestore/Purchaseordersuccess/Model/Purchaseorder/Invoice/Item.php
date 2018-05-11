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
 * Purchaseorder Invoice Item Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice_Item extends Mage_Core_Model_Abstract
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PURCHASE_ORDER_INVOICE_ITEM_ID = 'purchase_order_invoice_item_id';

    const PURCHASE_ORDER_INVOICE_ID = 'purchase_order_invoice_id';

    const PURCHASE_ORDER_ITEM_ID = 'purchase_order_item_id';

    const QTY_BILLED = 'qty_billed';

    const UNIT_PRICE = 'unit_price';

    const TAX = 'tax';

    const DISCOUNT = 'discount';

    const CREATED_AT = 'created_at';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_purchaseorder_invoice_item';

    /**
     * Initialization
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/purchaseorder_invoice_item');
    }

    /**
     * Get purchase order invoice item id
     *
     * @return int
     */
    public function getPurchaseOrderInvoiceItemId(){
        return $this->_getData(self::PURCHASE_ORDER_INVOICE_ITEM_ID);
    }

    /**
     * Set purchase order invoice item id
     *
     * @param int $purchaseOrderInvoiceItemId
     * @return $this
     */
    public function setPurchaseOrderInvoiceItemId($purchaseOrderInvoiceItemId){
        return $this->setData(self::PURCHASE_ORDER_INVOICE_ITEM_ID, $purchaseOrderInvoiceItemId);
    }

    /**
     * Get purchase order invoice id
     *
     * @return int
     */
    public function getPurchaseOrderInvoiceId(){
        return $this->_getData(self::PURCHASE_ORDER_INVOICE_ID);
    }

    /**
     * Set purchase order invoice id
     *
     * @param int $purchaseOrderInvoiceId
     * @return $this
     */
    public function setPurchaseOrderInvoiceId($purchaseOrderInvoiceId){
        return $this->setData(self::PURCHASE_ORDER_INVOICE_ID, $purchaseOrderInvoiceId);
    }

    /**
     * Get purchase order item id
     *
     * @return int
     */
    public function getPurchaseOrderItemId(){
        return $this->_getData(self::PURCHASE_ORDER_ITEM_ID);
    }

    /**
     * Set purchase order item id
     *
     * @param int $purchaseOrderItemId
     * @return $this
     */
    public function setPurchaseOrderItemId($purchaseOrderItemId){
        return $this->setData(self::PURCHASE_ORDER_ITEM_ID, $purchaseOrderItemId);
    }

    /**
     * Get qty billed
     *
     * @return float
     */
    public function getQtyBilled(){
        return $this->_getData(self::QTY_BILLED);
    }

    /**
     * Set qty billed
     *
     * @param float $qtyBilled
     * @return $this
     */
    public function setQtyBilled($qtyBilled){
        return $this->setData(self::QTY_BILLED, $qtyBilled);
    }

    /**
     * Get unit price
     *
     * @return float
     */
    public function getUnitPrice(){
        return $this->_getData(self::UNIT_PRICE);
    }

    /**
     * Set unit price
     *
     * @param float $unitPrice
     * @return $this
     */
    public function setUnitPrice($unitPrice){
        return $this->setData(self::UNIT_PRICE, $unitPrice);
    }

    /**
     * Get tax
     *
     * @return float
     */
    public function getTax(){
        return $this->_getData(self::TAX);
    }

    /**
     * Set tax
     *
     * @param float $tax
     * @return $this
     */
    public function setTax($tax){
        return $this->setData(self::TAX, $tax);
    }

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount(){
        return $this->_getData(self::DISCOUNT);
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return $this
     */
    public function setDiscount($discount){
        return $this->setData(self::DISCOUNT, $discount);
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