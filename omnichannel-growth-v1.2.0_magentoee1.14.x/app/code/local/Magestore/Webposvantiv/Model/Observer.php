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
 * @package     Magestore_Webposvantiv
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webposvantiv Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Webposvantiv
 * @author      Magestore Developer
 */
class Magestore_Webposvantiv_Model_Observer
{
    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Webposvantiv_Model_Observer
     */
    public function webposGetPaymentAfter($observer)
    {
        if(!Mage::helper('webpos/payment')->isRetailerPos()) {
            return $this;
        }
        $vantivHelper = Mage::helper('webposvantiv');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isVantivEnable = $vantivHelper->isEnableVantiv();
        if($isVantivEnable) {
            $vantivPayment = $this->addWebposVantiv();
            $paymentList[] = $vantivPayment;
        }
        $payments->setList($paymentList);
    }

    /**
     * add vantiv information
     *
     * @return array
     */
    public function addWebposVantiv()
    {
        $paymentHelper = Mage::helper('webpos/payment');
        $vantivHelper = Mage::helper('webposvantiv');
        $isSandbox = $vantivHelper->getConfig('payment/vantiv_is_sandbox');
        $accountId = $vantivHelper->getConfig('payment/vantiv_account_id');
        $applicationId = $vantivHelper->getConfig('payment/vantiv_application_id');
        $acceptorId = $vantivHelper->getConfig('payment/vantiv_acceptor_id');
        $accountToken = $vantivHelper->getConfig('payment/vantiv_account_token');
        $isDefault = ('vantiv_integration' == $paymentHelper->getDefaultPaymentMethod()) ? 1 : 0;
        $paymentModel = array();
        $paymentModel['code'] = 'vantiv_integration';
        $paymentModel['icon_class'] = 'icon-iconPOS-payment-cp1forpos';
        $paymentModel['title'] = Mage::helper('webpos')->__('Web POS - Vantiv Integration');
        $paymentModel['information'] = '';
        $paymentModel['type'] = '2';
        $paymentModel['type_id'] = '2';
        $paymentModel['is_default'] = $isDefault;
        $paymentModel['is_reference_number'] = 1;
        $paymentModel['is_pay_later'] = 0;
        $paymentModel['multiable'] = 1;
        $paymentModel['account_id'] = $accountId;
        $paymentModel['application_id'] = $applicationId;
        $paymentModel['acceptor_id'] = $acceptorId;
        $paymentModel['account_token'] = $accountToken;
        $paymentModel['is_sandbox'] = $isSandbox;
        return $paymentModel;
    }

}