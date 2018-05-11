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
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

class Magestore_Reportsuccess_Adminhtml_Salesreport_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('reportsuccess/report')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('ReportSuccess'), Mage::helper('adminhtml')->__('ReportSuccess')
            )->_title($this->__('Inventory'))
            ->_title($this->__('ReportSuccess'));
        $this->_title($this->__('Sales Reports'));

        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {

        //$this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');
        /* save Dimensions */
        $typeReport = $this->getRequest()->getParam('type');
        if($typeReport){
            Mage::helper('reportsuccess')->service()
                ->updateDementionAndMetrics($typeReport,Magestore_Reportsuccess_Helper_Data::salesreportGridJsObjectdimentions);
        }
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Add refresh statistics links
     *
     * @param string $flagCode
     * @param string $refreshCode
     * @return Mage_Adminhtml_Controller_Report_Abstract
     */
    protected function _showLastExecutionTime($flagCode, $refreshCode)
    {
        $cpBlock = $this->getLayout()->getBlockSingleton('Magestore_Reportsuccess_Block_Adminhtml_Salesreport_Criteria');
        $updatedAt = $cpBlock->lastUpdateSaleReport();
        Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('Last updated: <strong id ="time_notification"> %s </strong>. To refresh lastest time statistics</a>,
                please click <a onclick= "SalesreportCeritial.updateSalesReportData(this)" >Here</a>.', $updatedAt));
        return $this;
    }

    /**
     *
     */
    public function indexgridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *
     */
    public function exportCsvAction()
    {
        $fileName = 'sales_report.csv';
        $content = $this->getLayout()
            ->createBlock('reportsuccess/adminhtml_salesreport_grid_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function getTotalsAction()
    {
        $data = Mage::helper('reportsuccess')->totalService(Magestore_Reportsuccess_Helper_Data::SALESREPORT);
        echo json_encode($data);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('reportsuccess/report/salereport');
    }

    /**
     * updateSalesData
     */
    public function updateSalesDataAction()
    {
        $data = Mage::getResourceModel('reportsuccess/salesreport')->reindexAll();
        echo json_encode($data);
    }

    public function orderAction()
    {
        return $this->loadLayout()
            ->renderLayout();
    }
}
