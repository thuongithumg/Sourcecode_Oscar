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
 * @package     Magestore_Webpospaypal
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webpospaypal Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Webpospaypal
 * @author      Magestore Developer
 */
class Magestore_Webpospaypal_Model_Observer
{
    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Webpospaypal_Model_Observer
     */
    public function webposGetPaymentAfter($observer)
    {
        $helper = Mage::helper('webpos/payment');
        if(!$helper->isRetailerPos())
            return $this;
        $paypalHelper = Mage::helper('webpospaypal');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isPaypalEnable = $paypalHelper->isEnablePaypal();
        if($isPaypalEnable) {
            $paypalPayment = $this->addWebposPaypal();
            $paymentList[] = $paypalPayment;
        }
        $isAllowPaypalHere = $paypalHelper->isAllowPaypalHere();
        if($isAllowPaypalHere) {
            $paypalPayment = $this->addWebposPaypalHere();
            $paymentList[] = $paypalPayment;
        }
        $payments->setList($paymentList);
    }

    /**
     * add paypal information
     *
     * @return array
     */
    public function addWebposPaypal()
    {
        $paymentHelper = Mage::helper('webpos/payment');
        $paypalHelper = Mage::helper('webpospaypal');
        $isSandbox = $paypalHelper->getConfig('payment/paypal_is_sandbox');
        $clientId = $paypalHelper->getConfig('payment/paypal_client_id');
        $isDefault = ('paypal_integration' == $paymentHelper->getDefaultPaymentMethod()) ? 1 : 0;
        $paymentModel = array();
        $paymentModel['code'] = 'paypal_integration';
        $paymentModel['icon_class'] = 'icon-iconPOS-payment-cp1forpos';
        $paymentModel['title'] = Mage::helper('webpos')->__('Web POS - Paypal Integration');
        $paymentModel['information'] = '';
        $paymentModel['type'] = '2';
        $paymentModel['type_id'] = '2';
        $paymentModel['is_default'] = $isDefault;
        $paymentModel['is_reference_number'] = 0;
        $paymentModel['is_pay_later'] = 0;
        $paymentModel['multiable'] = 1;
        $paymentModel['client_id'] = $clientId;
        $paymentModel['is_sandbox'] = $isSandbox;
        return $paymentModel;
    }

    /**
     * add paypal here information
     *
     * @return array
     */
    public function addWebposPaypalHere()
    {
        $paymentHelper = Mage::helper('webpos/payment');
        $paypalHelper = Mage::helper('webpospaypal');
        $isSandbox = $paypalHelper->getConfig('payment/paypal_is_sandbox');
        $clientId = $paypalHelper->getConfig('payment/paypal_client_id');
        $isDefault = ('paypal_here' == $paymentHelper->getDefaultPaymentMethod()) ? 1 : 0;
        $paymentModel = array();
        $paymentModel['code'] = 'paypal_here';
        $paymentModel['icon_class'] = 'icon-iconPOS-payment-cp1forpos';
        $paymentModel['title'] = Mage::helper('webpos')->__('Web POS - Paypal Here');
        $paymentModel['information'] = ('');
        $paymentModel['type'] = '2';
        $paymentModel['type_id'] = '2';
        $paymentModel['is_default'] = $isDefault;
        $paymentModel['is_reference_number'] = 1;
        $paymentModel['is_pay_later'] = 0;
        $paymentModel['multiable'] = 1;
        $paymentModel['is_sandbox'] = $isSandbox;
        $paymentModel['client_id'] = $clientId;
        $accessToken = $paypalHelper->getConfig('payment/paypal_access_token');
        if($accessToken) {
            $paymentModel['access_token'] = $accessToken;
        }
        return $paymentModel;
    }
}