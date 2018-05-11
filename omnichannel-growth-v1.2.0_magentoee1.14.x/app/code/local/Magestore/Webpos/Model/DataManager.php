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

class Magestore_Webpos_Model_DataManager {

    const DEFAULT_STORE_CODE = 'default_store_code';
    const DEFAULT_CUSTOMER = 'default_customer';
    const DEFAULT_PAYMENT_METHOD = 'default_payment_method';
    const DEFAULT_SHIPPING_METHOD = 'default_shipping_method';
    const DEFAULT_SHIPPING_TITLE = 'default_shipping_title';
    const TAX_CLASS = 'tax_class';
    const TAX_RULES = 'tax_rules';
    const TAX_RATES = 'tax_rates';
    const CUSTOMER_GROUP = 'customer_group';
    const WEBPOS_STORE_ADDRESS = 'webpos_store_address';
    const DEFAULT_CUSTOMER_ID = 'default_customer_id';
    const SHIPPING = 'shipping';
    const PAYMENT = 'payment';
    const CC_TYPES = 'cc_types';
    const CC_MONTHS = 'cc_months';
    const CC_YEARS = 'cc_years';
    const CURRENT_WAREHOUSE_ID = 'current_warehouse_id';
    /**
     * @var Magestore_Webpos_Model_Shipping_ShippingRepository
     */
    protected $_shippingRepository = false;

    /**
     * @var Magestore_Webpos_Model_Payment_PaymentRepository
     */
    protected $_paymentRepository = false;

    /**
     * @var Mage_Payment_Model_Config
     */
    protected $_paymentConfigModel = false;

    /**
     * @var Magestore_Webpos_Helper_Config
     */
    protected $_helperConfig = false;

    /**
     * @var Magestore_Webpos_Helper_Permission
     */
    protected $_helperPermission = false;

    /**
     * @var Magestore_Webpos_Helper_Data
     */
    protected $_helper = false;

    /**
     * @var Magestore_Webpos_Model_User_Webpossession
     */
    protected $_session = false;

    public function __construct() {
        $this->_shippingRepository = Mage::getSingleton('webpos/shipping_shippingRepository');
        $this->_paymentRepository = Mage::getSingleton('webpos/payment_paymentRepository');
        $this->_paymentConfigModel = Mage::getSingleton('payment/config');
        $this->_helperConfig = Mage::helper('webpos/config');
        $this->_helper = Mage::helper('webpos');
        $this->_helperPermission = Mage::helper('webpos/permission');
        $this->_session = Mage::getModel('webpos/user_webpossession');
    }

