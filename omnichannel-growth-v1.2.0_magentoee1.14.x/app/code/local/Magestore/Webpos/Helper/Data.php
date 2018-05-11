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
 * Webpos Helper
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Helper_Data extends Mage_Core_Helper_Abstract {
    
    public function getOnePage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function isCustomerLoggedIn() {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function getConfigData() {
        $configData = array();
        $configItems = array();
        if(!empty($configItems))
            foreach ($configItems as $configItem) {
                $config = explode('/', $configItem);
                $value = $config[1];
                $configData[$value] = Mage::getStoreConfig('webpos/' . $configItem);
            }
        return $configData;
    }

    public function getStoreId() {
        return Mage::app()->getStore()->getId();
    }


    public function checkEE() {
        return Mage::getEdition() == Mage::EDITION_ENTERPRISE;
    }

    /**
     * Get version of WebPOS extension
     *
     * @return string
     */
    public function getWebposVersion() {
        if (Mage::helper('core')->isModuleEnabled('Magestore_Webpos')) {
            return (string) Mage::getConfig()->getModuleConfig('Magestore_Webpos')->version;
        }
        return null;
    }

    /**
     * Check if WebPOS is upgrade from old version
     *
     * @return string
     */
    public function isWebposUpgradeFromOld() {
        if (((int) str_replace('.', '', $this->getWebposVersion())) > 10) {
            return true;
        }
        return false;
    }

    public function columnExist($tableName, $columnName) {
        $resource = Mage::getSingleton('core/resource');
        $writeAdapter = $resource->getConnection('core_write');

        Zend_Db_Table::setDefaultAdapter($writeAdapter);
        $table = new Zend_Db_Table($tableName);
        if (!in_array($columnName, $table->info('cols'))) {
            return false;
        } return true;
    }

    public function tableExist($tableName) {
        $exists = (boolean) Mage::getSingleton('core/resource')
            ->getConnection('core_write')
            ->showTableStatus(trim($tableName, '`'));
        return $exists;
    }

    /**
     * format date
     *
     * @param date value
     * @return date formated
     */
    public function formatDate($value) {
        return Mage::helper('core')->formatDate($value, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
    }

    /**
     * format price without symbol
     *
     * @param price value
     * @return price formated
     */
    public function formatPriceWithoutSymbol($value) {
        return strip_tags(Mage::helper('core')->currency($value, true, false));
    }

    /**
     * format price without symbol
     *
     * @param price value
     * @return price formated
     */
    public function formatPriceDefault($value) {
        return Mage::helper('core')->currency($value, true, false);
    }

    /**
     * Remove characters that are not numberic
     */
    public function removeCharacterNotNumber($str){
        return preg_replace("/[^0-9,.]/", "", $str);
    }

    /**
     * @param $order
     * @param $key
     * @param $baseKey
     * @return mixed
     */
    public function getOrderFieldValue($order, $key, $baseKey){
        if($order && $key && $baseKey){
            $amount = $order->getData($key);
            $baseAmount = $order->getData($baseKey);
            $orderCurrencyCode = $order->getOrderCurrencyCode();
            $baseOrderCurrencyCode = $order->getBaseCurrencyCode();
            $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
            if($baseOrderCurrencyCode == $currentCurrencyCode){
                $amount = $baseAmount;
            }elseif($orderCurrencyCode == $currentCurrencyCode){
                $amount = $amount;
            }else{
                $amount = Mage::helper('directory')->currencyConvert($amount, $orderCurrencyCode, $currentCurrencyCode);
            }
            return $amount;
        }
        return 0;
    }

    /**
     * @param $order
     * @param float $amount
     * @return array
     */
    public function getValueForOrder($order, $amount = 0){
        $baseAmount = 0;
        if($order && $amount){
            $orderCurrencyCode = $order->getOrderCurrencyCode();
            $baseOrderCurrencyCode = $order->getBaseCurrencyCode();
            $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
            if($orderCurrencyCode == $currentCurrencyCode){
                $amount = $amount;
            }else{
                $amount = Mage::helper('directory')->currencyConvert($amount, $currentCurrencyCode, $orderCurrencyCode);
            }
            $baseAmount = Mage::helper('directory')->currencyConvert($amount, $orderCurrencyCode, $baseOrderCurrencyCode);
        }
        return array('amount' => floatval($amount), 'base' => floatval($baseAmount));
    }

    /**
     * @return string
     */
    public function getTimeSyncOrder()
    {
        $config = Mage::getStoreConfig('webpos/offline/order_limit');
        $sourceOption = Mage::getModel('webpos/source_adminhtml_limit')->toOptionArray();
        $limitTitle = '';
        foreach ($sourceOption as $value) {
            if (isset($value['value'])  && $value['value'] == $config) {
                $limitTitle = $value['label'];
            }
        }
        return $limitTitle;
    }
    /**
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path){
        return Mage::getStoreConfig($path, Mage::app()->getStore()->getId());
    }


    public function getWebposLogo()
    {
        return $this->getStoreConfig('webpos/general/webpos_logo');
    }

    /**
     * @return bool
     */
    public function isRewardPointsEnable(){
        $moduleEnable = Mage::helper('core')->isModuleEnabled('Magestore_RewardPoints');
        $configEnable = $this->getStoreConfig('rewardpoints/general/enable');
        return ($moduleEnable && $configEnable)?true:false;
    }
    /**
     * @return bool
     */
    public function isStoreCreditEnable(){
        $moduleEnable = Mage::helper('core')->isModuleEnabled('Magestore_Customercredit');
        $configEnable = $this->getStoreConfig('customercredit/general/enable');
        return ($moduleEnable && $configEnable)?true:false;
    }

    /**
     * @return bool
     */
    public function isGiftCardEnable(){
        $moduleEnable = Mage::helper('core')->isModuleEnabled('Magestore_Giftvoucher');
        $configEnable = $this->getStoreConfig('giftvoucher/general/active');
        return ($moduleEnable && $configEnable)?true:false;
    }

    /**
     * @return bool
     */
    public function isInventorySuccessEnable(){
        $moduleEnable = Mage::helper('core')->isModuleEnabled('Magestore_Inventorysuccess');
        return ($moduleEnable)?true:false;
    }

    /**
     * @return bool
     */
    public function isStorePickupEnable(){
        $moduleEnable = Mage::helper('core')->isModuleEnabled('Magestore_Storepickup');
        return ($moduleEnable)?true:false;
    }

    /**
     * @return bool
     */
    public function isPdfinvoiceEnable(){
        $moduleEnable = Mage::helper('core')->isModuleEnabled('Magestore_Pdfinvoiceplus');
        $configEnable = $this->getStoreConfig('pdfinvoiceplus/general/enable');
        return ($moduleEnable && $configEnable)?true:false;
    }

    /**
     * @param $amount
     * @param $from
     * @param $to
     * @return float
     */
    public function toBasePrice($amount, $from, $to){
        $baseAmount = 0;
        if($amount){
            $to = ($to)?$to:Mage::app()->getStore()->getBaseCurrencyCode();
            $from = ($from)?$from:Mage::app()->getStore()->getCurrentCurrencyCode();
            if($from == $to){
                $baseAmount = $amount;
            }else{
                $allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
                $rates = Mage::getModel('directory/currency')->getCurrencyRates($to, array_values($allowedCurrencies));
                $baseAmount=$amount/$rates[$from];
            }
        }
        return floatval($baseAmount);
    }

    /**
     * @param $amount
     * @param $from
     * @param $to
     * @return float
     */
    public function convertPrice($amount, $from, $to){
        $convertedAmount = 0;
        if($amount){
            $from = ($from)?$from:Mage::app()->getStore()->getBaseCurrencyCode();
            $to = ($to)?$to:Mage::app()->getStore()->getCurrentCurrencyCode();
            if($from == $to){
                $convertedAmount = $amount;
            }else{
                $convertedAmount = Mage::helper('directory')->currencyConvert($amount, $from, $to);
            }
        }
        return floatval($convertedAmount);
    }

    public function getTaxClass() {
        $collection = Mage::getModel('tax/class')->getCollection()->addFieldToFilter('class_type', 'PRODUCT');
        $allTaxClass = array();
        foreach ($collection as $taxClass) {
            $allTaxClass[] = array(
                'tax_class_id' => $taxClass->getData('class_id'),
                'tax_class_name' => $taxClass->getData('class_name')
            );
        }
        return $allTaxClass;
    }

    /**
     * Get custom sales product
     *
     * @return boolean | Mage_Catalog_Model_Product
     */
    public function createCustomSaleProduct($taxclass = '') {
        $sku = ($taxclass)?'webpos-customsale-'.$taxclass:'webpos-customsale';
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product');

        if ($productId = $product->getIdBySku($sku)) {
            return $product->load($productId);
        }

        $entityType = $product->getResource()->getEntityType();
        $attributeSet = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityType->getId())
            ->getFirstItem();

        $product->setAttributeSetId($attributeSet->getId())
            ->setTypeId('customsale')
            ->setSku($sku)
            ->setWebsiteIds(array_keys(Mage::app()->getWebsites()));

        $product->addData(array(
            'name' => 'Custom Sale',
            'url_key' => $sku,
            'weight' => 1,
            'status' => 1,
            'visibility' => 1,
            'price' => 0,
            'description' => 'Custom Sale for POS system',
            'short_description' => 'Custom Sale for POS system',
        ));

        $stockData = array(
            'manage_stock' => 0,
            'use_config_manage_stock' => 0,
        );
        /*
         * todo: integrate IM rebuild
         * app/code/local/Magestore/Inventorysuccess/Model/Service/Adjuststock/AdjuststockService.php
         * change code line 41
         * $admin = $this->getAdminSession()->getUser();
         * $data[Magestore_Inventorysuccess_Model_Adjuststock::CREATED_BY] :
         * empty($admin)?'Anonymous':$admin->getUsername();
         * */
        $iMModuleEnable = Mage::helper('core')->isModuleEnabled('Magestore_Inventorysuccess');
        if($iMModuleEnable){
            $stockData = array(
                'manage_stock' => 1,
                'use_config_manage_stock' => 1,
                'is_in_stock' => 1,
                'use_config_min_qty' => 1,
                'use_config_min_sale_qty' => 1,
                'use_config_max_sale_qty' => 1,
                'use_config_backorders' => 1,
                'use_config_notify_stock_qty' => 1,
                'use_config_enable_qty_increments' => 1,
                'use_config_qty_increments' => 1,
            );

            $warehouseData = array();
            $collection = Mage::getModel('inventorysuccess/warehouse')->getCollection();
            $items = $collection->toArray(array(
                Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID,
                Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_NAME,
                Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE,
            ));

            if (isset($items['items']) && count($items['items'])) {
                foreach ($items['items'] as $item) {
                    $warehouseData[] = array(
                        'warehouse'      => $item[ Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID ] . '',
                        'warehouse_id'   => $item[ Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID ] . '',
                        'total_qty'      => 99999999,
                        'shelf_location' => 99999999,
                    );
                }
            }

            $product->setWarehouseStock($warehouseData);
        }

        $product->setStockData($stockData);

        if (!is_array($errors = $product->validate())) {
            try {
                $product->setTaxClassId($taxclass);
                $product->save();
                if ($iMModuleEnable) {
                    Magestore_Coresuccess_Model_Service::productSaveService()->handleProductSaveAfter($product, array('warehouse_stock' => $warehouseData));
                }
            } catch (Exception $e) {
                return $this->__('Cannot create custom product: %s', $e->getMessage());
            }
        }
        return $product;
    }

    public function getCountryArray()
    {
        $country = Mage::getSingleton('directory/country')->getResourceCollection()
            ->loadByStore()->toOptionArray();
        $newCountryArray = array();
        foreach ($country as $index => $value) {
            if ($value['value']!="") {
                $newCountryArray[] = $value;
            }
        }
        return $newCountryArray;
    }

    /**
     * @return array
     */
    public function getWebposConfig($session = false) {

        $currentStore = $this->getCurrentStore($session);

        $allowedCurrencies = array($currentStore->getCurrentCurrencyCode());
        /**
         * Get the currency rates
         * returns array with key as currency code and value as currency rate
         */
        $currencyRates = Mage::getModel('directory/currency')
            ->getCurrencyRates($currentStore->getBaseCurrencyCode(), array_values($allowedCurrencies));
        $config = array(
            'baseCurrencyCode' => $currentStore->getBaseCurrencyCode(),
            'shiftId' => Mage::helper('webpos/permission')->getCurrentShiftId(),
            'currentCurrencyCode' => $currentStore->getCurrentCurrencyCode(),
            'currentCurrencySymbol' => Mage::app()->getLocale()->currency($currentStore->getCurrentCurrencyCode())->getSymbol(),
            'currencyRates' => $currencyRates,
            'priceFormat' => Mage::app()->getLocale()->getJsPriceFormat(),
            'regionJson' => Mage::helper('directory')->getRegionJson(),
            'customerGroup' => Mage::getModel('webpos/source_adminhtml_customergroup')->getAllCustomerByCurrentStaff(),
            'defaultCustomerGroup' => Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID),
            'country' => $this->getCountryArray(),
            'defaultCountry' => Mage::getStoreConfig('general/country/default'),
            'timeoutSession' => Mage::helper('webpos/permission')->getTimeoutSession(),
            'search_string' => Mage::helper('webpos')->getStoreConfig('webpos/product_search/product_attribute'),
            'barcode_string' => Mage::helper('webpos')->getStoreConfig('webpos/product_search/barcode'),
            'tax_class' => Mage::helper('webpos')->getTaxClass(),
            'currentStoreName' => $currentStore->getName(),
            'guestCustomerId' => Mage::helper('webpos/config')->getGuestCustomerId(),
            'enablePoleDisplay' => Mage::helper('webpos/config')->isEnablePoleDisplay(),
            'authorizenet_directpost_cgi_url' => Mage::getStoreConfig('payment/authorizenet_directpost/cgi_url'),
            'manage_stock' => Mage::getStoreConfig('cataloginventory/item_options/manage_stock')

        );

        $taxConfiguration = Mage::helper('webpos/config')->getTaxConfiguration();
        $config = array_merge_recursive($config, $taxConfiguration);
        $shippingConfiguration = Mage::helper('webpos/config')->getShippingConfiguration();
        $config = array_merge_recursive($config, $shippingConfiguration);
        $receiptConfiguration = Mage::helper('webpos/config')->getReceiptConfiguration();
        $config = array_merge_recursive($config, $receiptConfiguration);
        $webposConfiguration = Mage::helper('webpos/config')->getWebposConfiguration();
        $config = array_merge_recursive($config, $webposConfiguration);
        $integrationConfiguration = Mage::helper('webpos/config')->getIntegrationConfiguration();
        $config = array_merge_recursive($config, $integrationConfiguration);
        $onlineConfig = Mage::getModel('webpos/config_onlineConfigProvider')->getConfig();
        $config = array_merge_recursive($config, $onlineConfig);

        $staffInfo = Mage::getModel('webpos/config_staff')->getConfig();
        $config = array_merge_recursive($config, $staffInfo);
        return $config;
    }

    /**
     * Get store by store id from session (selected when login)
     * @return mixed
     */
    public function getCurrentStore($session = false){
        $store = Mage::app()->getStore();
        $session = ($session)?$session:Mage::helper('webpos/permission')->getCurrentSession();
        if($session){
            $storeId = Mage::getModel('webpos/user_webpossession')->getStoreIdBySession($session);
            $store = Mage::app()->getStore($storeId);
        }
        return $store;
    }

    /**
     * Add product webpos visibility attribute
     */
    public function addWebposVisibilityAttribute(){
        $installer = new Mage_Eav_Model_Entity_Setup('catalog_setup');
        $installer->startSetup();

        $productAttributes = Mage::getResourceModel('catalog/product_attribute_collection');
        $attributeCodeArray = array();
        foreach ($productAttributes as $productAttribute) {
            $attributeCodeArray[] = $productAttribute->getAttributeCode();
        }
        if (!in_array('webpos_visible', $attributeCodeArray)) {
            $installer->addAttribute('catalog_product', 'webpos_visible', array(
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Enable On Webpos',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'webpos/source_entity_attribute_source_boolean',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '1',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => ''
                )
            );
        }
        $installer->endSetup();
    }

    /**
     * Add additional field on core tables
     */
    public function addAdditionalFields(){
        $installer = new Mage_Core_Model_Resource_Setup('core_setup');
        $installer->startSetup();
        
        $webposHelper = $this;

        /**
         * Add new fields
         */
        if (!$webposHelper->columnExist($installer->getTable('sales/quote'), 'webpos_cart_discount_name')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/quote')} ADD `webpos_cart_discount_name` text NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/quote'), 'webpos_cart_discount_type')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/quote')} ADD `webpos_cart_discount_type` text NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/quote'), 'webpos_cart_discount_value')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/quote')} ADD `webpos_cart_discount_value` decimal(12,4) NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/quote'), 'webpos_staff_id')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/quote')} ADD `webpos_staff_id` text NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/quote'), 'webpos_staff_name')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/quote')} ADD `webpos_staff_name` text NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/quote'), 'webpos_delivery_date')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/quote')} ADD `webpos_delivery_date` text NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/quote'), 'location_id')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/quote')} ADD `location_id` text NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_order_id')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `webpos_order_id` text NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_delivery_date')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `webpos_delivery_date` text NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'location_id')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `location_id` text NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_change')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `webpos_change` decimal(12,4) NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_base_change')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `webpos_base_change` decimal(12,4) NULL; ");
        }
        if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'location_id')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `location_id` text NULL; ");
        }

        /**
         * Modify exist columns
         */
        if ($webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_admin_id')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} CHANGE COLUMN `webpos_admin_id` `webpos_staff_id` int(11) unsigned NULL; ");
        }else{
            if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_staff_id')) {
                $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `webpos_staff_id` int(11) unsigned NULL; ");
            }
        }
        if ($webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_admin_name')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} CHANGE COLUMN `webpos_admin_name` `webpos_staff_name` text NULL; ");
        }else{
            if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_staff_name')) {
                $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `webpos_staff_name` text NULL; ");
            }
        }

        if ($webposHelper->columnExist($installer->getTable('sales/order'), 'till_id')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} CHANGE COLUMN `till_id` `webpos_till_id` int(11) unsigned NULL; ");
        }else{
            if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_till_id')) {
                $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `webpos_till_id` int(11) unsigned NULL; ");
            }
        }

        /**
         * Drop unused fields
         */
        if ($webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_discount_amount')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} DROP `webpos_discount_amount`; ");
        }
        if ($webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_giftwrap_amount')) {
            $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} DROP `webpos_giftwrap_amount`; ");
        }

        $installer->endSetup();
    }

    /**
     * Add new tables
     */
    public function addNewTables(){
        $installer = new Mage_Core_Model_Resource_Setup('core_setup');
        $installer->startSetup();

        $webposHelper = $this;
        if (!$webposHelper->tableExist($installer->getTable('webpos_api_session'))) {
            $installer->run("
                  CREATE TABLE {$installer->getTable('webpos_api_session')} (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `staff_id` int(11) NOT NULL DEFAULT 0,
                  `current_quote_id` int(11) NULL DEFAULT 0,
                  `current_store_id` int(11) NULL DEFAULT 0,
                  `current_till_id` int(11) NULL DEFAULT 0,
                  `logged_date` datetime DEFAULT NULL,
                  `session_id` varchar(40) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;
            ");
        }
        if (!$webposHelper->tableExist($installer->getTable('webpos_till_transaction'))) {
            $installer->run("
                  CREATE TABLE {$installer->getTable('webpos_till_transaction')} (
                      `id` int(11) unsigned NOT NULL auto_increment,
                      `till_id` int(11) unsigned NOT NULL default '0',
                      `staff_id` int(11) unsigned NOT NULL default '0',
                      `order_increment_id` varchar(255) NULL default '0',
                      `created_at` datetime NULL default NULL,
                      `amount` float DEFAULT '0',
                      `base_amount` float DEFAULT '0',
                      `transaction_currency_code` varchar(255) NOT NULL,
                      `base_currency_code` varchar(255) NOT NULL,
                      `note` text NULL,
                      `is_manual` smallint(6) NOT NULL default '1',
                      `is_opening` smallint(6) NOT NULL default '0',
                      `status` smallint(6) NOT NULL default '1',
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
        }
        if (!$webposHelper->tableExist($installer->getTable('webpos_zreport'))) {
            $installer->run("
                  CREATE TABLE {$installer->getTable('webpos_zreport')} (
                      `id` int(11) unsigned NOT NULL auto_increment,
                      `till_id` int(11) unsigned NOT NULL default '0',
                      `staff_id` int(11) unsigned NOT NULL default '0',
                      `opened_at` datetime DEFAULT NULL,
                      `closed_at` datetime DEFAULT NULL,
                      `opening_amount` float DEFAULT '0',
                      `base_opening_amount` float DEFAULT '0',
                      `closed_amount` float DEFAULT '0',
                      `base_closed_amount` float DEFAULT '0',
                      `cash_left` float DEFAULT '0',
                      `base_cash_left` float DEFAULT '0',
                      `cash_added` float DEFAULT '0',
                      `base_cash_added` float DEFAULT '0',
                      `cash_removed` float DEFAULT '0',
                      `base_cash_removed` float DEFAULT '0',
                      `cash_sale` float DEFAULT '0',
                      `base_cash_sale` float DEFAULT '0',
                      `report_currency_code` varchar(255) NOT NULL,
                      `base_currency_code` varchar(255) NOT NULL,
                      `sale_by_payments` text NULL,
                      `sales_summary` text NULL,
                      `note` text NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
        }
        if (!$webposHelper->tableExist($installer->getTable('webpos_order_payment'))) {
            $installer->run("
                CREATE TABLE {$installer->getTable('webpos_order_payment')} (
                  `payment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `order_id` int(11) NOT NULL DEFAULT 0,
                  `till_id` varchar(20) DEFAULT '0',
                  `base_payment_amount` float DEFAULT '0',
                  `payment_amount` float DEFAULT '0',
                  `base_real_amount` float DEFAULT '0',
                  `real_amount` float DEFAULT '0',
                  `method` varchar(255),
                  `method_title` varchar(255),
                  `invoice_id` varchar(255),
                  `reference_number` varchar(255),
                  PRIMARY KEY (`payment_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;
            ");
        }
        if (!$webposHelper->tableExist($installer->getTable('webpos_till'))) {
            $installer->run("
                CREATE TABLE {$installer->getTable('webpos_till')} (
                  `till_id` int(11) unsigned NOT NULL auto_increment,
			      `till_name` varchar(255) NOT NULL default '',
			      `location_id` int(11) NULL,
			      `status` smallint(6) NOT NULL default '1',
			      PRIMARY KEY (`till_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;
            ");
        }
        $installer->endSetup();
    }

    /**
     * check guest customer
     *
     * @param array $quote
     * @return boolean
     */
    public function isGuestCustomer($quote)
    {
        $helper = Mage::helper('webpos/config');
        $customerId = $quote->getCustomerId();
        $customerEmail = $quote->getCustomerEmail();
        $guestCustomerId = $helper->getDefaultCustomerId();
        $guestCustomerEmail = $helper->getDefaultCustomerEmail();
        if($customerEmail && $customerEmail != $guestCustomerEmail &&
            $customerId && $customerId != $guestCustomerId) {
            return false;
        }
        return true;
    }

    /**
     * Convert database from cash drawer to shift manager
     */
    public function convertDatabase() {
        $webposHelper = $this;
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $installer = new Mage_Core_Model_Resource_Setup('core_setup');
        $installer->startSetup();

        $webposShiftTableName = $installer->getTable('webpos_shift');
        $webposTillTransactionTableName = $installer->getTable('webpos_till_transaction');
        $webposZreportTableName = $installer->getTable('webpos_zreport');
        $webposPosTableName = $installer->getTable('webpos_pos');
        $webposTillTableName = $installer->getTable('webpos_till');
        $webposCashTransactionTableName = $installer->getTable('webpos_cash_transaction');

        if (!$webposHelper->columnExist($webposTillTransactionTableName, 'shift_id')) {
            $installer->run(" ALTER TABLE {$webposTillTransactionTableName} ADD `shift_id` int(11);");
        }

        if (!$webposHelper->columnExist($webposZreportTableName, 'opened_note')) {
            $installer->run(" ALTER TABLE {$webposZreportTableName} ADD `opened_note` text;");
        }

        if (!$webposHelper->columnExist($webposZreportTableName, 'status')) {
            $installer->run(" ALTER TABLE {$webposZreportTableName} ADD `status` smallint(1) default 0 ;");
        }

        if ($webposHelper->tableExist($webposShiftTableName)) {
            $convertZreportToShift = "INSERT INTO $webposShiftTableName (shift_id, staff_id, location_id, float_amount, base_float_amount,
            closed_amount, base_closed_amount, cash_left, base_cash_left, total_sales, base_total_sales, base_balance, 
            balance, cash_sale, base_cash_sale, cash_added, base_cash_added, cash_removed, base_cash_removed, opened_at, 
            closed_at, opened_note, closed_note, status, base_currency_code, shift_currency_code, indexeddb_id, updated_at)
            SELECT id, staff_id, 1, opening_amount, base_opening_amount, closed_amount, base_closed_amount, cash_left, 
            base_cash_left, (SELECT SUM(amount) FROM $webposTillTransactionTableName WHERE $webposTillTransactionTableName.shift_id = $webposZreportTableName.id), 
            (SELECT SUM(base_amount) FROM $webposTillTransactionTableName WHERE $webposTillTransactionTableName.shift_id = $webposZreportTableName.id), 
            (SELECT SUM(amount) FROM $webposTillTransactionTableName WHERE $webposTillTransactionTableName.shift_id = $webposZreportTableName.id), 
            (SELECT SUM(base_amount) FROM $webposTillTransactionTableName WHERE $webposTillTransactionTableName.shift_id = $webposZreportTableName.id), 
            cash_sale, base_cash_sale, cash_added, base_cash_added, cash_removed, base_cash_removed, opened_at, 
            closed_at, opened_note, '', status, base_currency_code, report_currency_code, '', NULL
            FROM $webposZreportTableName ";
            $writeConnection->query($convertZreportToShift);
        }

        if ($webposHelper->tableExist($installer->getTable('webpos_pos'))) {
            $convertTillToPos = "INSERT INTO $webposPosTableName (pos_name, location_id, store_id, user_id, status, denomination_ids)
            SELECT till_name, location_id, store_id, user_id, status, ''
            FROM $webposTillTableName";
            $writeConnection->query($convertTillToPos);
        }

        if ($webposHelper->tableExist($installer->getTable('webpos_cash_transaction'))) {
            $convertTillToCashTransaction = "INSERT INTO $webposCashTransactionTableName (shift_id, location_id, order_id, value, base_value, 
            balance, base_balance, created_at, note, type, base_currency_code, transaction_currency_code, indexeddb_id, updated_at, staff_id, staff_name)
            SELECT shift_id, 1 , order_increment_id, amount, base_amount, amount, base_amount, created_at, note, 'add', 
            base_currency_code, transaction_currency_code, NULL, NULL, staff_id, NULL
            FROM $webposTillTransactionTableName  ";
            $writeConnection->query($convertTillToCashTransaction);
        }
    }
}
