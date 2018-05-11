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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type as PurchaseorderType;

class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_Purchaseorder_Invoice_ImportController
    extends Magestore_Purchaseordersuccess_Controller_Action
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_Item_ImportService
     */
    protected $importService;

    /**
     *  @var Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_InvoiceService
     */
    protected $invoiceService;

    public function _construct()
    {
        parent::_construct();
        $this->importService = Magestore_Coresuccess_Model_Service::getService(
            'purchaseordersuccess/service_purchaseorder_item_importService'
        );
        $this->invoiceService = Magestore_Coresuccess_Model_Service::getService(
            'purchaseordersuccess/service_purchaseorder_invoiceService'
        );
    }


    public function downloadSampleAction()
    {
        $supplierId = $this->getRequest()->getParam('supplier_id');
        $purchaseId = $this->getRequest()->getParam('id');
        $this->purchaseorderService->registerPurchaseOrder($purchaseId);
        $fileName = 'purchase_order_invoice_item_import_sample.csv';
        $content = $this->importService->getSampleInvoiceImportCSV($purchaseId);
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * import invoice products
     * @return void
     */
    public function importAction()
    {
        if ($this->getRequest()->isPost()) {
            if (isset($_FILES['import_product']['name']) && $_FILES['import_product']['name'] != '') {
                $path_parts = pathinfo($_FILES["import_product"]["name"]);
                $extension = $path_parts['extension'];
                $params = $this->getRequest()->getParams();
                if ($extension != 'csv') {
                    $this->getAdminSession()->addError($this->__('Please choose CSV file to import.'));
                }else if(!isset($params['billed_at'])){
                    $this->getAdminSession()->addError($this->__('Please select Date to Invoice'));
                }else{
                    $dataImported = $this->importService->getCsvData(
                        $_FILES['import_product']['tmp_name']
                    );
                    $purchaseId = $this->getRequest()->getParam('id');
                    $validateData = $this->invoiceService->validateInvoiceProductImported($dataImported, $purchaseId);
                    if($validateData['success']){
                        $selectedItems = $validateData['selected_items'];
                    }
                    if (!empty($selectedItems)) {
                        $billedAt = $params['billed_at'];
                        $invoiceData = $this->invoiceService->processInvoiceParam($selectedItems);
                        if (empty($invoiceData)) {
                            $this->getAdminSession()->addError($this->__('Please receive at least one item.'));
                        } else {
                            try {
                                $this->purchaseorderService->registerPurchaseOrder($params['id']);
                                $purchaseOrder = Mage::registry('current_purchase_order');
                                $this->purchaseorderService->createInvoice(
                                    $purchaseOrder, $invoiceData, $params['billed_at']
                                );
                                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Create invoice successfully.'));
                            } catch (\Exception $e) {
                                $this->getAdminSession()->addError($e->getMessage());
                            }
                        }
                        $this->getResponse()->setBody('success');
                    } else {
                        $this->getAdminSession()->addError($this->__($validateData['message']));
                        $this->getResponse()->setBody('failed');
                    }
                }
            }
        }

    }
}