    /**
     * @return array
     */
    public function getWebposData($session = false){
        $quote = Mage::getModel('sales/quote');
        $session = ($session)?$session:$this->_helperPermission->getCurrentSession();
        if($session){
            $storeId = $this->_session->getStoreIdBySession($session);
            $quoteId = $this->_session->getQuoteIdBySession($session);
            $tillId = $this->_session->getTillIdBySession($session);
            $locationId = $this->_helperPermission->getCurrentLocation();
            if($quoteId){
                $quote = $quote->load($quoteId);
            }
            if($this->_helper->isInventorySuccessEnable()){
                $locationMapping = Magestore_Coresuccess_Model_Service::locationService();
                $warehouseId = $locationMapping->getWarehouseIdByLocationId($locationId);
            }
        }
        $defaultCustomer = $this->_helperConfig->getDefaultCustomer();
        $quoteData = array(
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::STORE_ID => ($session)?$storeId:Mage::app()->getStore()->getStoreId(),
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TILL_ID => ($session)?$tillId:0,
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID => ($quote->getId())?$quote->getId():'',
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CURRENCY_ID => ($quote->getId())?$quote->getQuoteCurrencyCode():Mage::app()->getStore()->getCurrentCurrencyCode(),
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID => ($quote->getId())?$quote->getCustomerId():$defaultCustomer->getId(),
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_GROUP_ID => ($quote->getId())?$quote->getCustomerGroupId():$defaultCustomer->getGroupId(),
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_EMAIL => ($quote->getId())?$quote->getCustomerEmail():$defaultCustomer->getEmail(),
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_FULLNAME => ($quote->getId())?$quote->getCustomer()->getName():$defaultCustomer->getName(),
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::BILLING_ADDRESS => ($quote->getId() && $quote->getBillingAddress())?$quote->getBillingAddress()->getData():$this->_helperConfig->getWebposStoreAddress(),
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING_ADDRESS => ($quote->getId() && $quote->getShippingAddress())?$quote->getShippingAddress()->getData():$this->_helperConfig->getWebposStoreAddress(),
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_DATA => ($quote->getId())?$this->getCustomerData($quote->getCustomerId()):$this->getCustomerData($defaultCustomer->getId())
        );
        $storeId = Mage::getModel('webpos/user_webpossession')->getStoreIdBySession($session);
        $store = Mage::app()->getStore($storeId);

        $data = array(
//            self::CURRENT_WAREHOUSE_ID => ($session && $warehouseId)?$warehouseId:0,
            self::DEFAULT_STORE_CODE => ($session)?$store->getCode():Mage::app()->getStore()->getCode(),
            self::DEFAULT_CUSTOMER => $this->getCustomerData($defaultCustomer->getId()),
            self::DEFAULT_PAYMENT_METHOD => $this->_helperConfig->getDefaultPaymentMethod(),
            self::DEFAULT_SHIPPING_METHOD => $this->_helperConfig->getDefaultShippingMethod(),
            self::DEFAULT_SHIPPING_TITLE => $this->_helperConfig->getDefaultShippingTitle(),
            self::TAX_CLASS => $this->getTaxClassData(),
            self::TAX_RULES => $this->getTaxRulesData(),
            self::TAX_RATES => $this->getTaxRatesData(),
            self::CUSTOMER_GROUP => $this->getCustomerGroupData(),
            self::WEBPOS_STORE_ADDRESS => $this->_helperConfig->getWebposStoreAddress(),
            self::DEFAULT_CUSTOMER_ID => $this->_helperConfig->getDefaultCustomerId(),
            self::CC_TYPES => $this->getAvailableTypes(),
            self::CC_MONTHS => $this->getMonths(),
            self::CC_YEARS => $this->getYears(),
            self::SHIPPING => $this->_shippingRepository->getOfflineShippingData(),
            self::PAYMENT => $this->_paymentRepository->getOfflinePaymentData(),
            Magestore_Webpos_Api_CheckoutInterface::STORE => (isset($quoteData[Magestore_Webpos_Api_CheckoutInterface::STORE]))?$quoteData[Magestore_Webpos_Api_CheckoutInterface::STORE]:Mage::app()->getStore()->getCode(),
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::STORE_ID => $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::STORE_ID],
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TILL_ID => $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TILL_ID],
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CURRENCY_ID => $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CURRENCY_ID],
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID => $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID],
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID => $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID],
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_GROUP_ID => $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_GROUP_ID],
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_EMAIL => $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_EMAIL],
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_FULLNAME => $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_FULLNAME],
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::BILLING_ADDRESS => $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::BILLING_ADDRESS],
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING_ADDRESS => $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING_ADDRESS],
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_DATA => $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_DATA]
        );
        return $data;
    }

    /**
     * Get tax rates data
     * @return array
     */
    public function getTaxRatesData(){
        $result = array();
        $collection = Mage::getModel('tax/calculation_rate')->getCollection();
        if(count($collection) > 0){
            foreach ($collection as $rate) {
                $data = array(
                    'id' => $rate->getId(),
                    'country' => $rate->getTaxCountryId(),
                    'region_id' => $rate->getTaxRegionId(),
                    'postcode' => $rate->getTaxPostcode(),
                    'rate' => $rate->getRate(),
                    'zip_is_range' => $rate->getZipIsRange(),
                    'zip_from' => $rate->getZipFrom(),
                    'zip_to' => $rate->getZipTo()
                );
                $result[] = $data;
            }
        }
        return $result;
    }

    /**
     * Get tax rules data
     * @return array
     */
    public function getTaxRulesData(){
        $result = array();
        $collection = Mage::getModel('tax/calculation_rule')->getCollection();
        if(count($collection) > 0){
            foreach ($collection as $rule) {
                $calculation = Mage::getModel('tax/calculation');
                $ruleId = $rule->getId();
                $data = array(
                    'id' => $ruleId,
                    'customer_tc_ids' => array_values(array_unique($calculation->getCustomerTaxClasses($ruleId))),
                    'product_tc_ids' => array_values(array_unique($calculation->getProductTaxClasses($ruleId))),
                    'rates_ids' => array_values(array_unique($calculation->getRates($ruleId))),
                    'priority' => $rule->getPriority()
                );
                $result[] = $data;
            }
        }
        return $result;
    }

    /**
     * Get tax class data
     * @return array
     */
    public function getTaxClassData(){
        $result = array();
        $collection = Mage::getModel('tax/class')->getCollection();
        if(count($collection) > 0){
            foreach ($collection as $class) {
                $data = array(
                    'class_id' => $class->getId(),
                    'class_name' => $class->getClassName(),
                    'class_type' => $class->getClassType()
                );
                $result[] = $data;
            }
        }
        return $result;
    }

    /**
     * Get customer group data
     * @return array
     */
    public function getCustomerGroupData(){
        $result = array();
        $collection = Mage::getModel('customer/group')->getCollection();
        if(count($collection) > 0){
            foreach ($collection as $group) {
                $data = array(
                    'id' => $group->getId(),
                    'tax_class_id' => $group->getTaxClassId(),
                    'code' => $group->getCode()
                );
                $result[] = $data;
            }
        }
        return $result;
    }

    /**
     * Get availables credit card types
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getAvailableTypes()
    {
        $types = $this->_paymentConfigModel->getCcTypes();
        $availableTypes = array('AE', 'VI', 'MC', 'DI');
        foreach ($types as $code => $name) {
            if (!in_array($code, $availableTypes)) {
                unset($types[$code]);
            }
        }
        $types = array('' => $this->_helperConfig->__('')) + $types;
        return $types;
    }

    /**
     * Get credit card expire months
     *
     * @return array
     */
    public function getMonths()
    {
        $months = $this->_paymentConfigModel->getMonths();
        $months = array('0' => $this->_helperConfig->__('Month')) + $months;
        return $months;
    }

    /**
     * Get credit card expire years
     *
     * @return array
     */
    public function getYears()
    {
        $years = $this->_paymentConfigModel->getYears();
        $years = array('0' => $this->_helperConfig->__('Year')) + $years;
        return $years;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getCustomerData($customerId){
        $data = array();
        if($customerId){
            $data = Mage::getModel('webpos/api2_customer_rest_admin_v1')->loadCustomer($customerId);
        }
        return $data;

    }
}
