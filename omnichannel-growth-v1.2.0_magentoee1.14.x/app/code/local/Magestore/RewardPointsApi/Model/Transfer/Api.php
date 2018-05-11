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
class Magestore_RewardPointsApi_Model_Transfer_Api extends Mage_Api_Model_Resource_Abstract {

  

    /**
     * fetch list transfer of customers by filters
     * @param type $filters
     * @return array of array
     */
    public function items($filters) {
        $this->_noStFault();
//        $filters = $this->_prepareData($filters);
        $apiHelper = Mage::helper('rewardpointsapi/api');
        $filters = $apiHelper->parseFilters($filters);
//        return $filters;
        $collection = Mage::getResourceModel('rewardpointstransfer/rewardpointstransfer_collection');
//        if (is_array($filters)) {
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_error', $e->getMessage());
            // If we are adding filter on non-existent attribute
        }
//        }
        $result = array();
        foreach ($collection as $transaction) {
            $result[] = $transaction->toArray();
        }

        return $result;
    }
//    public function items($filters) {
//        $this->_noStFault();
//        $apiHelper = Mage::helper('rewardpointsapi/api');
//        $filters = $apiHelper->parseFilters($filters);
//        $collection = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->getCollection();
//        try {
//            foreach ($filters as $field => $value) {
//                $collection->addFieldToFilter($field, $value);
//            }
//        } catch (Mage_Core_Exception $e) {
//            $this->_fault('data_error', $e->getMessage());
//        }
//        $result = array();
//        foreach ($collection as $transfer) {
//            $result[] = $transfer->toArray();
//        }
//
//        return $result;
//    }

    protected function _prepareData($data) {
        return $data;
    }

    /**
     * add a transfer
     * @param type $transfer
     * @return int
     */
    public function add($transferData) {
        $this->_noStFault();
        $transferData = $this->_prepareData($transferData);
        if (!is_array($transferData)) {
            $msg = Mage::helper('rewardpointsapi')->__("transferData must be an array.");
            $this->_fault('data_error', $msg);
        }
        if(!isset($transferData['emailSend']) || !isset($transferData['emailReceive']) || !isset($transferData['pointAmount']) || !isset($transferData['storeId'])){
            $msg = Mage::helper('rewardpointsapi')->__("emailSend, emailReceive, pointAmount, storeId are required fields.");
            $this->_fault('data_error', $msg);
        }
        $emailSend = trim($transferData['emailSend']);
        if(!$this->checkMail($emailSend)){
            $msg = Mage::helper('rewardpointsapi')->__("emailSend is invalid.");
            $this->_fault('data_error', $msg);
        }
        $emailReceive = trim($transferData['emailReceive']);
        if(!$this->checkMail($emailReceive)){
            $msg = Mage::helper('rewardpointsapi')->__("emailReceive is invalid.");
            $this->_fault('data_error', $msg);
        }
        $pointAmount = trim($transferData['pointAmount']);
        if(!is_numeric($pointAmount) || $pointAmount<0){
            $msg = Mage::helper('rewardpointsapi')->__("pointAmount must be a positive number.");
            $this->_fault('data_error', $msg);
        }
        if(isset($transferData['message'])) $message = $transferData['message'];
        else $message = '';

        if($emailSend == $emailReceive){
            $msg = Mage::helper('rewardpointsapi')->__("emailSend and emailReceive are the same.");
            $this->_fault('data_error', $msg);
        }
        $store_id = trim($transferData['storeId']);
        $websiteId = Mage::getModel('core/store')->load($store_id)->getWebsiteId();
        if(!$websiteId){
            $msg = Mage::helper('rewardpointsapi')->__("storeId does not exist in the system.");
            $this->_fault('data_error', $msg);
        }
        $sender = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmail($emailSend);
        if(!$sender->getId()){
            $msg = Mage::helper('rewardpointsapi')->__("%s does not exist on this store.", $emailSend);
            $this->_fault('data_error', $msg);
        }
        
        $group = Mage::helper('customer')->getCustomer()->getGroupId();
        $customerGroup = Mage::helper('rewardpointstransfer')->getTransferconfig('customer_group');
        $cusGroup = explode(',', $customerGroup);
        $check = 0;
        foreach($cusGroup as $key=>$val){
            if($val == $group) $check = 1;
        }
        if(!$check){
            $msg = Mage::helper('rewardpointsapi')->__("%s (%s) does not have the permission to transfer.", $sender->getName(), $emailSend);
            $this->_fault('data_error', $msg);
        }
        
        $rewardCustomer = Mage::getModel('rewardpoints/customer')->load($sender->getId(), 'customer_id');
        if(!$rewardCustomer->getId()){
            $msg = Mage::helper('rewardpointsapi')->__("%s (%s) does not have enough points in balance to transfer.", $sender->getName(), $emailSend);
            $this->_fault('data_error', $msg);
        }else{
            $senderAmount = $rewardCustomer->getPointBalance();
            $pointTransfer = Mage::helper('rewardpointstransfer')->getTransferConfig('maximum_point');
            if($pointTransfer == '' || $pointTransfer <= 0 || is_nan($pointTransfer)) $pointTransfer = $pointAmount+1;
            $pointMin = Mage::helper('rewardpointstransfer')->getTransferConfig('minimum_point');
            if($pointMin == '' || $pointMin <= 0 || is_nan($pointMin)) $pointMin = 0;
            
            if($senderAmount < $pointMin || $pointAmount>$pointTransfer || $pointAmount > $senderAmount) {
                $msg = Mage::helper('rewardpointsapi')->__("%s (%s) does not have enough points in balance to transfer.", $sender->getName(), $emailSend);
                $this->_fault('data_error', $msg);
            }
        }
        try{
            $transfer = Mage::helper('rewardpointstransfer')->addTransferTransaction($emailSend, $emailReceive, $pointAmount, $message, $store_id); 
            return $transfer->getId();
        }catch(Exception $e){
            $msg = Mage::helper('rewardpointsapi')->__("Error. Please try again.");
            $this->_fault('data_error', $msg);
        }
    }
    public function checkMail($email){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return false;
        }
        return true;
    }

    /**
     * cancel transfers
     * @param type $transferIds
     * @return boolean
     */
    public function cancel($transferIds) {
        $this->_noStFault();
        $transferIds = $this->_prepareData($transferIds);
        if (!is_array($transferIds)) {
            $msg = Mage::helper('rewardpointsapi')->__("transferIds must be an array.");
            $this->_fault('data_error', $msg);
        }
        $collection = Mage::getResourceModel('rewardpointstransfer/rewardpointstransfer_collection')
                ->addFieldToFilter('transfer_id', array('in' => $transferIds));
        $result = array();
        foreach($transferIds as $key=>$val){
            $result[$val] = false;
        }
        try {
            foreach ($collection as $transfer){
                $reason = Mage::helper('rewardpointsapi')->__("Cancel by Api");
                $transfer = $this->transferHelper()->cancelTransfer($transfer, $reason);
                if($transfer) $result[$transfer->getId()] = true;
            }
            return $result;
        } catch (Exception $exc) {
            return $result;
        }
        return $result;
    }

    /**
     * complete transfers
     * @param type $transferIds
     * @return boolean
     */
    public function complete($transferIds) {
        $this->_noStFault();
        $transferIds = $this->_prepareData($transferIds);
        if (!is_array($transferIds)) {
            $msg = Mage::helper('rewardpointsapi')->__("transferIds must be an array.");
            $this->_fault('data_error', $msg);
        }
        $collection = Mage::getResourceModel('rewardpointstransfer/rewardpointstransfer_collection')
                ->addFieldToFilter('transfer_id', array('in' => $transferIds));
        $result = array();
        foreach($transferIds as $key=>$val){
            $result[$val] = false;
        }
        try {
            foreach ($collection as $transfer){
               // $reason = Mage::helper('rewardpointsapi')->__("Completed by Api");
                $transfer = $this->transferHelper()->completeTransfer($transfer);
                if($transfer) $result[$transfer->getId()] = true;
            }
            return $result;
        } catch (Exception $exc) {
            return $result;
        }
        return $result;
    }
    public function transferHelper(){
        return Mage::helper('rewardpointstransfer');
    }
    /**
     * send update transfer email to customer
     * @param type $transferIds
     * @return boolean
     */
    public function sendTransferEmail($transferIds) {
        $this->_noStFault();
        $transferIds = $this->_prepareData($transferIds);
        if (!is_array($transferIds)) {
            $msg = Mage::helper('rewardpointsapi')->__("transferIds must be an array.");
            $this->_fault('data_error', $msg);
        }
        $collection = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->getCollection()
                ->addFieldToFilter('transfer_id', array('in' => $transferIds));
        $result = array();
        foreach($transferIds as $key=>$val){
            $result[$val] = false;
        }
        try {
            foreach ($collection as $transfer){
                $customer = Mage::getModel('customer/customer')->load($transfer->getReceiverCustomerId());
                if($customer->getId()){
                    if($transfer->getStatus() == Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING){
                        Mage::getModel('rewardpointstransfer/rewardpointstransfer')->sendAccountTransferEmail($transfer);
                        $result[$transfer->getId()] = true;
                    }
                }else{
                    if($transfer->getStatus() == Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING){
                        Mage::getModel('rewardpointstransfer/rewardpointstransfer')->sendTransferEmail($transfer);
                        $result[$transfer->getId()] = true;
                    }                   
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