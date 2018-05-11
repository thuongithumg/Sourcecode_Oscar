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

class Magestore_Reportsuccess_Adminhtml_Inventoryreport_DetailsController extends Mage_Adminhtml_Controller_Action {

    /**
     * @return $this
     */
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('reportsuccess/report')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Retailer Report'), Mage::helper('adminhtml')->__('Retailer Report')
            ) ->_title($this->__('Inventory'))
            ->_title($this->__('Retailer Report'));
        $this->_title($this->__('Stock Quantity Report'));
        return $this;
    }
    /**
     * index action
     */
    public function indexAction() {
        /* check and update metrics if not exist */
        Mage::helper('reportsuccess')->service()
            ->updateDementionAndMetrics(null,Magestore_Reportsuccess_Helper_Data::detailsGridJsObject);
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     *
     */
    public function indexgridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *
     */
    public function getTotalsAction(){
        //$this->getResponse()->setBody($data);
        $data = Mage::helper('reportsuccess')->totalService(Magestore_Reportsuccess_Helper_Data::DETAILS);
        echo json_encode($data);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('reportsuccess/report/inventoryreport');
    }

    /**
     *
     */
    public function exportCsvAction() {
        $fileName = 'details_report.csv';
        $content = $this->getLayout()
            ->createBlock('reportsuccess/adminhtml_inventoryreport_details_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

}
