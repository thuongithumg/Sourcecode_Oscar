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
class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_Purchaseorder_Invoice_RefundController
    extends Magestore_Purchaseordersuccess_Controller_Action
{
    public function registerInvoice(){
        $invoiceId = $this->getRequest()->getParam('id');
        $invoiceService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_purchaseorder_invoiceService');
        $invoiceService->registerPurchaseOrderInvoice($invoiceId);
        $purchaseId = $this->getRequest()->getParam('purchase_id');
        $this->purchaseorderService->registerPurchaseOrder($purchaseId);
    }
    
    /**
     * Grid purchase order item action
     *
     * @return $this
     */
    public function gridAction()
    {
        $this->registerInvoice();
        return $this->_initAction()
            ->renderLayout();
    }
    
    public function saveAction(){
        $params = $this->getRequest()->getParams();
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice_Refund $refund */
        $refund = Mage::getModel('purchaseordersuccess/purchaseorder_invoice_refund');
        $refund->setData($params);
        try {
            $refund->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('purchaseordersuccess')->__('Create refund successfully.')
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        return $this->getResponse()->setBody('success');
    }

    /**
     * Export purchase order grid to csv file
     */
    public function exportCsvAction()
    {
        $this->registerInvoice();
        $fileName = 'purchaseorder_invoice_refund.csv';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_invoice_edit_tab_refund_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export purchase order grid to xml file
     */
    public function exportXmlAction()
    {
        $this->registerInvoice();
        $fileName = 'purchaseorder_invoice_refund.xml';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_invoice_edit_tab_refund_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}