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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Model_Shipping_Storepickup
 */
class Magestore_Storepickup_Model_Shipping_Storepickup extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    /**
     * @var string
     */
    protected $_code = 'storepickup';

    /**
     * @return string
     */
    public function getCode() {
		return $this->_code;
	}

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|Mage_Shipping_Model_Rate_Result|void
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
		if (!$this->getConfigFlag('active')) {
			return false;
		}
		$items = $request->getAllItems();

		if (!count($items)) {
			return false;
		}
		$result = Mage::getModel('shipping/rate_result');
		/* @var $result Mage_Shipping_Model_Rate_Result */

		$result->append($this->_getStandardShippingRate());

		return $result;
	}

    /**
     * @return mixed
     */
    protected function _getStandardShippingRate() {
		/* @var $rate Mage_Shipping_Model_Rate_Result_Method */
		$method = Mage::getModel('shipping/rate_result_method');

		$method->setCarrier($this->_code);

		/**
		 * getConfigData(config_key) returns the configuration value for the
		 * carriers/[carrier_code]/[config_key]
		 */
		$method->setCarrierTitle($this->getConfigData('title'));

		$method->setMethod('storepickup');
		$method->setMethodTitle($this->getConfigData('shipping_method_title'));
		$storepickup_shipping_price = Mage::getSingleton('checkout/session')->getData('storepickup_shipping_price');
		$flag = Mage::getSingleton('adminhtml/session')->getData('storepickup_shipping_price_flag');
		if(isset($flag) && $flag == 1){
			$storepickup_shipping_price = 0;
		}
		if (isset($storepickup_shipping_price)) {
			$price = $storepickup_shipping_price;
		} else {
			$price = 0;
		}

		$shippingPrice = $this->getFinalPriceWithHandlingFee($price);
		$method->setPrice($shippingPrice);
		$method->setCost($shippingPrice);

		return $method;
	}

    /**
     * @return array
     */
    public function getAllowedMethods() {
		return array('storepickup' => 'storepickup');
	}

}
