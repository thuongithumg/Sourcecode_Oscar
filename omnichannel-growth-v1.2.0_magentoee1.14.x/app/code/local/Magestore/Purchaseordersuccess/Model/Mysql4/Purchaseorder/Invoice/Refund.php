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
 * Purchaseorder Invoice Refund Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Invoice_Refund extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Purchase order invoice refund table
     */
    const TABLE_NAME = 'os_purchase_order_invoice_refund';

    public function _construct()
    {
        $this->_init('purchaseordersuccess/purchaseorder_invoice_refund', 'purchase_order_invoice_refund_id');
    }

    /**
     * Perform actions before object save
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice_Refund $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        try {
            /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice $invoice */
            $invoice = Mage::getModel('purchaseordersuccess/purchaseorder_invoice')->load($object->getPurchaseOrderInvoiceId());
            $maxRefund = $invoice->getGrandTotalInclTax() - $invoice->getTotalDue() - $invoice->getTotalRefund();
            if ($object->getRefundAmount() > $maxRefund)
                $object->setRefundAmount($maxRefund);
            $invoice->setTotalRefund($invoice->getTotalRefund() + $object->getRefundAmount());
            $invoice->save();
        } catch (Exception $e) {
            throw $e;
        }
        return parent::_beforeSave($object);
    }
}