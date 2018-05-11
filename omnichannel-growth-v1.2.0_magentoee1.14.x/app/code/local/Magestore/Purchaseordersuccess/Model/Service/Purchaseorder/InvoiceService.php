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
class Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_InvoiceService
    extends Magestore_Purchaseordersuccess_Model_Service_AbstractService
{
    /**
     * @param null|int $invoiceId
     * @return Exception|null
     */
    public function registerPurchaseOrderInvoice($invoiceId = null)
    {
        $invoice = Mage::getModel('purchaseordersuccess/purchaseorder_invoice');
        if ($invoiceId)
            try {
                $invoice->load($invoiceId);
            } catch (\Exception $e) {
                return $e;
            }
        Mage::register('current_purchase_order_invoice', $invoice, true);
        return null;
    }
    
    /**
     * Process create invoice data
     *
     * @param array $params
     * @return array
     */
    public function processInvoiceParam($params = array())
    {
        $result = array();
        foreach ($params as $productId => $item) {
            if (!isset($item['bill_qty']) || !isset($item['unit_price']))
                continue;
            if (!is_numeric($item['bill_qty']) || $item['bill_qty'] <= 0 || !is_numeric($item['unit_price']) || $item['unit_price'] <= 0)
                continue;
            if ((!is_numeric($item['tax']) && $item['tax'] != '') || (!is_numeric($item['discount']) && $item['discount'] != ''))
                continue;
            $result[$productId] = array(
                'product_id' => $productId,
                'qty_billed' => $item['bill_qty'],
                'unit_price' => $item['unit_price'],
                'tax' => $item['tax'] == '' ? 0 : $item['tax'],
                'discount' => $item['discount'] == '' ? 0 : $item['discount']
            );
        }
        return $result;
    }

    /**
     * Prepare invoice data
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param $invoiceTime
     * @param $createdBy
     * @return Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice
     */
    public function prepareInvoiceData($purchaseOrder, $invoiceTime)
    {
        return Mage::getModel('purchaseordersuccess/purchaseorder_invoice')
            ->setPurchaseOrderId($purchaseOrder->getPurchaseOrderId())
            ->setBilledAt($invoiceTime);
    }

    /**
     * Create an invoice and add item item
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection $purchaseItems
     * @param array $invoiceData
     * @param string|null $invoiceTime
     * @return $this
     */
    public function createInvoice($purchaseOrder, $purchaseItems, $invoiceData = array(), $invoiceTime = null)
    {
        $invoice = $this->prepareInvoiceData($purchaseOrder, $invoiceTime);
        $invoice->save();
        /** @var Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_Invoice_ItemService $invoiceItemService */
        $invoiceItemService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_purchaseorder_invoice_itemService');
        foreach ($purchaseItems as $item) {
            $productId = $item->getProductId();
            if (!in_array($productId, array_keys($invoiceData)))
                continue;
            $invoiceItemService->createInvoiceItem($invoice, $item, $invoiceData[$productId]);
        }
        $this->processInvoiceAndPurchaseData($purchaseOrder, $invoice);
        $invoice->save();
        $purchaseOrder->save();
        return $this;
    }

    /**
     * Process Invoice data and purchase order data
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice $invoice
     */
    public function processInvoiceAndPurchaseData($purchaseOrder, $invoice)
    {
        $invoice->setGrandTotalExclTax($invoice->getSubtotal() - $invoice->getTotalDiscount());
        $invoice->setGrandTotalInclTax($invoice->getGrandTotalExclTax() + $invoice->getTotalTax());
        $invoice->setTotalDue($invoice->getGrandTotalInclTax());
        $purchaseOrder->setTotalQtyBilled($purchaseOrder->getTotalQtyBilled() + $invoice->getTotalQtyBilled());
        $purchaseOrder->setTotalBilled($purchaseOrder->getTotalBilled() + $invoice->getGrandTotalInclTax());
    }
    /**
     * validate imported product to invoice
     * @var $products
     * @var $purchase_id
     * */
    public function validateInvoiceProductImported($products, $purchase_id){
        $message = 'No invoice item imported.<br/>';
        $success = false;
        $importableProducts = array();
        foreach ($products as $product){
            if(isset($product['product_sku']) && isset($product['bill_qty']) && isset($product['unit_price'])
                && isset($product['tax']) && isset($product['discount'])){
                $sku = $product['product_sku'];
                /* import qty must greater than 0 */
                if((int)$product['bill_qty'] > 0){
                    $purchaseorder_item = Mage::getModel('purchaseordersuccess/purchaseorder_item')
                        ->getCollection()
                        ->addFieldToFilter('purchase_order_id', $purchase_id)
                        ->addFieldToFilter('product_sku', $sku)
                    ;
                    /* check product can received */
                    if($purchaseorder_item->getData()){
                        $item = $purchaseorder_item->getData()[0];
                        $product_id = $item['product_id'];
                        $importableProducts[$product_id] = $product;
                        $success = true;
                    }
                }else{
                    $message .= "Invalid transferred_qty of <b>". $product['product_sku'] ."</b><br/>";
                }
            }else{
                $message = 'Invalid file upload attempt.';
                break;
            }
        }
        $response = array('success' => $success, 'message' => $message, 'selected_items' => $importableProducts);
        return $response;
    }
}