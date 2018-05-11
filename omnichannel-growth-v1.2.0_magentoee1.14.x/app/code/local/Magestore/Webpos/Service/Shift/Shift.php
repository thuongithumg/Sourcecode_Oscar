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

class Magestore_Webpos_Service_Shift_Shift extends Magestore_Webpos_Service_Abstract
{

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
     * @param $zReportData
     * @return mixed
     * @throws Exception
     */
    public function save($zReportData){
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        try {
            $model = $this->_getModel('webpos/zreport');
            $model->setData($zReportData);
            $this->checkAssignPosStaff($model);
            $model->save();
            $transactionService = $this->_createService('transaction_transaction');
            $transactionService->deactiveByCashDrawerId($model->getTillId());
    } catch (Exception $e) {
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
            $message[] = $e->getMessage();
        }
        if ($model->getId()) {
            $shift = $model;
            $data = $this->getShiftResponse($shift);
        }
        return $this->getResponseData($data, $message, $status, false);
    }

    /**
     * @param $shift
     * @return mixed
     */
    public function getShiftResponse($shift)
    {
        $shiftId = $shift->getId();
        $shiftData = $shift->getData();
        $shiftData['zreport_sales_summary'] = $this->getSalesSummaryByShift($shiftId);
        $shiftData['sales_summary'] = $this->getSalesPaymentsByShift($shiftId);
        $shiftData['cash_transaction'] = $this->getTransactionList($shiftId);
        $shiftData['base_cash_added'] = $this->getBaseCashAdded($shiftId);
        $shiftData['base_cash_removed'] = $this->getBaseCashRemoved($shiftId);
        $shiftData['base_cash_sale'] = $this->getBaseCashSales($shiftId);
        return $shiftData;
    }

    /**
     * @param $cashDrawerId
     * @return mixed
     */
    public function getShiftData($cashDrawerId){
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        try{
            $data['sales_summary'] = $this->_getSalesSummary($cashDrawerId);
            $data['sales_by_payments'] = $this->_getSalesByPayments($cashDrawerId);
            $data['base_balance'] = $this->_getBaseBalance($cashDrawerId);
            $data['base_opening_amount'] = $this->_getBaseOpeningAmount($cashDrawerId);
            $data['open_at'] = $this->_getOpeningAt($cashDrawerId);
        }catch (Exception $e){
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
            $message[] = $e->getMessage();
        }
        return $this->getResponseData($data, $message, $status, false);
    }

    /**
     * @param $cashDrawerId
     * @return float
     */
    public function _getBaseOpeningAmount($cashDrawerId){
        $resource = $this->_getResource('webpos/shift_cashtransaction_collection');
        $transactions = $resource->getActiveTransactions($cashDrawerId);
        $transactions->addFieldToFilter('is_opening', Magestore_Webpos_Model_Transaction::TRUE);
        return ($transactions->getSize() > 0)?$transactions->getFirstItem()->getBaseAmount():0;
    }

    /**
     * @param $cashDrawerId
     * @return float
     */
    public function _getBaseBalance($cashDrawerId){
        $baseBalance = 0;
        $resource = $this->_getResource('webpos/shift_cashtransaction_collection');
        $transactions = $resource->getActiveTransactions($cashDrawerId);
        if($transactions->getSize() > 0){
            foreach ($transactions as $transaction){
                $baseBalance += $transaction->getBaseAmount();
            }
        }
        return $baseBalance;
    }

    /**
     * @param $cashDrawerId
     * @return string
     */
    public function _getOpeningAt($cashDrawerId){
        $collection = $this->_getResource('webpos/zreport_collection')->addFieldToFilter('till_id', $cashDrawerId);
        if($collection->getSize() > 0){
            $collection->setOrder('id', 'DESC');
            $lastTimeUsed = $collection->getFirstItem()->getClosedAt();
        }else{
            $lastTimeUsed = '2016-12-19 00:00:00';
        }
        return $lastTimeUsed;
    }

