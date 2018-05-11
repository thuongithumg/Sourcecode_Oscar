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
class Magestore_Webpos_Adminhtml_Report_Sales_Location_DailyController extends Mage_Adminhtml_Controller_Report_Abstract {

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

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_sales_location_daily.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Retrieve array of collection names by code specified in request
     *
     * @deprecated after 1.4.0.1
     * @return array
     */
    protected function _getCollectionNames()
    {
        return array();
    }

    /**
     * Refresh statistics for last 25 hours
     *
     * @deprecated after 1.4.0.1
     * @return Mage_Adminhtml_Report_SalesController
     */
    public function refreshRecentAction()
    {
        return $this->_forward('refreshRecent', 'report_statistics');
    }

    /**
     * Refresh statistics for all period
     *
     * @deprecated after 1.4.0.1
     * @return Mage_Adminhtml_Report_SalesController
     */
    public function refreshLifetimeAction()
    {
        return $this->_forward('refreshLifetime', 'report_statistics');
    }

    /**
     * Export sales report grid to CSV format
     */
    public function exportSalesCsvAction()
    {
        $fileName   = 'sales.csv';
        $grid       = $this->getLayout()->createBlock('webpos/adminhtml_report_sales_location_daily_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportSalesExcelAction()
    {
        $fileName   = 'sales.xml';
        $grid       = $this->getLayout()->createBlock('webpos/adminhtml_report_sales_location_daily_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function taxAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Tax'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_TAX_FLAG_CODE, 'tax');

        $this->_initAction()
            ->_setActiveMenu('report/sales/tax')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tax'), Mage::helper('adminhtml')->__('Tax'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_tax.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export tax report grid to CSV format
     */
    public function exportTaxCsvAction()
    {
        $fileName   = 'tax.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/report_sales_tax_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export tax report grid to Excel XML format
     */
    public function exportTaxExcelAction()
    {
        $fileName   = 'tax.xml';
        $grid       = $this->getLayout()->createBlock('adminhtml/report_sales_tax_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function shippingAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Shipping'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_SHIPPING_FLAG_CODE, 'shipping');

        $this->_initAction()
            ->_setActiveMenu('report/sales/shipping')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Shipping'), Mage::helper('adminhtml')->__('Shipping'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_shipping.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export shipping report grid to CSV format
     */
    public function exportShippingCsvAction()
    {
        $fileName   = 'shipping.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/report_sales_shipping_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export shipping report grid to Excel XML format
     */
    public function exportShippingExcelAction()
    {
        $fileName   = 'shipping.xml';
        $grid       = $this->getLayout()->createBlock('adminhtml/report_sales_shipping_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function invoicedAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Total Invoiced'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_INVOICE_FLAG_CODE, 'invoiced');

        $this->_initAction()
            ->_setActiveMenu('report/sales/invoiced')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Total Invoiced'), Mage::helper('adminhtml')->__('Total Invoiced'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_invoiced.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export invoiced report grid to CSV format
     */
    public function exportInvoicedCsvAction()
    {
        $fileName   = 'invoiced.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/report_sales_invoiced_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export invoiced report grid to Excel XML format
     */
    public function exportInvoicedExcelAction()
    {
        $fileName   = 'invoiced.xml';
        $grid       = $this->getLayout()->createBlock('adminhtml/report_sales_invoiced_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function refundedAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Total Refunded'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_REFUNDED_FLAG_CODE, 'refunded');

        $this->_initAction()
            ->_setActiveMenu('report/sales/refunded')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Total Refunded'), Mage::helper('adminhtml')->__('Total Refunded'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_refunded.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export refunded report grid to CSV format
     */
    public function exportRefundedCsvAction()
    {
        $fileName   = 'refunded.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/report_sales_refunded_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export refunded report grid to Excel XML format
     */
    public function exportRefundedExcelAction()
    {
        $fileName   = 'refunded.xml';
        $grid       = $this->getLayout()->createBlock('adminhtml/report_sales_refunded_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function couponsAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Coupons'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_COUPONS_FLAG_CODE, 'coupons');

        $this->_initAction()
            ->_setActiveMenu('report/sales/coupons')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Coupons'), Mage::helper('adminhtml')->__('Coupons'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_coupons.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export coupons report grid to CSV format
     */
    public function exportCouponsCsvAction()
    {
        $fileName   = 'coupons.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/report_sales_coupons_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export coupons report grid to Excel XML format
     */
    public function exportCouponsExcelAction()
    {
        $fileName   = 'coupons.xml';
        $grid       = $this->getLayout()->createBlock('adminhtml/report_sales_coupons_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * @deprecated after 1.4.0.1
     */
    public function refreshStatisticsAction()
    {
        return $this->_forward('index', 'report_statistics');
    }
    
    /**
     * 
     * @return bool
     */
    protected function _isAllowed() 
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/webpos/webpos_report');
    }        

    /*
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'sales':
                return $this->_getSession()->isAllowed('report/salesroot/sales');
                break;
            case 'tax':
                return $this->_getSession()->isAllowed('report/salesroot/tax');
                break;
            case 'shipping':
                return $this->_getSession()->isAllowed('report/salesroot/shipping');
                break;
            case 'invoiced':
                return $this->_getSession()->isAllowed('report/salesroot/invoiced');
                break;
            case 'refunded':
                return $this->_getSession()->isAllowed('report/salesroot/refunded');
                break;
            case 'coupons':
                return $this->_getSession()->isAllowed('report/salesroot/coupons');
                break;
            case 'shipping':
                return $this->_getSession()->isAllowed('report/salesroot/shipping');
                break;
            case 'bestsellers':
                return $this->_getSession()->isAllowed('report/products/bestsellers');
                break;
            default:
                return $this->_getSession()->isAllowed('report/salesroot');
                break;
        }
    }
    */
}
