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

class Magestore_Reportsuccess_Adminhtml_Inventoryreport_HistoricsController extends Mage_Adminhtml_Controller_Action {

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
        $this->_title($this->__('Historical Report'));
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
     * return CSV
     */
    public function exportCsvAction() {
        $fileName = 'historics_report.csv';
        $content = $this->getLayout()
            ->createBlock('reportsuccess/adminhtml_inventoryreport_historics_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * @return mixed
     */
    public function downloadAction(){
        /* @var $backup Mage_Backup_Model_Backup */
        $backup = Mage::getModel('reportsuccess/historics')->loadByTimeAndType(
            $this->getRequest()->getParam('time'),
            $this->getRequest()->getParam('type')
        );
        if (!$backup->getTime() || !$backup->exists()) {
            return $this->_redirect('*/*');
        }
        $fileName = Mage::helper('reportsuccess/backup')->generateBackupDownloadName($backup);
        $this->_prepareDownloadResponse($fileName, null, 'application/octet-stream', $backup->getSize());
        $this->getResponse()->sendHeaders();
        $backup->output();
        exit();
    }

    /**
     * @return mixed
     */
    public function massDeleteAction()
    {
        $backupIds = $this->getRequest()->getParam('ids', array());
        if (!is_array($backupIds) || !count($backupIds)) {
            return $this->_redirect('*/*/index');
        }
        /** @var $backupModel Mage_Backup_Model_Backup */
        $backupModel = Mage::getModel('reportsuccess/historics');
        $resultData = new Varien_Object();
        $resultData->setIsSuccess(false);
        $resultData->setDeleteResult(array());
        Mage::register('backup_manager_ms', $resultData);
        $deleteFailMessage = Mage::helper('backup')->__('Failed to delete one or several backups.');
        try {
            $allBackupsDeleted = true;
            foreach ($backupIds as $id) {
                list($time, $type) = explode('_', $id);
                $backupModel
                    ->loadByTimeAndType($time, $type)
                    ->deleteFile();

                if ($backupModel->exists()) {
                    $allBackupsDeleted = false;
                    $result = Mage::helper('adminhtml')->__('failed');
                } else {
                    $result = Mage::helper('adminhtml')->__('successful');
                }

                $resultData->setDeleteResult(
                    array_merge($resultData->getDeleteResult(), array($backupModel->getFileName() . ' ' . $result))
                );
            }
            $resultData->setIsSuccess(true);
            if ($allBackupsDeleted) {
                $this->_getSession()->addSuccess(
                    Mage::helper('backup')->__('The selected backup(s) has been deleted.')
                );
            }
            else {
                throw new Exception($deleteFailMessage);
            }
        } catch (Exception $e) {
            $resultData->setIsSuccess(false);
            $this->_getSession()->addError($deleteFailMessage);
        }
        $code = Magestore_Reportsuccess_Helper_Data::HISTORICS;
        $report_header = Mage::helper('reportsuccess')->base64Encode($code);
        return $this->_redirect('*/*/index',array('report'=>$report_header));
    }
}