    /**
     * @param $cashDrawerId
     * @return array
     */
    public function _getSalesByPayments($cashDrawerId){
        $sales = array();
        $sales[Magestore_Webpos_Helper_Payment::CASH_PAYMENT_CODE] = array(
            'payment_method' => Magestore_Webpos_Helper_Payment::CASH_PAYMENT_CODE,
            'method_title' => $this->_getHelper('webpos/payment')->getCashMethodTitle(),
            'base_payment_amount' => $this->_getBaseBalance($cashDrawerId),
        );
        $openingAmount = $this->_getOpeningAt($cashDrawerId);
        $orderCollection = $this->_getResource('sales/order_collection');
        $orderCollection->addFieldToFilter('webpos_till_id', $cashDrawerId);
        $orderCollection->addFieldToFilter('created_at', array('from' => $openingAmount));
        $orderIds = $orderCollection->getAllIds();
        if(!empty($orderIds)){
            $collection = $this->_getModel('webpos/payment_orderPayment')->getCollection();
            $collection->addFieldToFilter('till_id', $cashDrawerId);
            $collection->addFieldToFilter('order_id', array('in' => $orderIds));
            $collection->addFieldToFilter('method', array('neq' => Magestore_Webpos_Helper_Payment::CASH_PAYMENT_CODE));
            if($collection->getSize() > 0){
                foreach ($collection as $orderPayment){
                    if(isset($sales[$orderPayment->getMethod()])){
                        $sales[$orderPayment->getMethod()]['base_payment_amount'] += $orderPayment->getBasePaymentAmount();
                    }else{
                        $sales[$orderPayment->getMethod()] = array(
                            'payment_method' => $orderPayment->getMethod(),
                            'method_title' => $orderPayment->getMethodTitle(),
                            'base_payment_amount' => $orderPayment->getBaseRealAmount()
                        );
                    }
                }
            }
        }
        return array_values($sales);
    }

    /**
     * @param $cashDrawerId
     * @return array
     */
    public function _getSalesSummary($cashDrawerId){
        $salesSummary = array(
            'base_grand_total' => 0,
            'base_total_refunded' => 0,
            'base_discount_amount' => 0
        );
        $openingAmount = $this->_getOpeningAt($cashDrawerId);
        $orderCollection = $this->_getResource('sales/order_collection');
        $orderCollection->addFieldToFilter('webpos_till_id', $cashDrawerId);
        $orderCollection->addFieldToFilter('created_at', array('from' => $openingAmount));
        if($orderCollection->getSize() > 0){
            foreach ($orderCollection as $order){
                $salesSummary['base_grand_total'] += ($order->getBaseGrandTotal())?$order->getBaseGrandTotal():0;
                $salesSummary['base_total_refunded'] += ($order->getBaseTotalRefunded())?$order->getBaseTotalRefunded():0;
                $salesSummary['base_discount_amount'] += ($order->getBaseDiscountAmount())?$order->getBaseDiscountAmount():0;
            }
        }
        return $salesSummary;
    }

    /**
     * @param $shiftId
     * @return mixed
     */
    public function getTransactionList($shiftId){
        $resource = Mage::getResourceModel('webpos/shift_cashtransaction_collection');
        $transactions = $resource->addFieldToFilter('shift_id', $shiftId);
        $transactions->setOrder('created_at', 'ASC');
        $transactionsData = array();
        if($transactions->getSize()) {
            foreach ($transactions as $transaction) {
                $transactionsData[] = $transaction->load($transaction->getId())->getData();
            }
        }
        return $transactionsData;
    }

    /**
     * @param $shiftId
     * @return string
     */
    public function getOpeningAtByShift($shiftId){
        $collection = $this->_getResource('webpos/zreport_collection')->addFieldToFilter('shift_id', $shiftId);
        if($collection->getSize() > 0){
            $collection->setOrder('id', 'DESC');
            $lastTimeUsed = $collection->getFirstItem()->getClosedAt();
        }else{
            $lastTimeUsed = '2016-12-19 00:00:00';
        }
        return $lastTimeUsed;
    }

