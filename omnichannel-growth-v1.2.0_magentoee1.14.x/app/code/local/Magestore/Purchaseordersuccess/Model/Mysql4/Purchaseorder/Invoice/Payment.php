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
 * Purchaseorder Invoice Payment Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Invoice_Payment extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Purchase order invoice table
     */
    const TABLE_NAME = 'os_purchase_order_invoice_payment';

    public function _construct()
    {
        $this->_init('purchaseordersuccess/purchaseorder_invoice_payment', 'purchase_order_invoice_payment_id');
    }

    /**
     * Perform actions before object save
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice_Payment $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        try {
            /** @var Magestore_Purchaseordersuccess_Model_Service_Config_PaymentMethod $service */
            $service = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_config_paymentMethod');
            $params = $service->saveConfig($object->getData());
            $object->setPaymentMethod($params['payment_method']);
            /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice $invoice */
            $invoice = Mage::getModel('purchaseordersuccess/purchaseorder_invoice')->load($object->getPurchaseOrderInvoiceId());
            if ($object->getPaymentAmount() > $invoice->getTotalDue())
                $object->setPaymentAmount($invoice->getTotalDue());
            $invoice->setTotalDue($invoice->getTotalDue() - $object->getPaymentAmount());
            $invoice->save();
        } catch (Exception $e) {
            throw $e;
        }
        return parent::_beforeSave($object);
    }
}