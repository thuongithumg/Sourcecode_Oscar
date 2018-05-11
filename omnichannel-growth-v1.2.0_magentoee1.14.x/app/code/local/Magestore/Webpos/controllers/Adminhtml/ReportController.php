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
class Magestore_Webpos_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title($this->__('Webpos'))
            ->_title($this->__('Web POS Sales Report'));
        $this->loadLayout()
            ->_setActiveMenu('webpos');
        $this->renderLayout();
        return $this;
    }

    public function gridAction() {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function exportXmlAction() {
        $session = Mage::getModel('core/session');
        if ($session->getType() == 'location') {
            $fileName = 'Sales_By_Location.xml';
            $gridBlock = $this->getLayout()->createBlock('webpos/adminhtml_report_location');
        }
        if ($session->getType() == 'user' || !$session->getType()) {
            $fileName = 'Sales_By_User.xml';
            $gridBlock = $this->getLayout()->createBlock('webpos/adminhtml_report_grid');
        }
        $conditions = $session->getData('rp_conditions');
        $gridBlock->setFilterCondition($conditions);
        $this->_prepareDownloadResponse($fileName, $gridBlock->getExcelFile());
    }

    public function exportCsvAction() {
        $session = Mage::getModel('core/session');
        if ($session->getType() == 'location') {
            $fileName = 'Sales_By_Location.csv';
            $gridBlock = $this->getLayout()->createBlock('webpos/adminhtml_report_location');
        }
        if ($session->getType() == 'user' || !$session->getType()) {
            $fileName = 'Sales_By_User.csv';
            $gridBlock = $this->getLayout()->createBlock('webpos/adminhtml_report_grid');
        }
        $conditions = $session->getData('rp_conditions');
        $gridBlock->setFilterCondition($conditions);
        $this->_prepareDownloadResponse($fileName, $gridBlock->getCsvFile());
    }

    public function filterAction() {
        $result = array();
        try {
            $filterBy = $this->getRequest()->getParam('filterBy');
            $period = $this->getRequest()->getParam('period');
            $range = $this->getRequest()->getParam('selectRange');
            $orderStatuses = $this->getRequest()->getParam('order_status');
            $orderType = $this->getRequest()->getParam('order_type');
            $from = $this->getRequest()->getParam('from');
            $to = $this->getRequest()->getParam('to');
            $isShowEmpty = $this->getRequest()->getParam('showEmpty');
            $conditions = array('range' => $range, 'period' => $period, 'from' => $from, 'to' => $to, 'order_statuses' => $orderStatuses, 'rp_settings' => array(
                'show_empty_result' => $isShowEmpty,
            ));
            if ($range != 5) {
                unset($conditions['from']);
                unset($conditions['to']);
            }
            if ($orderType == 0)
                $conditions['order_statuses'] = array();
            if ($filterBy == 'user')
                $gridBlock = $this->getLayout()->createBlock('webpos/adminhtml_report_grid');
            else
                $gridBlock = $this->getLayout()->createBlock('webpos/adminhtml_report_location');
            $gridBlock->setFilterCondition($conditions);
            $gridHtml = $gridBlock->toHtml();
            $result['report_grid'] = $gridHtml;
            $result['success'] = true;
            $totalRow = $gridBlock->getTotalRow();
            if ($filterBy == 'location')
                $result['totalRow'] = $totalRow;
            $totalRowByUser = $gridBlock->getTotalRowByUser();
            if ($filterBy == 'user')
                $result['totalRowByUser'] = $totalRowByUser;
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }
        $this->getResponse()->setBody(json_encode($result));
    }

    public function refreshDataAction() {
        $webPosOrder = Mage::getModel('webpos/posorder')->getCollection()->addFieldToFilter('order_status', array('nin' => 'closed'));
        $wpOrderIds = array();
        foreach ($webPosOrder as $wpOrder) {
            $wpOrderIds[] = $wpOrder->getOrderId();
        }
        $systemOrder = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id', array('in' => $wpOrderIds));
        $count = 0;
        foreach ($systemOrder as $sysOrder) {
            $wposOrder = Mage::getModel('webpos/posorder')->getCollection()
                ->addFieldToFilter('order_id', $sysOrder->getIncrementId())
                ->getFirstItem();
            if ($wposOrder->getOrderStatus() != $sysOrder->getStatus()) {
                try {
                    Mage::getModel('webpos/posorder')->load($wposOrder->getId())->setOrderStatus($sysOrder->getStatus())->save();
                    $count++;
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        $message = "Total of " . $count . " record(s) were successfully updated";
        $this->getResponse()->setBody($message);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('sales/webpos/webpos_report');
    }

}
