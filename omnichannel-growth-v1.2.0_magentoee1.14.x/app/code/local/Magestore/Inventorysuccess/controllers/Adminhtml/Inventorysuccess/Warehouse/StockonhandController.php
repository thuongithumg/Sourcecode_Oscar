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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Warehouse_StockonhandController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Get stock on hand grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {
        $warehouseId = $this->getRequest()->getParam('id', false);
        $warehouse = Mage::getResourceModel('inventorysuccess/warehouse_product_collection')
            ->getTotalQtysFromWarehouse($warehouseId);
        $permissionDelete = Magestore_Coresuccess_Model_Service::permissionService()->checkPermission(
            'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/delete_warehouse',
            Mage::getModel('inventorysuccess/warehouse')->load($warehouseId)
        );
        $canDelete = false;
        if ($warehouse->getSumTotalQty() <= 0 && $warehouse->getSumQtyToShip() <= 0 && $permissionDelete) {
            $canDelete = true;
        }
        $selectedProduct = json_decode($this->getRequest()->getParam('selected_items'), true);
        if ($warehouseId && count($selectedProduct) > 0) {
            Magestore_Coresuccess_Model_Service::warehouseStockService()
                ->updateStockInGrid($warehouseId, $selectedProduct);
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The stocks information has been saved.'));
            $warehouse = Mage::getResourceModel('inventorysuccess/warehouse_product_collection')
                ->getTotalQtysFromWarehouse($warehouseId);
            if (($warehouse->getSumTotalQty() <= 0 && $warehouse->getSumQtyToShip() <= 0 && $permissionDelete) ||
                (($warehouse->getSumTotalQty() > 0 || $warehouse->getSumQtyToShip() > 0) && $canDelete)
            ) {
                return $this->getResponse()->setBody($this->_getDeniedJson($warehouseId));
            }
        }
        return $this->_forward('grid');
    }

    /**
     * Retrieve response
     *
     * @param $warehouseId
     * @return mixed
     */
    protected function _getDeniedJson($warehouseId)
    {
        return Mage::helper('core')->jsonEncode(array(
            'ajaxExpired' => 1,
            'ajaxRedirect' => $this->getUrl(
                '*/inventorysuccess_warehouse/edit', array('id' => $warehouseId, 'stock_on_hand' => true)
            )
        ));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed(
            'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/view_stock_on_hand'
        );
    }
}