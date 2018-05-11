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
 * Class Magestore_Storepickup_Block_Rewrite_CheckoutOnepagePaymentMethods
 */
class Magestore_Storepickup_Block_Rewrite_CheckoutOnepagePaymentMethods extends Mage_Checkout_Block_Onepage_Payment_Methods {

    /**
     * @return array
     */
    public function getMethods() {
        $methods = $this->getData('methods');
        if (is_null($methods)) {
            $store = $this->getQuote() ? $this->getQuote()->getStoreId() : null;
            $methods = $this->helper('payment')->getStoreMethods($store, $this->getQuote());
            foreach ($methods as $key => $method) {
                if ($this->_canUseMethod($method)) {
                    $this->_assignMethod($method);
                } else {
                    unset($methods[$key]);
                }
            }
            //mycode;
            $shipping_method = Mage::getModel('checkout/cart')->getQuote()->getShippingAddress()->getShippingMethod();
            //$storepickup_data = Mage::getSingleton('checkout/session')->getData('storepickup_session');

            //$is_storepickup = isset($storepickup_data['is_storepickup']) ? $storepickup_data['is_storepickup'] : '2';

            $newmethods = array();

            // if($is_storepickup == '1')
            if ($shipping_method && $shipping_method == 'storepickup_storepickup') {
                $is_all_method = Mage::getStoreConfig('carriers/storepickup/sallowspecific_payment');

                if (intval($is_all_method) == 0) {
                    $newmethods = $methods;
                } else {

                    $allow_method_keys = Mage::getStoreConfig('carriers/storepickup/specificpayment');
                    $allow_method_keys = explode(',', $allow_method_keys);

                    if (!count($allow_method_keys))
                        return '';

                    foreach ($methods as $method) {
                        if (in_array($method->getCode(), $allow_method_keys)) {
                            $newmethods[] = $method;
                        }
                    }
                }
            } else {

                $newmethods = $methods;
            }
            $this->setData('methods', $newmethods);

            return $newmethods;
        }
        return $methods;
    }

}