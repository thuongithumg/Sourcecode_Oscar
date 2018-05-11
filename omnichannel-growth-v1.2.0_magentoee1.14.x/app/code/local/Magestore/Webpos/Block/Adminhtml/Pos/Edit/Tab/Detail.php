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

class Magestore_Webpos_Block_Adminhtml_Pos_Edit_Tab_Detail extends Mage_Adminhtml_Block_Template {

    const STAFF_NAME = "staff_name";
    const SALE_SUMMARY = "sale_summary";
    const CASH_TRANSACTION = "cash_transaction";
    const ZREPORT_SALES_SUMMARY = "zreport_sales_summary";
    /**
     * @return string
     */


    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Session Detail');
        $this->setTemplate('webpos/detail.phtml');
        parent::__construct();
    }

    public function getCurrentPosId(){
        return $this->getRequest()->getParam('id');
    }

    /**
     * @param bool $isJson
     * @param string $key
     * @return array|mixed|string
     */
    public function getSessionData($isJson = true, $key = ''){
        $data = array();
        $data['current_pos_id'] = $this->getCurrentPosId();
        $data['sessions'] = $this->getCurrentSessions();
        $data['denominations'] = $this->getCurrentDenominations();
        $data['get_sessions_url'] = $this->getUrl('adminhtml/pos/getSessions', array('form_key' => $this->getFormKey(), 'pos_id' => $this->getRequest()->getParam('id')));
        $data['save_transaction_url'] = $this->getUrl('adminhtml/pos/makeAdjustment', array('form_key' => $this->getFormKey(), 'pos_id' => $this->getRequest()->getParam('id')));
        $data['close_session_url'] = $this->getUrl('adminhtml/pos/closeSession', array('form_key' => $this->getFormKey(), 'pos_id' => $this->getRequest()->getParam('id')));
        if ($key) {
            $data = (isset($data[$key]))?$data[$key]:'';
        }
        return ($isJson)?Mage::helper('core')->jsonEncode($data):$data;
    }

    /**
     * @return array
     */
    public function getCurrentSessions(){
        $posId = $this->getCurrentPosId();
        $sessionsData = array();
        $sessions = ($posId)?$this->getOpenSession($posId):array();
        if(!empty($sessions)){
            foreach ($sessions as $session){
                $sessionData = $session->getData();
                $sessionData[self::STAFF_NAME] =  $session->getStaffName();
                $sessionData[self::SALE_SUMMARY] =  $session->getSaleSummary();
                $sessionData[self::CASH_TRANSACTION] =  $session->getCashTransaction();
                $sessionData[self::ZREPORT_SALES_SUMMARY] =  $session->getZreportSalesSummary();
                $sessionData['print_url'] =  $this->getUrl('adminhtml/zreport/print', array('id' => $session->getEntityId(), 'onlyprint' => 1));
                $sessionsData[] = $sessionData;
            }
        }
        return $sessionsData;
    }

    public function getOpenSession($posId = '')
    {
        $collection = Mage::getModel('webpos/shift')->getCollection();
        if ($posId) {
            $collection->addFieldToFilter('pos_id', $posId);
        }
        $collection->addFieldToFilter('status', 0);
        if ($collection->getSize() > 0) {
            return $collection->getItems();
        }
        return array();
    }

    /**
     * @return array
     */
    public function getCurrentDenominations(){
        $posId = $this->getCurrentPosId();
        $denominations = array();
        $pos = ($posId)?Mage::getModel('webpos/pos')->load($posId):false;
        if($pos){
            $denominations = $pos->getDenominations();
        }
        return $denominations;
    }


    /**
     * Retrieve webpos configuration
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getWebposConfig()
    {
        $currentStore = Mage::app()->getStore();

        $allowedCurrencies = array($currentStore->getCurrentCurrencyCode());
        /**
         * Get the currency rates
         * returns array with key as currency code and value as currency rate
         */
        $currencyRates = Mage::getModel('directory/currency')
            ->getCurrencyRates($currentStore->getBaseCurrencyCode(), array_values($allowedCurrencies));
        $config = array(
            'baseCurrencyCode' => $currentStore->getBaseCurrencyCode(),
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
        return $config;
    }

}