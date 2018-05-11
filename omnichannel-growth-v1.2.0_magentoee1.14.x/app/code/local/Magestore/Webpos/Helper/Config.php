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
 * Webpos Helper Config
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Daniel Magestore Developer
 */
class Magestore_Webpos_Helper_Config extends Mage_Core_Helper_Abstract {
	
	const DEFAULT_OPENING_CASH_PATH = "webpos/report/default_transfer_money";
	const CASH_COUNTING_DENOMINATION_PATH = "webpos/report/denomination";
	const ENABLE_BACKORDER_PATH = "webpos/general/ignore_checkout";
	const ENABLE_CASH_DRAWER_PATH = "webpos/general/enable_tills";
	const DEFAULT_SHIPPING_METHOD_PATH = "webpos/shipping/defaultshipping";
	const DEFAULT_PAYMENT_METHOD_PATH = "webpos/payment/defaultpayment";
	const ENABLE_DELIVERY_DATE_PATH = "webpos/general/enable_delivery_date";
	const DEFAULT_CUSTOMER_ID_PATH = "webpos/guest_checkout/customer_id";
    const ENABLE_POLE_DISPLAY = "webpos/general/enable_pole_display";
	protected $WEBPOS_STORE_ADDRESS_PATHS = array(
	    'country_id' => 'webpos/guest_checkout/country_id',
	    'region_id' => 'webpos/guest_checkout/region_id',
	    'postcode' => 'webpos/guest_checkout/zip',
	    'street' => 'webpos/guest_checkout/street',
	    'telephone' => 'webpos/guest_checkout/telephone',
	    'city' => 'webpos/guest_checkout/city',
	    'firstname' => 'webpos/guest_checkout/first_name',
	    'lastname' => 'webpos/guest_checkout/last_name',
	    'email' => 'webpos/guest_checkout/email'
    );

	public function isEnablePoleDisplay() {
        return (boolean) $this->getStoreConfig(self::ENABLE_POLE_DISPLAY);
    }
	/*
	* return (int)Store Id
	*/
	public function getCurrentStoreId(){
		return Mage::app()->getStore()->getStoreId();
	}	
	
	/*
	* Input: configuration path
	* return store configuration
	*/
	public function getStoreConfig($path){
		$storeId = $this->getCurrentStoreId();
		return Mage::getStoreConfig($path,$storeId);
	}	
	
	/*
	* function to get config enable/disable delivery date
	* return boolean
	*/
	public function isEnableDeliveryDate(){
		return $this->getStoreConfig(self::ENABLE_DELIVERY_DATE_PATH);
	}

    /**
     * Get default payment method
     * @return string
     */
	public function getDefaultPaymentMethod(){
	    return Mage::helper('webpos/payment')->getDefaultPaymentMethod();
	}

	/**
     * Get default shipping method
     * @return string
     */
	public function getDefaultShippingMethod(){
		return $this->getStoreConfig(self::DEFAULT_SHIPPING_METHOD_PATH);
	}

	/**
     * Get default shipping method title
     * @return string
     */
	public function getDefaultShippingTitle(){
        $code = $this->getDefaultShippingMethod();
		return Mage::getModel('webpos/source_adminhtml_shipping')->getShippingTitleByCode($code);
	}

	/**
     * Get default customer id
     * @return string
     */
	public function getDefaultCustomerId(){
		return $this->getStoreConfig(self::DEFAULT_CUSTOMER_ID_PATH);
	}

	/**
     * Get default customer group id
     * @return string
     */
	public function getDefaultCustomer(){
        $customer = Mage::getModel('customer/customer');
        $customerId = $this->getDefaultCustomerId();
        if($customerId){
            $customer->load($customerId);
        }
		return $customer;
	}

	/**
     * Get default customer group id
     * @return string
     */
	public function getDefaultCustomerGroupId(){
	    $customerGroup = 0;
        $customer = $this->getDefaultCustomer();
        if($customer->getId()){
            $customerGroup = $customer->getGroupId();
        }
		return $customerGroup;
	}

	/**
     * Get default customer group id
     * @return string
     */
	public function getDefaultCustomerEmail(){
	    $email = '';
        $customer = $this->getDefaultCustomer();
        if($customer->getId()){
            $email = $customer->getEmail();
        }
		return $email;
	}

	/**
     * Get default customer name
     * @return string
     */
	public function getDefaultCustomerName(){
	    $name = '';
        $customer = $this->getDefaultCustomer();
        if($customer->getId()){
            $name = $customer->getName();
        }
		return $name;
	}

    /**
     * Get webpos store address from config
     * @return array
     */
	public function getWebposStoreAddress(){
	    $address = array();
	    $addressPaths = $this->WEBPOS_STORE_ADDRESS_PATHS;
        if(count($addressPaths) > 0){
            foreach ($addressPaths as $key => $path) {
                $address[$key] = $this->getStoreConfig($path);
            }
        }
        return $address;
    }

