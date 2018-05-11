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

class Magestore_Reportsuccess_Adminhtml_Inventoryreport_LocationsController extends Mage_Adminhtml_Controller_Action {

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
        $this->_title($this->__('Report by Warehouse'));
        return $this;
    }
    /**
     * index action
     */
    public function indexAction() {
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
        $fileName = 'locations_report.csv';
        $content = $this->getLayout()
            ->createBlock('reportsuccess/adminhtml_inventoryreport_locations_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }


}
