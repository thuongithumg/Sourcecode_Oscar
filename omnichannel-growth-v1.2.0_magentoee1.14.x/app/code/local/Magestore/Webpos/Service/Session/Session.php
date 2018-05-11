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

class Magestore_Webpos_Service_Session_Session extends Magestore_Webpos_Service_Abstract
{
    public function getSessionData($postId) {
        $result = array();
        $result['items'] = array();
        $saleSummaryModel = Mage::getModel('webpos/saleSummary');
        $sessionColection = Mage::getModel('webpos/shift')->getCollection()
            ->setOrder('opened_at', 'DESC')->addFieldToFilter('pos_id', $postId);
        foreach ($sessionColection as $session) {
            $shiftId = $session['shift_id'];
            $allCashTransaction = Mage::getModel('webpos/shift_cashtransaction')->getCollection()->addFieldToFilter("shift_id", $shiftId)->getData();
            $data = $session->getData();
            //$data['opened_at'] = Mage::helper('webpos')->formatDate($data['opened_at']);
            //$data['closed_at'] = Mage::helper('webpos')->formatDate($data['closed_at']);
            $data['sale_summary'] =  $saleSummaryModel->getSaleSummary($shiftId);
            $data['cash_transaction'] = $allCashTransaction;
            $data['staff_name'] = $session->getStaffName();
            $data['pos_name'] = $session->getPosName();
            $data['zreport_sales_summary'] = $session->getZreportSalesSummary()->getData();
            $result['items'][] = $data;
        }
        $result['total_count'] = $sessionColection->getSize();
        return $result;
    }


    /**
     * @param $zReportData
     * @return mixed
     */
    public function closeShift($zReportData){
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        try{
            $model = $this->_getModel('webpos/zreport');
            $model->setData($zReportData);
            $model->save();
            $transactionService = $this->_createService('transaction_transaction');
            $transactionService->deactiveByCashDrawerId($model->getTillId());
            $transactionService->openStoreAfterClose($model);
        }catch (Exception $e){
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
            $message[] = $e->getMessage();
        }
        return $this->getResponseData($data, $message, $status, false);
    }

    /**
     * get detail information of a shift with specify shift_id
     * this function call to detail function of Shift Model
     * @param int $shift_id
     * @return mixed
     */
    public function detail($shift_id)
    {
        $shiftModel = Mage::getModel('webpos/shift');
        $data = $shiftModel->detail($shift_id);

        return $data;
    }


    public function save($shift)
    {
        //$indexeddbId = $shift->getIndexeddbId();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        $shiftModel = Mage::getModel('webpos/shift');
        $shiftModel->setData($shift);
        try {
            //add by Mark to make sure don't duplicate session
            $oldShiftModel = $shiftModel->getCollection()->addFieldToFilter('shift_id',$shift['shift_id'])
                ->getFirstItem();
            if($oldShiftModel->getEntityId()){
                $shiftModel->setEntityId($oldShiftModel->getEntityId());
            }
            //end add by Mark
            $shiftModel->save();

        } catch (\Exception $exception) {

        }

        $shiftId = $shiftModel->getShiftId();
        if (!$shiftId) {
            return;
        } else {
            $shiftModel->load($shiftId, "shift_id");
        }

        $shiftData = Mage::helper('webpos/shift')->prepareOfflineShiftData($shiftModel->getShiftId());
        $zReportSalesSummary = $shiftData["zreport_sales_summary"];
        $shiftData["zreport_sales_summary"] = $zReportSalesSummary->getData();
        $response[] = $shiftData;
        return $this->getResponseData($response, $message, $status, false);
    }

    /**
     * get shit information
     *
     * @return array
     */
    public function get($shiftId)
    {
        $shiftModel = Mage::getModel('webpos/shift')->load($shiftId);
        return $shiftModel;
    }

    /**
     * get shit information
     *
     * @return array
     */
    public function getInfo($shiftId)
    {
        $shift = $this->get($shiftId);
        $data = $shift->getInfo();
        return $data;

    }

    /**
     * check assign pos & staff
     * @return mixed
     */
    public function checkAssignPosStaff($shift)
    {
        $posId = $shift->getPosId();
        $staffId = $shift->getStaffId();
        $status = $shift->getStatus();
        if ($posId && $staffId) {
            if ($status == 1) {
                Mage::getModel('webpos/pos')->unassignStaff($staffId);
            } else if ($status == 0) {
                Mage::getModel('webpos/pos')->assignStaff($posId, $staffId);
            }
        }
    }

    /**
     * check opened session/shift by staff id
     *
     * @params string $staffId
     * @return boolean
     */
    public function checkOpenedShift($staffId)
    {
        $collection = Mage::getModel('webpos/shift')->getCollection();
        $collection->addFieldToFilter('staff_id', $staffId)
            ->addFieldToFilter('pos_id', Mage::helper('webpos/permission')->getCurrentPosId())
            ->addFieldToFilter('status', 0);
        if ($collection->getSize() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get open session for pos
     *
     */
    public function getOpenSession($posId = '')
    {
        $collection = Mage::getModel('webpos/shift')->getCollection();
        if ($posId) {
            $collection->addFieldToFilter('pos_id', $posId);
        }
        $collection->addFieldToFilter('status', 0);
        if ($collection->getSize() > 0) {
            return $collection->getItems();
        }
        return array();
    }
}