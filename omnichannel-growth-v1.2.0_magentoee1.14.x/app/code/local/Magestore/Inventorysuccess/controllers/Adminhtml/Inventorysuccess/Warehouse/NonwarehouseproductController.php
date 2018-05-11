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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Warehouse_NonwarehouseproductController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('inventorysuccess/stocklisting/non_warehouse_product')
            ->_addBreadcrumb(
                $this->__('Inventory'),
                $this->__('Non-Warehouse Product')
            )
            ->_title($this->__('Inventory'))
            ->_title($this->__('Non-Warehouse Product'));
        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

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
        return $this->_forward('grid');
    }

    public function masswarehouseAction()
    {
        $productIds = $this->getRequest()->getParam('product_id');
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        if (empty($productIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select products.'));
        } else if (empty($warehouseId)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select a warehouse.'));
        } else {
            Magestore_Coresuccess_Model_Service::warehouseService()->addProductToWarehouse($warehouseId, $productIds);
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Products have been successfully added to warehouse.'));
        }
        return $this->getResponse()->setBody('success');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/inventorysuccess/stocklisting/non_warehouse_product');
    }
}