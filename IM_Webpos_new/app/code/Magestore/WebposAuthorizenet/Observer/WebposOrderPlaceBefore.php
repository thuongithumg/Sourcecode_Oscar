<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposAuthorizenet\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class WebposOrderPlaceBefore
 * @package Magestore\WebposAuthorizenet\Observer
 */
class WebposOrderPlaceBefore implements ObserverInterface
{
    /**
     * @var \Magestore\WebposAuthorizenet\Api\AuthorizenetServiceInterface
     */
    protected $authorizenetService;

    /**
     * @var \Magestore\Webpos\Helper\Payment
     */
    protected $paymentHelper;

    /**
     * WebposOrderPlaceBefore constructor.
     * @param \Magestore\WebposAuthorizenet\Api\AuthorizenetServiceInterface $authorizenetService
     * @param \Magestore\Webpos\Helper\Payment $paymentHelper
     */
    public function __construct(
        \Magestore\WebposAuthorizenet\Api\AuthorizenetServiceInterface $authorizenetService,
        \Magestore\Webpos\Helper\Payment $paymentHelper
    )
    {
        $this->authorizenetService = $authorizenetService;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if ($this->paymentHelper->isRetailerPos())
            return $this;
        $order = $observer->getData('order');
        if (!$order)
            return $this;
        $quoteId = $order->getQuoteId();
        if (!$payment = $order->getPayment())
            return $this;
        if (!$methodData = $payment->getMethodData())
            return $this;
        if (!is_array($methodData))
            return $this;
        foreach ($methodData as $paymentItem) {
            if ($paymentItem->getCode() == 'authorizenet_integration') {
                foreach ($paymentItem->getAdditionalData() as $key => $value) {
                    if ($key == 'token' && !empty($value)) {
                        $token = $value;
                        $baseAmount = $paymentItem->getBaseAmount();
                        $paymentItem->setAdditionalData([]);
                        $referenceNumber = $this->authorizenetService->finishPayment($quoteId, $token, $baseAmount);
                        $paymentItem->setReferenceNumber($referenceNumber);
                    }
                }
            }
        }
    }
}