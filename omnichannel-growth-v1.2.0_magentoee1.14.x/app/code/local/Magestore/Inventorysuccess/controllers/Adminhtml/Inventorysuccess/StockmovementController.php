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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_StockmovementController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('inventorysuccess/stockcontrol/stock_movement_history')
            ->_addBreadcrumb(
                $this->__('Inventory'),
                $this->__('Stock Movement')
            )
            ->_title($this->__('Inventory'))
            ->_title($this->__('Stock Movement'));
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
    
    public function gridAction(){
        $this->loadLayout()
            ->renderLayout();
    }
    
    /**
     * Export warehouse grid to csv file
     */
    public function exportCsvAction()
    {
        $fileName = 'stock_movement.csv';
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_stockMovement_grid')->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export warehouse grid to xml file
     */
    public function exportXmlAction()
    {
        $fileName = 'stock_movement.xml';
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_stockMovement_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/inventorysuccess/stockcontrol/stock_movement_history');
    }
}
