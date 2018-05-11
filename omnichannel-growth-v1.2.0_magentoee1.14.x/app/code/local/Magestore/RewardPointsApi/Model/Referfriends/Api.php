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
class Magestore_RewardPointsApi_Model_Referfriends_Api extends Mage_Api_Model_Resource_Abstract {
    /**
     * get coupon code by customer id by email
     * @param type $customerCode
     * @return type
     */
    public function getCouponByCustomer($customerCode){
        $this->_noStFault();
        $customerId = $this->_getCustomerId($customerCode);
        
        $collection = Mage::getModel('rewardpointsreferfriends/rewardpointsrefercustomer')->load($customerId, 'customer_id');
        if($collection->getCoupon()) return $collection->getCoupon();
        else $this->_fault('enable_plugin', Mage::helper('rewardpointsapi')->__('There is no coupon code for customer %s', $customerCode));
    }
 /**
  * get link key by customer id or email
  * @param type $customerCode
  * @return type
  */
    public function getLinkByCustomer($customerCode){
        $this->_noStFault();
        $customerId = $this->_getCustomerId($customerCode);
        
        $collection = Mage::getModel('rewardpointsreferfriends/rewardpointsrefercustomer')->load($customerId, 'customer_id');
        if($collection->getKey()){
            return $collection->getKey();
        }
        else $this->_fault('enable_plugin', Mage::helper('rewardpointsapi')->__('Link key of Customer: %s is not found ', $customerCode));
    }
    /**
     * get customer id
     * @param type $customerCode
     * @return type
     */
    protected function _getCustomerId($customerCode){
        if(!Mage::getStoreConfig('rewardpoints/referfriendplugin/enable'))
            $this->_fault('enable_plugin');
        if(filter_var($customerCode, FILTER_VALIDATE_EMAIL)){
            $customer = Mage::getModel('customer/customer')->getCollection()
                    ->addFieldToFilter('email', $customerCode);
            $customer = $customer->getFirstItem();
        }elseif(is_numeric($customerCode)){
            $customer = Mage::getModel('customer/customer')->load($customerCode);
        } else $customer = null;
        if(!$customer) $this->_fault('enable_plugin', Mage::helper('rewardpointsapi')->__('Customer does not exist'));
        else return $customer->getId();
    }
  /**
   * fetch customer by coupon
   * @param type $coupon
   * @return type
   */
    public function getCustomerByCoupon($couponCode){
        $this->_noStFault();
        $coupon = $couponCode;
        if(!Mage::getStoreConfig('rewardpoints/referfriendplugin/enable'))
            $this->_fault('enable_plugin');
        if(!is_string($coupon)) $this->_fault('enable_plugin', Mage::helper('rewardpointsapi')->__('Coupon code invalid'));
        $refer = Mage::getModel('rewardpointsreferfriends/rewardpointsrefercustomer')->load($coupon, 'coupon');
        if($refer->getCustomerId()){
//            return $refer->getCustomerId();
            $customer_id = $refer->getCustomerId();
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            if($customer){
                $customerData = $customer->getData();
                if(isset($customerData['entity_id'])){
                    $customerData['customer_id']=$customerData['entity_id'];
                    unset($customerData['entity_id']);
                }
                return $customerData;
            }
            else $this->_fault('enable_plugin', Mage::helper('rewardpointsapi')->__('Customer is not exist'));
        }
        else $this->_fault('enable_plugin', Mage::helper('rewardpointsapi')->__('Coupon is not exist'));
    }
    /**
     * fetch customer by link key
     * @param type $link
     * @return type
     */
    public function getCustomerByLink($linkKey){
        $this->_noStFault();
        if(!Mage::getStoreConfig('rewardpoints/referfriendplugin/enable'))
            $this->_fault('enable_plugin');
        if(!is_string($linkKey)) $this->_fault('enable_plugin', Mage::helper('rewardpointsapi')->__('Link key is invalid'));
        $linkKey = trim($linkKey);

        $refer = Mage::getModel('rewardpointsreferfriends/rewardpointsrefercustomer')->load($linkKey, 'key');

        if($refer->getCustomerId()){
            $customer_id = $refer->getCustomerId();
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            if($customer){
                $customerData = $customer->getData();
                if(isset($customerData['entity_id'])){
                    $customerData['customer_id']=$customerData['entity_id'];
                    unset($customerData['entity_id']);
                }
                return $customerData;
            }
            else $this->_fault('enable_plugin', Mage::helper('rewardpointsapi')->__('Customer is not exist'));
        }
        else $this->_fault('enable_plugin', Mage::helper('rewardpointsapi')->__('Link key is not exist'));
    }
    /**
     * show message error
     * @return \Magestore_RewardPointsApi_Model_Referfriends_Api
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