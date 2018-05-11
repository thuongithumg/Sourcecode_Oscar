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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type as PurchaseorderType;

class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_Purchaseorder_ReceiveditemController
    extends Magestore_Purchaseordersuccess_Controller_Action
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_Item_ReceivedService
     */
    protected $receivedService;

    public function _construct()
    {
        parent::_construct();
        $this->receivedService = Magestore_Coresuccess_Model_Service::getService(
            'purchaseordersuccess/service_purchaseorder_item_receivedService'
        );
    }

    public function receiveallAction()
    {
        $purchaseId = $this->getRequest()->getParam('id');
        if (!$purchaseId) {
            $this->getAdminSession()->addError($this->__('Please select a purchase order to receive product(s)'));
            return $this->redirectGrid(PurchaseorderType::TYPE_PURCHASE_ORDER);
        }
        try {
            $this->purchaseorderService->receiveAllProduct($purchaseId);
            $this->getAdminSession()->addSuccess($this->__('Receive all products successfully.'));
        } catch (\Exception $e) {
            $this->getAdminSession()->addError($e->getMessage());
        }
        return $this->getResponse()->setBody(\Zend_Json::encode($this->getDisplayButton()));
    }

    public function receiveAction()
    {
        $purchaseId = $this->getRequest()->getParam('id');
        if (!$purchaseId) {
            $this->getAdminSession()->addError($this->__('Please select a purchase order to received product'));
        } else {
            $modal = $this->getRequest()->getParam('modal');
            $barcodes = $this->getRequest()->getParam('barcodes');
            $postItems = $this->getRequest()->getParam('selected_items');
            $receivedAt = $this->getRequest()->getParam('received_at');
            $selectedItems = array();
            if ($modal == 'scanbarcode' && !empty($barcodes)) {
                $barcodes = Zend_Json::decode($barcodes);
                $purchaseOrder = $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($purchaseId);
                /** @var Magestore_Purchaseordersuccess_Model_Service_Barcode_ScanService $scanService */
                $scanService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_barcode_scanService');
                $selectedItems = $scanService->prepareSelectedItem($purchaseOrder, $barcodes, 'receive_qty');
            } else if (empty($postItems)) {
                $this->getAdminSession()->addError($this->__('Please receive at least one product.'));
                return $this->getResponse()->setBody(\Zend_Json::encode($this->getDisplayButton()));
            } else {
                $selectedItems = \Zend_Json::decode($postItems);
            }
            if (!empty($selectedItems)) {
                $receivedData = $this->receivedService->processReceivedData($selectedItems);
                if (empty($receivedData)) {
                    $this->getAdminSession()->addError($this->__('Please receive at least one item.'));
                } else {
                    try {
                        $this->purchaseorderService->receiveProducts($purchaseId, $receivedData, $receivedAt);
                        $this->getAdminSession()->addSuccess($this->__('Receive item(s) successfully.'));
                    } catch (\Exception $e) {
                        $this->getAdminSession()->addError($e->getMessage());
                    }
                }
            } else {
                $this->getAdminSession()->addError($this->__('Please receive at least one product.'));
            }
        }
        return $this->getResponse()->setBody(\Zend_Json::encode($this->getDisplayButton()));
    }

    public function gridmodalAction()
    {
        return $this->gridAction();
    }

    /**
     * Export purchase order grid to csv file
     */
    public function exportCsvAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->purchaseorderService->registerPurchaseOrder($id);
        $fileName = 'purchaseorder_received_item.csv';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_receiveditem_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export purchase order grid to xml file
     */
    public function exportXmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->purchaseorderService->registerPurchaseOrder($id);
        $fileName = 'purchaseorder_received_item.xml';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_receiveditem_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function loadBarcodeAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $purchaseId = $this->getRequest()->getParam('id');
        $supplierId = $this->getRequest()->getParam('supplier_id');
        $barcode = $this->getRequest()->getParam('barcode');
        /** @var Magestore_Purchaseordersuccess_Model_Service_Barcode_ScanService $scanService */
        $scanService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_barcode_scanService');
        $collection = $scanService->searchBarcode($supplierId, $barcode);
        /** @var Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection $purchaseItems */
        $purchaseItems = Mage::getResourceModel('purchaseordersuccess/purchaseorder_item_collection');
        $productIds = $purchaseItems->addFieldToFilter('purchase_order_id', $purchaseId)
            ->addFieldToFilter(new \Zend_Db_Expr('main_table.qty_orderred - main_table.qty_received'), array('gt' => 0))
            ->getColumnValues('product_id');
        $collection->addFieldToFilter('barcode.product_id', array('in' => $productIds));
        $barcode = $collection->getFirstItem();
        $data = $barcode->getId() ? $barcode->getData() : array();
        return $this->getResponse()->setBody(json_encode($data));
    }
}