    /**
     * Get webpos config.
     *
     * @return array
     */
    public function getWebposConfiguration(){
        $paths = array(
            self::ENABLE_BACKORDER_PATH,
            self::ENABLE_CASH_DRAWER_PATH,
            self::ENABLE_DELIVERY_DATE_PATH,
            self::DEFAULT_OPENING_CASH_PATH,
            self::CASH_COUNTING_DENOMINATION_PATH
        );
        $data = array();
        if(count($paths)) {
            foreach($paths as $path) {
                $value = $this->getStoreConfig($path);
                $data[$path] = $value;
            }
        }
        return $data;
    }

    /**
     * Get tax config.
     *
     * @return array
     */
    public function getTaxConfiguration(){
        $paths = array(
            'tax/classes/shipping_tax_class',
            'tax/classes/default_product_tax_class',
            'tax/classes/default_customer_tax_class',
            'tax/calculation/algorithm',
            'tax/calculation/based_on',
            'tax/calculation/price_includes_tax',
            'tax/calculation/shipping_includes_tax',
            'tax/calculation/apply_after_discount',
            'tax/calculation/discount_tax',
            'tax/calculation/apply_tax_on',
            'tax/calculation/cross_border_trade_enabled',
            'tax/cart_display/price',
            'tax/cart_display/subtotal'
        );
        $data = array();
        if(count($paths)) {
            foreach($paths as $path) {
                $value = $this->getStoreConfig($path);
                $data[$path] = $value;
            }
        }
        return $data;
    }

    /**
     * Get config.
     *
     * @return array
     */
    public function getShippingConfiguration(){
        $paths = array(
            'shipping/origin/region_id',
            'shipping/origin/country_id',
            'shipping/origin/postcode'
        );
        $data = array();
        if(count($paths)) {
            foreach($paths as $path) {
                $value = $this->getStoreConfig($path);
                $data[$path] = $value;
            }
        }
        return $data;
    }

    /**
     * Get receipt config.
     *
     * @return array
     */
    public function getReceiptConfiguration(){
        $paths = array(
            'webpos/receipt/auto_print',
            'webpos/receipt/font_type',
            'webpos/receipt/footer_text',
            'webpos/receipt/header_text',
            'webpos/receipt/show_cashier_name',
            'webpos/receipt/show_comment',
            'webpos/receipt/show_barcode',
            'webpos/receipt/show_receipt_logo'
        );
        $data = array();
        if(count($paths)) {
            foreach($paths as $path) {
                $value = $this->getStoreConfig($path);
                $data[$path] = $value;
            }
        }
        $data['webpos/general/webpos_logo'] = $this->getLogoUrl();
        return $data;
    }

    /**
     * @return string
     */
    public function getLogoUrl($imgUrl = false)
    {
        $imageUrl = ($imgUrl)?$imgUrl:Mage::helper('webpos')->getWebposLogo();
        if ($imageUrl) {
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'webpos/logo/'.$imageUrl;
        } else {
            return '';
        }
    }

    /**
     * @return array
     */
    public function getIntegrationConfiguration(){
        $config = array();
        $config['plugins'] = $this->getEnablePlugins();
        $config['plugins_config'] = $this->getEnablePluginsConfig();
        return $config;
    }

    /**
     * @return array
     */
    public function getEnablePlugins(){
        $helper = Mage::helper('webpos');
        $plugins = array();
        if ($helper->isStoreCreditEnable()) {
            $plugins[] = 'os_store_credit';
        }
        if ($helper->isRewardPointsEnable()) {
            $plugins[] = 'os_reward_points';
        }
        if ($helper->isGiftCardEnable()) {
            $plugins[] = 'os_gift_card';
        }
        if ($helper->isPdfinvoiceEnable()) {
            $plugins[] = 'os_pdf_invoice_plus';
        }
        if ($helper->isInventorySuccessEnable()) {
            $plugins[] = 'os_inventory_success';
        }
        if ($helper->isStorePickupEnable()) {
            $plugins[] = 'os_storepickup';
        }
        return $plugins;
    }

    /**
     * @return array
     */
    public function getEnablePluginsConfig(){
        $helper = Mage::helper('webpos');
        $config = array();
        if ($helper->isStoreCreditEnable()) {
            $config['os_store_credit'] = $this->getModuleConfig('customercredit');
        }
        if ($helper->isRewardPointsEnable()) {
            $config['os_reward_points'] = $this->getModuleConfig('rewardpoints');
        }
        if ($helper->isGiftCardEnable()) {
            $config['os_gift_card'] = $this->getModuleConfig('giftvoucher');
        }
        if ($helper->isPdfinvoiceEnable()) {
            $config['os_pdf_invoice_plus'] = $this->getPdfInvoiceConfigData();
        }
        if ($helper->isInventorySuccessEnable()) {
            $config['os_inventory_success'] = $this->getInventorySuccessConfigData();
        }
        if ($helper->isStorePickupEnable()) {
            $config['os_storepickup'] = $this->getStorePickupConfigData();
        }
        return $config;
    }

