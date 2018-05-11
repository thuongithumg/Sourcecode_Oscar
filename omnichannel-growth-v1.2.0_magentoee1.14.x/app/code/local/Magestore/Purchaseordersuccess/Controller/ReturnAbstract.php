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
class Magestore_Purchaseordersuccess_Controller_ReturnAbstract
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Service_ReturnService
     */
    protected $returnService;

    public function _construct()
    {
        $this->returnService = Mage::getSingleton('purchaseordersuccess/service_returnService');
    }

    /**
     * @return Mage_Adminhtml_Model_Session
     */
    protected function getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('purchaseordersuccess')
            ->_title($this->__('Return Management'));
        return $this;
    }

    /**
     * Grid purchase order item action
     *
     * @return $this
     */
    public function gridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->returnService->registerReturnRequest($id);
        return $this->_initAction()
            ->renderLayout();
    }

    /**
     * Redirect to grid quotation or purchase order
     *
     * @param int $type
     * @return $this
     */
    protected function redirectGrid()
    {
        return $this->_redirect('*/*/index');
    }

    /**
     * @param $type
     * @param int $id
     * @return $this
     */
    protected function redirectForm($id = null)
    {
        $action = $id ? 'view' : 'new';
        $params = $id ? array('id' => $id) : array();
        return $this->_redirect('*/*/' . $action, $params);
    }

    protected function getDisplayButton()
    {
        $buttonShow = $buttonHide = array();
        /** @var Magestore_Purchaseordersuccess_Model_Return $returnRequest */
        $returnRequest = Mage::registry('current_return_request');
        if (!$returnRequest) {
            $this->returnService->registerReturnRequest($this->getRequest()->getParam('id'));
            $returnRequest = Mage::registry('current_return_request');
        }

        if (Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess') && $returnRequest->canTransferItem())
            array_push($buttonShow, 'transfer_item_button_top');
        else
            array_push($buttonHide, 'transfer_item_button_top');
        return array('button_show' => $buttonShow, 'button_hide' => $buttonHide);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/return');
    }

}