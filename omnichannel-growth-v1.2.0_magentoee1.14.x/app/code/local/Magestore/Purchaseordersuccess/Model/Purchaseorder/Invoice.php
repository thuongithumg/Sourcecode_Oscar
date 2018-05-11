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
 * Purchaseorder Invoice Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice extends Mage_Core_Model_Abstract
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PURCHASE_ORDER_INVOICE_ID = 'purchase_order_invoice_id';

    const PURCHASE_ORDER_ID = 'purchase_order_id';

    const INVOICE_CODE = 'invoice_code';

    const BILLED_AT = 'billed_at';

    const TOTAL_QTY_BILLED = 'total_qty_billed';

    const SUBTOTAL = 'subtotal';

    const TOTAL_TAX = 'total_tax';

    const TOTAL_DISCOUNT = 'total_discount';

    const GRAND_TOTAL_EXCL_TAX = 'grand_total_excl_tax';

    const GRAND_TOTAL_INCL_TAX = 'grand_total_incl_tax';

    const TOTAL_DUE = 'total_due';

    const TOTAL_REFUND = 'total_refund';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const CODE_LENGTH = 8;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_purchaseorder_invoice';

    /**
     * Initialization
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/purchaseorder_invoice');
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
     * Get purchase order id
     *
     * @return int
     */
    public function getPurchaseOrderId(){
        return $this->_getData(self::PURCHASE_ORDER_ID);
    }

    /**
     * Set purchase order id
     *
     * @param int $purchaseOrderId
     * @return $this
     */
    public function setPurchaseOrderId($purchaseOrderId){
        return $this->setData(self::PURCHASE_ORDER_ID, $purchaseOrderId);
    }

    /**
     * Get invoice code
     *
     * @return string
     */
    public function getInvoiceCode(){
        return $this->_getData(self::INVOICE_CODE);
    }

    /**
     * Set invoice code
     *
     * @param string $invoiceCode
     * @return $this
     */
    public function setInvoiceCode($invoiceCode){
        return $this->setData(self::INVOICE_CODE, $invoiceCode);
    }

    /**
     * Get billed at
     *
     * @return string
     */
    public function getBilledAt(){
        return $this->_getData(self::BILLED_AT);
    }

    /**
     * Set billed at
     *
     * @param string $billedAt
     * @return $this
     */
    public function setBilledAt($billedAt){
        return $this->setData(self::BILLED_AT, $billedAt);
    }

    /**
     * Get total qty billed
     *
     * @return float
     */
    public function getTotalQtyBilled(){
        return $this->_getData(self::TOTAL_QTY_BILLED);
    }

    /**
     * Set total qty billed
     *
     * @param float $totalQtyBilled
     * @return $this
     */
    public function setTotalQtyBilled($totalQtyBilled){
        return $this->setData(self::TOTAL_QTY_BILLED, $totalQtyBilled);
    }

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal(){
        return $this->_getData(self::SUBTOTAL);
    }

    /**
     * Set subtotal
     *
     * @param float $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal){
        return $this->setData(self::SUBTOTAL, $subtotal);
    }

    /**
     * Get total tax
     *
     * @return float
     */
    public function getTotalTax(){
        return $this->_getData(self::TOTAL_TAX);
    }

    /**
     * Set total tax
     *
     * @param float $totalTax
     * @return $this
     */
    public function setTotalTax($totalTax){
        return $this->setData(self::TOTAL_TAX, $totalTax);
    }

    /**
     * Get total discount
     *
     * @return float
     */
    public function getTotalDiscount(){
        return $this->_getData(self::TOTAL_DISCOUNT);
    }

    /**
     * Set total discount
     *
     * @param float $totalDiscount
     * @return $this
     */
    public function setTotalDiscount($totalDiscount){
        return $this->setData(self::TOTAL_DISCOUNT, $totalDiscount);
    }

    /**
     * Get grand total exclude tax
     *
     * @return float
     */
    public function getGrandTotalExclTax(){
        return $this->_getData(self::GRAND_TOTAL_EXCL_TAX);
    }

    /**
     * Set grand total exclude tax
     *
     * @param float $grandTotalExclTax
     * @return $this
     */
    public function setGrandTotalExclTax($grandTotalExclTax){
        return $this->setData(self::GRAND_TOTAL_EXCL_TAX, $grandTotalExclTax);
    }

    /**
     * Get grand total include tax
     *
     * @return float
     */
    public function getGrandTotalInclTax(){
        return $this->_getData(self::GRAND_TOTAL_INCL_TAX);
    }

    /**
     * Set grand total include tax
     *
     * @param float $grandTotalInclTax
     * @return $this
     */
    public function setGrandTotalInclTax($grandTotalInclTax){
        return $this->setData(self::GRAND_TOTAL_INCL_TAX, $grandTotalInclTax);
    }

    /**
     * Get total due
     *
     * @return float
     */
    public function getTotalDue(){
        return $this->_getData(self::TOTAL_DUE);
    }

    /**
     * Set total due
     *
     * @param float $totalDue
     * @return $this
     */
    public function setTotalDue($totalDue){
        return $this->setData(self::TOTAL_DUE, $totalDue);
    }

    /**
     * Get total refund
     *
     * @return float
     */
    public function getTotalRefund(){
        return $this->_getData(self::TOTAL_REFUND);
    }

    /**
     * Set total refund
     *
     * @param float $totalRefund
     * @return $this
     */
    public function setTotalRefund($totalRefund){
        return $this->setData(self::TOTAL_REFUND, $totalRefund);
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

    /**
     * Get purchase order invoice item
     *
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Invoice_Item_Collection
     */
    public function getItems($invoiceId = null)
    {
        $collection = Mage::getResourceModel('purchaseordersuccess/purchaseorder_invoice_item_collection');
        if ($this->getPurchaseOrderInvoiceId())
            $collection->addFieldToFilter('purchase_order_invoice_id', $this->getPurchaseOrderInvoiceId());
        else
            $collection->addFieldToFilter('purchase_order_invoice_id', $invoiceId);
        return $collection;
    }
    
    /**
     * Get purchase order invoice item
     *
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Invoice_Payment_Collection
     */
    public function getPayments($invoiceId = null)
    {
        $collection = Mage::getResourceModel('purchaseordersuccess/purchaseorder_invoice_payment_collection');
        if ($this->getPurchaseOrderInvoiceId())
            $collection->addFieldToFilter('purchase_order_invoice_id', $this->getPurchaseOrderInvoiceId());
        else
            $collection->addFieldToFilter('purchase_order_invoice_id', $invoiceId);
        return $collection;
    }
    
    /**
     * Get purchase order invoice item
     *
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Invoice_Refund_Collection
     */
    public function getRefund($invoiceId = null)
    {
        $collection = Mage::getResourceModel('purchaseordersuccess/purchaseorder_invoice_refund_collection');
        if ($this->getPurchaseOrderInvoiceId())
            $collection->addFieldToFilter('purchase_order_invoice_id', $this->getPurchaseOrderInvoiceId());
        else
            $collection->addFieldToFilter('purchase_order_invoice_id', $invoiceId);
        return $collection;
    }

    /**
     * @return bool
     */
    public function canPayment(){
        return $this->getTotalDue() > 0; 
    }

    /**
     * @return bool
     */
    public function canRefund(){
        return $this->getGrandTotalInclTax() - $this->getTotalDue() > $this->getTotalRefund(); 
    }
}