    /**
     * @param $shiftId
     * @return array
     */
    public function getSalesPaymentsByShift($shiftId){
        $sales = array();
        $collection = $this->_getModel('webpos/payment_orderPayment')->getCollection();
        $collection->addFieldToFilter('shift_id', $shiftId);
        if($collection->getSize() > 0){
            $paymentHelper = Mage::helper('webpos/payment');
            foreach ($collection as $orderPayment){
                if($paymentHelper->isPayLater($orderPayment->getMethod())) {
                    continue;
                }
                if(isset($sales[$orderPayment->getMethod()])){
                    $sales[$orderPayment->getMethod()]['base_payment_amount'] += $orderPayment->getBasePaymentAmount();
                }else{
                    $sales[$orderPayment->getMethod()] = array(
                        'payment_method' => $orderPayment->getMethod(),
                        'method_title' => $orderPayment->getMethodTitle(),
                        'base_payment_amount' => $orderPayment->getBaseRealAmount()
                    );
                }
            }
        }
        return array_values($sales);
    }

    /**
     * @param $shiftId
     * @return array
     */
    public function getSalesSummaryByShift($shiftId){
        $salesSummary = array(
            'base_grand_total' => 0,
            'base_total_refunded' => 0,
            'base_discount_amount' => 0
        );
        $orderCollection = $this->_getResource('sales/order_collection');
        $orderCollection->addFieldToFilter('shift_id', $shiftId);
        if($orderCollection->getSize() > 0){
            foreach ($orderCollection as $order){
                $salesSummary['base_grand_total'] += ($order->getBaseGrandTotal())?$order->getBaseGrandTotal():0;
                $salesSummary['base_total_refunded'] += ($order->getBaseTotalRefunded())?$order->getBaseTotalRefunded():0;
                $salesSummary['base_discount_amount'] += ($order->getBaseDiscountAmount())?$order->getBaseDiscountAmount():0;
            }
        }
        return $salesSummary;
    }

    /**
     * @param $shiftId
     * @return mixed
     */
    public function getBaseCashAdded($shiftId){
        $transactions = Mage::getResourceModel('webpos/shift_cashtransaction_collection');
        $transactions->addFieldToFilter('shift_id', $shiftId);
        $transactions->addFieldToFilter('type', 'add');
        $baseCashAdded = 0;
        if($transactions->getSize()) {
            foreach ($transactions as $transaction) {
                $baseCashAdded += $transaction->getBaseAmount();
            }
        }
        return $baseCashAdded;
    }

    /**
     * @param $shiftId
     * @return mixed
     */
    public function getBaseCashRemoved($shiftId){
        $transactions = Mage::getResourceModel('webpos/shift_cashtransaction_collection');
        $transactions->addFieldToFilter('shift_id', $shiftId);
        $transactions->addFieldToFilter('type', 'remove');
        $baseCashRemoved = 0;
        if($transactions->getSize()) {
            foreach ($transactions as $transaction) {
                $baseCashRemoved += $transaction->getBaseAmount();
            }
        }
        return $baseCashRemoved;
    }

    /**
     * @param $shiftId
     * @return mixed
     */
    public function getBaseCashSales($shiftId){
        $transactions = Mage::getResourceModel('webpos/shift_cashtransaction_collection');
        $transactions->addFieldToFilter('shift_id', $shiftId);
        $transactions->addFieldToFilter('type', 'order');
        $baseCashSale = 0;
        if($transactions->getSize()) {
            foreach ($transactions as $transaction) {
                $baseCashSale += $transaction->getBaseAmount();
            }
        }
        return $baseCashSale;
    }

    /**
     * @param $shiftId
     * @return float
     */
    public function getBaseBalanceByShift($shiftId){
        $baseBalance = 0;
        $resource = $this->_getResource('webpos/shift_cashtransaction_collection');
        $transactions = $resource->getActiveTransactionsByShift($shiftId);
        if($transactions->getSize() > 0){
            foreach ($transactions as $transaction){
                $baseBalance += $transaction->getBaseAmount();
            }
        }
        return $baseBalance;
    }

    /**
     * check assign pos & staff
     *
     * @param array $shift
     * @return mixed
     */
    public function checkAssignPosStaff($shift)
    {
        $posId = $shift->getTillId();
        $staffId = $shift->getStaffId();
        $status = $shift->getStatus();
        if($posId && $staffId) {
            if($status == 1) {
                Mage::getModel('webpos/till')->unassignStaff($posId);
            } else if ($status == 0) {
                Mage::getModel('webpos/till')->assignStaff($posId, $staffId);
            }
        }
    }

}
