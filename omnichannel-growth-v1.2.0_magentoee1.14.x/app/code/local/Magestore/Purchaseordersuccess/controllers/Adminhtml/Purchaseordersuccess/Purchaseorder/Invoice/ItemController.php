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
 * Purchaseordersuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_Purchaseorder_Invoice_ItemController
    extends Magestore_Purchaseordersuccess_Controller_Action
{
    /**
     * Grid purchase order item action
     *
     * @return $this
     */
    public function gridAction()
    {
        $invoiceId = $this->getRequest()->getParam('id');
        $invoiceService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_purchaseorder_invoiceService');
        $invoiceService->registerPurchaseOrderInvoice($invoiceId);
        $purchaseId = $this->getRequest()->getParam('purchase_id');
        $this->purchaseorderService->registerPurchaseOrder($purchaseId);
        return $this->_initAction()
            ->renderLayout();
    }

    /**
     * Export purchase order grid to csv file
     */
    public function exportCsvAction()
    {
        $invoiceId = $this->getRequest()->getParam('id');
        $invoiceService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_purchaseorder_invoiceService');
        $invoiceService->registerPurchaseOrderInvoice($invoiceId);
        $purchaseId = Mage::registry('current_purchase_order_invoice')->getPurchaseOrderId();
        $this->purchaseorderService->registerPurchaseOrder($purchaseId);
        $fileName = 'purchaseorder_invoice_item.csv';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_invoice_edit_tab_summary_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export purchase order grid to xml file
     */
    public function exportXmlAction()
    {
        $invoiceId = $this->getRequest()->getParam('id');
        $invoiceService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_purchaseorder_invoiceService');
        $invoiceService->registerPurchaseOrderInvoice($invoiceId);
        $purchaseId = Mage::registry('current_purchase_order_invoice')->getPurchaseOrderId();
        $this->purchaseorderService->registerPurchaseOrder($purchaseId);
        $fileName = 'purchaseorder_invoice_item.xml';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_invoice_edit_tab_summary_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}