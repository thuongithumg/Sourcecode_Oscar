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
 * @package     Magestore_Webposauthorizenet
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webposauthorizenet Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Webposauthorizenet
 * @author      Magestore Developer
 */
class Magestore_Webposauthorizenet_Model_Observer
{
    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Webposauthorizenet_Model_Observer
     */
    public function webposGetPaymentAfter($observer)
    {
        $helper = Mage::helper('webpos/payment');
        if(!$helper->isRetailerPos())
            return $this;
        $authorizenetHelper = Mage::helper('webposauthorizenet');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isAuthorizenetEnable = $authorizenetHelper->isEnableAuthorizenet();
        if($isAuthorizenetEnable) {
            $authorizenetPayment = $this->addWebposAuthorizenet();
            $paymentList[] = $authorizenetPayment;
        }
        $payments->setList($paymentList);
    }

    /**
     * add authorizenet information
     *
     * @return array
     */
    public function addWebposAuthorizenet()
    {
        $paymentHelper = Mage::helper('webpos/payment');
        $authorizenetHelper = Mage::helper('webposauthorizenet');
        $isSandbox = $authorizenetHelper->getConfig('payment/authorizenet_is_sandbox');
        $apiLogin = $authorizenetHelper->getConfig('payment/authorizenet_api_login');
        $clientId = $authorizenetHelper->getConfig('payment/authorizenet_client_id');
        $isDefault = ('authorizenet_integration' == $paymentHelper->getDefaultPaymentMethod()) ? 1 : 0;
        $paymentModel = array();
        $paymentModel['code'] = 'authorizenet_integration';
        $paymentModel['icon_cldass'] = 'icon-iconPOS-payment-cp1forpos';
        $paymentModel['title'] = Mage::helper('webpos')->__('Web POS - Authorizenet Integration');
        $paymentModel['information'] = '';
        $paymentModel['type'] = '1';
        $paymentModel['type_id'] = '1';
        $paymentModel['is_default'] = $isDefault;
        $paymentModel['is_reference_number'] = 1;
        $paymentModel['is_pay_later'] = 0;
        $paymentModel['multiable'] = 1;
        $paymentModel['api_login'] = $apiLogin;
        $paymentModel['client_id'] = $clientId;
        $paymentModel['is_sandbox'] = $isSandbox;
        return $paymentModel;
    }
}