    /**
     * @return array
     */
    public function getModuleConfig($code)
    {
        $results = array();
        $configs = $this->getStoreConfig($code);
        if (count($configs) > 0) {
            foreach ($configs as $index => $subConfigs) {
                foreach ($subConfigs as $subIndex => $value) {
                    $results[$code . '/' . $index . '/' . $subIndex] = $value;
                }
            }
        }
        return $results;
    }

    /**
     * @return string
     */
    public function getGuestCustomerId()
    {
        return $this->getDefaultCustomerId();
    }

    /**
     * @return bool
     */
    public function isEnableCashDrawer()
    {
        return ($this->getStoreConfig(self::ENABLE_CASH_DRAWER_PATH))?true:false;
    }

    public function generateGuestCustomerAccount()
    {
        $customerId = $this->getStoreConfig('webpos/guest_checkout/customer_id');
        $customerData = Mage::getModel('customer/customer')->load($customerId);

        $first_name = $this->getStoreConfig('webpos/guest_checkout/first_name');
        $last_name = $this->getStoreConfig('webpos/guest_checkout/last_name');
        $street = $this->getStoreConfig('webpos/guest_checkout/street');
        $country_id = $this->getStoreConfig('webpos/guest_checkout/country_id');
        $region_id = $this->getStoreConfig('webpos/guest_checkout/region_id');
        $city = $this->getStoreConfig('webpos/guest_checkout/city');
        $zip = $this->getStoreConfig('webpos/guest_checkout/zip');
        $telephone = $this->getStoreConfig('webpos/guest_checkout/telephone');
        $email = $this->getStoreConfig('webpos/guest_checkout/email');

        $first_name = $first_name ? $first_name : "Guest" ;
        $last_name = $last_name ? $last_name : "POS" ;
        $street = $street ? $street : "Street" ;
        $country_id = $country_id ? $country_id : "US" ;
        $region_id = $region_id ? $region_id : "12" ;
        $city = $city ? $city : "Guest City" ;
        $zip = $zip ? $zip : "90034" ;
        $telephone = $telephone ? $telephone : "12345678" ;
        $email = $email ? $email : "guest@example.com" ;

        $customerModel = Mage::getModel('customer/resource_customer_collection')
            ->addFieldToFilter('email', $email)
            ->getFirstItem();
        if ($customerModel->getId()) {
            $customerData = $customerModel;
            $email = $customerModel->getEmail();
        }

        $websites = Mage::getModel('core/website')->getCollection()->addFieldToFilter('is_default', 1);
        $website = $websites->getFirstItem();
        $websiteId = $website->getId();

        $customerData->setData('email',$email);
        $customerData->setData('website_id',$websiteId);
        $customerData->setData('firstname',$first_name);
        $customerData->setData('lastname',$last_name);
        $customerData->setData('city',$city);
        $customerData->setData('region_id',$region_id);
        $customerData->setData('region',$region_id);
        $customerData->setData('group_id',Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID));

        $addressCustomer = $customerData->getAddresses();

