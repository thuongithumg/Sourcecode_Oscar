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

/**
 * Class Magestore_Webpos_Model_Payment_Method_Multipayment
 */
class Magestore_Webpos_Model_Payment_Method_Multipayment extends Mage_Payment_Model_Method_Abstract {

    const ACTION_POS_ORDER             = 'pos_order';
    const EVENT_WEBPOS_START_PROCESS_PAYMENT             = 'webpos_start_process_payment';

    protected $_isInitializeNeeded      = true;
    protected $_code = 'multipaymentforpos';
    protected $_infoBlockType = 'webpos/payment_method_multipayment_info_multipayment';
    protected $_formBlockType = 'webpos/payment_method_multipayment_multipaymentforpos';

    public function isAvailable($quote = null) {
        $isWebposApi = Mage::helper('webpos/permission')->validateRequestSession();
        $routeName = Mage::app()->getRequest()->getRouteName();
        $multipaymentEnabled = Mage::helper('webpos/payment')->isMultiPaymentEnabled();
        if (($routeName == "webpos" || $isWebposApi) && $multipaymentEnabled == true)
            return true;
        else
            return false;
    }

    /**
     * @param string $paymentAction
     * @param object $stateObject
     * @return $this
     */
    public function initialize($paymentAction, $stateObject)
    {
        $paymentInfo = $this->getInfoInstance();
        $order = $paymentInfo->getOrder();
        $methodInstance = $paymentInfo->getMethodInstance();
        $orderPayment = $order->getPayment();
        $selectedMethods = $orderPayment->getAdditionalInformation(Magestore_Webpos_Api_Checkout_PaymentInterface::DATA);
        foreach ($selectedMethods as $method){
            $methodInstance->startPayment($order, $method);
        }
        return true;
    }

    /**
     * @param $order
     * @param $method
     */
    public function startPayment($order, $method)
    {
        $code = $method[Magestore_Webpos_Api_Checkout_PaymentItemInterface::CODE];
        $amount = $method[Magestore_Webpos_Api_Checkout_PaymentItemInterface::REAL_AMOUNT];
        $baseAmount = $method[Magestore_Webpos_Api_Checkout_PaymentItemInterface::BASE_REAL_AMOUNT];
        $eventData = array(
            'method_data' => $method,
            'method' => $code,
            'amount' => $amount,
            'base_amount' => $baseAmount,
            'order' => $order
        );
        Mage::dispatchEvent(self::EVENT_WEBPOS_START_PROCESS_PAYMENT, $eventData);
    }


    /**
     * Get config payment action url
     * Used to universalize payment actions when processing payment place
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return self::ACTION_POS_ORDER;
    }
}
