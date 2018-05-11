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

class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_Return_ItemController
    extends Magestore_Purchaseordersuccess_Controller_ReturnAbstract
{

    public function saveAction()
    {
        $returnId = $this->getRequest()->getParam('id');
        $supplierId = $this->getRequest()->getParam('supplier_id');
        $this->returnService->registerReturnRequest($returnId);
        /** @var Magestore_Purchaseordersuccess_Model_Return $returnRequest */
        $returnRequest = Mage::registry('current_return_request');

        $modal = $this->getRequest()->getParam('modal');
        $barcodes = $this->getRequest()->getParam('barcodes');
        if ($modal == 'scanbarcode' && !empty($barcodes)) {
            $barcodes = Zend_Json::decode($barcodes);
            /** @var Magestore_Purchaseordersuccess_Model_Service_Barcode_ScanService $scanService */
            $scanService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_barcode_scanService');
            $selectedItems = $scanService->prepareSelectedItemReturn($returnRequest, $barcodes, 'qty_returned');

        } else {
            $selectedItems = Zend_Json::decode($this->getRequest()->getParam('selected_items'));
        }
        if (empty($selectedItems)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select at least one item.'));
        } else {
            try {
                $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($returnRequest->getWarehouseId());
                $supplier = Mage::getModel('suppliersuccess/supplier')->load($supplierId);
                $this->returnService->addProductsToReturnRequest($returnRequest, $supplier, $warehouse, $selectedItems);
                if ($modal)
                    Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Add product(s) successfully'));
                else
                    Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Update item(s) successfully'));
            } catch (\Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        if ($modal)
            $this->getResponse()->setBody('success');
        else
            $this->_forward('grid');
    }

    public function deleteAction()
    {
        $returnId = $this->getRequest()->getParam('return_id');
        $product_id = $this->getRequest()->getParam('product_id');
        $this->returnService->registerReturnRequest($returnId);
        $returnRequest = Mage::registry('current_return_request');
        try {
            $this->returnService->removeProduct($returnRequest, $product_id);
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Remove item successfully'));
        } catch (\Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_forward('grid');
    }

    public function modalAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->returnService->registerReturnRequest($id);
        $modalName = $this->getRequest()->getParam('modal');
        $modal = $this->getModal($modalName);
        $update = $this->getLayout()->getUpdate();
        $update->addHandle($modal);
        $this->loadLayoutUpdates();
        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;
        $this->renderLayout();
    }

    public function reloadtotalAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->returnService->registerReturnRequest($id);
        $returnRequest = Mage::registry('current_return_request');
        $this->returnService->updateReturnTotal($returnRequest);
//        return $this->_initAction()
//            ->renderLayout();
    }

    protected function getModal($modalName)
    {
        return 'adminhtml_purchaseordersuccess_return_item_' . $modalName;
    }

    public function loadBarcodeAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $supplierId = $this->getRequest()->getParam('supplier_id');
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        $barcode = $this->getRequest()->getParam('barcode');
        /** @var Magestore_Purchaseordersuccess_Model_Service_Barcode_ScanService $scanService */
        $scanService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_barcode_scanService');
        $collection = $scanService->searchBarcode($supplierId, $barcode, $warehouseId);
        $barcode = $collection->getFirstItem();
        $data = $barcode->getId() ? $barcode->getData() : array();
        return $this->getResponse()->setBody(json_encode($data));
    }
}