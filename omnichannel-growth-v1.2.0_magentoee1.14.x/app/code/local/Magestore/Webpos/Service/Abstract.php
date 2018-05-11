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

abstract class Magestore_Webpos_Service_Abstract extends Varien_Object
{
    /**
     * @var Magestore_Webpos_Model_Checkout_Create
     */
    protected $_createModel = false;

    /**
     * @var Magestore_Webpos_Helper_Data
     */
    protected $_helper = false;

    /**
     * @var Magestore_Webpos_Helper_Config
     */
    protected $_config = false;

    /**
     * Magestore_Webpos_Model_Api2_Abstract constructor.
     */
    public function __construct() {
        $this->_helper = $this->_getHelper('webpos');
        $this->_config = $this->_getHelper('webpos/config');
        $this->_createModel = $this->_getModel('webpos/checkout_create', true);
    }
    /**
     * @param $name
     * @param array $arg
     * @return bool | Service instance
     */
    protected function _createService($name, $arg = array()){
        return (!empty($name))?$this->_getModel('magestore_webpos_service_'.$name, true, $arg):false;
    }

    /**
     * @return Magestore_Webpos_Model_Checkout_Create
     */
    public function getCheckoutModel()
    {
        return $this->_createModel;
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return ($this->getCheckoutModel()->getQuoteId())?$this->getCheckoutModel()->getQuote():$this->getStaffQuote();
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getStaffQuote()
    {
        $quote = $this->_getModel('sales/quote');
        $session = $this->_getCurrentStaffSession();
        if($session){
            $quoteId = $session->getCurrentQuoteId();
            $storeId = $session->getCurrentStoreId();
            $quote->setStoreId($storeId)->load($quoteId);
        }
        return ($session)?$quote:false;
    }

    /**
     * @return mixed
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return mixed
     */
    public function getHelperConfig()
    {
        return $this->_config;
    }

    /**
     * @param $class
     * @return mixed
     */
    protected function _getHelper($class){
        return Mage::helper($class);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function __($string){
        return $this->_helper->__($string);
    }

    /**
     * @param $eventName
     * @param $eventData
     */
    protected function _dispatchEvent($eventName, $eventData){
        Mage::dispatchEvent($eventName, $eventData);
    }

    /**
     * @param $class
     * @param bool $isSingleton
     * @return mixed
     */
    protected function _getModel($class, $isSingleton = false, $args = array()){
        return ($isSingleton)?Mage::getSingleton($class, $args):Mage::getModel($class, $args);
    }

    /**
     * @param $class
     * @param array $args
     * @return mixed
     */
    protected function _getResource($class, $args = array()){
        return Mage::getResourceModel($class, $args);
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function _getCheckoutApi($name = ''){
        $modelName = ($name)?'checkout/cart_'.$name.'_api':'checkout/cart_api';
        return $this->_getModel($modelName);
    }

    /**
     * @param $scope
     * @param $name
     * @return mixed
     */
    protected function _getDataModel($scope, $name){
        $modelName = 'webpos/'.$scope.'_data_'.$name;
        return $this->_getModel($modelName);
    }

    /**
     * @return mixed
     */
    protected function getResponseDataModel(){
        return $this->_getModel('webpos/api2_response');
    }

    /**
     * @param array $data
     * @param array $messages
     * @param $status
     * @return mixed
     */
    protected function getResponseData($data = array(), $messages = array(), $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS, $dispatchEvent = true){
        $response = $this->getResponseDataModel();
        $response->setStatus($status);
        $response->setMessages($messages);
        $response->setResponseData($data);
        if($dispatchEvent == true){
            $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_SEND_RESPONSE_BEFORE, array(
                'response' => $response,
                'quote' => $this->getQuote()
            ));
        }
        return $response->getData();
    }

    /**
     * @param Magestore_Webpos_Api_Cart_Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @return Magestore_Webpos_Model_Checkout_Create
     */
    protected function _startAction($quoteData){
        $checkout = $this->getCheckoutModel();
        $checkout->initData($quoteData);
        $checkout->enableOnlineMode();
        $this->_assignQuoteToStaff($quoteData);
        $session = $this->_getModel('checkout/session');
        $session->setWebposQuoteId($checkout->getQuote()->getId());
        $this->_getHelper('catalog/product')->setSkipSaleableCheck(true);
        return $checkout;
    }

    /**
     * @param bool $nosave
     * @return $this
     */
    protected function _finishAction($saveQuote = true){
        $checkout = $this->getCheckoutModel();
        $checkout->disableOnlineMode();
        if($saveQuote){
            $checkout->saveQuote();
        }
        return $this;
    }

    /**
     * Get session key from param
     * @return bool
     */
    protected function _getSession(){
        $session = Mage::app()->getRequest()->getParam('session');
        return ($session)?$session:false;
    }

    /**
     * Get current staff model
     * @return bool
     */
    protected function _getCurrentStaffSession(){
        $session = $this->_getSession();
        if($session){
            $sessionModel = $this->_getModel('webpos/user_webpossession');
            $sessionModel->loadBySession($session);
            if($sessionModel->getId()){
                return $sessionModel;
            }
        }
        return false;
    }

    /**
     * Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     * @param array $quoteData
     */
    protected function _assignQuoteToStaff($quoteData){
        $sessionModel = $this->_getCurrentStaffSession();
        if($sessionModel && $sessionModel->getId()){
            //$sessionModel->setCurrentTillId((!empty($quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TILL_ID]))?$quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TILL_ID]:0);
            $sessionModel->setCurrentQuoteId((!empty($quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID]))?$quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID]:0);
            $sessionModel->save();
            $this->_assignStaffToQuote($quoteData, $sessionModel);
        }
    }

    /**
     * Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     * @param array $quoteData
     */
    protected function _assignStaffToQuote($quoteData, $sessionModel){
        $sessionModel = ($sessionModel)?$sessionModel:$this->_getCurrentStaffSession();
        if($sessionModel && $sessionModel->getId() && $quoteData){
            $quoteId = $quoteData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID];
            if(!empty($quoteId)){
                $quote = $this->getQuote();
                if(!$quote->getId() || $quote->getId() != $quoteId){
                    $quote = $this->_getModel('sales/quote')->load($quoteId);
                }
                $quote->setWebposStaffId($sessionModel->getStaffId());
                $quote->setWebposStaffName($sessionModel->getStaffName());
                $quote->setWebposTillId($sessionModel->getCurrentTillId());
                $quote->setLocationId($sessionModel->getStaffLocationId());
            }
        }
    }
}