        foreach ($addressCustomer as $key => $value) {
            $value->setData('lastname',$last_name);
            $value->setData('firstname',$first_name);
            $value->setData('city',$city);
            $value->setData('region',$region_id);
            $value->setData('region_id',$region_id);
            $value->setData('postcode',$zip);
            $value->setData('telephone',$telephone);
            $value->setData('street',$street);
            $value->setData('country_id',$country_id);
            $value->setData('email',$email);
            $value->save();
        }
        try{
            $customerData->save();
            Mage::getConfig()->saveConfig('webpos/guest_checkout/customer_id', $customerData->getId());
        }  catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }
    
    public function isEnableAutoSendEmail($type) {
        $config = false;
        switch ($type) {
            case 'order':
                $config = $this->getStoreConfig('webpos/email_configuration/auto_email_orders');
                break;
            case 'invoice':
                $config = $this->getStoreConfig('webpos/email_configuration/auto_email_invoice');
                break;
            case 'shipment':
                $config = $this->getStoreConfig('webpos/email_configuration/auto_email_shipment');
                break;
            case 'creditmemo':
                $config = $this->getStoreConfig('webpos/email_configuration/auto_email_creditmemo');
                break;
        }
        return $config;
    }
    
    public function getWebposEmailTemplate($type) {
        $config = array();
        switch ($type) {
            case 'order':
                $guestTemplate = $this->getStoreConfig('webpos/email_configuration/email_template_guest_order');
                $customerTemplate = $this->getStoreConfig('webpos/email_configuration/email_template_customer_order');
                $config['guest'] = ($guestTemplate != 'webpos_email_configuration_email_template_guest_invoice') ? $guestTemplate : '';
                $config['customer'] = ($customerTemplate != 'webpos_email_configuration_email_template_customer_order') ? $customerTemplate : '';
                break;
            case 'invoice':
                $guestTemplate = $this->getStoreConfig('webpos/email_configuration/email_template_guest_invoice');
                $customerTemplate = $this->getStoreConfig('webpos/email_configuration/email_template_customer_invoice');
                $config['guest'] = ($guestTemplate != 'webpos_email_configuration_email_template_guest_invoice') ? $guestTemplate : '';
                $config['customer'] = ($customerTemplate != 'webpos_email_configuration_email_template_customer_invoice') ? $customerTemplate : '';
                break;
            case 'shipment':
                $guestTemplate = $this->getStoreConfig('webpos/email_configuration/email_template_guest_shipment');
                $customerTemplate = $this->getStoreConfig('webpos/email_configuration/email_template_customer_shipment');
                $config['guest'] = ($guestTemplate != 'webpos_email_configuration_email_template_guest_shipment') ? $guestTemplate : '';
                $config['customer'] = ($customerTemplate != 'webpos_email_configuration_email_template_customer_shipment') ? $customerTemplate : '';
                break;
            case 'creditmemo':
                $guestTemplate = $this->getStoreConfig('webpos/email_configuration/email_template_guest_creditmemo');
                $customerTemplate = $this->getStoreConfig('webpos/email_configuration/email_template_customer_creditmemo');
                $config['guest'] = ($guestTemplate != 'webpos_email_configuration_email_template_guest_creditmemo') ? $guestTemplate : '';
                $config['customer'] = ($customerTemplate != 'webpos_email_configuration_email_template_customer_creditmemo') ? $customerTemplate : '';
                break;
        }
        return $config;
    }

    public function getPdfInvoiceConfigData(){
        $config = array(
            'print_urls' => array(
                'Letter' => Mage::getUrl('pdfinvoiceplus/order/print', array('type' => 'Letter', 'order_id' => 'entity_id_here')),
                'A4' => Mage::getUrl('pdfinvoiceplus/order/print', array('type' => 'A4', 'order_id' => 'entity_id_here')),
                'A5' => Mage::getUrl('pdfinvoiceplus/order/print', array('type' => 'A5', 'order_id' => 'entity_id_here')),
                'A6' => Mage::getUrl('pdfinvoiceplus/order/print', array('type' => 'A6', 'order_id' => 'entity_id_here')),
                'A7' => Mage::getUrl('pdfinvoiceplus/order/print', array('type' => 'A7', 'order_id' => 'entity_id_here'))
            ),
            'template_names' => array(
                'Letter' => '',
                'A4' => '',
                'A5' => '',
                'A6' => '',
                'A7' => ''
            )
        );
        $collection = Mage::getModel('pdfinvoiceplus/template')->getCollection()->addFieldToFilter('status', 1);

        if(Mage::helper('pdfinvoiceplus')->useMultistore()){
            $collection->addFieldToFilter('stores', array('finset' => Mage::helper('webpos/permission')->getCurrentStoreId()));
            if($collection->getSize() == 0){
                $collection = Mage::getModel('pdfinvoiceplus/template')->getCollection()
                    ->addFieldToFilter('status', 1);
            }
        }
        $collection->setOrder('created_at','ASC');
        $allowTemplates[] = array();
        if($collection->getSize() > 0){
            foreach ($collection as $template) {
                $allowTemplates[$template->getFormat()] = $template->getFormat();
                $config['template_names'][$template->getFormat()] = $template->getTemplateName();
            }
        }
        foreach ($config['print_urls'] as $format => $url) {
            if(!isset($allowTemplates[$format])){
                unset($config['print_urls'][$format]);
                unset($config['template_names'][$format]);
            }
        }
        return $config;
    }

    /**
     * Get inventory success data
     * @return array
     */
    public function getInventorySuccessConfigData(){
        $config = array(
            'warehouses' => array()
        );
        $warehouseService = Magestore_Coresuccess_Model_Service::warehouseService();
        $warehouses = $warehouseService->getWarehouses();
        if(!empty($warehouses)){
            foreach ($warehouses as $warehouseData){
                $config['warehouses'][] = $warehouseData;
            }
        }
        return $config;
    }

    /**
     * Get store pickup data
     * @return array
     */
    public function getStorePickupConfigData(){
        $config = array(
            'enable' => false
        );
        $storePickupHelper = Mage::helper('storepickup');
        if($storePickupHelper->getConfig('active')){
            $config['enable'] = true;
        }
        return $config;
    }
}