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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webpos Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Adminhtml_Report_Sales_LocationController extends Mage_Adminhtml_Controller_Report_Abstract {

    /**
     * Add report/sales breadcrumbs
     *
     * @return Mage_Adminhtml_Report_SalesController
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('reports')->__('Sales'), Mage::helper('reports')->__('Sales'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

//        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_setActiveMenu('report/sales/sales')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_sales_location.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export sales report grid to CSV format
     */
    public function exportSalesCsvAction()
    {
        $fileName   = 'sales.csv';
        $grid       = $this->getLayout()->createBlock('webpos/adminhtml_report_sales_location_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportSalesExcelAction()
    {
        $fileName   = 'sales.xml';
        $grid       = $this->getLayout()->createBlock('webpos/adminhtml_report_sales_location_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
    /**
     * 
     * @return bool
     */
    protected function _isAllowed() 
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/webpos/webpos_report');
    }     
}
