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

class Magestore_Webpos_Model_Config_Config extends Magestore_Webpos_Model_Abstract
{
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $configs = $this->getAllConfiguration();
        if(count($configs)) {
            return $configs;
        }
        return array();
    }

    /**
     * Get all configurations for api.
     *
     * @param
     * @return array
     */
    public function getAllConfiguration() {
        $config = $this;
        $list = $this->setConfigPaths();
        $configurations = array();
        /* get Webpos configuration */
        foreach ($list as $item) {
            $value = Mage::helper('webpos/config')->getStoreConfig('webpos/' . $item);
            if (strpos($item, 'webpos_logo') !== false) {
                $value = Mage::helper('webpos/config')->getLogoUrl();
            }
            $configurations[] = array(
                'path' => 'webpos/' . $item,
                'value' => $value
            );
        }

        $currencyConfigData = Mage::getModel('webpos/config_currency')->getConfig();
        $taxConfigData = Mage::getModel('webpos/config_tax')->getConfig();
        $defaultConfigData = Mage::getModel('webpos/config_default')->getConfig();
        $inventoryConfigData = Mage::getModel('webpos/config_inventory')->getConfig();
        $rewardpointConfigData = Mage::getModel('webpos/config_rewardpoint')->getConfig();

        /* get global config */
        $globalConfigs = array(
            $defaultConfigData,
            $currencyConfigData,
            $taxConfigData,
            $inventoryConfigData,
            $rewardpointConfigData
        );
        foreach ($globalConfigs as $globalConfig){
            if(count($globalConfig)) {
                foreach($globalConfig as $key => $data) {
                    if(is_array($data)) {
                        //$data = \Zend_Json::encode($data);
                    }
                    $configurations[] = array('path' => $key, 'value' => $data);
                }
            }
        }

        return $configurations;
    }

    /**
     * Get general config.
     *
     * @param
     * @return array
     */
    public function setConfigPaths() {
        $configurations = array(
            'general/enable_delivery_date',
            'general/webpos_logo',
            'general/webpos_color',
            'general/confirm_delete_order',
            'general/enable_session',
            'general/active_key',
            'product_search/product_attribute',
            'product_search/barcode',
            'offline/stock_item_time',
            'offline/product_time',
            'offline/customer_time',
            'offline/order_time',
            'offline/order_limit',
            'guest_checkout/customer_id',
            'guest_checkout/first_name',
            'guest_checkout/last_name',
            'guest_checkout/street',
            'guest_checkout/country_id',
            'guest_checkout/city',
            'guest_checkout/region_id',
            'guest_checkout/zip',
            'guest_checkout/telephone',
            'guest_checkout/email',
            'receipt/auto_print',
            'receipt/font_type',
            'receipt/header_text',
            'receipt/footer_text',
            'receipt/show_receipt_logo',
            'receipt/show_cashier_name',
            'receipt/show_comment'
        );
        return $configurations;
    }
}