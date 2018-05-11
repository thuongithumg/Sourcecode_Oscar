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

class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_Return_Transferreditem_ImportController
    extends Magestore_Purchaseordersuccess_Controller_ReturnAbstract
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Service_Return_Item_ImportService
     */
    protected $importService;

    /**
     * @var Magestore_Purchaseordersuccess_Model_Service_Return_Item_TransferredService
     */
    protected $transferredService;

    public function _construct()
    {
        parent::_construct();
        $this->transferredService = Mage::getSingleton(
            'purchaseordersuccess/service_return_item_transferredService'
        );
        $this->importService = Mage::getSingleton(
            'purchaseordersuccess/service_return_item_importService'
        );
    }


    public function downloadSampleAction()
    {
        $supplierId = $this->getRequest()->getParam('supplier_id');
        $returnId = $this->getRequest()->getParam('id');
        $this->returnService->registerReturnRequest($returnId);
        $fileName = 'return_request_transferred_item_import_sample.csv';
        $content = $this->importService->getSampleTransferredImportCSV($returnId);
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * import transferred products
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
                }  else if(!isset($params['transferred_at'])){
                    $this->getAdminSession()->addError($this->__('Please select Date to Transfer'));
                }
                else{
                    $dataImported = $this->importService->getCsvData(
                        $_FILES['import_product']['tmp_name']
                    );
                    $returnId = $this->getRequest()->getParam('id');
                    $validateData = $this->transferredService->validateTransferProductImported($dataImported, $returnId);
                    if($validateData['success']){
                        $selectedItems = $validateData['selected_items'];
                    }
                    if (!empty($selectedItems)) {
                        $transferredData = $this->transferredService->processTransferredData($selectedItems);
                        if (empty($transferredData)) {
                            $this->getAdminSession()->addError($this->__('Please select at least one item to transfer.'));
                        } else {
                            $total_transferred_item = $validateData['total_transferred_item'];
                            try {
                                $userName = Mage::getSingleton('admin/session')->getUser()->getUsername();
                                $transferStockItemData = $this->returnService->transferProducts(
                                    $returnId, $transferredData, $params, $userName
                                );
                                if (!empty($transferStockItemData)) {
                                    if($params['is_subtract'] == 'true') {
                                        $transferStock = $this->transferredService->createTransferStock($params, $userName);
                                        $this->transferredService->saveTransferStockData($transferStock, $transferStockItemData);
                                    }
                                    $this->getAdminSession()->addSuccess($this->__('Delivery '.$total_transferred_item.' item(s) successfully.'));
                                }
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
