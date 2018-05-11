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
class Magestore_Purchaseordersuccess_Controller_Action
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Service_PurchaseorderService
     */
    protected $purchaseorderService;

    public function _construct()
    {
        $this->purchaseorderService = Magestore_Coresuccess_Model_Service::purchaseorderService();
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
            ->_title($this->__('Purchase Management'));
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
        $this->purchaseorderService->registerPurchaseOrder($id);
        return $this->_initAction()
            ->renderLayout();
    }

    /**
     * Get type label of current item
     *
     * @param int $type
     * @return string
     */
    public function getTypeLabel($type)
    {
        return Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type::getTypeLabel($type);
    }

    /**
     * Redirect to grid quotation or purchase order
     *
     * @param int $type
     * @return $this
     */
    protected function redirectGrid($type)
    {
        $controllerName = $type == Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type::TYPE_QUOTATION
            ? 'purchaseordersuccess_quotation'
            : 'purchaseordersuccess_purchaseorder';
        return $this->_redirect('*/' . $controllerName . '/index');
    }

    /**
     * @param $type
     * @param int $id
     * @return $this
     */
    protected function redirectForm($type, $id = null)
    {
        $controllerName = $type == Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type::TYPE_QUOTATION
            ? 'purchaseordersuccess_quotation'
            : 'purchaseordersuccess_purchaseorder';
        $action = $id ? 'view' : 'new';
        $params = $id ? array('id' => $id) : array();
        return $this->_redirect('*/' . $controllerName . '/' . $action, $params);
    }

    protected function getDisplayButton()
    {
        $buttonShow = $buttonHide = array();
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::registry('current_purchase_order');
        if (!$purchaseOrder) {
            $this->purchaseorderService->registerPurchaseOrder($this->getRequest()->getParam('id'));
            $purchaseOrder = Mage::registry('current_purchase_order');
        }
        if ($purchaseOrder->canReceiveItem())
            array_push($buttonShow, 'receive_item_button_top');
        else
            array_push($buttonHide, 'receive_item_button_top');
        if (Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess') && $purchaseOrder->canTransferItem())
            array_push($buttonShow, 'transfer_item_button_top');
        else
            array_push($buttonHide, 'transfer_item_button_top');
        return array('button_show' => $buttonShow, 'button_hide' => $buttonHide);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/purchaseorder');
    }     
    
}