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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_ManagestockController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('inventorysuccess/stocklisting/stock_in_warehouse')
            ->_addBreadcrumb(
                $this->__('Inventory'),
                $this->__('Manage Stock')
            )
            ->_title($this->__('Inventory'))
            ->_title($this->__('Manage Stock'));
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
     * Export stock onhand csv file
     */
    public function exportStockOnHandCsvAction()
    {
        $warehouseId = $this->getRequest()->getParam('warehouse_id', false);
        $warehouseinfo = '';
        if ($warehouseId) {
            $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
            $warehouseinfo = $warehouse->getWarehouseCode() . '-';
        }
        $fileName = 'warehouse-stock-onhand-' . $warehouseinfo . date('Ymd') . '.csv';
        $content = $this->getLayout()->createBlock('inventorysuccess/adminhtml_manageStock_product_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export stock onhand xml file
     */
    public function exportStockOnHandXmlAction()
    {
        $warehouseId = $this->getRequest()->getParam('warehouse_id', false);
        $warehouseinfo = '';
        if ($warehouseId) {
            $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
            $warehouseinfo = $warehouse->getWarehouseCode() . '-';
        }
        $fileName = 'warehouse-stock-onhand-' . $warehouseinfo . date('Ymd') . '.xml';
        $content = $this->getLayout()->createBlock('inventorysuccess/adminhtml_manageStock_product_grid')->getXml();
        $content = str_replace('sum_', '', $content);
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/inventorysuccess/stocklisting/stock_in_warehouse');
    }
}
