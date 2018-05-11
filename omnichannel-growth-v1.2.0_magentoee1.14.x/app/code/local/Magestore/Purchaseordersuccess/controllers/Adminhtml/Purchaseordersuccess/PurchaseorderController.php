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

class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_PurchaseorderController
    extends Magestore_Purchaseordersuccess_Controller_Action
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_setActiveMenu('purchaseordersuccess/purchaseorder/view')
            ->_title($this->__('Purchase Orders'))
            ->renderLayout();
    }

    /**
     * Export purchase order grid to csv file
     */
    public function exportCsvAction()
    {
        $fileName = 'purchaseorder.csv';
        $content = $this->getLayout()->createBlock('purchaseordersuccess/adminhtml_purchaseorder_grid')->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export purchase order grid to xml file
     */
    public function exportXmlAction()
    {
        $fileName = 'purchaseorder.xml';
        $content = $this->getLayout()->createBlock('purchaseordersuccess/adminhtml_purchaseorder_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * New purchase order action
     */
    public function newAction()
    {
        return $this->_forward('view');
    }

    /**
     * View purchase order action
     */
    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $type = $this->getRequest()->getParam(Purchaseorder::TYPE, PurchaseorderType::TYPE_PURCHASE_ORDER);
        $this->getRequest()->setParam('type', $type);
        $typeLabel = $this->getTypeLabel($type);
        $register = $this->purchaseorderService->registerPurchaseOrder($id);
        if ($register !== null) {
            $this->getAdminSession()->addError($register->getMessage());
            $this->redirectGrid($type);
        }
        $this->_initAction()
            ->_title($typeLabel);
        if ($id) {
            $code = Mage::registry('current_purchase_order')->getPurchaseCode();
            $code = $code ? $code : $id;
            $this->_title($this->__('View %s #%s', $typeLabel, $code));
            if ($type == PurchaseorderType::TYPE_PURCHASE_ORDER)
                $this->_setActiveMenu('purchaseordersuccess/purchaseorder/view');
            else
                $this->_setActiveMenu('purchaseordersuccess/quotation/view');
            if (Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Barcodesuccess')) {
//                $this->getLayout()->getUpdate()->addHandle('adminhtml_purchaseordersuccess_barcode_scan');
//                $this->loadLayoutUpdates();
//                $this->generateLayoutXml()->generateLayoutBlocks();
            }
        } else {
            $this->_title($this->__('New %s ', $typeLabel));
            if ($type == PurchaseorderType::TYPE_PURCHASE_ORDER)
                $this->_setActiveMenu('purchaseordersuccess/purchaseorder/create');
            else
                $this->_setActiveMenu('purchaseordersuccess/quotation/create');
        }
        $this->renderLayout();
    }

    /**
     * Save purchase order action
     *
     * @return $this
     */
    public function saveAction()
    {
        $params = $this->getRequest()->getParams();
        $id = (isset($params['purchase_order_id']) && $params['purchase_order_id'] > 0) ? $params['purchase_order_id'] : null;
        $type = (isset($params[Purchaseorder::TYPE]) && $params[Purchaseorder::TYPE] > 0) ? $params[Purchaseorder::TYPE] : null;
        if (!$type) {
            return $this->redirectGrid(PurchaseorderType::TYPE_QUOTATION);
        }
        $typeLabel = $this->getTypeLabel($type);
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder');
        if ($id) {
            try {
                $purchaseOrder->load($id);
                if ($purchaseOrder->getType() != $type) {
                    $this->getAdminSession()->addError('%s does not exist.', $typeLabel);
                    return $this->redirectGrid($type);
                }
            } catch (\Exception $e) {
                $this->getAdminSession()->addError($e->getMessage());
                return $this->redirectGrid($type);
            }
        }
        $purchaseOrder->addData($params)->setId($id);
        $canSendEmail = $purchaseOrder->canSendEmail();
        try {
            $purchaseOrder->save();
            $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($purchaseOrder->getId());
            $type = $purchaseOrder->getType();
            $supplier = Mage::getModel('suppliersuccess/supplier')->load($purchaseOrder->getSupplierId());
            if (isset($params['selected_items']) && !empty($params['selected_items'])) {
                $selectedItems = Zend_Json::decode($params['selected_items']);
                if (!empty($selectedItems)) {
                    $this->purchaseorderService->addProductsToPurchaseOrder($purchaseOrder, $supplier, $selectedItems);
                }
            }
            if ($actionType = $this->getRequest()->getParam('action_type')) {
                try {
                    $this->purchaseorderService->$actionType($purchaseOrder);
                    if($actionType == 'delete') {
                        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('%s has been deleted.', $typeLabel));
                        return $this->redirectGrid($type);
                    }
                } catch (\Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    return $this->redirectForm($type, $id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('%s has been saved.', $typeLabel));
            }
            $this->purchaseorderService->updatePurchaseTotal($purchaseOrder);
        } catch (\Exception $e) {
            $this->getAdminSession()->addError($e->getMessage());
            return $this->redirectForm($type, $id);
        }
        return $this->redirectForm($type, $purchaseOrder->getPurchaseOrderId());
    }

    /**
     * Send purchase order request action
     *
     * @return $this
     */
    public function sendrequestAction()
    {
        $purchaseId = $this->getRequest()->getParam('id');
        $type = $this->getRequest()->getParam(Purchaseorder::TYPE);
        try {
            $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($purchaseId);
            if ($purchaseOrder && $purchaseOrder->getPurchaseOrderId()) {
                Mage::register('current_purchase_order', $purchaseOrder, true);
                try {
                    if (Magestore_Coresuccess_Model_Service::purchaseorderService()->sendEmailToSupplier($purchaseOrder))
                        $this->getAdminSession()->addSuccess($this->__('An email has been sent to supplier'));
                    else
                        $this->getAdminSession()->addError($this->__('Cannot send email to supplier'));
                } catch (\Exception $e) {
                    $this->getAdminSession()->addError($this->__('Cannot find supplier email address'));
                }
            } else {
                $this->getAdminSession()->addError($this->__('Cannot send email to supplier'));
            }
        } catch (\Exception $e) {
            $this->getAdminSession()->addError($e->getMessage());
        }
        return $this->redirectForm($type, $purchaseId);
    }

    public function receiveditemAction()
    {
        return $this->gridAction();
    }

    public function shortfallitemAction()
    {
        return $this->gridAction();
    }

    public function returneditemAction()
    {
        return $this->gridAction();
    }

    public function transferreditemAction()
    {
        return $this->gridAction();
    }

    public function invoiceAction()
    {
        return $this->gridAction();
    }

    public function printAction()
    {
        return $this->gridAction();
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/purchaseorder');
    }    
}