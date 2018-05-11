<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Payment\Method;

/**
 * class \Magestore\Webpos\Block\Payment\Method\
 *
 * Abstract for POS info block
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Block\Payment\Method\
 * @module      Webpos
 * @author      Magestore Developer
 */
class InfoAbstract extends \Magento\Payment\Block\Info
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
     * @param \Magento\Framework\Model\Context $context
     * @param \Magestore\Webpos\Helper\Payment $helperPayment
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Webpos\Helper\Payment $helperPayment,
        \Magento\Framework\Pricing\Helper\Data $helperPricing,
        \Magestore\Webpos\Model\Payment\OrderPayment $orderPayment,
        array $data = []
    ) {
        $this->_helperPayment = $helperPayment;
        $this->_helperPricing = $helperPricing;
        $this->_orderPayment = $orderPayment;
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
        $orderId = $this->getInfo()->getData('parent_id');
        $code =  $this->getInfo()->getData('method');
        $amount = $this->getPaymentAmount($orderId, $code);
        if ($amount) {
            $referenceLabel = __('Reference No');
            $data[(string)$referenceLabel] = $this->_helperPricing->currency($amount, true, false);
        }
        $transport = parent::_prepareSpecificInformation($transport);
        return $transport->setData(array_merge($data, $transport->getData()));
    }

    /**
     *
     * @param string, string
     * @return float
     */
    public function getPaymentAmount($orderId, $code)
    {
        $payments = $this->_orderPayment->getCollection()
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('method', $code)
        ;
        $amount = 0;
        if($payments->getSize() > 0){
            $payment = $payments->getFirstItem();
            $amount = $payment->getRealAmount();
        }
        return $amount;
    }

    /**
     * Construct function
     */
    protected function _construct()
    {
        parent::_construct();
    }

}
