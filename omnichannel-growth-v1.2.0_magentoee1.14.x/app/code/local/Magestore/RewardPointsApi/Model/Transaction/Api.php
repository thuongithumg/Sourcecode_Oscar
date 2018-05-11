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
 * @package     Magestore_RewardPointsApi
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPointsApi Customer Api Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsApi
 * @author      Magestore Developer
 */
class Magestore_RewardPointsApi_Model_Transaction_Api extends Mage_Api_Model_Resource_Abstract {

    /**
     * fetch list transaction of customers by filters
     * @param type $filters
     * @return array of array
     */
    public function items($filters) {
        $this->_noStFault();
//        $filters = $this->_prepareData($filters);
        $apiHelper = Mage::helper('rewardpointsapi/api');
        $filters = $apiHelper->parseFilters($filters);
//        return $filters;
        $collection = Mage::getResourceModel('rewardpoints/transaction_collection');
//        if (is_array($filters)) {
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
            // If we are adding filter on non-existent attribute
        }
//        }
        $result = array();
        foreach ($collection as $transaction) {
            $result[] = $transaction->toArray();
        }

        return $result;
    }

    protected function _prepareData($data) {
        return $data;
    }

    /**
     * add a transaction
     * @param type $transaction
     * @return int
     */
    public function add($transactionData) {
        $this->_noStFault();
        $transactionData = $this->_prepareData($transactionData);
        if (!is_array($transactionData)) {
            $msg = Mage::helper('rewardpointsapi')->__("transaction data must be an array.");
            $this->_fault('transactionid_invalid', $msg);
        }
        $apiObject = new Varien_Object; //array();
        $actionCode = $transactionData['actionCode'];
        if (!$actionCode)
            $actionCode = 'api';
        else {
            $modelActionClass = Mage::helper('rewardpoints/action')->getActionModelClass($actionCode);
            if (!$modelActionClass)
                $this->_fault('transactionid_invalid', Mage::helper('rewardpointsapi')->__("Action code is not valid."));
            else {
                $modelAction = Mage::getModel($modelActionClass);
                //$apiObject['title'] = $modelAction->getTitle();
                //$apiObject->setData('title', $modelAction->getTitle());
                $apiObject->setData('actionType', $modelAction->getActionType());
            }
        }
        if ($transactionData['title'])
            $apiObject->setData('title', $transactionData['title']);
        else
            $apiObject->setData('title', Mage::helper('rewardpointsapi')->__('Transaction created by API'));

        $customer_id = $transactionData['customerId'];
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        if ($customer == null)
            $this->_fault('transactionid_invalid', Mage::helper('rewardpointsapi')->__("Customer ID must be imported to the transaction data."));

        if ($transactionData['pointAmount']) {
            $pointAmount = $transactionData['pointAmount'];
        }
        else
            $this->_fault('transactionid_invalid', Mage::helper('rewardpointsapi')->__("Point amount must be a number."));
        //$apiObject['point_amount'] = $pointAmount;
        $apiObject->setData('point_amount', $pointAmount);

        if ($transactionData['orderId'])
            $apiObject->setData('order_id', $transactionData['orderId']);
        $extracontent = $transactionData['extraContent'];
        if (!$extracontent)
            $extracontent = 'api';
        if ($transactionData['expireAfter'])
            $apiObject->setData('expiration_date', $transactionData['expireAfter']);
        else
            $apiObject->setData('expiration_date', Mage::getStoreConfig('rewardpoints/earning/expire'));
        $storeId = $transactionData['storeId'];
        if ($storeId == null) {
            $this->_fault('transactionid_invalid', Mage::helper('rewardpointsapi')->__("storeId is required field."));
        }
        $apiObject->setData('store_id', $storeId);
        switch ($transactionData['status']) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
                $apiObject->setData('status', $transactionData['status']);
        }

        $trans = Mage::helper('rewardpoints/action')->addTransaction('api', $customer, $apiObject, $extracontent);

        if ($transactionData['actionCode']) {
            $collection = Mage::getModel('rewardpoints/transaction')->load($trans->getId());
            $collection->addData(array('action' => $transactionData['actionCode']));
            $collection->setId($trans->getId())->save();
        }
        return $trans->getId();
    }

    /**
     * cancel transactions
     * @param type $transactionIds
     * @return boolean
     */
    public function cancel($transactionIds) {
        $this->_noStFault();
        if (!is_array($transactionIds)) {
            $msg = Mage::helper('rewardpointsapi')->__("transactionId must be an array.");
            $this->_fault('transactionid_invalid', $msg);
        }
        $isCancel = array();
        foreach ($transactionIds as $trans) {
            $trans_data = Mage::getModel('rewardpoints/transaction')
                    ->load($trans);
            if ($trans_data->getId()) {
                try {
                    $trans_data->cancelTransaction();
                    $isCancel[$trans] = true;
                } catch (Exception $exc) {
                    $isCancel[$trans] = false;
                    continue;
                }
            } else {
                $isCancel[$trans] = false;
            }
        }
        return $isCancel;
    }

    /**
     * complete transaction
     * @param type $transactionId
     * @return boolean
     */
    public function complete($transactionIds) {
        $this->_noStFault();
        if (!is_array($transactionIds)) {
            $msg = Mage::helper('rewardpointsapi')->__("transactionId must be an array.");
            $this->_fault('transactionid_invalid', $msg);
        }
        $isComplete = array();
        foreach ($transactionIds as $trans) {
            $trans_data = Mage::getModel('rewardpoints/transaction')
                    ->load($trans);
            if ($trans_data->getId()) {
                try {
                    $trans_data->completeTransaction();
                    $isComplete[$trans] = true;
                } catch (Exception $exc) {
                    $isComplete[$trans] = false;
                    continue;
                }
            } else {
                $isComplete[$trans] = false;
            }
        }
        return $isComplete;
    }

    /**
     * expire transaction
     * @param type $transactionId
     * @return boolean
     */
    public function expire($transactionIds) {
        $this->_noStFault();
        $transactionIds = $this->_prepareData($transactionIds);
        if (!is_array($transactionIds)) {
            $msg = Mage::helper('rewardpointsapi')->__("transactionId must be an array.");
            $this->_fault('transactionid_invalid', $msg);
        }
        $expire=array();
        foreach ($transactionIds as $trans){
            $expire[$trans]=false;
        }
        $collection = Mage::getResourceModel('rewardpoints/transaction_collection')
                ->addAvailableBalanceFilter()
                ->addFieldToFilter('status', array(
                    'lteq' => Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED
                ))
                ->addFieldToFilter('expiration_date', array('notnull' => true))
                ->addFieldToFilter('expiration_date', array('to' => now()))
                ->addFieldToFilter('transaction_id', array('in' => $transactionIds));
        foreach ($collection as $transaction) {
            try {
                $transaction->expireTransaction();
                $expire[$transaction->getId()]=true;
            } catch (Exception $e) {
                continue;
            }
        }
        return $expire;
    }

    /**
     * send update transaction email to customer
     * @param type $transactionIds
     * @return boolean
     */
    public function sendUpdateBalanceEmail($transactionIds) {
        $this->_noStFault();
        $transactionIds = $this->_prepareData($transactionIds);
        if (!is_array($transactionIds)) {
            $msg = Mage::helper('rewardpointsapi')->__("transactionIds must be an array.");
            $this->_fault('transactionid_invalid', $msg);
        }
        $collection = Mage::getResourceModel('rewardpoints/transaction_collection')
                ->addFieldToFilter('transaction_id', array('in' => $transactionIds));
        $result = array();
        foreach ($transactionIds as $key => $val) {
            $result[$val] = false;
        }
        try {
            foreach ($collection as $transaction) {
                if (!Mage::getStoreConfigFlag(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_ENABLE, $transaction->getStoreId())) {
                    continue;
                }
                $rewardAccount = Mage::getModel('rewardpoints/customer')->load($transaction->getRewardId());
                if (!$rewardAccount->getId() || !$rewardAccount->getIsNotification()) {
                    continue;
                }
                $customer = Mage::getModel('customer/customer')->load($transaction->getCustomerId());
                if (!$customer->getId()) {
                    continue;
                }
                $transaction->sendUpdateBalanceEmail();
                $result[$transaction->getId()] = true;
            }
        } catch (Exception $exc) {
            return $result;
        }
        return $result;
    }

    /**
     * send email befor expire transaction to customers
     * @param type $transactionIds
     * @return boolean
     */
    public function sendBeforeExpireEmail($transactionIds) {
        $this->_noStFault();
        $transactionIds = $this->_prepareData($transactionIds);
        if (!is_array($transactionIds)) {
            $msg = Mage::helper('rewardpointsapi')->__("Transaction id must be an array.");
            $this->_fault('transactionid_invalid', $msg);
        }
        $collection = Mage::getResourceModel('rewardpoints/transaction_collection')
                ->addFieldToFilter('transaction_id', array('in' => $transactionIds));
        $result = array();
        foreach ($transactionIds as $key => $val) {
            $result[$val] = false;
        }
        try {
            foreach ($collection as $transaction) {
                if (!Mage::getStoreConfigFlag(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_ENABLE, $transaction->getStoreId())) {
                    continue;
                }
                $rewardAccount = Mage::getModel('rewardpoints/customer')->load($transaction->getRewardId());
                if (!$rewardAccount->getId() || !$rewardAccount->getIsNotification()) {
                    continue;
                }
                $customer = Mage::getModel('customer/customer')->load($transaction->getCustomerId());
                if (!$customer->getId()) {
                    continue;
                }
                if ($transaction->getExpirationDate()) {
                    $transaction->sendBeforeExpireEmail();
                    $result[$transaction->getId()] = true;
                }
            }
        } catch (Exception $exc) {
            return $result;
        }
        return $result;
    }

    /**
     * get message error
     * @return \Magestore_RewardPointsApi_Model_Transaction_Api
     * @throws Exception
     */
    public function _noStFault() {
        try {
            if (Mage::getConfig()->getModuleConfig('Magestore_RewardPoints')->is('active', 'false')) {
                throw new Exception(Mage::helper('rewardpointsapi')->__("RewarPoints must be installed on the server in order to use the RewarPoints Api"));
            }
        } catch (Exception $e) {
            $this->_fault('api_usage_exception', $e->getMessage());
        }
        return $this;
    }

}

// Class Mage_Sales_Model_Order_Api End