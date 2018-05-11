<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

/**
 * class \Magestore\Webpos\Helper\Customer
 * 
 * Web POS Customer helper
 * Methods:
 *  getAllDefaultCustomerInfo
 *  getDefaultCity
 *  getDefaultCountry
 *  getDefaultCustomerId
 *  getDefaultEmail
 *  getDefaultFirstName
 *  getDefaultLastName
 *  getDefaultState
 *  getDefaultStreet
 *  getDefaultTelephone
 *  getDefaultZip
 *  getStoreAddressData
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
/**
 * Class Customer
 * @package Magestore\Webpos\Helper
 */
class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface 
     */
    protected $_storeManager;
    
    /**
     *
     * @var \Magestore\Webpos\Model\WebPosSession 
     */
    protected $_webposSession;
    
    /**
     *
     * @var \Magento\Framework\App\ObjectManager 
     */
    protected $_objectManager;
    
    /**
     *
     * @var \Magento\Checkout\Model\Session 
     */
    protected $_checkoutSession;
    
    /**
     *
     * @var \Magento\Customer\Model\Session 
     */
    protected $_customerSession;
    
    /**
     *
     * @var \Psr\Log\LoggerInterface 
     */
    protected $_logger;
    
    /**
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Webpos\Model\WebPosSession $webPosSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Model\WebPosSession $webPosSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_storeManager = $storeManager;
        $this->_webposSession = $webPosSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_logger = $context->getLogger();
        parent::__construct($context);
    }
    
    /**
     * 
     * @return string
     */
    public function getDefaultFirstName() {
        return $this->scopeConfig->getValue('webpos/guest_checkout/first_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return string
     */
    public function getDefaultLastName() {
        return $this->scopeConfig->getValue('webpos/guest_checkout/last_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return string
     */
    public function getDefaultStreet() {
        return $this->scopeConfig->getValue('webpos/guest_checkout/street', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return string
     */
    public function getDefaultCountry() {
        return $this->scopeConfig->getValue('webpos/guest_checkout/country_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return string
     */
    public function getDefaultState() {
        return $this->scopeConfig->getValue('webpos/guest_checkout/region_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return string
     */
    public function getDefaultCity() {
        return $this->scopeConfig->getValue('webpos/guest_checkout/city', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return string
     */
    public function getDefaultZip() {
        return $this->scopeConfig->getValue('webpos/guest_checkout/zip', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return string
     */
    public function getDefaultTelephone() {
        return $this->scopeConfig->getValue('webpos/guest_checkout/telephone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return string
     */
    public function getDefaultEmail() {
        return $this->scopeConfig->getValue('webpos/guest_checkout/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return string
     */
    public function getDefaultCustomerId() {
        return $this->scopeConfig->getValue('webpos/guest_checkout/customer_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getDefaultRegionCode() {
        if ($this->getDefaultState() && is_numeric($this->getDefaultState())) {
            $regionModel = $this->_objectManager->get('Magento\Directory\Model\Region')->load($this->getDefaultState());
            return $regionModel->getCode();
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getDefaultRegionLabel() {
        if ($this->getDefaultState() && is_numeric($this->getDefaultState())) {
            $regionModel = $this->_objectManager->get('Magento\Directory\Model\Region')->load($this->getDefaultState());
            return $regionModel->getName();
        } else {
            return $this->getDefaultState();
        }
    }

    /**
     * 
     * @return array
     */
    public function getStoreAddressData() {
        $customerData = array();
        $customerData['customer_id'] = $this->getDefaultCustomerId();
        $customerData['country_id'] = $this->getDefaultCountry();
        $customerData['region_id'] = $this->getDefaultState();
        $customerData['postcode'] = $this->getDefaultZip();
        $customerData['street'] = [$this->getDefaultStreet(),""];
        $customerData['telephone'] = $this->getDefaultTelephone();
        $customerData['city'] = $this->getDefaultCity();
        $customerData['firstname'] = $this->getDefaultFirstName();
        $customerData['lastname'] = $this->getDefaultLastName();
        $customerData['email'] = $this->getDefaultEmail();
        if (isset($customerData['customer_id'])) {
            $customer = $this->_objectManager->get('Magento\Customer\Model\Customer');
            $customer = $customer->load($customerData['customer_id']);
            if ($customer->getId()) {
                $billingDefault = $customer->getDefaultBillingAddress();
                if (isset($billingDefault) && !empty($billingDefault) && $billingDefault instanceof Magento\Customer\Model\Address) {
                    $billingData = $billingDefault->getData();
                    if (isset($billingData['country_id']))
                        $customerData['country_id'] = $billingData['country_id'];
                    if (isset($billingData['region_id']))
                        $customerData['region_id'] = $billingData['region_id'];
                    if (isset($billingData['postcode']))
                        $customerData['postcode'] = $billingData['postcode'];
                    if (isset($billingData['street']))
                        $customerData['street'] = [str_replace("\n", " ", $billingData['street']),''];
                    if (isset($billingData['telephone']))
                        $customerData['telephone'] = $billingData['telephone'];
                    if (isset($billingData['city']))
                        $customerData['city'] = $billingData['city'];
                    if (isset($billingData['firstname']))
                        $customerData['firstname'] = $billingData['firstname'];
                    if (isset($billingData['lastname']))
                        $customerData['lastname'] = $billingData['lastname'];
                    if (isset($billingData['email']))
                        $customerData['email'] = $billingData['email'];
                }
            }
        }
        return $customerData;
    }
}