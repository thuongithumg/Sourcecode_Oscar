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
 * @package     Magestore_Webposadyen
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webposadyen Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Webposadyen
 * @author      Magestore Developer
 */
class Magestore_Webposadyen_Model_Observer
{
    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Webposadyen_Model_Observer
     */
    public function webposGetPaymentAfter($observer)
    {
        if(!Mage::helper('webpos/payment')->isRetailerPos()) {
            return $this;
        }
        $adyenHelper = Mage::helper('webposadyen');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isAdyenEnable = $adyenHelper->isEnableAdyen();
        if($isAdyenEnable) {
            $adyenPayment = $this->addWebposAdyen();
            $paymentList[] = $adyenPayment;
        }
        $payments->setList($paymentList);
    }

    /**
     * add adyen information
     *
     * @return array
     */
    public function addWebposAdyen()
    {
        $paymentHelper = Mage::helper('webpos/payment');
        $isDefault = ('adyen_integration' == $paymentHelper->getDefaultPaymentMethod()) ? 1 : 0;
        $paymentModel = array();
        $paymentModel['code'] = 'adyen_integration';
        $paymentModel['icon_class'] = 'icon-iconPOS-payment-cp1forpos';
        $paymentModel['title'] = Mage::helper('webpos')->__('Web POS - Adyen Integration');
        $paymentModel['information'] = '';
        $paymentModel['type'] = '2';
        $paymentModel['type_id'] = '2';
        $paymentModel['is_default'] = $isDefault;
        $paymentModel['is_reference_number'] = 1;
        $paymentModel['is_pay_later'] = 0;
        $paymentModel['multiable'] = 1;
        return $paymentModel;
    }

}