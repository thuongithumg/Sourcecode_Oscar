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
 * Purchaseorder Service
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_Invoice_ItemService
    extends Magestore_Purchaseordersuccess_Model_Service_AbstractService
{
    /**
     * @var float
     */
    protected $taxType;

    public function getTaxType()
    {
        if (empty($this->taxType))
            $this->taxType = Magestore_Purchaseordersuccess_Model_Service_Config_TaxAndShipping::getTaxType();
        return $this->taxType;
    }

    /**
     * Set qty billed for invoice item data
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $purchaseItem
     * @param array $invoiceItemData
     * @return array
     */
    public function setQtyBilled($purchaseItem, $invoiceItemData = array())
    {
        $qty = $purchaseItem->getQtyOrderred() - $purchaseItem->getQtyBilled();
        if (!isset($invoiceItemData['qty_billed']) || $invoiceItemData['qty_billed'] > $qty)
            $invoiceItemData['qty_billed'] = $qty;
        return $invoiceItemData;
    }

    /**
     * @param $invoiceId
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $purchaseItem
     * @param array $invoiceItemData
     * @return InvoiceItemInterface
     */
    public function prepareInvoiceItem($invoiceId, $purchaseItem, $invoiceItemData = array())
    {
        $invoiceItemData = $this->setQtyBilled($purchaseItem, $invoiceItemData);
        return Mage::getModel('purchaseordersuccess/purchaseorder_invoice_item')
            ->setPurchaseOrderInvoiceId($invoiceId)
            ->setPurchaseOrderItemId($purchaseItem->getPurchaseOrderItemId())
            ->setQtyBilled($invoiceItemData['qty_billed'])
            ->setUnitPrice($invoiceItemData['unit_price'])
            ->setTax($invoiceItemData['tax'])
            ->setDiscount($invoiceItemData['discount']);
    }

    /**
     * Process invoice data
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice $invoice
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice_Item $invoiceItem
     */
    public function processInvoiceData($invoice, $invoiceItem)
    {
        $billedQty = $invoiceItem->getQtyBilled();
        $discountPercent = $invoiceItem->getDiscount();
        $taxPercent = $invoiceItem->getTax();
        $subtotal = $invoiceItem->getUnitPrice() * $billedQty;
        $discount = $subtotal * ($discountPercent ? $discountPercent : 0) / 100;
        if ($this->getTaxType() == 0) {
            $tax = $subtotal * ($taxPercent ? $taxPercent : 0) / 100;
        } else {
            $tax = ($subtotal - $discount) * ($taxPercent ? $taxPercent : 0) / 100;
        }
        $invoice->setTotalQtyBilled($invoice->getTotalQtyBilled() + $billedQty);
        $invoice->setSubtotal($invoice->getSubtotal() + $subtotal);
        $invoice->setTotalDiscount($invoice->getTotalDiscount() + $discount);
        $invoice->setTotalTax($invoice->getTotalTax() + $tax);
    }

    /**
     * Create an invoice item
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice $invoice
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $item
     * @param array $invoiceItemData
     * @return bool
     */
    public function createInvoiceItem($invoice, $purchaseItem, $invoiceItemData = array())
    {
        $invoiceItemData = $this->setQtyBilled($purchaseItem, $invoiceItemData);
        if ($invoiceItemData['qty_billed'] == 0)
            return true;
        $invoiceItem = $this->prepareInvoiceItem($invoice->getPurchaseOrderInvoiceId(), $purchaseItem, $invoiceItemData);
        try {
            $invoiceItem->save();
            $purchaseItem->setQtyBilled($purchaseItem->getQtyBilled() + $invoiceItem->getQtyBilled());
            $purchaseItem->save();
            $this->processInvoiceData($invoice, $invoiceItem);
        } catch (\Exception $e) {
            return false;
        }
        return true;

    }
}