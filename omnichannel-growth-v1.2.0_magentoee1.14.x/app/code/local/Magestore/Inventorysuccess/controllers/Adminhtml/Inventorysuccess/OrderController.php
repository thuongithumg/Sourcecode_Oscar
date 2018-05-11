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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorysuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function massWarehouseAction()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        if (!$warehouseId) {
            $this->addError(Mage::helper('inventorysuccess')->__('Please select a warehouse!'));
            return $this->_redirect('adminhtml/sales_order/index');
        }
        if (!$orderIds) {
            $this->addError(Mage::helper('inventorysuccess')->__('Please select orders to change warehouse!'));
            return $this->_redirect('adminhtml/sales_order/index');
        }
        /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouse */
        $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
        if (!$warehouse->getId()) {
            $this->addError(Mage::helper('inventorysuccess')->__('Selected warehouse is not existed!'));
            return $this->_redirect('adminhtml/sales_order/index');
        }
        $orderCollection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => $orderIds));
        /** @var Mage_Sales_Model_Order $order */
        foreach ($orderCollection as $order) {
            if ($order->canShip()) {
                Magestore_Coresuccess_Model_Service::changeOrderWarehouseService()->execute($order, $warehouse);
            }
        }
        $this->addSuccess(Mage::helper('inventorysuccess')->__('Change warehouse for selected orders successfully!'));
        return $this->_redirect('adminhtml/sales_order/index');
    }

    /**
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function changeWarehouseAction()
    {
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$warehouseId) {
            $this->addError(Mage::helper('inventorysuccess')->__('Please select a warehouse!'));
            return $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
        }
        if (!$orderId) {
            $this->addError(Mage::helper('inventorysuccess')->__('Please select an order to change warehouse!'));
            return $this->_redirect('adminhtml/sales_order/index');
        }
        /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouse */
        $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
        if (!$warehouse->getId()) {
            $this->addError(Mage::helper('inventorysuccess')->__('Selected warehouse is not existed!'));
            return $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
        }
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            $this->addError(Mage::helper('inventorysuccess')->__('Please select an order to change warehouse!'));
            return $this->_redirect('adminhtml/sales_order/index');
        }
        if ($order->canShip()) {
            Magestore_Coresuccess_Model_Service::changeOrderWarehouseService()->execute($order, $warehouse);
            $this->addSuccess(Mage::helper('inventorysuccess')->__('Change warehouse for this order successfully!'));
        } else {
            $this->addError(Mage::helper('inventorysuccess')->__('Cannot change warehouse for this order!'));
        }
        return $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }

    /**
     * @param $message
     */
    protected function addError($message)
    {
        return Mage::getSingleton('adminhtml/session')->addError($message);
    }

    /**
     * @param $message
     */
    protected function addSuccess($message)
    {
        return Mage::getSingleton('adminhtml/session')->addSuccess($message);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Magestore_Coresuccess_Model_Service::orderProcessService()->canChangeOrderWarehouse();
    }
}