<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Payment\Method\Multipayment\Info;

/**
 * class \Magestore\Webpos\Block\Payment\Method\Multipayment\InfoMultipaymentCc
 * 
 * Multipayment for POS info block
 * Methods:
 *  _construct
 *  _prepareSpecificInformation
 *  getMethodTitle
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Block\Payment\Method\Multipayment\Info
 * @module      Webpos
 * @author      Magestore Developer
 */
class Multipayment extends \Magento\Payment\Block\Info
{
    
    /**
     * Helper payment object
     *
     * @var \Magestore\Webpos\Helper\Payment
     */
    protected $_helperPayment = '';

    /**
     * Helper payment object
     *
     * @var \Magestore\Webpos\Helper\Payment
     */
    protected $_helperPricing = '';

    /**
     * Model order payment factory
     *
     * @var \Magestore\Webpos\Model\Payment\OrderPayment
     */
    protected $_orderPayment = '';

    /**
     * Model order repository
     *
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $_orderRepository = '';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magestore\Webpos\Helper\Payment $helperPayment
     * @param \Magento\Framework\Pricing\Helper\Data
     * @param \Magestore\Webpos\Model\Payment\OrderPayment $orderPayment
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Webpos\Helper\Payment $helperPayment,
        \Magento\Framework\Pricing\Helper\Data $helperPricing,
        \Magestore\Webpos\Model\Payment\OrderPayment $orderPayment,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        array $data = []
    ) {
        $this->_helperPayment = $helperPayment;
        $this->_helperPricing = $helperPricing;
        $this->_orderPayment = $orderPayment;
        $this->_orderRepository = $orderRepository;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * 
     * @param \Magento\Framework\DataObject $transport
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $data = array();
        $payments = $this->getOrderPaymentMethods();
        foreach($payments as $payment){
            $this->showSubPayment($payment, $data);
        }
        $this->addIntegrationInfo($data);
        $transport = parent::_prepareSpecificInformation($transport);
        return $transport->setData(array_merge($data, $transport->getData()));
    }
    
    /**
     * Construct function
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Magestore_Webpos::payment/method/info/multipaymentforpos.phtml');
    }
    
    /**
     * Get method title from setting
     */
    public function getMethodTitle()
    {
        return $this->_helperPayment->getMultipaymentMethodTitle();
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
        $cardType = '';
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
                if($payment->getCardType()){
                    $cardType .= $payment->getCardType().' ';
                }
            }
            $paymentInfo['amount'] = $paymentAmount;
            $paymentInfo['base_amount'] = $basePaymentAmount;
            $paymentInfo['reference_number'] = $paymentReference;
            $paymentInfo['card_type'] = $cardType;
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
        $orderId = $this->getInfo()->getData('parent_id');
        $referenceInfo = $this->getPaymentInfo($orderId, $code);
        $referenceNumber = $payment->getReferenceNumber();
        $cardType = $payment->getCardType();
        $paymentAmount = $payment->getRealAmount();
        $basePaymentAmount = $payment->getBaseRealAmount();
        $title = $payment->getMethodTitle();
        if(count($referenceInfo) > 0){
            $referenceNumber =  $referenceInfo['reference_number'];
            $paymentAmount =  $referenceInfo['amount'];
            $basePaymentAmount =  $referenceInfo['base_amount'];
        }
        $paymentInfo = '';
        if($referenceNumber && $cardType){
            $paymentInfo .= '('.$referenceNumber. ' - '. $cardType.')';
        } else if($referenceNumber){
            $paymentInfo .= '('.$referenceNumber.')';
        }

        $paymentInfo = strip_tags($paymentInfo. $this->_helperPricing->currency($basePaymentAmount, true, false));

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
        $orderId = $this->getInfo()->getData('parent_id');
        $order = $this->_orderRepository->get($orderId);
        if($order && $order->getId()){
            $reward = $order->getData('rewardpoints_base_discount');
            $giftcard = $order->getData('base_gift_voucher_discount');
            if(!empty($reward) && $reward > 0){
                $title = (String)__('Customer\'s Reward Points');
                $amount = $this->_helperPricing->currency($reward, true, false);
                $data[$title] = $amount;
            }
            if(!empty($giftcard) && $giftcard > 0){
                $title = (String)__('Gift Voucher');
                $amount = $this->_helperPricing->currency($giftcard, true, false);
                $data[$title] = $amount;
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
        $orderId = $this->getInfo()->getData('parent_id');
        $payments = $this->_orderPayment->getCollection()
            ->addFieldToFilter('order_id', $orderId)
        ;
        return $payments;
    }

}