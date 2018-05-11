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

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Block_Payment_Method_Multipayment_Info_Multipayment extends Mage_Payment_Block_Info {

    /**
     * Helper payment object
     *
     * @var \Magestore\Webpos\Helper\Payment
     */
    protected $_helperPayment = '';

    /**
     * Model order payment factory
     *
     * @var \Magestore\Webpos\Model\Payment\OrderPayment
     */
    protected $_orderPayment = '';

    /**
     * Constructor
     */
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('webpos/content/payment/method/info/multipaymentforpos.phtml');
        $this->_helperPayment = Mage::helper('webpos/payment');
        $this->_orderPayment = Mage::getModel('webpos/payment_orderPayment');
    }

    protected function _prepareSpecificInformation($transport = null) {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $data = array();
        $payments = $this->getOrderPaymentMethods();
        foreach($payments as $payment){
            $this->showSubPayment($payment, $data);
        }
        $this->addIntegrationInfo($data);
        $this->addOldVersionInfo($data);
        $transport = parent::_prepareSpecificInformation($transport);
        return $transport->setData(array_merge($data, $transport->getData()));
    }

    public function getMethodTitle() {
        return Mage::helper('webpos/payment')->getMultipaymentMethodTitle();
    }


    /**
     * Get method amount by order id and method code
     *
     * @param string, string
     * @return string
     */
    public function getPaymentInfo($orderId, $code)
    {
        $payments = $this->_orderPayment->getCollection()
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('method', $code)
        ;
        $paymentInfo = array();
        $paymentAmount = 0;
        $basePaymentAmount = 0;
        $paymentReference = '';
        if($payments->getSize() > 0){
            foreach($payments as $payment){
                if($payment->getRealAmount() > 0){
                    $paymentAmount += $payment->getRealAmount();
                }
                if($payment->getBaseRealAmount() > 0){
                    $basePaymentAmount += $payment->getBaseRealAmount();
                }
                if($payment->getReferenceNumber()){
                    $paymentReference .= $payment->getReferenceNumber().' ';
                }
            }
            $paymentInfo['amount'] = $paymentAmount;
            $paymentInfo['base_amount'] = $basePaymentAmount;
            $paymentInfo['reference_number'] = $paymentReference;
        }
        return $paymentInfo;
    }

    /**
     * Get method amount by order id and method code
     *
     * @param string, string
     * @return
     */
    public function showSubPayment($payment, &$data)
    {
        $code = $payment->getMethod();
        $order = $this->getInfo()->getOrder();
        $orderId = $order->getId();
        $referenceInfo = $this->getPaymentInfo($orderId, $code);
        $referenceNumber = $payment->getReferenceNumber();
        $paymentAmount = $payment->getRealAmount();
        $basePaymentAmount = $payment->getBaseRealAmount();
        $title = $payment->getMethodTitle();
        if(count($referenceInfo) > 0){
            $referenceNumber =  $referenceInfo['reference_number'];
            $paymentAmount =  $referenceInfo['amount'];
            $basePaymentAmount =  $referenceInfo['base_amount'];
        }
        $paymentInfo = '';
        if($referenceNumber){
            $paymentInfo .= '('.$referenceNumber.')';
        }
        if($paymentAmount){
            $amount = $this->getDisplayAmount($order, $paymentAmount, $basePaymentAmount);
            $paymentInfo = strip_tags($paymentInfo. Mage::helper('core')->formatPrice($amount, true, false));
        }else{
            $data[$title] = $paymentInfo;
        }
        if($paymentInfo != '')
            $data[$title] = $paymentInfo;
    }

    /**
     * Add info of some integration module
     *
     * @param array
     * @return
     */
    public function addIntegrationInfo(&$data)
    {
        $order = $this->getInfo()->getOrder();
        if($order && $order->getId()){
            $reward = $order->getData('rewardpoints_discount');
            $giftcard = $order->getData('gift_voucher_discount');
            if(!empty($reward) && $reward > 0){
                $title = (String)$this->__('Customer\'s Reward Points');
                $amount = Mage::helper('core')->currency($reward, true, false);
                $data[$title] = $amount;
            }
            if(!empty($giftcard) && $giftcard > 0){
                $title = (String)$this->__('Gift Voucher');
                $amount = Mage::helper('core')->currency($giftcard, true, false);
                $data[$title] = $amount;
            }
        }
    }

    /**
     * Add info of Web POS old version < 3.0.0
     *
     * @param array
     * @return
     */
    public function addOldVersionInfo(&$data)
    {
        $order = $this->getInfo()->getOrder();
        if($order && $order->getId()){
            $payments = Mage::getModel('webpos/source_adminhtml_multipaymentforpos')->getAllowPaymentMethodsWithLabel();
            if(!empty($payments)){
                foreach ($payments as $code => $label){
                    $code = ($code == 'cashforpos')?'cash':$code;
                    $amountFieldName = 'webpos_'.$code;
                    $baseAmountFieldName = 'webpos_base_'.$code;
                    $amount = $order->getData($amountFieldName);
                    $baseAmount = $order->getData($baseAmountFieldName);
                    if($amount && $amount > 0){
                        $value = $this->getDisplayAmount($order, $amount, $baseAmount);
                        $data[$label] = Mage::helper('core')->currency($value, true, false);
                    }
                }
            }
        }
    }

    /**
     * Get payment
     *
     * @param string, string
     * @return \Magento\Framework\DataObject
     */
    public function getOrderPaymentMethods()
    {
        $order = $this->getInfo()->getOrder();
        $orderId = $order->getId();
        $payments = $this->_orderPayment->getCollection()
            ->addFieldToFilter('order_id', $orderId)
        ;
        return $payments;
    }

    /**
     * @param $order
     * @param float $amount
     * @param float $baseAmount
     * @return float
     */
    public function getDisplayAmount($order, $amount = 0, $baseAmount = 0){
        $result = 0;
        if($order && $amount){
            $orderCurrencyCode = $order->getOrderCurrencyCode();
            $baseOrderCurrencyCode = $order->getBaseCurrencyCode();
            $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
            if($orderCurrencyCode == $currentCurrencyCode){
                $result = $amount;
            }elseif($baseOrderCurrencyCode == $currentCurrencyCode){
                $result = $baseAmount;
            }else{
                $result = Mage::helper('directory')->currencyConvert($baseAmount, $baseOrderCurrencyCode, $currentCurrencyCode);
            }
        }
        return floatval($result);
    }
}
