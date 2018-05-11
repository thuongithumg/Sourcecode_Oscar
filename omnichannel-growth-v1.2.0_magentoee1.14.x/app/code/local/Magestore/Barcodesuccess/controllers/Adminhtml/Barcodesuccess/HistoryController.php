<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Barcodesuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Adminhtml_Barcodesuccess_HistoryController extends
    Mage_Adminhtml_Controller_Action
{

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/barcodesuccess/history');
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('barcodesuccess/history')
             ->_addBreadcrumb(
                 Mage::helper('adminhtml')->__('Barcode Creating History'),
                 Mage::helper('adminhtml')->__('Barcode Creating History')
             )->_title(Mage::helper('barcodesuccess')->__('Barcode Creating History'));
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function gridAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'barcode_created_history.csv';
        $content  = $this->getLayout()
                         ->createBlock('barcodesuccess/adminhtml_history_grid')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'barcode_created_history.xml';
       /* $content  = $this->getLayout()
                         ->createBlock('barcodesuccess/adminhtml_history_grid')
                         ->getXml();
        $this->_prepareDownloadResponse($fileName, $content); */
        $grid       = $this->getLayout()->createBlock('barcodesuccess/adminhtml_history_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));

    }

    /**
     *
     */
    public function viewAction()
    {
        $historyId = $this->getRequest()->getParam('id');
        $history   = Mage::getModel('barcodesuccess/history')->load($historyId);

        if ( $history->getId() ) {
            Mage::register('history_data', $history);
            $this->loadLayout();
            $this->_setActiveMenu('barcodesuccess/history');
            $this->_addBreadcrumb(
                Mage::helper('barcodesuccess')->__('History Information'),
                Mage::helper('barcodesuccess')->__('History Information')
            );
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('barcodesuccess/adminhtml_import_notice'))
                 ->_addContent($this->getLayout()->createBlock('barcodesuccess/adminhtml_history_edit'))
                 ->_addLeft($this->getLayout()->createBlock('barcodesuccess/adminhtml_history_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('barcodesuccess')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function historyviewAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function historyviewgridAction()
    {
        return $this->loadLayout()->renderLayout();
    }
}
