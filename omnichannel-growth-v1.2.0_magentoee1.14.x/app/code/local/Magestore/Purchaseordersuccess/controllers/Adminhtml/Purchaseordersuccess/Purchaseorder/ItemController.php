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

class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_Purchaseorder_ItemController
    extends Magestore_Purchaseordersuccess_Controller_Action
{

    public function saveAction()
    {
        $purchaseId = $this->getRequest()->getParam('id');
        $supplierId = $this->getRequest()->getParam('supplier_id');
        $this->purchaseorderService->registerPurchaseOrder($purchaseId);
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::registry('current_purchase_order');

        $modal = $this->getRequest()->getParam('modal');
        $barcodes = $this->getRequest()->getParam('barcodes');
        if ($modal == 'scanbarcode' && !empty($barcodes)) {
            $barcodes = Zend_Json::decode($barcodes);
            /** @var Magestore_Purchaseordersuccess_Model_Service_Barcode_ScanService $scanService */
            $scanService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_barcode_scanService');
            $selectedItems = $scanService->prepareSelectedItem($purchaseOrder, $barcodes, 'qty_orderred', true);

        } else {
            $selectedItems = Zend_Json::decode($this->getRequest()->getParam('selected_items'));
        }
        if (empty($selectedItems)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select at least one item.'));
        } else {
            try {
                $supplier = Mage::getModel('suppliersuccess/supplier')->load($supplierId);
                $this->purchaseorderService->addProductsToPurchaseOrder($purchaseOrder, $supplier, $selectedItems);
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

    public function reloadtotalAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->purchaseorderService->registerPurchaseOrder($id);
        $purchaseOrder = Mage::registry('current_purchase_order');
        $this->purchaseorderService->updatePurchaseTotal($purchaseOrder);
        return $this->_initAction()
            ->renderLayout();
    }

    public function deleteAction()
    {
        $purchaseId = $this->getRequest()->getParam('purchase_id');
        $product_id = $this->getRequest()->getParam('product_id');
        $this->purchaseorderService->registerPurchaseOrder($purchaseId);
        $purchaseOrder = Mage::registry('current_purchase_order');
        try {
            $this->purchaseorderService->removeProduct($purchaseOrder, $product_id);
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Remove item successfully'));
        } catch (\Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_forward('grid');
    }

    public function modalAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->purchaseorderService->registerPurchaseOrder($id);
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

    protected function getModal($modalName)
    {
        return 'adminhtml_purchaseordersuccess_purchaseorder_item_' . $modalName;
    }

    public function loadBarcodeAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $supplierId = $this->getRequest()->getParam('supplier_id');
        $barcode = $this->getRequest()->getParam('barcode');
        /** @var Magestore_Purchaseordersuccess_Model_Service_Barcode_ScanService $scanService */
        $scanService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_barcode_scanService');
        $collection = $scanService->searchBarcode($supplierId, $barcode);
        $barcode = $collection->getFirstItem();
        $data = $barcode->getId() ? $barcode->getData() : array();
//        \Zend_Debug::dump($data);die();
        if(!isset($data['product_name']) && $data) {
            $data['product_name'] = $data['name'];
            $data['product_supplier_sku'] = '';
            $data['cost'] = 0;
        }
        return $this->getResponse()->setBody(json_encode($data));
    }
}