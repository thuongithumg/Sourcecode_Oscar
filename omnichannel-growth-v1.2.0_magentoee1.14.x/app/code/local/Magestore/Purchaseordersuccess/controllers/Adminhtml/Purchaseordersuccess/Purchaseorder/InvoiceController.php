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
use Magestore_Purchaseordersuccess_Model_Purchaseorder as Purchaseorder;

class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_Purchaseorder_InvoiceController
    extends Magestore_Purchaseordersuccess_Controller_Action
{
    /**
     * Export purchase order invoice grid to csv file
     */
    public function exportCsvAction()
    {
        $this->purchaseorderService->registerPurchaseOrder($this->getRequest()->getParam('id'));
        $fileName = 'purchaseorder_invoice.csv';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_invoice_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export purchase order invoice grid to xml file
     */
    public function exportXmlAction()
    {
        $this->purchaseorderService->registerPurchaseOrder($this->getRequest()->getParam('id'));
        $fileName = 'purchaseorder_invoice.xml';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_invoice_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function gridmodalAction()
    {
        return $this->gridAction();
    }

    public function invoiceAction()
    {
        $params = $this->getRequest()->getParams();
        if (!$params['id']) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select a purchase order to create invoice'));
            return $this->getResponse()->setBody('success');
        }
        if (!isset($params['selected_items'])) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select at least one product to create invoice.'));
            return $this->getResponse()->setBody('success');
        }
        $selectedItems = Zend_Json::decode($this->getRequest()->getParam('selected_items'));
        $invoiceData = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_purchaseorder_invoiceService')
            ->processInvoiceParam($selectedItems);
        if (empty($invoiceData)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select at least one product to create invoice.'));
            return $this->getResponse()->setBody('success');
        }
        try {
            $this->purchaseorderService->registerPurchaseOrder($params['id']);
            $purchaseOrder = Mage::registry('current_purchase_order');
            $this->purchaseorderService->createInvoice(
                $purchaseOrder, $invoiceData, $params['billed_at']
            );
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Create invoice successfully.'));
        } catch (\Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        return $this->getResponse()->setBody('success');
    }

    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select an invoice to view.'));
            return $this->_redirect('*/purchaseordersuccess_purchaseorder/index');
        }
        try {
            /** @var Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_InvoiceService $invoiceService */
            $invoiceService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_purchaseorder_invoiceService');
            $register = $invoiceService->registerPurchaseOrderInvoice($id);
            if ($register) {
                Mage::getSingleton('adminhtml/session')->addError($register->getMessage());
                return $this->_redirect('*/purchaseordersuccess_purchaseorder/index');
            }
            /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice $invoice */
            $invoice = Mage::registry('current_purchase_order_invoice');
            Magestore_Coresuccess_Model_Service::purchaseorderService()
                ->registerPurchaseOrder($invoice->getPurchaseOrderId());
            $this->_initAction()
                ->_title('Invoice')
                ->_title($this->__('View Invoice %s', $invoice->getInvoiceCode()));
            $this->_setActiveMenu('purchaseordersuccess/purchaseorder/view');
            return $this->renderLayout();
        } catch (\Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select an invoice to view.'));
            return $this->_redirect('*/purchaseordersuccess_purchaseorder/index');
        }
    }
    
    public function paymentAction(){
        $id = $this->getRequest()->getParam('id');
        /** @var Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_InvoiceService $invoiceService */
        $invoiceService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_purchaseorder_invoiceService');
        $invoiceService->registerPurchaseOrderInvoice($id);
        $invoice = Mage::registry('current_purchase_order_invoice');
        Magestore_Coresuccess_Model_Service::purchaseorderService()
            ->registerPurchaseOrder($invoice->getPurchaseOrderId());
        return $this->loadLayout()
            ->renderLayout();
    }
    
    public function refundAction(){
        return $this->paymentAction();
    }

    public function reloadtotalAction(){
        return $this->paymentAction();
    }
}