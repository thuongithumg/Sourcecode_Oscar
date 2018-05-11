<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripe\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class GetPaymentAfter
 * @package Magestore\WebposStripe\Observer
 */
class GetPaymentAfter implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $stripeHelper = \Magento\Framework\App\ObjectManager::getInstance()
                         ->create('Magestore\WebposStripe\Helper\Data');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isStripeEnable = $stripeHelper->isEnableStripe();
        if($isStripeEnable) {
            $stripePayment = $this->addWebposStripe();
            $paymentList[] = $stripePayment->getData();
        }
        $payments->setList($paymentList);
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function addWebposStripe()
    {
        $paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
                            ->create('Magestore\Webpos\Helper\Payment');
        $helper = \Magento\Framework\App\ObjectManager::getInstance()
                        ->create('Magestore\Webpos\Helper\Data');
        $isSandbox = $helper->getStoreConfig('webpos/payment/stripe/is_sandbox');
        $publishableKey = $helper->getStoreConfig('webpos/payment/stripe/publishable_key');
        $isDefault = ('stripe_integration' == $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        $paymentModel = \Magento\Framework\App\ObjectManager::getInstance()
                            ->create('Magestore\Webpos\Model\Payment\Payment');
        $paymentModel->setCode('stripe_integration');
        $paymentModel->setIconClass('stripe_integration');
        $paymentModel->setTitle(_('Web POS - Stripe Integration'));
        $paymentModel->setInformation('');
        $paymentModel->setType('1');
        $paymentModel->setTypeId('1');
        $paymentModel->setIsDefault($isDefault);
        if ($paymentHelper->isRetailerPos()) {
            $paymentModel->setIsReferenceNumber(1);
        } else {
            $paymentModel->setIsReferenceNumber(0);
        }

        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        $paymentModel->setPublishableKey($publishableKey);
        $paymentModel->setIsSandbox($isSandbox);
        return $paymentModel;
    }
}