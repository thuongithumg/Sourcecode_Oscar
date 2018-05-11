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

class Magestore_Webpos_Model_Config_Default extends Magestore_Webpos_Model_Abstract
{
    public function getConfig()
    {
        $output = array();
        $_store = Mage::app()->getStore();
        $output['priceFormat'] = Mage::helper('core')->jsonDecode(Mage::helper('tax')->getPriceFormat(Mage::app()->getStore()));
        $output['basePriceFormat'] = Mage::helper('core')->jsonDecode(Mage::Helper('tax')->getPriceFormat(Mage::app()->getStore()));
        $currentLocation = Mage::helper('webpos/permission')->getCurrentLocationObject();
        $output['locationId'] = Mage::helper('webpos/permission')->getCurrentLocation();
        $output['location_name'] = $currentLocation->getDisplayName();
        $output['location_address'] = $currentLocation->getAddress();

        $staffModel = Mage::helper('webpos/permission')->getCurrentStaffModel();
        $output['staffId'] = $staffModel->getId();
        $output['staffName'] = $staffModel->getDisplayName();
        $output['customerGroupOfStaff'] = $staffModel->getCustomerGroup();

        $output['storeCode'] = $_store->getCode();
        $output['last_offline_order_id'] =  Mage::helper('webpos/permission')->getCurrentLastOfflineOrderId();
        $output['country'] = Mage::getModel('webpos/config_country')->getList();
        $output['regionJson'] = Mage::helper('directory')->getRegionJson();

        $output['maximum_discount_percent'] = Mage::helper('webpos/permission')->getMaximumDiscountPercent();
        $output['order_sync_time'] = Mage::helper('webpos')->getTimeSyncOrder();
        $output['order_sync_time_period'] = Mage::getStoreConfig('webpos/offline/order_limit', Mage::app()->getStore()->getId());
        $output['timeoutSession'] = Mage::helper('webpos/permission')->getTimeoutSession();
        $output['currentCurrencyCode'] = Mage::app()->getStore()->getCurrentCurrency()->getCode();
        $output['baseCurrencyCode'] = Mage::app()->getStore()->getBaseCurrency()->getCode();
        $output['currentCurrencySymbol'] = Mage::app()->getLocale()->currency($output['currentCurrencyCode'])->getSymbol();
        $output['can_adjust_stock'] = true;

        $output['customerGroup'] = Mage::getModel('webpos/source_adminhtml_customergroup')->getAllCustomerByCurrentStaff();

        $output['cc_types'] = $this->getAvailableTypes();
        $output['cc_months'] = $this->getMonths();
        $output['cc_years'] = $this->getYears();

        /* S: Daniel - integration */
        $output['plugins'] = $this->getEnablePlugins();
        $output['plugins_config'] = $this->getEnablePluginsConfig();
        /* E: Daniel - integration */

        $resourceAccess = array();
        if (Mage::helper('webpos/permission')->getCurrentUser()) {
            $staffId = Mage::helper('webpos/permission')->getCurrentUser();
            $staffModel = Mage::getModel('webpos/user')->load($staffId);
            $roleId = $staffModel->getRoleId();

            $roleModel =  Mage::getModel('webpos/role')->load($roleId);

            $authorizeRuleCollection = explode(',',$roleModel->getPermissionIds());
            $roleOptionsArray = $roleModel->getOptionArray();
            foreach ($authorizeRuleCollection as $authorizeRule) {
                if (array_key_exists($authorizeRule,$roleOptionsArray))
                {
                    $resourceAccess[] = $roleOptionsArray[$authorizeRule];
                }
            }
        }

        $output['staffResourceAccess'] = $resourceAccess;
        $output['currencies'] = $this->getCurrencyList();

        $configObject = new Varien_Object();
        $configObject->setData($output);
        Mage::dispatchEvent('webpos_config_load_after', array(
            'object_config' => $configObject
        ));
        $output = $configObject->getData();

//        echo "<pre>"; print_r($output); die;
        return $output;
    }

