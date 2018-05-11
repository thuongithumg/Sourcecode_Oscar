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
 * Class Magestore_Inventorysuccess_Model_Observer_Adminhtml_ControllerActionPredispatch
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Observer_Adminhtml_BlockHtmlBefore
{
    /**
     *
     * @param type $observer
     */
    public function execute($observer)
    {
        /** @var Mage_Adminhtml_Block_Template $block */
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid || 
            $block->getId() == 'sales_order_grid'
        ) {
            $this->addChangeWarehouseAction($block);
            return $this;
        }
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            $this->beforeViewOrder($block);
            return $this;
        }
    }

    /**
     * Add change warehouse mass action in order grid
     *
     * @param Mage_Adminhtml_Block_Sales_Order_Grid $block
     * @return $this
     */
    protected function addChangeWarehouseAction($block)
    {
        $orderProcessService = Magestore_Coresuccess_Model_Service::orderProcessService();
        if ($orderProcessService->canChangeOrderWarehouse()) {
            $warehouses = Mage::getSingleton('inventorysuccess/warehouse_options_warehouse')->getOptionHash();
            $block->getMassactionBlock()->addItem('change_warehouse', array(
                'label' => Mage::helper('inventorysuccess')->__('Change Warehouse'),
                'url' => $block->getUrl('adminhtml/inventorysuccess_order/massWarehouse', array('_current' => true)),
                'confirm' => Mage::helper('inventorysuccess')->__('Are you sure you want to change warehouse for selected orders?'),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'warehouse_id',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('inventorysuccess')->__('Warehouse'),
                        'values' => $warehouses
                    ))
            ));
        }
        return $this;
    }

    /**
     * @param Mage_Adminhtml_Block_Sales_Order_View $block
     * @return $this
     */
    protected function beforeViewOrder($block)
    {
        $orderProcessService = Magestore_Coresuccess_Model_Service::orderProcessService();
        $order = Mage::registry('current_order');
        $warehouse = $this->getWarehouse($order);
        if (!$orderProcessService->canChangeOrderWarehouse()) {
            /** @var Mage_Sales_Model_Order $order */
            if ($order) {
                if (!$warehouse || !$warehouse->getId() || !$orderProcessService->canViewWarehouse($warehouse)) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('inventorysuccess')->__('You don not have permission to view order #%s', $order->getIncrementId())
                    );
                    return Mage::app()->getResponse()->setRedirect(Mage::getUrl('adminhtml/sales_order/index'));
                }
            }
        }

        if (!$orderProcessService->canCreateShipment($warehouse)) {
            $block->removeButton('order_ship');
        }
        if (!$orderProcessService->canCreateCreditmemo($warehouse)) {
            $block->removeButton('order_creditmemo');
        }
        if (!$orderProcessService->canCancelOrder($warehouse)) {
            $block->removeButton('order_cancel');
        }
    }

    /**
     * Get warehouse by order
     *
     * @param Mage_Sales_Model_Order $order
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function getWarehouse($order)
    {
        $warehouse = Mage::registry('current_warehouse');
        if (!$warehouse) {
            $warehouseId = $order->getWarehouseId();
            $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
            Mage::register('current_warehouse', $warehouse);
        }
        return $warehouse;
    }
}