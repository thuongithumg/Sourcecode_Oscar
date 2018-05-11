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
 * @package     Magestore_Webposstripe
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webposstripe Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Webposstripe
 * @author      Magestore Developer
 */
class Magestore_Webposstripe_Model_Observer
{
    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Webposstripe_Model_Observer
     */
    public function webposGetPaymentAfter($observer)
    {
//        $helper = Mage::helper('webpos/payment');
//        if(!$helper->isRetailerPos())
//            return $this;
        $stripeHelper = Mage::helper('webposstripe');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isStripeEnable = $stripeHelper->isEnableStripe();
        if($isStripeEnable) {
            $stripePayment = $this->addWebposStripe();
            $paymentList[] = $stripePayment;
        }
        $payments->setList($paymentList);
    }

    /**
     * add stripe information
     *
     * @return array
     */
    public function addWebposStripe()
    {
        $paymentHelper = Mage::helper('webpos/payment');
        $stripeHelper = Mage::helper('webposstripe');
        $isSandbox = $stripeHelper->getConfig('payment/stripe_is_sandbox');
        $publishableKey = $stripeHelper->getConfig('payment/stripe_publishable_key');
        $isDefault = ('stripe_integration' == $paymentHelper->getDefaultPaymentMethod()) ? 1 : 0;
        $paymentModel = array();
        $paymentModel['code'] = 'stripe_integration';
        $paymentModel['icon_class'] = 'icon-iconPOS-payment-cp1forpos';
        $paymentModel['title'] = Mage::helper('webpos')->__('Web POS - Stripe Integration');
        $paymentModel['information'] = '';
        $paymentModel['type'] = '1';
        $paymentModel['type_id'] = '1';
        $paymentModel['is_default'] = $isDefault;
        if (Mage::helper('webpos/payment')->isRetailerPos()) {
            $paymentModel['is_reference_number'] = 1;
        } else {
            $paymentModel['is_reference_number'] = 0;
        }

        $paymentModel['is_pay_later'] = 0;
        $paymentModel['multiable'] = 1;
        $paymentModel['publishable_key'] = $publishableKey;
        $paymentModel['is_sandbox'] = $isSandbox;
        return $paymentModel;
    }

}