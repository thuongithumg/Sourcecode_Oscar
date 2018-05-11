<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config;

/**
 * Class Magestore\Webpos\Model\Config\Config
 *
 */
class Config extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Model\WebposConfigProvider\ConfigProviderInterface {

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $_helper;
    
    /**
     * @var type
     * @var \Magestore\Webpos\Model\WebposConfigProvider\CompositeConfigProvider
     */
    protected $_configProvider;

    /**
     * @var array
     */
    protected $_configurations;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\Model\Context $context,
    \Magento\Framework\Registry $registry,
    \Magestore\Webpos\Helper\Data $helper,
    \Magestore\Webpos\Model\WebposConfigProvider\CompositeConfigProvider $configProvider,
    \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
    \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
    array $data = []
    ) {
        $this->_helper = $helper;
        $this->_configurations = $this->setConfigPaths();
        $this->_configProvider = $configProvider;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get general config.
     *
     * @param
     * @return array
     */
    public function setConfigPaths() {
        $configurations = [
            'general/enable_delivery_date', 'general/webpos_logo', 'general/webpos_color',
            'product_search/product_attribute', 'product_search/barcode',
            'offline/stock_item_time', 'offline/product_time', 'offline/customer_time',
            'offline/order_time', 'offline/order_limit',
            'guest_checkout/customer_id', 'guest_checkout/first_name', 'guest_checkout/last_name',
            'guest_checkout/street', 'guest_checkout/country_id', 'guest_checkout/city',
            'guest_checkout/region_id', 'guest_checkout/zip', 'guest_checkout/telephone', 'guest_checkout/email',
            'receipt/general/auto_print','receipt/content/title', 'receipt/content/font_type','receipt/content/header_text','receipt/content/footer_text',
            'receipt/optional/show_receipt_logo', 'receipt/optional/show_cashier_name','receipt/optional/show_comment',
            'shipping/enable_mark_as_shipped_default', 'general/enable_session', 'general/confirm_delete_order', 'general/ask_for_sale_order_email', 'general/day_to_show_session_history', 'general/active_key',
            'general/custom_sale_default_tax_class','general/suggest_address','general/google_api_key'
        ];
        return $configurations;
    }

    /**
     * Get all configurations for api.
     *
     * @param
     * @return array
     */
    public function getAllConfiguration() {
        $config = $this;
        $list = $this->_configurations;
        $configurations = array();
        /* get Webpos configuration */
        foreach ($list as $item) {
            $config->setData('path', 'webpos/' . $item);
            $config->setData('value', $this->_helper->getStoreConfig('webpos/' . $item));
            $configurations[] = $config->getData();
        }

        $config->setData('path', 'webpos/general/webpos_logo_url');
        $config->setData('value', $this->_helper->getWebPosImages());
        $configurations[] = $config->getData();

        /* get global config */
        $globalConfig = $this->_configProvider->getConfig();
        if(count($globalConfig)) {
            foreach($globalConfig as $key => $data) {
                 if(is_array($data)) {
                     //$data = \Zend_Json::encode($data);
                 }
                 $configurations[] = ['path' => $key, 'value' => $data];
            }
        }
        
        $taxCalculation = $this->getTaxConfiguration();
        if($taxCalculation && count($taxCalculation) > 0){
            foreach($taxCalculation as $key => $data) {
                 $configurations[] = ['path' => $key, 'value' => $data];
            }
        }

        $shippingCalculation = $this->getShippingConfiguration();
        if($shippingCalculation && count($shippingCalculation) > 0){
            foreach($shippingCalculation as $key => $data) {
                $configurations[] = ['path' => $key, 'value' => $data];
            }
        }

        $addCustomer = $this->getAddCustomerConfiguration();
        if($addCustomer && count($addCustomer) > 0){
            foreach($addCustomer as $key => $data) {
                $configurations[] = ['path' => $key, 'value' => $data];
            }
        }
        return $configurations;
    }

    /**
     * Get config by path.
     *
     * @param $list
     * @return array
     */
    public function getConfigByPath($path) {
        return $this->_helper->getStoreConfig($path);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $output = [];
        $configs = $this->getAllConfiguration();
        if(count($configs)) {
            foreach($configs as $config) {
                $output[$config['path']] = $config['value'];
            }
        }
        return $output;
    }    
    
     /**
     * Get config.
     *
     * @return array
     */
    public function getTaxConfiguration(){
        $paths = [
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
            'tax/cart_display/subtotal',
            'tax/sales_display/subtotal'
        ];
        $data = [];
        if(count($paths)) {
            foreach($paths as $path) {
                $value = $this->getConfigByPath($path);
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
        $paths = [
            'shipping/origin/region_id',
            'shipping/origin/country_id',
            'shipping/origin/postcode'
        ];
        $data = [];
        if(count($paths)) {
            foreach($paths as $path) {
                $value = $this->getConfigByPath($path);
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
    public function getAddCustomerConfiguration(){
        $paths = [
            'customer/address/dob_show',
            'customer/address/taxvat_show',
            'customer/address/gender_show'
        ];
        $data = [];
        if(count($paths)) {
            foreach($paths as $path) {
                $value = $this->getConfigByPath($path);
                $data[$path] = $value;
            }
        }
        return $data;
    }
}
