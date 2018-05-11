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

class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_ReturnController
    extends Magestore_Purchaseordersuccess_Controller_ReturnAbstract
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_setActiveMenu('purchaseordersuccess/return/view')
            ->_title($this->__('Return Requests'))
            ->renderLayout();
    }

    /**
     * Export purchase order grid to csv file
     */
    public function exportCsvAction()
    {
        $fileName = 'returnrequest.csv';
        $content = $this->getLayout()->createBlock('purchaseordersuccess/adminhtml_return_grid')->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export purchase order grid to xml file
     */
    public function exportXmlAction()
    {
        $fileName = 'returnrequest.xml';
        /* $content = $this->getLayout()->createBlock('purchaseordersuccess/adminhtml_return_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content); */
        $grid = $this->getLayout()->createBlock('purchaseordersuccess/adminhtml_return_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));

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

        $register = $this->returnService->registerReturnRequest($id);
        if ($register !== null) {
            $this->getAdminSession()->addError($register->getMessage());
            $this->redirectGrid();
        }
        $this->_initAction()
            ->_title($this->__('Return Request'));
        if ($id) {
            $code = Mage::registry('current_return_request')->getReturnCode();
            $code = $code ? $code : $id;
            $this->_title($this->__('View Return Request #%s', $code));
            $this->_setActiveMenu('purchaseordersuccess/return/view');
            if (Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Barcodesuccess')) {
//                $this->getLayout()->getUpdate()->addHandle('adminhtml_purchaseordersuccess_barcode_scan');
//                $this->loadLayoutUpdates();
//                $this->generateLayoutXml()->generateLayoutBlocks();
            }
        } else {
            $this->_title($this->__('New Return Request'));
            $this->_setActiveMenu('purchaseordersuccess/return/create');
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
        $id = (isset($params['return_id']) && $params['return_id'] > 0) ? $params['return_id'] : null;
        
        /** @var Magestore_Purchaseordersuccess_Model_Return $returnRequest */
        $returnRequest = Mage::getModel('purchaseordersuccess/return');
        if ($id) {
            try {
                $returnRequest->load($id);
            } catch (\Exception $e) {
                $this->getAdminSession()->addError($e->getMessage());
                return $this->redirectGrid();
            }
        }
        $returnRequest->addData($params)->setId($id);

        try {
            $returnRequest->save();
            $returnRequest = Mage::getModel('purchaseordersuccess/return')->load($returnRequest->getId());
            $supplier = Mage::getModel('suppliersuccess/supplier')->load($returnRequest->getSupplierId());
            $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($returnRequest->getWarehouseId());
            if (isset($params['selected_items']) && !empty($params['selected_items'])) {
                $selectedItems = Zend_Json::decode($params['selected_items']);
                if (!empty($selectedItems)) {
                    $this->returnService->addProductsToReturnRequest($returnRequest, $supplier, $warehouse, $selectedItems);
                }
            }
            if ($actionType = $this->getRequest()->getParam('action_type')) {
                try {
                    $this->returnService->$actionType($returnRequest);
                    if($actionType == 'delete') {
                        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Return request has been deleted.'));
                        return $this->redirectGrid();
                    }
                } catch (\Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    return $this->redirectForm($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Return request has been saved.'));
            }
            $this->returnService->updateReturnTotal($returnRequest);
        } catch (\Exception $e) {
            $this->getAdminSession()->addError($e->getMessage());
            return $this->redirectForm($id);
        }
        return $this->redirectForm($returnRequest->getReturnOrderId());
    }

    /**
     * Send purchase order request action
     *
     * @return $this
     */
    public function sendrequestAction()
    {
        $returnId = $this->getRequest()->getParam('id');
        try {
            /** @var Magestore_Purchaseordersuccess_Model_Return $returnRequest */
            $returnRequest = Mage::getModel('purchaseordersuccess/return')->load($returnId);
            if ($returnRequest && $returnRequest->getReturnOrderId()) {
                Mage::register('current_return_request', $returnRequest, true);
                try {
                    if (Mage::getSingleton('purchaseordersuccess/service_returnService')->sendEmailToSupplier($returnRequest))
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
        return $this->redirectForm($returnId);
    }

    public function transferreditemAction()
    {
        return $this->gridAction();
    }

    public function printAction()
    {
        return $this->gridAction();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/return');
    }
}