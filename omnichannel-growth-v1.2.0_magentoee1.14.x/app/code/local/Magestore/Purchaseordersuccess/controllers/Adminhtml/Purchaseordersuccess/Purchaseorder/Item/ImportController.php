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

class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_Purchaseorder_Item_ImportController
    extends Magestore_Purchaseordersuccess_Controller_Action
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_Item_ImportService
     */
    protected $importService;

    public function _construct()
    {
        parent::_construct();
        $this->importService = Magestore_Coresuccess_Model_Service::getService(
            'purchaseordersuccess/service_purchaseorder_item_importService'
        );
    }


    public function downloadSampleAction()
    {
        $supplierId = $this->getRequest()->getParam('supplier_id');
        $purchaseId = $this->getRequest()->getParam('id');
        $this->purchaseorderService->registerPurchaseOrder($purchaseId);
        $fileName = 'purchase_order_item_import_sample.csv';
        $content = $this->importService->getSampleCSV($supplierId);
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * import products to supplier
     */
    public function importAction()
    {
        if ($this->getRequest()->isPost()) {
            if (isset($_FILES['import_product']['name']) && $_FILES['import_product']['name'] != '') {
                $path_parts = pathinfo($_FILES["import_product"]["name"]);
                $extension = $path_parts['extension'];
                if ($extension != 'csv') {
                    Mage::getSingleton('adminhtml/session')->addError($this->__('Please choose CSV file to import.'));
                } else {
                    try {
                        $purchaseId = $this->getRequest()->getParam('id');
                        $this->purchaseorderService->registerPurchaseOrder($purchaseId);
                        $importRow = $this->importService->importFromCsvFile(
                            $_FILES['import_product']['tmp_name'],
                            $this->getRequest()->getParam('supplier_id')
                        );
                        if($importRow > 0)
                        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Import product(s) successfully'));
                        else
                            Mage::getSingleton('adminhtml/session')->addWarning($this->__('There are no product was imported'));
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($this->__('There was an error attempting to upload the file.'));
                    }
                }
            }
        }
        $this->getResponse()->setBody('success');
    }
}