    /**
     * Get availables credit card types
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getAvailableTypes()
    {
        $types = Mage::getSingleton('payment/config')->getCcTypes();
        $availableTypes = array('AE', 'VI', 'MC', 'DI');
        foreach ($types as $code => $name) {
            if (!in_array($code, $availableTypes)) {
                unset($types[$code]);
            }
        }
        $types = array('' => Mage::helper('webpos')->__('')) + $types;
        return $types;
    }

    /**
     * Get credit card expire months
     *
     * @return array
     */
    public function getMonths()
    {
        $months = Mage::getSingleton('payment/config')->getMonths();
        $months = array('0' => Mage::helper('webpos')->__('Month')) + $months;
        return $months;
    }

    /**
     * Get credit card expire years
     *
     * @return array
     */
    public function getYears()
    {
        $years = Mage::getSingleton('payment/config')->getYears();
        $years = array('0' => Mage::helper('webpos')->__('Year')) + $years;
        return $years;
    }


    /**
     * @return array
     */
    public function getEnablePlugins(){
        $plugins = array();
        if (Mage::helper('core')->isModuleEnabled('Magestore_Customercredit')) {
            $plugins[] = 'os_store_credit';
        }
        if (Mage::helper('core')->isModuleEnabled('Magestore_Rewardpoints')) {
            $plugins[] = 'os_reward_points';
        }
        if (Mage::helper('core')->isModuleEnabled('Magestore_Giftvoucher')) {
            $plugins[] = 'os_gift_card';
        }
        return $plugins;
    }

    /**
     * @return array
     */
    public function getEnablePluginsConfig(){
        $config = array();

        if (Mage::helper('core')->isModuleEnabled('Magestore_Customercredit')) {
            $config['os_store_credit'] = $this->getModuleConfig('Magestore_Customercredit', 'customercredit');
        }
        if (Mage::helper('core')->isModuleEnabled('Magestore_Rewardpoints')) {
            $config['os_reward_points'] = $this->getModuleConfig('Magestore_Rewardpoints', 'rewardpoints');
        }
        if (Mage::helper('core')->isModuleEnabled('Magestore_Giftvoucher')) {
            $config['os_gift_card'] = $this->getModuleConfig('Magestore_Giftvoucher', 'giftvoucher');
        }
        return $config;
    }

    /**
     * @return array
     */
    public function getModuleConfig($module, $code)
    {
        $results = array();
        $configs = array();
        $helper = Mage::helper('webpos');
        if (Mage::helper('core')->isModuleEnabled($module)) {
            $code = 'webpos';
            $configs = $helper->getStoreConfig($code);
        }

        /* convert configs to flat path */
        if (count($configs) > 0) {
            foreach ($configs as $index => $subConfigs) {
                foreach ($subConfigs as $subIndex => $value) {
                    $results[$code . '/' . $index . '/' . $subIndex] = $value;
                }
            }
        }
        return $results;
    }

    public function getCurrencyList()
    {
        $currency = Mage::getModel('directory/currency');
        $collection = $currency->getConfigAllowCurrencies();
        $baseCurrencies = $currency->getConfigBaseCurrencies();
        $baseCurrencyCode = $baseCurrencies[0];
        $baseCurrency = $currency->load($baseCurrencyCode);
        $currencyList = array();
        if(count($collection) > 0) {
            foreach ($collection as $code) {
                $currencyRate = $baseCurrency->getRate($code);
                if(!$currencyRate) {
                    continue;
                }
                $currencySymbol= Mage::app()->getLocale()->currency($code)->getSymbol();
                $currencyName= Mage::app()->getLocale()->currency($code)->getName();
                $isDefault = '0';
                if($code == $baseCurrencyCode)
                    $isDefault = '1';
                $currency->setCode($code);
                $currency->setCurrencyName($currencyName);
                $currency->setCurrencySymbol($currencySymbol);
                $currency->setIsDefault($isDefault);
                $currency->setCurrencyRate($currencyRate);
                $currencyList[] = $currency->getData();
            }
        }
        return $currencyList;
    }
}
