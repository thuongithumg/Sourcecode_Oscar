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

class Magestore_Webpos_Adminhtml_ZreportController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('webpos/manage_webpos_zreport')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('POS Z-Report Manager'),
                Mage::helper('adminhtml')->__('POS Z-Report Manager')
            );
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
     * print action
     */
    public function printAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'pos_zreport.csv';
        $content    = $this->getLayout()
                           ->createBlock('webpos/adminhtml_zreport_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'pos_zreport.xml';
        $content    = $this->getLayout()
                           ->createBlock('webpos/adminhtml_zreport_grid')
                           ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/webpos/manage_webpos_zreport');
    }
}