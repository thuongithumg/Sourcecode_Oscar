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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_StockMovement_TransferController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        Magestore_Coresuccess_Model_Service::stockTransferService()->addAllStockMovement();
        $this->loadLayout()
            ->_setActiveMenu('inventorysuccess/stockcontrol/stock_transfer')
            ->_addBreadcrumb(
                $this->__('Inventory'),
                $this->__('Receipt/ Delivery History')
            )
            ->_title($this->__('Inventory'))
            ->_title($this->__('Receipt/ Delivery History'));
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
     * grid action
     */
    public function gridAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * view action
     */
    public function viewAction()
    {
        $id = $this->getRequest()->getParam('stock_transfer_id');
        if (!$id) {
            return $this->_redirect('*/*/index');
        }
        $stockTransfer = Mage::getModel('inventorysuccess/stockMovement_stockTransfer')->load($id);
        if (!$stockTransfer->getId()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Selected history is not existed.'));
            return $this->_redirect('*/*/index');
        }
        Mage::register('current_stock_transfer', $stockTransfer);
        $this->_initAction();
        $this->loadLayout()->_title('View');
        $this->renderLayout();
    }

    /**
     * grid item action
     */
    public function itemsAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     * Export stock transfer csv file
     */
    public function exportCsvAction()
    {
        $fileName = 'stock-transfer.csv';
        $content = $this->getLayout()->createBlock('inventorysuccess/adminhtml_stockMovement_transfer_grid')->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export stock transfer xml file
     */
    public function exportXmlAction()
    {
        $fileName = 'stock-transfer.xml';
        $content = $this->getLayout()->createBlock('inventorysuccess/adminhtml_stockMovement_transfer_grid')->getExcelFile();
        $content = str_replace('sum_', '', $content);
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export stock transfer item csv file
     */
    public function exportItemsCsvAction()
    {
        $fileName = 'stock-transfer-items.csv';
        $content = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_stockMovement_transfer_edit_tab_items')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export stock transfer xml file
     */
    public function exportItemsXmlAction()
    {
        $fileName = 'stock-transfer_items.xml';
        $content = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_stockMovement_transfer_edit_tab_items')
            ->getXml();
        $content = str_replace('sum_', '', $content);
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/inventorysuccess/stockcontrol/stock_transfer');
    }
}
