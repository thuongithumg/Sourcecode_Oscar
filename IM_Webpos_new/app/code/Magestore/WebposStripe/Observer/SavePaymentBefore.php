<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripe\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SavePaymentBefore
 * @package Magestore\WebposStripe\Observer
 */
class SavePaymentBefore implements ObserverInterface
{
    protected $moduleManager;

    protected $paymentHelper;

    protected $stripe;

    public function __construct(
        \Magento\Framework\Module\Manager $manager,
        \Magestore\Webpos\Helper\Payment $paymentHelper,
        \Magestore\WebposStripe\Model\Stripe $stripe
    ){
        $this->moduleManager = $manager;
        $this->paymentHelper = $paymentHelper;
        $this->stripe = $stripe;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $moduleManager = $this->moduleManager;
        $paymentHelper = $this->paymentHelper;

        $payment = $observer->getData('payment');
        $methodData = $payment['method_data'];
        if ($moduleManager->isEnabled('Magestore_WebposStripe') && !$paymentHelper->isRetailerPos()) {
            if (isset($methodData) && $methodData) {
                foreach ($methodData as $key=>&$method) {
                    if ($method['code'] == 'stripe_integration') {
                        $transactionId = $this->stripe->placeOrderStripeCard($method['additional_data'], $method['base_amount']);
                        if ($transactionId) {
                            $method['reference_number'] = $transactionId;
                        }
                    }
                }
            }
        }
    }
}