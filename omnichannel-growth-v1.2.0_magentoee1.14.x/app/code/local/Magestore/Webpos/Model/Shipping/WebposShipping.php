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

class Magestore_Webpos_Model_Shipping_WebposShipping extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_method_code = 'storepickup';
    protected $_cs1_method_code = 'cs1';

    protected $_code = 'webpos_shipping';

    public function getAllowedMethods() {
        return array(
            $this->_method_code => $this->getMethodName(),
            $this->_cs1_method_code => $this->getCs1MethodName()
        );
    }

    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        $result = Mage::getModel('shipping/rate_result');
        $result->append($this->_getRate());
        $result->append($this->_getCustomOneRate());
        return $result;
    }

    protected function _getRate() {
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod($this->_method_code);
        $rate->setMethodTitle($this->getMethodName());
        $rate->setPrice($this->getMethodPrice());
        $rate->setCost();
        return $rate;
    }

    protected function _getCustomOneRate() {
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod($this->_cs1_method_code);
        $rate->setMethodTitle($this->getCs1MethodName());
        $rate->setPrice($this->getCs1MethodPrice());
        $rate->setCost();
        return $rate;
    }

    public function isTrackingAvailable() {
        return true;
    }

    /**
     * Determine whether current carrier enabled for activity
     *
     * @return bool
     */
    public function isActive() {
        $active = $this->getConfigData('active');
        if (strpos(Mage::app()->getRequest()->getRequestUri(), "webpos") === false && ($active == 1 || $active == 'true'))
            return true;
        return false;
    }

    public function checkAvailableShipCountries(Mage_Shipping_Model_Rate_Request $request) {
        $active = $this->getConfigData('active');
        if ( strpos(Mage::app()->getRequest()->getRequestUri(), "webpos") !== false && ($active == 1 || $active == 'true'))
            return true;
        return false;
    }

    public function getCarrierName() {
        return $this->getConfigData('title');
    }

    public function getMethodCode() {
        return $this->_code."_".$this->_method_code;
    }

    public function getMethodName() {
        return $this->getConfigData('name');
    }

    public function getMethodPrice($a = 0, $b = 0) {
        return $this->getConfigData('price');
    }

    public function getCs1MethodName() {
        return $this->getConfigData('cs1_name');
    }

    public function getCs1MethodPrice($a = 0, $b = 0) {
        return $this->getConfigData('cs1_price');
    }

}