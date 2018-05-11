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
 * RewardPointsApi Helper
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsApi
 * @author      Magestore Developer
 */
class Magestore_RewardPointsApi_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*Customer*/
    /**
     * get Customer by email
     * @param type $customer_email
     * @param int $website_id
     * @return type
     */
    public function getCustomerByEmail($customer_email, $website_id = 1) {
        return $this->_getModel('Customer')->getCustomerByEmail($customer_email, $website_id = 1);
    }
    /**
     * get Customer Id by email
     * @param type $customer_email
     * @param int $website_id
     * @return type
     */
    public function getCustomerIdByEmail($customer_email, $website_id = 1) {
        return $this->_getModel('Customer')->getCustomerIdByEmail($customer_email, $website_id = 1);
    }
    /**
     * get Balance of customer by email
     * @param type $customer_email
     * @param int $website_id
     * @return type
     */
    public function getBalanceByEmail($customer_email, $website_id = 1) {
        return $this->_getModel('Customer')->getBalanceByEmail($customer_email, $website_id = 1);
    }
    /**
     * get Balance of customer by id
     * @param type $customer_id
     * @return type
     */
    public function getBalanceById($customer_id) {
        return $this->_getModel('Customer')->getBalanceById($customer_id);
    }
    /**
     * get Balance of customer by filter
     * @param type $filters
     * @return type
     */
    public function getCustomersBalance($filters) {
        return $this->_getModel('Customer')->getCustomersBalance($filters);
    }
    
    /*Transaction*/
    /**
     * get Transactions by filter
     * @param type $filters
     * @return type
     */
    public function listTransaction($filters){
        return $this->_getModel('Transaction')->items($filters);
    }
    /**
     * Add a new transaction
     * @param type $transaction
     * @return type
     */
    public function addTransaction($transactionData){
        return $this->_getModel('Transaction')->add($transactionData);
    }
    /**
     * Cancel transaction
     * @param type $transactionIds
     * @return type
     */
    public function cancelTransaction($transactionIds){
        return $this->_getModel('Transaction')->cancel($transactionIds);
    }
    /**
     * Complete Transactions
     * @param type $transactionIds
     * @return type
     */
    public function completeTransaction($transactionIds){
        return $this->_getModel('Transaction')->complete($transactionIds);
    }
    /**
     * Expire Transactions
     * @param type $transactionIds
     * @return type
     */
    public function expireTransaction($transactionIds){
        return $this->_getModel('Transaction')->expire($transactionIds);
    }
    /**
     * Send update balance email to customer
     * @param type $transactionIds
     * @return type
     */
    public function sendUpdateBalanceEmail($transactionIds){
        return $this->_getModel('Transaction')->sendUpdateBalanceEmail($transactionIds);
    }
    /**
     * Send before expire transaction email to customer
     * @param type $transactionIds
     * @return type
     */
    public function sendBeforeExpireEmail($transactionIds){
        return $this->_getModel('Transaction')->sendBeforeExpireEmail($transactionIds);
    }
    /*Refer friend */
    /**
     * Get coupon code by customer id or email
     * @param type $customerCode
     * @return type
     */
    public function getCouponByCustomer($customerCode){
        return $this->_getModel('Referfriends')->getCouponByCustomer($customerCode);
    }
    /**
     * get link key by customer id or email
     * @param type $customerCode
     * @return type
     */
    public function getLinkByCustomer($customerCode){
        return $this->_getModel('Referfriends')->getLinkByCustomer($customerCode);
    }
    /**
     * Get customer data by coupon code
     * @param type $coupon
     * @return type
     */
    public function getCustomerByCoupon($coupon){
        return $this->_getModel('Referfriends')->getCustomerByCoupon($coupon);
    }
    /**
     * Get customer data by link key
     * @param type $link
     * @return type
     */
    public function getCustomerByLink($link){
        return $this->_getModel('Referfriends')->getCustomerByLink($link);
    }    
    /*Transfer*/
    /**
     * get Transfer by filter
     * @param type $filters
     * @return type
     */
    public function listTransfer($filters){
        return $this->_getModel('Transfer')->items($filters);
    }
    /**
     * Add a new Transfer
     * @param type $transaction
     * @return type
     */
    public function addTransfer($transferData){
        return $this->_getModel('Transfer')->add($transferData);
    }
    /**
     * Cancel Transfers
     * @param type $transactionIds
     * @return type
     */
    public function cancelTransfer($transferIds){
        return $this->_getModel('Transfer')->cancel($transferIds);
    }
    /**
     * Complete Transfers
     * @param type $transactionIds
     * @return type
     */
    public function completeTransfer($transferIds){
        return $this->_getModel('Transfer')->complete($transferIds);
    }
    /**
     * Send transfer email
     * @param type $transferIds
     * @return type
     */
    public function sendTransferEmail($transferIds){
        return $this->_getModel('Transfer')->sendTransferEmail($transferIds);
    }
    protected function _getModel($name){
        return Mage::getModel('rewardpointsapi/'.$name.'_Api');
    }
}