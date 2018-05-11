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
class Magestore_RewardPointsApi_Model_Customer_Api extends Mage_Api_Model_Resource_Abstract {

    /**
     * map attribute customer_id
     * @var type 
     */
    protected $_mapAttributes = array(
        'customer_id' => 'entity_id'
    );

    /**
     * get customer info by email
     * @param type $customer_email
     * @param type $website_id
     * @return array
     */
    public function getCustomerByEmail($customer_email, $website_id = 1) {
        $this->_noStFault();
        $customer = Mage::getModel('customer/customer')->setWebsiteId((int) $website_id);
        $customer = $customer->loadByEmail($customer_email);
        if (!$customer->getId()) {
            $msg = Mage::helper('rewardpointsapi')->__("No such customer with email %s exists in website #%s.", $customer_email, $website_id);
            $this->_fault('no_such_customer', $msg);
        }
        return $customer->getData();
    }

    /**
     * get customer Id by email
     * @param type $customer_email
     * @param type $website_id
     * @return int
     */
    public function getCustomerIdByEmail($customer_email, $website_id = 1) {
        $this->_noStFault();
        $customer = Mage::getModel('customer/customer')->setWebsiteId((int) $website_id);
        $customer = $customer->loadByEmail($customer_email);
        if (!$customer->getId()) {
            $msg = Mage::helper('rewardpointsapi')->__("No such customer Id with email %s exists in website #%s.", $customer_email, $website_id);
            $this->_fault('no_such_customer', $msg);
        }
        return $customer->getId();
    }

    /**
     * get balance of customer by email
     * @param type $customer_email
     * @param type $website_id
     * @return int
     */
    public function getBalanceByEmail($customer_email, $website_id = 1) {
        $this->_noStFault();
        $customer = Mage::getModel('customer/customer')->setWebsiteId((int) $website_id);
        $customer = $customer->loadByEmail($customer_email);
        if (!$customer->getId()) {
            $msg = Mage::helper('rewardpointsapi')->__("No such customer with email %s exists in website #%s.", $customer_email, $website_id);
            $this->_fault('no_such_customer', $msg);
        }
        $reward_acc = Mage::getModel('rewardpoints/customer')->load($customer->getId(), 'customer_id');
        if (!$reward_acc->getId()) {
            $msg = Mage::helper('rewardpointsapi')->__("The customer with email %s have not reward account.", $customer_email);
            $this->_fault('no_such_customer', $msg);
        }
        $balance = $reward_acc->getPointBalance();
        return $balance;
    }

    /**
     * get balance of customer by id
     * @param type $customer_id
     * @return int
     */
    public function getBalanceById($customer_id) {
        $this->_noStFault();
        $customer = Mage::getModel('customer/customer');
        $customer = $customer->load($customer_id);
        if (!$customer->getId()) {
            $msg = Mage::helper('rewardpointsapi')->__("No such customer with ID %s exists.", $customer_id);
            $this->_fault('no_such_customer', $msg);
        }
        $reward_acc = Mage::getModel('rewardpoints/customer')->load($customer->getId(), 'customer_id');
        if (!$reward_acc->getId()) {
            $msg = Mage::helper('rewardpointsapi')->__("The customer with ID %s have not reward account.", $customer_id);
            $this->_fault('no_such_customer', $msg);
        }
        $balance = $reward_acc->getPointBalance();
        return $balance;
    }

    /**
     * fetch list customers balance by filters
     * @param type $filters
     * @return type
     */
    public function getCustomersBalance($filters) {
        $this->_noStFault();
        $customer_collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*');
        $fields = array('entity_id', 'entity_type_id', 'attribute_set_id', 'website_id', 'email', 'group_id', 'increment_id', 'store_id', 'created_at', 'updated_at', 'is_active', 'disable_auto_group_change');
        $apiHelper = Mage::helper('rewardpointsapi/api');
        $filters = $apiHelper->parseFilters($filters, $this->_mapAttributes);
        try {
            foreach ($filters as $field => $value) {
                if (in_array($field, $fields))
                    $customer_collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $customers_balance = array();
        foreach ($customer_collection as $customer) {
            $reward_collection = Mage::getModel('rewardpoints/customer')->getCollection()
                    ->addFieldToFilter('customer_id', $customer->getId());
            try {
                foreach ($filters as $field => $value) {
                    if (!in_array($field, $fields))
                        $reward_collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
                // If we are adding filter on non-existent attribute
            }
            $reward = $reward_collection->getFirstItem();
            if ($reward->getId()) {
                $customers_balance[] = array(
                    'customer_id' => $reward->getCustomerId(),
                    'website_id' => $customer->getWebsiteId(),
                    'email' => $customer->getEmail(),
                    'group_id' => $customer->getGroupId(),
                    'store_id' => $customer->getStoreId(),
                    'created_at' => $customer->getCreatedAt(),
                    'updated_at' => $customer->getUpdatedAt(),
                    'is_active' => $customer->getIsActive(),
                    'point_balance' => $reward->getPointBalance(),
                    'holding_balance' => $reward->getHoldingBalance(),
                    'spent_balance' => $reward->getSpentBalance(),
                    'created_in' => $reward->getCreatedIn(),
                    'firstname'=>$reward->getFirstname(),
                    'lastname'=>$reward->getLastName(),
                );
            }
        }
        if (count($customers_balance))
            return $customers_balance;
        else {
            $msg = Mage::helper('rewardpointsapi')->__("Not found.");
            $this->_fault('no_such_customer', $msg);
        }
    }

    /**
     * show message error
     * @return \Magestore_RewardPointsApi_Model_Customer_Api
     * @throws Exception
     */
    public function _noStFault() {
        try {
            if (Mage::getConfig()->getModuleConfig('Magestore_RewardPoints')->is('active', 'false')) {
                throw new Exception(Mage::helper('rewardpointsapi')->__("RewarPoints Core must be installed on the server in order to can use Plugin Api"));
            }
        } catch (Exception $e) {
            $this->_fault('api_usage_exception', $e->getMessage());
        }
        return $this;
    }

    protected function _prepareData($data) {
        return $data;
    }

}

// Class Mage_Sales_Model_Order_Api End